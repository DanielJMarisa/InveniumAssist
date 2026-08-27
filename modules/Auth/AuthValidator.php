<?php

declare(strict_types=1);

namespace Modules\Auth;

use Core\Validation\Validator;

final class AuthValidator extends Validator
{
    /**
     * Validate login data.
     *
     * @param array<string, mixed> $input
     */
    public function validate(
        array $input
    ): bool
    {
        $this->clearErrors();

        $username = trim(

            (string) $this->input(

                $input,

                'username'

            )

        );

        $password = (string) $this->input(

            $input,

            'password'

        );

        /*
        |--------------------------------------------------------------------------
        | Username
        |--------------------------------------------------------------------------
        */

        if ($username === '') {

            $this->addError(

                'username',

                'Username is required.'

            );

        }
        elseif (mb_strlen($username) > 100) {

            $this->addError(

                'username',

                'Username is too long.'

            );

        }

        /*
        |--------------------------------------------------------------------------
        | Password
        |--------------------------------------------------------------------------
        */

        if ($password === '') {

            $this->addError(

                'password',

                'Password is required.'

            );

        }
        elseif (mb_strlen($password) > 255) {

            $this->addError(

                'password',

                'Password is invalid.'

            );

        }

        return $this->passes();
    }

    /**
     * Return sanitized login data.
     *
     * @param array<string, mixed> $input
     * @return array<string, string>
     */
    public function validated(
        array $input
    ): array
    {
        return [

            'username' => trim(

                (string) $this->input(

                    $input,

                    'username'

                )

            ),

            'password' => (string) $this->input(

                $input,

                'password'

            )

        ];
    }
}