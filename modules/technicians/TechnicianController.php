<?php

declare(strict_types=1);

namespace Modules\Technicians;

use Core\Controller;
use Core\Http\Request;
use Core\Http\Response;
use Core\Auth\Auth;

final class TechnicianController extends Controller
{
    private TechnicianService $service;

    public function __construct(
        TechnicianService $service
    ) {
        $this->service = $service;
    }

    /**
     * Return available technicians.
     *
     * Public endpoint.
     */
    public function available(): Response
    {
        return $this->json([
            'success' => true,
            'technicians' =>
                $this->service->available(60)
        ]);
    }

    /**
     * Return current technician profile.
     *
     * Technician-authenticated endpoint.
     */
    public function current(): Response
    {
        $userId = Auth::id();

        if ($userId === null) {
            return $this->json([
                'success' => false,
                'error' => 'Authentication required.'
            ], 401);
        }

        $technician =
            $this->service->findByUserId(
                (int) $userId
            );

        if ($technician === null) {
            return $this->json([
                'success' => false,
                'error' =>
                    'Technician profile was not found.'
            ], 404);
        }

        return $this->json([
            'success' => true,
            'technician' => $technician
        ]);
    }

    /**
     * Update technician presence.
     */
    public function heartbeat(): Response
    {
        $userId = Auth::id();

        if ($userId === null) {
            return $this->json([
                'success' => false,
                'error' => 'Authentication required.'
            ], 401);
        }

        $technician =
            $this->service->findByUserId(
                (int) $userId
            );

        if ($technician === null) {
            return $this->json([
                'success' => false,
                'error' =>
                    'Technician profile was not found.'
            ], 404);
        }

        $input = Request::json();

        $status =
            trim(
                (string) (
                    $input['status']
                    ?? 'available'
                )
            );

        try {
            $result =
                $this->service->heartbeat(
                    (int) $technician['id'],
                    $status
                );
        } catch (\InvalidArgumentException $exception) {
            return $this->json([
                'success' => false,
                'error' => $exception->getMessage()
            ], 422);
        }

        return $this->json($result);
    }

    /**
     * Update technician status without changing heartbeat.
     */
    public function status(): Response
    {
        $userId = Auth::id();

        if ($userId === null) {
            return $this->json([
                'success' => false,
                'error' => 'Authentication required.'
            ], 401);
        }

        $technician =
            $this->service->findByUserId(
                (int) $userId
            );

        if ($technician === null) {
            return $this->json([
                'success' => false,
                'error' =>
                    'Technician profile was not found.'
            ], 404);
        }

        $input = Request::json();

        $status =
            trim(
                (string) ($input['status'] ?? '')
            );

        try {
            $success =
                $this->service->updateStatus(
                    (int) $technician['id'],
                    $status
                );
        } catch (\InvalidArgumentException $exception) {
            return $this->json([
                'success' => false,
                'error' => $exception->getMessage()
            ], 422);
        }

        return $this->json([
            'success' => $success,
            'status' => $status
        ]);
    }
}