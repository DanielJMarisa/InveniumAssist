<?php

declare(strict_types=1);

namespace Modules\Auth;

use Core\Service;
use Core\Session\Session;
use Core\Security\Hash;

final class AuthService extends Service
{
    private AuthRepository $repository;

    private AuthValidator $validator;

    /**
     * Maximum failed login attempts.
     */
    private const MAX_FAILED_ATTEMPTS = 5;

    public function __construct(
        AuthRepository $repository,
        AuthValidator $validator
    )
    {
        parent::__construct();

        $this->repository = $repository;

        $this->validator = $validator;
    }

    /**
     * Authenticate a user.
     *
     * @param array<string,mixed> $input
     * @return array<string,mixed>
     */
    public function authenticate(
        array $input
    ): array
    {
        /*
        |--------------------------------------------------------------------------
        | Validate Input
        |--------------------------------------------------------------------------
        */

        if (!$this->validator->validate($input)) {

            return [

                'success' => false,

                'message' => 'Validation failed.',

                'errors' => $this->validator->errors()

            ];

        }

        $credentials = $this->validator->validated($input);

        /*
        |--------------------------------------------------------------------------
        | Locate User
        |--------------------------------------------------------------------------
        */

        $user = $this->repository->findByUsername(

            $credentials['username']

        );

        if ($user === null) {

            return [

                'success' => false,

                'message' => 'Invalid username or password.'

            ];

        }

        /*
        |--------------------------------------------------------------------------
        | Locked?
        |--------------------------------------------------------------------------
        */

        if ($this->repository->isLocked(

            (int) $user['id']

        )) {

            return [

                'success' => false,

                'message' => 'Account temporarily locked.'

            ];

        }

        /*
        |--------------------------------------------------------------------------
        | Account Status
        |--------------------------------------------------------------------------
        */

        $status = $user['status'] ?? 'active';

        if ($status === 'inactive') {

            return [

                'success' => false,

                'message' => 'Account is inactive.'

            ];

        }

        if ($status === 'locked') {

            return [

                'success' => false,

                'message' => 'Account is locked.'

            ];

        }

        /*
        |--------------------------------------------------------------------------
        | Verify Password
        |--------------------------------------------------------------------------
        */

        if (
                !Hash::check(

                    $credentials['password'],

                    $user['password']

                )

            ) {

            $this->repository->incrementFailedAttempts(

                (int) $user['id']

            );

            if (

                ((int) $user['failed_logins'] + 1)

                >=

                self::MAX_FAILED_ATTEMPTS

            ) {

                $this->repository->lockAccount(

                    (int) $user['id']

                );

            }

            return [

                'success' => false,

                'message' => 'Invalid username or password.'

            ];

        }

        /*
        |--------------------------------------------------------------------------
        | Successful Login
        |--------------------------------------------------------------------------
        */



        error_log(
            'AUTH DEBUG BEFORE REGENERATE: '
            . 'session_status=' . session_status()
            . ' session_id=' . session_id()
            . ' use_cookies=' . ini_get('session.use_cookies')
            . ' use_only_cookies=' . ini_get('session.use_only_cookies')
        );

        $regenerated = Session::regenerate();

        error_log(
            'AUTH DEBUG AFTER REGENERATE: '
            . 'result=' . ($regenerated ? 'true' : 'false')
            . ' session_status=' . session_status()
            . ' session_id=' . session_id()
            . ' headers_sent=' . (headers_sent() ? 'yes' : 'no')
        );

        Session::put(

            'auth.user_id',

            (int) $user['id']

        );

        Session::put(

            'auth.username',

            $user['username']

        );

        Session::put(
            'auth.role_id',
            (int) $user['role_id']
        );

        Session::put(

            'auth.role',

            $user['role']

        );

        $this->repository->resetFailedAttempts(

            (int) $user['id']

        );

        $this->repository->updateLastLogin(

            (int) $user['id']

        );

        return [

            'success' => true,

            'message' => 'Login successful.',

            'user' => [

                'id' => (int) $user['id'],

                'first_name' => $user['first_name'],

                'last_name' => $user['last_name'],

                'email' => $user['email'],

                'username' => $user['username'],

                'role' => $user['role']

            ]

        ];
    }


    /**
     * Logout current user.
     */
    public function logout(): void
    {
        Session::destroy();
    }

    /**
     * Determine if a user is authenticated.
     */
    public function check(): bool
    {
                return Session::has(

            'auth.user_id'

        );
    }

    /**
     * Return authenticated user id.
     */
    public function userId(): ?int
    {
                $value = Session::get('auth.user_id');

                return $value !== null

                    ? (int) $value

                    : null;
    }
}