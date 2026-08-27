<?php

declare(strict_types=1);

namespace Core\Controllers;

use Core\Http\Request;
use Core\Http\Response;
use Core\Session\Session;

abstract class BaseController extends Controller
{
    /**
     * Return the current request.
     */
    protected function request(): Request
    {
        return new Request();
    }

    /**
     * Render a view.
     */
    protected function render(
        string $view,
        array $data = []
    ): void
    {
        $this->view(

            $view,

            $data

        );
    }

    /**
     * Redirect.
     */
    protected function redirect(
        string $path,
        int $status = 302
    ): never
    {
        Response::redirect(

            $path,

            $status

        );
    }

    /**
     * JSON response.
     */
    protected function json(
        array $data,
        int $status = 200
    ): never
    {
        Response::json(

            $data,

            $status

        );
    }

    /**
     * Flash success message.
     */
    protected function success(
        string $message
    ): void
    {
        Session::flash(

            'success',

            $message

        );
    }

    /**
     * Flash error message.
     */
    protected function error(
        string $message
    ): void
    {
        Session::flash(

            'error',

            $message

        );
    }

    /**
     * Is authenticated?
     */
    protected function authenticated(): bool
    {
        return Session::has(

            'auth.user_id'

        );
    }

    /**
     * Current user id.
     */
    protected function userId(): ?int
    {
        $id = Session::get(

            'auth.user_id'

        );

        return $id !== null

            ? (int) $id

            : null;
    }

    /**
     * Current username.
     */
    protected function username(): ?string
    {
        return Session::get(

            'auth.username'

        );
    }

    /**
     * Current role.
     */
    protected function role(): ?string
    {
        return Session::get(

            'auth.role'

        );
    }

    /**
     * Abort request.
     */
    protected function abort(
        int $status = 404,
        string $message = ''
    ): never
    {
        Response::abort(

            $status,

            $message

        );
    }
}