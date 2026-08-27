<?php

declare(strict_types=1);

namespace Modules\Auth;

use Core\Database\BaseRepository;
use PDO;

final class AuthRepository extends BaseRepository
{
    /**
     * Find user by username.
     */
    public function findByUsername(
        string $username
    ): ?array {
        return $this->findBy(
            'username',
            $username
        );
    }

    /**
     * Find user by email.
     */
    public function findByEmail(
        string $email
    ): ?array {
        return $this->findBy(
            'email',
            $email
        );
    }

    /**
     * Find a user by username or email.
     */
    private function findBy(
        string $column,
        string $value
    ): ?array {
        $allowed = [
            'username',
            'email'
        ];

        if (!in_array($column, $allowed, true)) {
            return null;
        }

        $sql = sprintf(
            "
            SELECT
                u.id,
                u.role_id,
                u.first_name,
                u.last_name,
                u.email,
                u.username,
                u.password,
                u.status,
                u.failed_logins,
                u.locked_until,
                u.last_login,
                u.remember_token,
                u.created_at,
                u.updated_at,
                r.name AS role
            FROM users u
            LEFT JOIN roles r
                ON r.id = u.role_id
            WHERE u.%s = :value
            LIMIT 1
            ",
            $column
        );

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'value' => $value
        ]);

        $user = $statement->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    /**
     * Update last login timestamp.
     */
    public function updateLastLogin(
        int $userId
    ): bool {
        $sql = "
            UPDATE users
            SET
                last_login = NOW(),
                updated_at = NOW()
            WHERE id = :id
        ";

        return $this->executeUpdate(
            $sql,
            [
                'id' => $userId
            ]
        );
    }

    /**
     * Increment failed login attempts.
     */
    public function incrementFailedAttempts(
        int $userId
    ): bool {
        $sql = "
            UPDATE users
            SET
                failed_logins = failed_logins + 1,
                updated_at = NOW()
            WHERE id = :id
        ";

        return $this->executeUpdate(
            $sql,
            [
                'id' => $userId
            ]
        );
    }

    /**
     * Reset failed login attempts.
     */
    public function resetFailedAttempts(
        int $userId
    ): bool {
        $sql = "
            UPDATE users
            SET
                failed_logins = 0,
                locked_until = NULL,
                updated_at = NOW()
            WHERE id = :id
        ";

        return $this->executeUpdate(
            $sql,
            [
                'id' => $userId
            ]
        );
    }

    /**
     * Lock user account.
     */
    public function lockAccount(
        int $userId
    ): bool {
        $sql = "
            UPDATE users
            SET
                locked_until = DATE_ADD(
                    NOW(),
                    INTERVAL 15 MINUTE
                ),
                updated_at = NOW()
            WHERE id = :id
        ";

        return $this->executeUpdate(
            $sql,
            [
                'id' => $userId
            ]
        );
    }

    /**
     * Unlock user account.
     */
    public function unlockAccount(
        int $userId
    ): bool {
        $sql = "
            UPDATE users
            SET
                failed_logins = 0,
                locked_until = NULL,
                updated_at = NOW()
            WHERE id = :id
        ";

        return $this->executeUpdate(
            $sql,
            [
                'id' => $userId
            ]
        );
    }

    /**
     * Determine whether account is locked.
     */
    public function isLocked(
        int $userId
    ): bool {
        $sql = "
            SELECT locked_until
            FROM users
            WHERE id = :id
            LIMIT 1
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'id' => $userId
        ]);

        $lockedUntil = $statement->fetchColumn();

        if (
            $lockedUntil === false
            || $lockedUntil === null
        ) {
            return false;
        }

        return strtotime($lockedUntil) > time();
    }

    /**
     * Execute an update statement.
     */
    private function executeUpdate(
        string $sql,
        array $parameters
    ): bool {
        $statement = $this->db->prepare($sql);

        return $statement->execute($parameters);
    }
}