<?php

declare(strict_types=1);

namespace Modules\Monitoring;

use Core\Database\BaseRepository;
use PDO;

final class MonitoringRepository extends BaseRepository
{
    /**
     * Find monitoring configuration for a device.
     *
     * @return array<string,mixed>|null
     */
    public function findByDeviceId(
        int $deviceId
    ): ?array {
        $statement = $this->db->prepare("
            SELECT
                dm.id,
                dm.device_id,
                dm.enabled,
                dm.interval_seconds,
                dm.timeout_seconds,
                dm.last_check_at,
                dm.next_check_at,
                dm.current_status,
                dm.current_latency_ms,
                dm.consecutive_failures,
                dm.consecutive_successes,
                dm.outage_started_at,
                dm.created_at,
                dm.updated_at
            FROM device_monitoring dm
            WHERE dm.device_id = :device_id
            LIMIT 1
        ");

        $statement->execute([
            'device_id' => $deviceId
        ]);

        $monitoring = $statement->fetch(PDO::FETCH_ASSOC);

        return $monitoring ?: null;
    }


    /**
     * Create monitoring configuration for a device.
     */
    public function create(
        int $deviceId,
        int $intervalSeconds = 60,
        int $timeoutSeconds = 10
    ): int {
        $statement = $this->db->prepare("
            INSERT INTO device_monitoring (
                device_id,
                enabled,
                interval_seconds,
                timeout_seconds,
                current_status,
                consecutive_failures,
                consecutive_successes,
                created_at,
                updated_at
            ) VALUES (
                :device_id,
                1,
                :interval_seconds,
                :timeout_seconds,
                'unknown',
                0,
                0,
                NOW(),
                NOW()
            )
        ");

        $statement->execute([
            'device_id' => $deviceId,
            'interval_seconds' => $intervalSeconds,
            'timeout_seconds' => $timeoutSeconds
        ]);

        return (int) $this->db->lastInsertId();
    }


    /**
     * Update monitoring configuration.
     */
    public function updateConfiguration(
        int $deviceId,
        bool $enabled,
        int $intervalSeconds,
        int $timeoutSeconds
    ): bool {
        $statement = $this->db->prepare("
            UPDATE device_monitoring
            SET
                enabled = :enabled,
                interval_seconds = :interval_seconds,
                timeout_seconds = :timeout_seconds,
                updated_at = NOW()
            WHERE device_id = :device_id
        ");

        return $statement->execute([
            'device_id' => $deviceId,
            'enabled' => $enabled ? 1 : 0,
            'interval_seconds' => $intervalSeconds,
            'timeout_seconds' => $timeoutSeconds
        ]);
    }


    /**
     * Return devices whose monitoring check is due.
     *
     * A device is due when:
     *
     * - monitoring is enabled, and
     * - next_check_at is null, or
     * - next_check_at has passed.
     *
     * @return array<int,array<string,mixed>>
     */
    public function dueDevices(): array
    {
        $statement = $this->db->query("
            SELECT
                d.id AS device_id,
                d.customer_id,
                d.hostname,
                d.device_name,
                d.operating_system,
                d.serial_number,
                d.mac_address,
                d.local_ip,
                d.public_ip,
                d.agent_version,
                d.status AS device_status,
                d.last_seen,

                dm.id AS monitoring_id,
                dm.enabled,
                dm.interval_seconds,
                dm.timeout_seconds,
                dm.last_check_at,
                dm.next_check_at,
                dm.current_status,
                dm.current_latency_ms,
                dm.consecutive_failures,
                dm.consecutive_successes,
                dm.outage_started_at

            FROM device_monitoring dm

            INNER JOIN devices d
                ON d.id = dm.device_id

            WHERE dm.enabled = 1

              AND (
                    dm.next_check_at IS NULL
                    OR dm.next_check_at <= NOW()
              )

            ORDER BY
                COALESCE(dm.next_check_at, '1970-01-01 00:00:00') ASC,
                dm.device_id ASC
        ");

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Record a monitoring check.
     */
    public function recordCheck(
        int $deviceId,
        string $checkedAt,
        string $status,
        ?int $latencyMs = null,
        ?string $errorCode = null,
        ?string $errorMessage = null
    ): int {
        $statement = $this->db->prepare("
            INSERT INTO monitoring_checks (
                device_id,
                checked_at,
                status,
                latency_ms,
                error_code,
                error_message,
                created_at
            ) VALUES (
                :device_id,
                :checked_at,
                :status,
                :latency_ms,
                :error_code,
                :error_message,
                NOW()
            )
        ");

        $statement->execute([
            'device_id' => $deviceId,
            'checked_at' => $checkedAt,
            'status' => $status,
            'latency_ms' => $latencyMs,
            'error_code' => $errorCode,
            'error_message' => $errorMessage
        ]);

        return (int) $this->db->lastInsertId();
    }


    /**
     * Update the calculated monitoring state.
     */
    public function updateState(
        int $deviceId,
        string $status,
        ?int $latencyMs,
        int $consecutiveFailures,
        int $consecutiveSuccesses,
        ?string $lastCheckAt,
        ?string $nextCheckAt,
        ?string $outageStartedAt
    ): bool {
        $statement = $this->db->prepare("
            UPDATE device_monitoring
            SET
                current_status = :current_status,
                current_latency_ms = :current_latency_ms,
                consecutive_failures = :consecutive_failures,
                consecutive_successes = :consecutive_successes,
                last_check_at = :last_check_at,
                next_check_at = :next_check_at,
                outage_started_at = :outage_started_at,
                updated_at = NOW()
            WHERE device_id = :device_id
        ");

        return $statement->execute([
            'device_id' => $deviceId,
            'current_status' => $status,
            'current_latency_ms' => $latencyMs,
            'consecutive_failures' => $consecutiveFailures,
            'consecutive_successes' => $consecutiveSuccesses,
            'last_check_at' => $lastCheckAt,
            'next_check_at' => $nextCheckAt,
            'outage_started_at' => $outageStartedAt
        ]);
    }


    /**
     * Update the device's operational status.
     */
    public function updateDeviceStatus(
        int $deviceId,
        string $status
    ): bool {
        $statement = $this->db->prepare("
            UPDATE devices
            SET
                status = :status,
                updated_at = NOW()
            WHERE id = :device_id
        ");

        return $statement->execute([
            'device_id' => $deviceId,
            'status' => $status
        ]);
    }


    /**
     * Update the last successful observation time for a device.
     */
    public function updateDeviceLastSeen(
        int $deviceId,
        string $lastSeen
    ): bool {
        $statement = $this->db->prepare("
            UPDATE devices
            SET
                last_seen = :last_seen,
                updated_at = NOW()
            WHERE id = :device_id
        ");

        return $statement->execute([
            'device_id' => $deviceId,
            'last_seen' => $lastSeen
        ]);
    }


    /**
     * Find the currently open incident for a device.
     *
     * @return array<string,mixed>|null
     */
    public function findOpenIncident(
        int $deviceId
    ): ?array {
        $statement = $this->db->prepare("
            SELECT
                id,
                device_id,
                started_at,
                resolved_at,
                duration_seconds,
                reason,
                status,
                created_at,
                updated_at
            FROM monitoring_incidents
            WHERE device_id = :device_id
              AND status = 'open'
            ORDER BY started_at DESC, id DESC
            LIMIT 1
        ");

        $statement->execute([
            'device_id' => $deviceId
        ]);

        $incident = $statement->fetch(PDO::FETCH_ASSOC);

        return $incident ?: null;
    }


    /**
     * Create a monitoring incident.
     */
    public function createIncident(
        int $deviceId,
        string $startedAt,
        ?string $reason = null
    ): int {
        $statement = $this->db->prepare("
            INSERT INTO monitoring_incidents (
                device_id,
                started_at,
                reason,
                status,
                created_at,
                updated_at
            ) VALUES (
                :device_id,
                :started_at,
                :reason,
                'open',
                NOW(),
                NOW()
            )
        ");

        $statement->execute([
            'device_id' => $deviceId,
            'started_at' => $startedAt,
            'reason' => $reason
        ]);

        return (int) $this->db->lastInsertId();
    }


    /**
     * Resolve an open monitoring incident.
     */
    public function resolveIncident(
        int $incidentId,
        string $resolvedAt,
        int $durationSeconds
    ): bool {
        $statement = $this->db->prepare("
            UPDATE monitoring_incidents
            SET
                resolved_at = :resolved_at,
                duration_seconds = :duration_seconds,
                status = 'resolved',
                updated_at = NOW()
            WHERE id = :id
              AND status = 'open'
        ");

        return $statement->execute([
            'id' => $incidentId,
            'resolved_at' => $resolvedAt,
            'duration_seconds' => $durationSeconds
        ]);
    }


    /**
     * Return recent monitoring checks for a device.
     *
     * @return array<int,array<string,mixed>>
     */
    public function recentChecks(
        int $deviceId,
        int $limit = 50
    ): array {
        $limit = max(1, min($limit, 500));

        $statement = $this->db->prepare("
            SELECT
                id,
                device_id,
                checked_at,
                status,
                latency_ms,
                error_code,
                error_message,
                created_at
            FROM monitoring_checks
            WHERE device_id = :device_id
            ORDER BY checked_at DESC, id DESC
            LIMIT {$limit}
        ");

        $statement->execute([
            'device_id' => $deviceId
        ]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Return monitoring incidents for a device.
     *
     * @return array<int,array<string,mixed>>
     */
    public function incidents(
        int $deviceId,
        int $limit = 50
    ): array {
        $limit = max(1, min($limit, 500));

        $statement = $this->db->prepare("
            SELECT
                id,
                device_id,
                started_at,
                resolved_at,
                duration_seconds,
                reason,
                status,
                created_at,
                updated_at
            FROM monitoring_incidents
            WHERE device_id = :device_id
            ORDER BY started_at DESC, id DESC
            LIMIT {$limit}
        ");

        $statement->execute([
            'device_id' => $deviceId
        ]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Return all devices with monitoring information.
     *
     * @return array<int,array<string,mixed>>
     */
    public function monitoredDevices(): array
    {
        $statement = $this->db->query("
            SELECT
                d.id AS device_id,
                d.customer_id,
                d.device_name,
                d.hostname,
                d.operating_system,
                d.status AS device_status,
                d.last_seen,

                c.company_name,

                dm.id AS monitoring_id,
                dm.enabled,
                dm.interval_seconds,
                dm.timeout_seconds,
                dm.last_check_at,
                dm.next_check_at,
                dm.current_status,
                dm.current_latency_ms,
                dm.consecutive_failures,
                dm.consecutive_successes,
                dm.outage_started_at

            FROM device_monitoring dm

            INNER JOIN devices d
                ON d.id = dm.device_id

            LEFT JOIN customers c
                ON c.id = d.customer_id

            ORDER BY
                CASE dm.current_status
                    WHEN 'offline' THEN 1
                    WHEN 'unknown' THEN 2
                    WHEN 'online' THEN 3
                    ELSE 4
                END,
                c.company_name ASC,
                d.device_name ASC,
                d.id ASC
        ");

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Return complete monitoring information for a device.
     *
     * @return array<string,mixed>|null
     */
    public function monitoringDetails(
        int $deviceId
    ): ?array {
        $statement = $this->db->prepare("
            SELECT
                d.id AS device_id,
                d.customer_id,
                d.device_name,
                d.hostname,
                d.operating_system,
                d.serial_number,
                d.mac_address,
                d.local_ip,
                d.public_ip,
                d.agent_version,
                d.status AS device_status,
                d.last_seen,

                c.company_name,

                dm.id AS monitoring_id,
                dm.enabled,
                dm.interval_seconds,
                dm.timeout_seconds,
                dm.last_check_at,
                dm.next_check_at,
                dm.current_status,
                dm.current_latency_ms,
                dm.consecutive_failures,
                dm.consecutive_successes,
                dm.outage_started_at,
                dm.created_at,
                dm.updated_at

            FROM devices d

            LEFT JOIN customers c
                ON c.id = d.customer_id

            LEFT JOIN device_monitoring dm
                ON dm.device_id = d.id

            WHERE d.id = :device_id

            LIMIT 1
        ");

        $statement->execute([
            'device_id' => $deviceId
        ]);

        $monitoring = $statement->fetch(
            PDO::FETCH_ASSOC
        );

        return $monitoring ?: null;
    }



}