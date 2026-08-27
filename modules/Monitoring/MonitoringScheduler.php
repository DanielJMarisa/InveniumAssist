<?php

declare(strict_types=1);

namespace Modules\Monitoring;

final class MonitoringScheduler
{
    private MonitoringService $service;

    private HealthChecker $checker;


    public function __construct(
        MonitoringService $service,
        HealthChecker $checker
    ) {
        $this->service = $service;
        $this->checker = $checker;
    }


    /**
     * Process all devices currently due for monitoring.
     *
     * @return array<int,array<string,mixed>>
     */
    public function run(): array
    {
        $devices = $this->service->dueDevices();

        $results = [];

        foreach ($devices as $device) {

            $deviceId = (int) $device['device_id'];

            $timeoutSeconds = max(
                1,
                (int) $device['timeout_seconds']
            );

            try {

                $check = $this->checker->check(
                    $device,
                    $timeoutSeconds
                );

                $result = $this->service->processCheck(
                    $deviceId,
                    $check['status'],
                    $check['latency_ms'],
                    $check['error_code'],
                    $check['error_message']
                );

                $results[] = [
                    'success' => true,
                    'device_id' => $deviceId,
                    'status' => $check['status'],
                    'result' => $result
                ];

            } catch (\Throwable $exception) {

                /*
                 * A failure processing one device must not
                 * prevent the remaining devices from being checked.
                 */
                $results[] = [
                    'success' => false,
                    'device_id' => $deviceId,
                    'status' => 'unknown',
                    'error' => $exception->getMessage()
                ];
            }
        }

        return $results;
    }
}