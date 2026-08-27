<?php

declare(strict_types=1);

namespace Modules\Dashboard;

use Core\Controller;
use Core\Http\Response;
use Core\Session\Session;

final class DashboardController extends Controller
{
    /**
     * Display the authenticated user's dashboard.
     */
    public function index(): Response
    {
        if (!Session::has('auth.user_id')) {
            return $this->redirect('login');
        }

        return $this->view(
            'dashboard/index',
            [
                'userId' => Session::get('auth.user_id'),
                'username' => Session::get('auth.username'),
                'role' => Session::get('auth.role'),
                'success' => Session::pull('auth.success')
            ]
        );
    }
}