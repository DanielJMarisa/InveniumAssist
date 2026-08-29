<?php

declare(strict_types=1);

namespace Modules\Audit;

use Core\Database\Database;
use PDO;

final class AuditRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }


    /**
     * Return all audit log entries.
     */
    public function all(): array
    {
        $sql = "
            SELECT
                a.id,
                a.user_id,
                a.module,
                a.action,
                a.ip_address,
                a.user_agent,
                a.created_at,
                CONCAT(
                    u.first_name,
                    ' ',
                    u.last_name
                ) AS user_name,
                u.email AS user_email
            FROM audit_logs a
            LEFT JOIN users u
                ON u.id = a.user_id
            ORDER BY a.created_at DESC, a.id DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );
    }


    /**
     * Find one audit log entry.
     */
    public function find(
        int $id
    ): ?array {
        $sql = "
            SELECT
                a.id,
                a.user_id,
                a.module,
                a.action,
                a.ip_address,
                a.user_agent,
                a.created_at,
                CONCAT(
                    u.first_name,
                    ' ',
                    u.last_name
                ) AS user_name,
                u.email AS user_email
            FROM audit_logs a
            LEFT JOIN users u
                ON u.id = a.user_id
            WHERE a.id = :id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'id' => $id
        ]);

        $audit = $stmt->fetch(
            PDO::FETCH_ASSOC
        );

        return $audit !== false
            ? $audit
            : null;
    }
}