<?php

declare(strict_types=1);

namespace Modules\Sessions;

use Core\Database\BaseRepository;
use PDO;

final class SessionRepository extends BaseRepository
{
    /**
     * Return all sessions with related entities.
     *
     * @return array<int,array<string,mixed>>
     */
    public function all(): array
    {
        $statement = $this->db->query("
            SELECT
                s.id,
                s.session_uuid,
                s.technician_id,
                s.customer_id,
                s.device_id,
                s.session_token,
                s.status,
                s.expires_at,
                s.started_at,
                s.ended_at,
                s.created_at,
                s.updated_at,

                CONCAT(
                    u.first_name,
                    ' ',
                    u.last_name
                ) AS technician_name,

                t.display_name AS technician_display_name,

                c.company_name,

                d.device_name,
                d.hostname,
                d.fqdn,
                d.monitoring_url

            FROM sessions s

            LEFT JOIN technicians t
                ON t.id = s.technician_id

            LEFT JOIN users u
                ON u.id = t.user_id

            LEFT JOIN customers c
                ON c.id = s.customer_id

            LEFT JOIN devices d
                ON d.id = s.device_id

            ORDER BY
                s.created_at DESC,
                s.id DESC
        ");

        return $statement->fetchAll(
            PDO::FETCH_ASSOC
        );
    }


    /**
     * Find a session by ID.
     *
     * @return array<string,mixed>|null
     */
    public function find(
        int $id
    ): ?array {
        $statement = $this->db->prepare("
            SELECT
                s.id,
                s.session_uuid,
                s.technician_id,
                s.customer_id,
                s.device_id,
                s.session_token,
                s.status,
                s.expires_at,
                s.started_at,
                s.ended_at,
                s.created_at,
                s.updated_at,

                CONCAT(
                    u.first_name,
                    ' ',
                    u.last_name
                ) AS technician_name,

                t.display_name AS technician_display_name,

                c.company_name,

                d.device_name,
                d.hostname,
                d.operating_system,
                d.serial_number,
                d.mac_address,
                d.local_ip,
                d.public_ip,
                d.fqdn,
                d.monitoring_url,
                d.agent_version

            FROM sessions s

            LEFT JOIN technicians t
                ON t.id = s.technician_id

            LEFT JOIN users u
                ON u.id = t.user_id

            LEFT JOIN customers c
                ON c.id = s.customer_id

            LEFT JOIN devices d
                ON d.id = s.device_id

            WHERE s.id = :id

            LIMIT 1
        ");

        $statement->execute([
            'id' => $id
        ]);

        $session = $statement->fetch(
            PDO::FETCH_ASSOC
        );

        return $session ?: null;
    }
}