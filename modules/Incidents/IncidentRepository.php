<?php

declare(strict_types=1);

namespace Modules\Incidents;

use Core\Database\BaseRepository;
use PDO;

final class IncidentRepository extends BaseRepository
{
    /**
     * Return monitoring incidents.
     *
     * @return array<int,array<string,mixed>>
     */
    public function all(): array
    {
        $statement = $this->db->query("
            SELECT
                mi.id,
                mi.device_id,
                mi.started_at,
                mi.resolved_at,
                mi.duration_seconds,
                mi.reason,
                mi.status,
                mi.notes,
                mi.created_at,
                mi.updated_at,

                d.device_name,
                d.hostname,
                d.status AS device_status,

                c.id AS customer_id,
                c.company_name

            FROM monitoring_incidents mi

            INNER JOIN devices d
                ON d.id = mi.device_id

            LEFT JOIN customers c
                ON c.id = d.customer_id

            ORDER BY
                CASE mi.status
                    WHEN 'open' THEN 1
                    WHEN 'resolved' THEN 2
                    ELSE 3
                END,
                mi.started_at DESC,
                mi.id DESC
        ");

        return $statement->fetchAll(
            PDO::FETCH_ASSOC
        );
    }


    /**
     * Find an incident by ID.
     *
     * @return array<string,mixed>|null
     */
    public function findById(
        int $incidentId
    ): ?array {
        $statement = $this->db->prepare("
            SELECT
                mi.id,
                mi.device_id,
                mi.started_at,
                mi.resolved_at,
                mi.duration_seconds,
                mi.reason,
                mi.status,
                mi.notes,
                mi.created_at,
                mi.updated_at,

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

                c.id AS customer_id,
                c.company_name

            FROM monitoring_incidents mi

            INNER JOIN devices d
                ON d.id = mi.device_id

            LEFT JOIN customers c
                ON c.id = d.customer_id

            WHERE mi.id = :id

            LIMIT 1
        ");

        $statement->execute([
            'id' => $incidentId
        ]);

        $incident = $statement->fetch(
            PDO::FETCH_ASSOC
        );

        return $incident ?: null;
    }


    /**
     * Return currently open incidents.
     *
     * @return array<int,array<string,mixed>>
     */
    public function open(): array
    {
        $statement = $this->db->query("
            SELECT
                mi.id,
                mi.device_id,
                mi.started_at,
                mi.resolved_at,
                mi.duration_seconds,
                mi.reason,
                mi.status,
                mi.notes,
                mi.created_at,
                mi.updated_at,

                d.device_name,
                d.hostname,
                d.status AS device_status,

                c.id AS customer_id,
                c.company_name

            FROM monitoring_incidents mi

            INNER JOIN devices d
                ON d.id = mi.device_id

            LEFT JOIN customers c
                ON c.id = d.customer_id

            WHERE mi.status = 'open'

            ORDER BY
                mi.started_at ASC,
                mi.id ASC
        ");

        return $statement->fetchAll(
            PDO::FETCH_ASSOC
        );
    }


    /**
     * Return resolved incidents.
     *
     * @return array<int,array<string,mixed>>
     */
    public function resolved(): array
    {
        $statement = $this->db->query("
            SELECT
                mi.id,
                mi.device_id,
                mi.started_at,
                mi.resolved_at,
                mi.duration_seconds,
                mi.reason,
                mi.status,
                mi.notes,
                mi.created_at,
                mi.updated_at,

                d.device_name,
                d.hostname,
                d.status AS device_status,

                c.id AS customer_id,
                c.company_name

            FROM monitoring_incidents mi

            INNER JOIN devices d
                ON d.id = mi.device_id

            LEFT JOIN customers c
                ON c.id = d.customer_id

            WHERE mi.status = 'resolved'

            ORDER BY
                mi.resolved_at DESC,
                mi.id DESC
        ");

        return $statement->fetchAll(
            PDO::FETCH_ASSOC
        );
    }


    /**
     * Update incident notes.
     */
    public function updateNotes(
        int $incidentId,
        ?string $notes
    ): bool {
        $statement = $this->db->prepare("
            UPDATE monitoring_incidents
            SET
                notes = :notes,
                updated_at = NOW()
            WHERE id = :id
        ");

        return $statement->execute([
            'id' => $incidentId,
            'notes' => $notes
        ]);
    }
}