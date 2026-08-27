<?php

declare(strict_types=1);

namespace Modules\Customers;

use Core\Service;

final class CustomerService extends Service
{
    private CustomerRepository $repository;

    public function __construct(
        CustomerRepository $repository
    ) {
        parent::__construct();

        $this->repository = $repository;
    }


    /**
     * Return all customers.
     *
     * @return array<int,array<string,mixed>>
     */
    public function all(): array
    {
        return $this->repository->all();
    }


    /**
     * Find customer by ID.
     *
     * @return array<string,mixed>|null
     */
    public function find(
        int $id
    ): ?array {
        $customer = $this->repository->find($id);

        if ($customer === null) {
            return null;
        }

        $customer['device_count'] =
            $this->repository->deviceCount($id);

        $customer['session_count'] =
            $this->repository->sessionCount($id);

        return $customer;
    }


    /**
     * Create customer.
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

        $this->repository->create([
            'company_name' => trim(
                (string) $input['company_name']
            ),
            'contact_name' => trim(
                (string) $input['contact_name']
            ),
            'email' => trim(
                (string) $input['email']
            ),
            'phone' => trim(
                (string) $input['phone']
            )
        ]);

        return [
            'success' => true,
            'errors' => []
        ];
    }


    /**
     * Update customer.
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
                    'customer' => 'Customer not found.'
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
            [
                'company_name' => trim(
                    (string) $input['company_name']
                ),
                'contact_name' => trim(
                    (string) $input['contact_name']
                ),
                'email' => trim(
                    (string) $input['email']
                ),
                'phone' => trim(
                    (string) $input['phone']
                )
            ]
        );

        return [
            'success' => true,
            'errors' => []
        ];
    }


    /**
     * Validate customer input.
     *
     * @param array<string,mixed> $input
     * @return array<string,string>
     */
    private function validate(
        array $input
    ): array {
        $errors = [];

        $companyName = trim(
            (string) ($input['company_name'] ?? '')
        );

        $contactName = trim(
            (string) ($input['contact_name'] ?? '')
        );

        $email = trim(
            (string) ($input['email'] ?? '')
        );

        $phone = trim(
            (string) ($input['phone'] ?? '')
        );

        if ($companyName === '') {
            $errors['company_name'] =
                'Company name is required.';
        } elseif (mb_strlen($companyName) > 255) {
            $errors['company_name'] =
                'Company name cannot exceed 255 characters.';
        }

        if ($contactName !== ''
            && mb_strlen($contactName) > 255
        ) {
            $errors['contact_name'] =
                'Contact name cannot exceed 255 characters.';
        }

        if ($email !== ''
            && !filter_var($email, FILTER_VALIDATE_EMAIL)
        ) {
            $errors['email'] =
                'Please enter a valid email address.';
        }

        if (mb_strlen($email) > 255) {
            $errors['email'] =
                'Email address cannot exceed 255 characters.';
        }

        if (mb_strlen($phone) > 50) {
            $errors['phone'] =
                'Phone number cannot exceed 50 characters.';
        }

        return $errors;
    }
}