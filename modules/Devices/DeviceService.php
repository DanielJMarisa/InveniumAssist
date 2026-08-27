<?php

declare(strict_types=1);

namespace Modules\Devices;

use Core\Service;
use Modules\Customers\CustomerRepository;

final class DeviceService extends Service
{
    private DeviceRepository $repository;

    private CustomerRepository $customerRepository;


    public function __construct(
        DeviceRepository $repository,
        CustomerRepository $customerRepository
    ) {
        parent::__construct();

        $this->repository = $repository;

        $this->customerRepository =
            $customerRepository;
    }


    /**
     * Return all devices.
     *
     * @return array<int,array<string,mixed>>
     */
    public function all(): array
    {
        return $this->repository->all();
    }


    /**
     * Find a device.
     *
     * @return array<string,mixed>|null
     */
    public function find(
        int $id
    ): ?array {
        return $this->repository->find($id);
    }


    /**
     * Create a device.
     *
     * @param array<string,mixed> $input
     * @return array{
     *     success: bool,
     *     errors: array<string,string>
     * }
     */
    public function create(
        array $input
    ): array {
        $errors = $this->validate($input);

        if ($errors !== []) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }

        $this->repository->create(
            $this->prepareData($input)
        );

        return [
            'success' => true,
            'errors' => []
        ];
    }


    /**
     * Update a device.
     *
     * @param array<string,mixed> $input
     * @return array{
     *     success: bool,
     *     errors: array<string,string>
     * }
     */
    public function update(
        int $id,
        array $input
    ): array {
        if ($this->repository->find($id) === null) {
            return [
                'success' => false,
                'errors' => [
                    'device' => 'Device not found.'
                ]
            ];
        }

        $errors = $this->validate($input);

        if ($errors !== []) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }

        $this->repository->update(
            $id,
            $this->prepareData($input)
        );

        return [
            'success' => true,
            'errors' => []
        ];
    }


    /**
     * Return all customers available for device assignment.
     *
     * @return array<int,array<string,mixed>>
     */
    public function customers(): array
    {
        return $this->customerRepository->all();
    }


    /**
     * Validate device input.
     *
     * @param array<string,mixed> $input
     * @return array<string,string>
     */
    private function validate(
        array $input
    ): array {
        $errors = [];

        $customerId = (int) (
            $input['customer_id'] ?? 0
        );

        $hostname = trim(
            (string) ($input['hostname'] ?? '')
        );

        $deviceName = trim(
            (string) ($input['device_name'] ?? '')
        );

        $operatingSystem = trim(
            (string) ($input['operating_system'] ?? '')
        );

        $serialNumber = trim(
            (string) ($input['serial_number'] ?? '')
        );

        $macAddress = trim(
            (string) ($input['mac_address'] ?? '')
        );

        $localIp = trim(
            (string) ($input['local_ip'] ?? '')
        );

        $publicIp = trim(
            (string) ($input['public_ip'] ?? '')
        );

        $fqdn = trim(
            (string) ($input['fqdn'] ?? '')
        );

        $monitoringUrl = trim(
            (string) ($input['monitoring_url'] ?? '')
        );

        $agentVersion = trim(
            (string) ($input['agent_version'] ?? '')
        );


        /*
         * Customer is mandatory.
         */
        if ($customerId <= 0) {

            $errors['customer_id'] =
                'Please select a customer.';

        } elseif (
            $this->customerRepository->find(
                $customerId
            ) === null
        ) {

            $errors['customer_id'] =
                'The selected customer does not exist.';
        }


        if (
            $deviceName !== ''
            && mb_strlen($deviceName) > 255
        ) {
            $errors['device_name'] =
                'Device name cannot exceed 255 characters.';
        }


        if (
            $hostname !== ''
            && mb_strlen($hostname) > 255
        ) {
            $errors['hostname'] =
                'Hostname cannot exceed 255 characters.';
        }


        if (
            $operatingSystem !== ''
            && mb_strlen($operatingSystem) > 255
        ) {
            $errors['operating_system'] =
                'Operating system cannot exceed 255 characters.';
        }


        if (
            $serialNumber !== ''
            && mb_strlen($serialNumber) > 255
        ) {
            $errors['serial_number'] =
                'Serial number cannot exceed 255 characters.';
        }


        if (
            $macAddress !== ''
            && mb_strlen($macAddress) > 100
        ) {
            $errors['mac_address'] =
                'MAC address cannot exceed 100 characters.';
        }


        if (
            $localIp !== ''
            && mb_strlen($localIp) > 50
        ) {
            $errors['local_ip'] =
                'Local IP cannot exceed 50 characters.';
        }


        if (
            $publicIp !== ''
            && mb_strlen($publicIp) > 50
        ) {
            $errors['public_ip'] =
                'Public IP cannot exceed 50 characters.';
        }

                if (
            $fqdn !== ''
            && mb_strlen($fqdn) > 255
        ) {
            $errors['fqdn'] =
                'FQDN cannot exceed 255 characters.';
        }


        if (
            $fqdn !== ''
            && filter_var(
                $fqdn,
                FILTER_VALIDATE_DOMAIN,
                FILTER_FLAG_HOSTNAME
            ) === false
        ) {
            $errors['fqdn'] =
                'Please enter a valid FQDN.';
        }


        if (
            $monitoringUrl !== ''
            && mb_strlen($monitoringUrl) > 2048
        ) {
            $errors['monitoring_url'] =
                'Monitoring URL cannot exceed 2048 characters.';
        }


        if (
            $monitoringUrl !== ''
            && filter_var(
                $monitoringUrl,
                FILTER_VALIDATE_URL
            ) === false
        ) {
            $errors['monitoring_url'] =
                'Please enter a valid monitoring URL.';
        }

        if (
            $agentVersion !== ''
            && mb_strlen($agentVersion) > 50
        ) {
            $errors['agent_version'] =
                'Agent version cannot exceed 50 characters.';
        }


        return $errors;
    }


    /**
     * Prepare validated device data.
     *
     * @param array<string,mixed> $input
     * @return array<string,mixed>
     */
    private function prepareData(
        array $input
    ): array {
        return [

            'customer_id' => (int) $input['customer_id'],

            'hostname' => trim(
                (string) ($input['hostname'] ?? '')
            ),

            'device_name' => trim(
                (string) ($input['device_name'] ?? '')
            ),

            'operating_system' => trim(
                (string) ($input['operating_system'] ?? '')
            ),

            'serial_number' => trim(
                (string) ($input['serial_number'] ?? '')
            ),

            'mac_address' => trim(
                (string) ($input['mac_address'] ?? '')
            ),

            'local_ip' => trim(
                (string) ($input['local_ip'] ?? '')
            ),

            'public_ip' => trim(
                (string) ($input['public_ip'] ?? '')
            ),

            'fqdn' => trim(
                (string) ($input['fqdn'] ?? '')
            ),

            'monitoring_url' => trim(
                (string) ($input['monitoring_url'] ?? '')
            ),

            'agent_version' => trim(
                (string) ($input['agent_version'] ?? '')
            ),

            /*
             * Newly created devices have not yet
             * been contacted by an agent.
             */
            'status' => 'unknown',

            'last_seen' => null
        ];
    }
}