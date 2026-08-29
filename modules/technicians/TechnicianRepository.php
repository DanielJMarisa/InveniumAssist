<?php

declare(strict_types=1);

namespace Modules\Technicians;

use Core\Database\BaseRepository;
use PDO;

final class TechnicianRepository extends BaseRepository
{
    /**
     * Find a technician by ID.
     *
     * @return array<string,mixed>|null
     */
    public function find(int $id): ?array
    {
        $statement = $this->db->prepare("
            SELECT
                t.id,
                t.user_id,
                t.display_name,
                t.status,
                t.heartbeat_at,
                t.current_sessions,
                t.created_at,
                t.updated_at,
                u.first_name,
                u.last_name,
                u.email,
                u.username,
                u.status AS user_status
            FROM technicians t
            INNER JOIN users u
                ON u.id = t.user_id
            WHERE t.id = :id
            LIMIT 1
        ");

        $statement->execute([
            'id' => $id
        ]);

        $technician = $statement->fetch(PDO::FETCH_ASSOC);

        return $technician ?: null;
    }

    /**
     * Find technician linked to a user.
     *
     * @return array<string,mixed>|null
     */
    public function findByUserId(int $userId): ?array
    {
        $statement = $this->db->prepare("
            SELECT
                t.id,
                t.user_id,
                t.display_name,
                t.status,
                t.heartbeat_at,
                t.current_sessions,
                t.created_at,
                t.updated_at,
                u.first_name,
                u.last_name,
                u.email,
                u.username,
                u.status AS user_status
            FROM technicians t
            INNER JOIN users u
                ON u.id = t.user_id
            WHERE t.user_id = :user_id
            LIMIT 1
        ");

        $statement->execute([
            'user_id' => $userId
        ]);

        $technician = $statement->fetch(PDO::FETCH_ASSOC);

        return $technician ?: null;
    }

    /**
     * Return all technicians.
     *
     * @return array<int,array<string,mixed>>
     */
    public function all(): array
    {
        $statement = $this->db->query("
            SELECT
                t.id,
                t.user_id,
                t.display_name,
                t.status,
                t.heartbeat_at,
                t.current_sessions,
                t.created_at,
                t.updated_at,
                u.first_name,
                u.last_name,
                u.email,
                u.username,
                u.status AS user_status
            FROM technicians t
            INNER JOIN users u
                ON u.id = t.user_id
            ORDER BY
                t.display_name ASC,
                t.id ASC
        ");

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Return technicians currently available.
     *
     * A technician is considered available only when:
     *
     * - technician status is available
     * - linked user account is active
     * - heartbeat is within the supplied threshold
     *
     * @return array<int,array<string,mixed>>
     */
    public function available(int $heartbeatSeconds = 60): array
    {
        $heartbeatSeconds = max(10, min($heartbeatSeconds, 3600));

        $statement = $this->db->prepare("
            SELECT
                t.id,
                t.user_id,
                t.display_name,
                t.status,
                t.heartbeat_at,
                t.current_sessions
            FROM technicians t
            INNER JOIN users u
                ON u.id = t.user_id
            WHERE t.status = 'available'
              AND u.status = 'active'
              AND t.heartbeat_at IS NOT NULL
              AND t.heartbeat_at >= DATE_SUB(
                    NOW(),
                    INTERVAL {$heartbeatSeconds} SECOND
              )
            ORDER BY
                t.display_name ASC,
                t.id ASC
        ");

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create a technician record.
     */
    public function create(
        int $userId,
        string $displayName
    ): int {
        $statement = $this->db->prepare("
            INSERT INTO technicians (
                user_id,
                display_name,
                status,
                heartbeat_at,
                current_sessions,
                created_at,
                updated_at
            ) VALUES (
                :user_id,
                :display_name,
                'offline',
                NULL,
                0,
                NOW(),
                NOW()
            )
        ");

        $statement->execute([
            'user_id' => $userId,
            'display_name' => trim($displayName)
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Update technician status.
     */
    public function updateStatus(
        int $technicianId,
        string $status
    ): bool {
        $statement = $this->db->prepare("
            UPDATE technicians
            SET
                status = :status,
                updated_at = NOW()
            WHERE id = :id
        ");

        return $statement->execute([
            'id' => $technicianId,
            'status' => $status
        ]);
    }

    /**
     * Record technician heartbeat.
     *
     * The heartbeat also updates the technician's status.
     */
    public function heartbeat(
        int $technicianId,
        string $status
    ): bool {
        $statement = $this->db->prepare("
            UPDATE technicians
            SET
                status = :status,
                heartbeat_at = NOW(),
                updated_at = NOW()
            WHERE id = :id
        ");

        return $statement->execute([
            'id' => $technicianId,
            'status' => $status
        ]);
    }

    /**
     * Increment current session count.
     */
    public function incrementSessions(
        int $technicianId
    ): bool {
        $statement = $this->db->prepare("
            UPDATE technicians
            SET
                current_sessions = current_sessions + 1,
                updated_at = NOW()
            WHERE id = :id
        ");

        return $statement->execute([
            'id' => $technicianId
        ]);
    }

    /**
     * Decrement current session count.
     */
    public function decrementSessions(
        int $technicianId
    ): bool {
        $statement = $this->db->prepare("
            UPDATE technicians
            SET
                current_sessions = GREATEST(
                    current_sessions - 1,
                    0
                ),
                updated_at = NOW()
            WHERE id = :id
        ");

        return $statement->execute([
            'id' => $technicianId
        ]);
    }

    /**
     * Mark stale available technicians offline.
     *
     * @return int Number of technicians updated.
     */
    public function markStaleOffline(
        int $heartbeatSeconds = 60
    ): int {
        $heartbeatSeconds = max(10, min($heartbeatSeconds, 3600));

        $statement = $this->db->prepare("
            UPDATE technicians
            SET
                status = 'offline',
                updated_at = NOW()
            WHERE status = 'available'
              AND (
                    heartbeat_at IS NULL
                    OR heartbeat_at < DATE_SUB(
                        NOW(),
                        INTERVAL {$heartbeatSeconds} SECOND
                    )
              )
        ");

        $statement->execute();

        return $statement->rowCount();
    }
}