<?php

declare(strict_types=1);

namespace Modules\Monitoring;

use Core\Service;
use RuntimeException;

final class MonitoringService extends Service
{
    private MonitoringRepository $repository;


    public function __construct(
        MonitoringRepository $repository
    ) {
        parent::__construct();

        $this->repository = $repository;
    }


    /**
     * Get monitoring configuration for a device.
     *
     * @return array<string,mixed>|null
     */
    public function find(
        int $deviceId
    ): ?array {
        return $this->repository->findByDeviceId(
            $deviceId
        );
    }


    /**
     * Enable monitoring for a device.
     *
     * If monitoring does not yet exist, it is created.
     *
     * @return array{
     *     success: bool,
     *     errors: array<string,string>
     * }
     */
    public function enable(
        int $deviceId,
        int $intervalSeconds = 60,
        int $timeoutSeconds = 10
    ): array {
        $errors = $this->validateConfiguration(
            $intervalSeconds,
            $timeoutSeconds
        );

        if ($errors !== []) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }

        $monitoring =
            $this->repository->findByDeviceId(
                $deviceId
            );

        if ($monitoring === null) {

            $this->repository->create(
                $deviceId,
                $intervalSeconds,
                $timeoutSeconds
            );

        } else {

            $this->repository->updateConfiguration(
                $deviceId,
                true,
                $intervalSeconds,
                $timeoutSeconds
            );
        }

