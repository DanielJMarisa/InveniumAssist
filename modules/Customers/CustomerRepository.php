<?php

declare(strict_types=1);

namespace Modules\Customers;

use Core\Database\BaseRepository;
use PDO;

final class CustomerRepository extends BaseRepository
{
    /**
     * Return all customers.
     *
     * @return array<int,array<string,mixed>>
     */
    public function all(): array
    {
        $statement = $this->db->query("
            SELECT
                id,
                company_name,
                contact_name,
                email,
                phone,
                created_at,
                updated_at
            FROM customers
            ORDER BY
                company_name ASC,
                id ASC
        ");

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Find a customer by ID.
     *
     * @return array<string,mixed>|null
     */
    public function find(
        int $id
    ): ?array {
        $statement = $this->db->prepare("
            SELECT
                id,
                company_name,
                contact_name,
                email,
                phone,
                created_at,
                updated_at
            FROM customers
            WHERE id = :id
            LIMIT 1
        ");

        $statement->execute([
            'id' => $id
        ]);

        $customer = $statement->fetch(PDO::FETCH_ASSOC);

        return $customer ?: null;
    }


    /**
     * Create a customer.
     */
    public function create(
        array $data
    ): int {
        $statement = $this->db->prepare("
            INSERT INTO customers (
                company_name,
                contact_name,
                email,
                phone,
                created_at,
                updated_at
            ) VALUES (
                :company_name,
                :contact_name,
                :email,
                :phone,
                NOW(),
                NOW()
            )
        ");

        $statement->execute([
            'company_name' => $data['company_name'],
            'contact_name' => $data['contact_name'],
            'email' => $data['email'],
            'phone' => $data['phone']
        ]);

        return (int) $this->db->lastInsertId();
    }


    /**
     * Update a customer.
     */
    public function update(
        int $id,
        array $data
    ): bool {
        $statement = $this->db->prepare("
            UPDATE customers
            SET
                company_name = :company_name,
                contact_name = :contact_name,
                email = :email,
                phone = :phone,
                updated_at = NOW()
            WHERE id = :id
        ");

        return $statement->execute([
            'id' => $id,
            'company_name' => $data['company_name'],
            'contact_name' => $data['contact_name'],
            'email' => $data['email'],
            'phone' => $data['phone']
        ]);
    }


    /**
     * Count devices belonging to a customer.
     */
    public function deviceCount(
        int $customerId
    ): int {
        $statement = $this->db->prepare("
            SELECT COUNT(*)
            FROM devices
            WHERE customer_id = :customer_id
        ");

        $statement->execute([
            'customer_id' => $customerId
        ]);

        return (int) $statement->fetchColumn();
    }


    /**
     * Count sessions belonging to a customer.
     */
    public function sessionCount(
        int $customerId
    ): int {
        $statement = $this->db->prepare("
            SELECT COUNT(*)
            FROM sessions
            WHERE customer_id = :customer_id
        ");

        $statement->execute([
            'customer_id' => $customerId
        ]);

        return (int) $statement->fetchColumn();
    }
}