<?php

declare(strict_types=1);

namespace Modules\Auth;

use Core\Controller;
use Core\Http\Request;
use Core\Http\Response;
use Core\Session\Session;

final class AuthController extends Controller
{
    private AuthService $service;

    public function __construct(
        AuthService $service
    ) {
        $this->service = $service;
    }

    /**
     * Display login page.
     */
    public function index(): Response
    {
        if ($this->service->check()) {

            return $this->redirect(
                'dashboard'
            );
        }

        return $this->view(
            'auth/login',
            [
                'title' => 'Sign In',
                'error' => Session::pull('auth.error')
            ]
        );
    }

    /**
     * Process login request.
     */
    public function login(): Response
    {
        $result = $this->service->authenticate(
            Request::post()
        );

        if ($result['success'] === true) {

            Session::flash(
                'auth.success',
                'Welcome back ' .
                $result['user']['email']
            );

            return $this->redirect(
                'dashboard'
            );
        }

        Session::flash(
            'auth.error',
            $result['message']
        );

        if (!empty($result['errors'])) {

            Session::flash(
                'auth.validation',
                $result['errors']
            );
        }

        return $this->redirect(
            'login'
        );
    }

    /**
     * Logout current user.
     */
    public function logout(): Response
    {
        $this->service->logout();

        Session::flash(
            'auth.success',
            'You have been signed out.'
        );

        return $this->redirect(
            'login'
        );
    }
}