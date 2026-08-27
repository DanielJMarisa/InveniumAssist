<?php

declare(strict_types=1);

namespace Modules\Devices;

use Core\Database\BaseRepository;
use PDO;

final class DeviceRepository extends BaseRepository
{
    /**
     * Return all devices with their customer.
     *
     * @return array<int,array<string,mixed>>
     */
    public function all(): array
    {
        $statement = $this->db->query("
            SELECT
                d.id,
                d.customer_id,
                d.hostname,
                d.device_name,
                d.operating_system,
                d.serial_number,
                d.mac_address,
                d.local_ip,
                d.public_ip,
                d.fqdn,
                d.monitoring_url,
                d.agent_version,
                d.status,
                d.last_seen,
                d.created_at,
                d.updated_at,
                c.company_name
            FROM devices d
            INNER JOIN customers c
                ON c.id = d.customer_id
            ORDER BY
                c.company_name ASC,
                d.device_name ASC,
                d.id ASC
        ");

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Find a device by ID.
     *
     * @return array<string,mixed>|null
     */
    public function find(
        int $id
    ): ?array {
        $statement = $this->db->prepare("
            SELECT
                d.id,
                d.customer_id,
                d.hostname,
                d.device_name,
                d.operating_system,
                d.serial_number,
                d.mac_address,
                d.local_ip,
                d.public_ip,
                d.fqdn,
                d.monitoring_url,
                d.agent_version,
                d.status,
                d.last_seen,
                d.created_at,
                d.updated_at,
                c.company_name
            FROM devices d
            INNER JOIN customers c
                ON c.id = d.customer_id
            WHERE d.id = :id
            LIMIT 1
        ");

        $statement->execute([
            'id' => $id
        ]);

        $device = $statement->fetch(PDO::FETCH_ASSOC);

        return $device ?: null;
    }


    /**
     * Create a device.
     */
    public function create(
        array $data
    ): int {
        $statement = $this->db->prepare("
            INSERT INTO devices (
                customer_id,
                hostname,
                device_name,
                operating_system,
                serial_number,
                mac_address,
                local_ip,
                public_ip,
                fqdn,
                monitoring_url,
                agent_version,
                status,
                last_seen,
                created_at,
                updated_at
            ) VALUES (
                :customer_id,
                :hostname,
                :device_name,
                :operating_system,
                :serial_number,
                :mac_address,
                :local_ip,
                :public_ip,
                :fqdn,
                :monitoring_url,
                :agent_version,
                :status,
                :last_seen,
                NOW(),
                NOW()
            )
        ");

        $statement->execute([
            'customer_id' => $data['customer_id'],
            'hostname' => $data['hostname'],
            'device_name' => $data['device_name'],
            'operating_system' => $data['operating_system'],
            'serial_number' => $data['serial_number'],
            'mac_address' => $data['mac_address'],
            'local_ip' => $data['local_ip'],
            'public_ip' => $data['public_ip'],
            'fqdn' => $data['fqdn'],
            'monitoring_url' => $data['monitoring_url'],
            'agent_version' => $data['agent_version'],
            'status' => $data['status'],
            'last_seen' => $data['last_seen']
        ]);

        return (int) $this->db->lastInsertId();
    }


    /**
     * Update a device.
     */
    public function update(
        int $id,
        array $data
    ): bool {
        $statement = $this->db->prepare("
            UPDATE devices
            SET
                customer_id = :customer_id,
                hostname = :hostname,
                device_name = :device_name,
                operating_system = :operating_system,
                serial_number = :serial_number,
                mac_address = :mac_address,
                local_ip = :local_ip,
                public_ip = :public_ip,
                fqdn = :fqdn,
                monitoring_url = :monitoring_url,
                agent_version = :agent_version,
                updated_at = NOW()
            WHERE id = :id
        ");

        return $statement->execute([
            'id' => $id,
            'customer_id' => $data['customer_id'],
            'hostname' => $data['hostname'],
            'device_name' => $data['device_name'],
            'operating_system' => $data['operating_system'],
            'serial_number' => $data['serial_number'],
            'mac_address' => $data['mac_address'],
            'local_ip' => $data['local_ip'],
            'public_ip' => $data['public_ip'],
            'fqdn' => $data['fqdn'],
            'monitoring_url' => $data['monitoring_url'],
            'agent_version' => $data['agent_version']
        ]);
    }
}