        return [
            'success' => true,
            'errors' => []
        ];
    }


    /**
     * Disable monitoring for a device.
     *
     * @return array{
     *     success: bool,
     *     errors: array<string,string>
     * }
     */
    public function disable(
        int $deviceId
    ): array {
        $monitoring =
            $this->repository->findByDeviceId(
                $deviceId
            );

        if ($monitoring === null) {
            return [
                'success' => false,
                'errors' => [
                    'monitoring' =>
                        'Monitoring configuration does not exist.'
                ]
            ];
        }

        $success =
            $this->repository->updateConfiguration(
                $deviceId,
                false,
                (int) $monitoring['interval_seconds'],
                (int) $monitoring['timeout_seconds']
            );

        if (!$success) {
            return [
                'success' => false,
                'errors' => [
                    'monitoring' =>
                        'Unable to disable monitoring.'
                ]
            ];
        }

        return [
            'success' => true,
            'errors' => []
        ];
    }


    /**
     * Return devices that are due for monitoring.
     *
     * @return array<int,array<string,mixed>>
     */
    public function dueDevices(): array
    {
        return $this->repository->dueDevices();
    }

    /**
     * Return all currently monitored devices.
     *
     * @return array<int,array<string,mixed>>
     */
    public function monitoredDevices(): array
    {
        return $this->repository->monitoredDevices();
    }

    /**
     * Process the result of a monitoring check.
     *
     * This is the core monitoring state engine.
     *
     * @return array<string,mixed>
     */
    public function processCheck(
        int $deviceId,
        string $status,
        ?int $latencyMs = null,
        ?string $errorCode = null,
        ?string $errorMessage = null
    ): array {
        $this->validateStatus(
            $status
        );

        $monitoring =
            $this->repository->findByDeviceId(
                $deviceId
            );

        if ($monitoring === null) {
            throw new RuntimeException(
                'Monitoring configuration does not exist.'
            );
        }

        $checkedAt =
            date('Y-m-d H:i:s');

        $previousStatus =
            (string) $monitoring['current_status'];

        $previousFailures =
            (int) $monitoring['consecutive_failures'];

        $previousSuccesses =
            (int) $monitoring['consecutive_successes'];


        /*
         * Record the raw check first.
         */
        $checkId =
            $this->repository->recordCheck(
                $deviceId,
                $checkedAt,
                $status,
                $latencyMs,
                $errorCode,
                $errorMessage
            );


        /*
         * Calculate consecutive results.
         */
        if ($status === 'online') {

            $consecutiveSuccesses =
                $previousSuccesses + 1;

            $consecutiveFailures = 0;

        } elseif ($status === 'offline') {

            $consecutiveFailures =
                $previousFailures + 1;

            $consecutiveSuccesses = 0;

        } else {

            /*
             * Unknown checks do not count as either
             * success or failure.
             */
            $consecutiveFailures =
                $previousFailures;

            $consecutiveSuccesses =
                $previousSuccesses;
        }


        /*
         * Determine the new calculated state.
         */
        $newStatus = $status;


        /*
         * Calculate next check time.
         */
        $intervalSeconds =
            (int) $monitoring['interval_seconds'];

        $nextCheckAt =
            date(
                'Y-m-d H:i:s',
                time() + $intervalSeconds
            );


        /*
         * Determine outage state.
         */
        $outageStartedAt =
            $monitoring['outage_started_at'];


        /*
         * Transition into offline state.
         */
        if (
            $newStatus === 'offline'
            && $previousStatus !== 'offline'
        ) {

            $outageStartedAt =
                $checkedAt;

            $openIncident =
                $this->repository->findOpenIncident(
                    $deviceId
                );

            if ($openIncident === null) {

                $reason =
                    $errorMessage
                    ?? $errorCode
                    ?? 'Device is offline.';

                $this->repository->createIncident(
                    $deviceId,
                    $checkedAt,
                    $reason
                );
            }
        }


        /*
         * Remain offline.
         *
         * Do not create duplicate incidents.
         */
        elseif (
            $newStatus === 'offline'
            && $previousStatus === 'offline'
        ) {

            $openIncident =
                $this->repository->findOpenIncident(
                    $deviceId
                );

            if (
                $openIncident === null
            ) {

                /*
                 * Defensive recovery in case the monitoring
                 * state says offline but no incident exists.
                 */
                $outageStartedAt =
                    $outageStartedAt
                    ?? $checkedAt;

                $reason =
                    $errorMessage
                    ?? $errorCode
                    ?? 'Device is offline.';

                $this->repository->createIncident(
                    $deviceId,
                    $outageStartedAt,
                    $reason
                );
            }
        }


        /*
         * Transition back online.
         */
        elseif (
            $newStatus === 'online'
            && $previousStatus === 'offline'
        ) {

            $openIncident =
                $this->repository->findOpenIncident(
                    $deviceId
                );

            if ($openIncident !== null) {

                $startedAt =
                    strtotime(
                        (string) $openIncident['started_at']
                    );

                $resolvedAt =
                    strtotime($checkedAt);

                $duration =
                    max(
                        0,
                        $resolvedAt - $startedAt
                    );

                $this->repository->resolveIncident(
                    (int) $openIncident['id'],
                    $checkedAt,
                    $duration
                );
            }

            $outageStartedAt = null;
        }


        /*
         * Online state has no active outage.
         */
        elseif (
            $newStatus === 'online'
        ) {

            $outageStartedAt =
                null;
        }


        /*
         * Persist calculated monitoring state.
         */
        $this->repository->updateState(
            $deviceId,
            $newStatus,
            $latencyMs,
            $consecutiveFailures,
            $consecutiveSuccesses,
            $checkedAt,
            $nextCheckAt,
            $outageStartedAt
        );


        /*
         * Synchronize the main device record.
         */
        $this->repository->updateDeviceStatus(
            $deviceId,
            $newStatus
        );

        /*
         * Only successful checks update last_seen.
         */
        if ($status === 'online') {
            $this->repository->updateDeviceLastSeen(
                $deviceId,
                $checkedAt
            );
        }


        return [
            'success' => true,
            'check_id' => $checkId,
            'device_id' => $deviceId,
            'previous_status' => $previousStatus,
            'status' => $newStatus,
            'latency_ms' => $latencyMs,
            'consecutive_failures' =>
                $consecutiveFailures,
            'consecutive_successes' =>
                $consecutiveSuccesses,
            'checked_at' => $checkedAt,
            'next_check_at' => $nextCheckAt,
            'outage_started_at' =>
                $outageStartedAt
        ];
    }


    /**
     * Validate monitoring configuration.
     *
     * @return array<string,string>
     */
    private function validateConfiguration(
        int $intervalSeconds,
        int $timeoutSeconds
    ): array {
        $errors = [];

        if ($intervalSeconds < 10) {
            $errors['interval_seconds'] =
                'Monitoring interval must be at least 10 seconds.';
        }

        if ($timeoutSeconds < 1) {
            $errors['timeout_seconds'] =
                'Monitoring timeout must be at least 1 second.';
        }

        if (
            $timeoutSeconds >= $intervalSeconds
        ) {
            $errors['timeout_seconds'] =
                'Monitoring timeout must be shorter than the monitoring interval.';
        }

        return $errors;
    }


    /**
     * Validate monitoring status.
     */
    private function validateStatus(
        string $status
    ): void {
        if (
            !in_array(
                $status,
                [
                    'online',
                    'offline',
                    'unknown'
                ],
                true
            )
        ) {
            throw new RuntimeException(
                'Invalid monitoring status.'
            );
        }
    }

    /**
     * Return monitored devices for the Monitor screen.
     *
     * @return array<int,array<string,mixed>>
     */
    public function devices(): array
    {
        return $this->repository->monitoredDevices();
    }


    /**
     * Return complete monitoring details for a device.
     *
     * @return array<string,mixed>|null
     */
    public function details(
        int $deviceId
    ): ?array {
        return $this->repository->monitoringDetails(
            $deviceId
        );
    }


    /**
     * Return recent checks for a device.
     *
     * @return array<int,array<string,mixed>>
     */
    public function recentChecks(
        int $deviceId,
        int $limit = 50
    ): array {
        return $this->repository->recentChecks(
            $deviceId,
            $limit
        );
    }


    /**
     * Return incidents for a device.
     *
     * @return array<int,array<string,mixed>>
     */
    public function incidents(
        int $deviceId,
        int $limit = 50
    ): array {
        return $this->repository->incidents(
            $deviceId,
            $limit
        );
    }




}