<?php

declare(strict_types=1);

namespace Modules\Users;

use Core\Controller;
use Core\Http\Request;
use Core\Http\Response;
use Core\Security\Csrf;
use Core\Session\Session;

final class UserController extends Controller
{
    private UserService $service;

    public function __construct(
        UserService $service
    ) {
        $this->service = $service;
    }


    /**
     * List users.
     */
    public function index(): Response
    {
        return $this->view(
            'users/index',
            [
                'title' => 'Users',
                'users' => $this->service->all(),
                'success' => Session::pull('users.success'),
                'error' => Session::pull('users.error')
            ]
        );
    }


    /**
     * Display create form.
     */
    public function create(): Response
    {
        return $this->view(
            'users/create',
            [
                'title' => 'Create User',
                'roles' => $this->service->roles(),
                'errors' => Session::pull('users.errors'),
                'old' => Session::pull('users.old')
            ]
        );
    }


    /**
     * Store new user.
     */
    public function store(): Response
    {
        $input = Request::post();

        $result = $this->service->create(
            $input
        );

        if (!$result['success']) {

            Session::flash(
                'users.errors',
                $result['errors']
            );

            Session::flash(
                'users.old',
                $input
            );

            return $this->redirect(
                'admin/users/create'
            );
        }

        Session::flash(
            'users.success',
            'User created successfully.'
        );

        return $this->redirect(
            'admin/users'
        );
    }


    /**
     * Display user.
     */
    public function show(
        int $id
    ): Response {
        $user = $this->service->find($id);

        if ($user === null) {
            return Response::make(
                'User not found.',
                404
            );
        }

        return $this->view(
            'users/show',
            [
                'title' => 'User Details',
                'user' => $user,
                'success' => Session::pull('users.success'),
                'error' => Session::pull('users.error')
            ]
        );
    }

    /**
     * Display edit form.
     */
    public function edit(
        int $id
    ): Response {
        $user = $this->service->find($id);

        if ($user === null) {
            return Response::make(
                'User not found.',
                404
            );
        }

        return $this->view(
            'users/edit',
            [
                'title' => 'Edit User',
                'user' => $user,
                'roles' => $this->service->roles(),
                'errors' => Session::pull('users.errors'),
                'old' => Session::pull('users.old')
            ]
        );
    }


    /**
     * Update existing user.
     */
    public function update(
        int $id
    ): Response {
        $input = Request::post();

        $result = $this->service->update(
            $id,
            $input
        );

        if (!$result['success']) {

            Session::flash(
                'users.errors',
                $result['errors']
            );

            Session::flash(
                'users.old',
                $input
            );

            return $this->redirect(
                'admin/users/' . $id . '/edit'
            );
        }

        Session::flash(
            'users.success',
            'User updated successfully.'
        );

        return $this->redirect(
            'admin/users/' . $id
        );
    }

    /**
     * Update status.
     */
    public function status(
        int $id
    ): Response {
        $status = (string) (
            Request::post()['status'] ?? ''
        );

        $user = $this->service->find($id);

        if ($user === null) {
            return Response::make(
                'User not found.',
                404
            );
        }

        $this->service->changeStatus(
            $id,
            $status
        );

        Session::flash(
            'users.success',
            'User status updated successfully.'
        );

        return $this->redirect(
            'admin/users/' . $id
        );
    }

    /**
     * Delete user.
     */
    public function delete(
        int $id
    ): Response {
        $result = $this->service->delete(
            $id
        );

        if (!$result['success']) {

            Session::flash(
                'users.error',
                $result['errors']['general']
                    ?? 'User could not be deleted.'
            );

            return $this->redirect(
                'admin/users/' . $id
            );
        }

        Session::flash(
            'users.success',
            'User deleted successfully.'
        );

        return $this->redirect(
            'admin/users'
        );
    }
}