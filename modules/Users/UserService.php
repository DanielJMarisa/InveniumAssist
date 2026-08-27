<?php

declare(strict_types=1);

namespace Modules\Users;

use Core\Security\Hash;
use Core\Service;
use InvalidArgumentException;

final class UserService extends Service
{
    private UserRepository $repository;

    public function __construct(
        UserRepository $repository
    ) {
        parent::__construct();

        $this->repository = $repository;
    }


    /**
     * Return all users.
     */
    public function all(): array
    {
        return $this->repository->all();
    }


    /**
     * Return one user.
     */
    public function find(
        int $id
    ): ?array {
        return $this->repository->find($id);
    }


    /**
     * Return available roles.
     */
    public function roles(): array
    {
        return $this->repository->roles();
    }


    /**
     * Create a user.
     *
     * @param array<string,mixed> $input
     * @return array<string,mixed>
     */
    public function create(
        array $input
    ): array {
        $errors = $this->validate(
            $input
        );

        if ($errors !== []) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }

        $roleId = (int) $input['role_id'];

        if (
            $this->repository->findRole($roleId)
            === null
        ) {
            return [
                'success' => false,
                'errors' => [
                    'role_id' => 'Selected role does not exist.'
                ]
            ];
        }

        $email = strtolower(
            trim((string) $input['email'])
        );

        $username = trim(
            (string) $input['username']
        );

        if (
            $this->repository->emailExists($email)
        ) {
            return [
                'success' => false,
                'errors' => [
                    'email' => 'Email address is already in use.'
                ]
            ];
        }

        if (
            $this->repository->usernameExists($username)
        ) {
            return [
                'success' => false,
                'errors' => [
                    'username' => 'Username is already in use.'
                ]
            ];
        }

        $id = $this->repository->create([
            'role_id' => $roleId,
            'first_name' => trim((string) $input['first_name']),
            'last_name' => trim((string) $input['last_name']),
            'email' => $email,
            'username' => $username,
            'password' => Hash::make(
                (string) $input['password']
            ),
            'status' => 'active'
        ]);

        return [
            'success' => true,
            'id' => $id
        ];
    }


    /**
     * Update a user.
     *
     * @param array<string,mixed> $input
     * @return array<string,mixed>
     */
    public function update(
        int $id,
        array $input
    ): array {
        $user = $this->repository->find($id);

        if ($user === null) {
            return [
                'success' => false,
                'errors' => [
                    'general' => 'User was not found.'
                ]
            ];
        }

        $errors = $this->validate(
            $input,
            false
        );

        if ($errors !== []) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }

        $roleId = (int) $input['role_id'];

        if (
            $this->repository->findRole($roleId)
            === null
        ) {
            return [
                'success' => false,
                'errors' => [
                    'role_id' => 'Selected role does not exist.'
                ]
            ];
        }

        $email = strtolower(
            trim((string) $input['email'])
        );

        $username = trim(
            (string) $input['username']
        );

        if (
            $this->repository->emailExists(
                $email,
                $id
            )
        ) {
            return [
                'success' => false,
                'errors' => [
                    'email' => 'Email address is already in use.'
                ]
            ];
        }

        if (
            $this->repository->usernameExists(
                $username,
                $id
            )
        ) {
            return [
                'success' => false,
                'errors' => [
                    'username' => 'Username is already in use.'
                ]
            ];
        }

        $this->repository->update(
            $id,
            [
                'role_id' => $roleId,
                'first_name' => trim((string) $input['first_name']),
                'last_name' => trim((string) $input['last_name']),
                'email' => $email,
                'username' => $username
            ]
        );

        return [
            'success' => true
        ];
    }


    /**
     * Change user status.
     */
    public function changeStatus(
        int $id,
        string $status
    ): bool {
        $allowed = [
            'active',
            'inactive',
            'locked'
        ];

        if (!in_array($status, $allowed, true)) {
            throw new InvalidArgumentException(
                'Invalid user status.'
            );
        }

        return $this->repository->updateStatus(
            $id,
            $status
        );
    }


    /**
     * Validate user input.
     *
     * @param array<string,mixed> $input
     * @return array<string,string>
     */
    private function validate(
        array $input,
        bool $requirePassword = true
    ): array {
        $errors = [];

        if (
            trim((string) ($input['first_name'] ?? ''))
            === ''
        ) {
            $errors['first_name'] = 'First name is required.';
        }

        if (
            trim((string) ($input['last_name'] ?? ''))
            === ''
        ) {
            $errors['last_name'] = 'Last name is required.';
        }

        $email = trim(
            (string) ($input['email'] ?? '')
        );

        if (
            $email === ''
            || filter_var($email, FILTER_VALIDATE_EMAIL) === false
        ) {
            $errors['email'] = 'A valid email address is required.';
        }

        if (
            trim((string) ($input['username'] ?? ''))
            === ''
        ) {
            $errors['username'] = 'Username is required.';
        }

        if (
            $requirePassword
            && strlen((string) ($input['password'] ?? '')) < 12
        ) {
            $errors['password'] =
                'Password must be at least 12 characters.';
        }

        if (
            !isset($input['role_id'])
            || !ctype_digit((string) $input['role_id'])
        ) {
            $errors['role_id'] = 'A valid role is required.';
        }

        return $errors;
    }

    /**
     * Delete a user.
     */
    public function delete(
        int $id
    ): array {
        $user = $this->repository->find($id);

        if ($user === null) {
            return [
                'success' => false,
                'errors' => [
                    'general' => 'User was not found.'
                ]
            ];
        }

        /*
         * Do not allow deletion of an already inactive/non-existent
         * account through this operation.
         */
        if (
            !$this->repository->delete($id)
        ) {
            return [
                'success' => false,
                'errors' => [
                    'general' => 'User could not be deleted.'
                ]
            ];
        }

        return [
            'success' => true
        ];
    }
}