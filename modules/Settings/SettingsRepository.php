<?php

declare(strict_types=1);

namespace Modules\Settings;

use Core\Database\Database;
use PDO;

final class SettingsRepository
{
    private PDO $db;


    public function __construct()
    {
        $this->db = Database::connection();
    }


    /**
     * Return all settings.
     */
    public function all(): array
    {
        $stmt = $this->db->prepare(
            "
            SELECT
                id,
                setting_key,
                setting_value,
                created_at,
                updated_at
            FROM settings
            ORDER BY setting_key ASC
            "
        );

        $stmt->execute();

        return $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );
    }


    /**
     * Find a setting by key.
     */
    public function findByKey(
        string $key
    ): ?array {
        $stmt = $this->db->prepare(
            "
            SELECT
                id,
                setting_key,
                setting_value,
                created_at,
                updated_at
            FROM settings
            WHERE setting_key = :setting_key
            LIMIT 1
            "
        );

        $stmt->execute([
            'setting_key' => $key
        ]);

        $setting = $stmt->fetch(
            PDO::FETCH_ASSOC
        );

        return $setting !== false
            ? $setting
            : null;
    }


    /**
     * Create or update a setting.
     */
    public function set(
        string $key,
        string $value
    ): bool {
        $existing = $this->findByKey(
            $key
        );

        if ($existing !== null) {

            $stmt = $this->db->prepare(
                "
                UPDATE settings
                SET
                    setting_value = :setting_value
                WHERE setting_key = :setting_key
                "
            );

            return $stmt->execute([
                'setting_value' => $value,
                'setting_key' => $key
            ]);
        }

        $stmt = $this->db->prepare(
            "
            INSERT INTO settings (
                setting_key,
                setting_value
            )
            VALUES (
                :setting_key,
                :setting_value
            )
            "
        );

        return $stmt->execute([
            'setting_key' => $key,
            'setting_value' => $value
        ]);
    }


    /**
     * Delete a setting.
     */
    public function delete(
        string $key
    ): bool {
        $stmt = $this->db->prepare(
            "
            DELETE FROM settings
            WHERE setting_key = :setting_key
            "
        );

        return $stmt->execute([
            'setting_key' => $key
        ]);
    }
}