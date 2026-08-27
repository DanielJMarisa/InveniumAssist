<?php

declare(strict_types=1);

namespace Modules\Users;

use Core\Database\BaseRepository;
use PDO;

final class UserRepository extends BaseRepository
{
    /**
     * Return all users with their role.
     *
     * @return array<int,array<string,mixed>>
     */
    public function all(): array
    {
        $statement = $this->db->query("
            SELECT
                u.id,
                u.role_id,
                u.first_name,
                u.last_name,
                u.email,
                u.username,
                u.status,
                u.failed_logins,
                u.locked_until,
                u.last_login,
                u.created_at,
                u.updated_at,
                r.name AS role,
                r.description AS role_description
            FROM users u
            LEFT JOIN roles r
                ON r.id = u.role_id
            ORDER BY
                u.last_name ASC,
                u.first_name ASC,
                u.id ASC
        ");

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Find a user by ID.
     *
     * @return array<string,mixed>|null
     */
    public function find(
        int $id
    ): ?array {
        $statement = $this->db->prepare("
            SELECT
                u.id,
                u.role_id,
                u.first_name,
                u.last_name,
                u.email,
                u.username,
                u.status,
                u.failed_logins,
                u.locked_until,
                u.last_login,
                u.created_at,
                u.updated_at,
                r.name AS role,
                r.description AS role_description
            FROM users u
            LEFT JOIN roles r
                ON r.id = u.role_id
            WHERE u.id = :id
            LIMIT 1
        ");

        $statement->execute([
            'id' => $id
        ]);

        $user = $statement->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }


    /**
     * Find a role by ID.
     *
     * @return array<string,mixed>|null
     */
    public function findRole(
        int $roleId
    ): ?array {
        $statement = $this->db->prepare("
            SELECT
                id,
                name,
                description
            FROM roles
            WHERE id = :id
            LIMIT 1
        ");

        $statement->execute([
            'id' => $roleId
        ]);

        $role = $statement->fetch(PDO::FETCH_ASSOC);

        return $role ?: null;
    }


    /**
     * Return all roles.
     *
     * @return array<int,array<string,mixed>>
     */
    public function roles(): array
    {
        $statement = $this->db->query("
            SELECT
                id,
                name,
                description
            FROM roles
            ORDER BY id ASC
        ");

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Check whether an email is already in use.
     */
    public function emailExists(
        string $email,
        ?int $exceptUserId = null
    ): bool {
        $sql = "
            SELECT id
            FROM users
            WHERE email = :email
        ";

        $parameters = [
            'email' => $email
        ];

        if ($exceptUserId !== null) {
            $sql .= " AND id <> :except_id";

            $parameters['except_id'] = $exceptUserId;
        }

        $sql .= " LIMIT 1";

        $statement = $this->db->prepare($sql);

        $statement->execute($parameters);

        return $statement->fetchColumn() !== false;
    }


    /**
     * Check whether a username is already in use.
     */
    public function usernameExists(
        string $username,
        ?int $exceptUserId = null
    ): bool {
        $sql = "
            SELECT id
            FROM users
            WHERE username = :username
        ";

        $parameters = [
            'username' => $username
        ];

        if ($exceptUserId !== null) {
            $sql .= " AND id <> :except_id";

            $parameters['except_id'] = $exceptUserId;
        }

        $sql .= " LIMIT 1";

        $statement = $this->db->prepare($sql);

        $statement->execute($parameters);

        return $statement->fetchColumn() !== false;
    }


    /**
     * Create a user.
     */
    public function create(
        array $data
    ): int {
        $statement = $this->db->prepare("
            INSERT INTO users (
                role_id,
                first_name,
                last_name,
                email,
                username,
                password,
                status,
                failed_logins,
                locked_until,
                created_at,
                updated_at
            ) VALUES (
                :role_id,
                :first_name,
                :last_name,
                :email,
                :username,
                :password,
                :status,
                0,
                NULL,
                NOW(),
                NOW()
            )
        ");

        $statement->execute([
            'role_id' => $data['role_id'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'username' => $data['username'],
            'password' => $data['password'],
            'status' => $data['status']
        ]);

        return (int) $this->db->lastInsertId();
    }


    /**
     * Update a user's profile and role.
     */
    public function update(
        int $id,
        array $data
    ): bool {
        $statement = $this->db->prepare("
            UPDATE users
            SET
                role_id = :role_id,
                first_name = :first_name,
                last_name = :last_name,
                email = :email,
                username = :username,
                updated_at = NOW()
            WHERE id = :id
        ");

        return $statement->execute([
            'id' => $id,
            'role_id' => $data['role_id'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'username' => $data['username']
        ]);
    }


    /**
     * Change account status.
     */
    public function updateStatus(
        int $id,
        string $status
    ): bool {
        $statement = $this->db->prepare("
            UPDATE users
            SET
                status = :status,
                updated_at = NOW()
            WHERE id = :id
        ");

        return $statement->execute([
            'id' => $id,
            'status' => $status
        ]);
    }

    /**
     * Delete a user.
     */
    public function delete(
        int $id
    ): bool {
        $statement = $this->db->prepare("
            DELETE FROM users
            WHERE id = :id
            LIMIT 1
        ");

        $statement->execute([
            'id' => $id
        ]);

        return $statement->rowCount() === 1;
    }
}