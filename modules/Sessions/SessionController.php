<?php

declare(strict_types=1);

namespace Modules\Sessions;

use Core\Controller;
use Core\Http\Response;
use Core\Exceptions\NotFoundException;

final class SessionController extends Controller
{
    private SessionService $service;


    public function __construct(
        SessionService $service
    ) {
        $this->service = $service;
    }


    /**
     * Display all sessions.
     */
    public function index(): Response
    {
        return $this->view(
            'sessions/index',
            [
                'sessions' => $this->service->all()
            ]
        );
    }


    /**
     * Display a single session.
     */
    public function show(
        int $id
    ): Response {
        $session = $this->service->find(
            $id
        );

        if ($session === null) {
            throw new NotFoundException(
                'Session not found.'
            );
        }

        return $this->view(
            'sessions/show',
            [
                'session' => $session
            ]
        );
    }
}