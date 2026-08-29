<?php

declare(strict_types=1);

namespace Modules\Technicians;

use Core\Service;
use InvalidArgumentException;

final class TechnicianService extends Service
{
    private TechnicianRepository $repository;

    private const STATUSES = [
        'available',
        'busy',
        'away',
        'offline'
    ];

    public function __construct(
        TechnicianRepository $repository
    ) {
        parent::__construct();

        $this->repository = $repository;
    }

    /**
     * Find a technician.
     *
     * @return array<string,mixed>|null
     */
    public function find(int $id): ?array
    {
        return $this->repository->find($id);
    }

    /**
     * Find technician belonging to a user.
     *
     * @return array<string,mixed>|null
     */
    public function findByUserId(int $userId): ?array
    {
        return $this->repository->findByUserId($userId);
    }

    /**
     * Return all technicians.
     *
     * @return array<int,array<string,mixed>>
     */
    public function all(): array
    {
        return $this->repository->all();
    }

    /**
     * Return technicians currently available.
     *
     * @return array<int,array<string,mixed>>
     */
    public function available(
        int $heartbeatSeconds = 60
    ): array {
        return $this->repository->available(
            $heartbeatSeconds
        );
    }

    /**
     * Register a technician.
     *
     * @return array<string,mixed>
     */
    public function register(
        int $userId,
        string $displayName
    ): array {
        $displayName = trim($displayName);

        if ($displayName === '') {
            return [
                'success' => false,
                'errors' => [
                    'display_name' =>
                        'Display name is required.'
                ]
            ];
        }

        if (
            $this->repository->findByUserId($userId)
            !== null
        ) {
            return [
                'success' => false,
                'errors' => [
                    'user_id' =>
                        'User is already registered as a technician.'
                ]
            ];
        }

        $id = $this->repository->create(
            $userId,
            $displayName
        );

        return [
            'success' => true,
            'id' => $id
        ];
    }

    /**
     * Update technician presence status.
     */
    public function updateStatus(
        int $technicianId,
        string $status
    ): bool {
        $this->validateStatus($status);

        return $this->repository->updateStatus(
            $technicianId,
            $status
        );
    }

    /**
     * Record a technician heartbeat.
     */
    public function heartbeat(
        int $technicianId,
        string $status = 'available'
    ): array {
        $this->validateStatus($status);

        $technician =
            $this->repository->find($technicianId);

        if ($technician === null) {
            return [
                'success' => false,
                'errors' => [
                    'technician' =>
                        'Technician was not found.'
                ]
            ];
        }

        $success =
            $this->repository->heartbeat(
                $technicianId,
                $status
            );

        return [
            'success' => $success,
            'technician_id' => $technicianId,
            'status' => $status
        ];
    }

    /**
     * Mark stale technicians offline.
     */
    public function markStaleOffline(
        int $heartbeatSeconds = 60
    ): int {
        return $this->repository->markStaleOffline(
            $heartbeatSeconds
        );
    }

    /**
     * Increment active session count.
     */
    public function incrementSessions(
        int $technicianId
    ): bool {
        return $this->repository->incrementSessions(
            $technicianId
        );
    }

    /**
     * Decrement active session count.
     */
    public function decrementSessions(
        int $technicianId
    ): bool {
        return $this->repository->decrementSessions(
            $technicianId
        );
    }

    /**
     * Validate technician status.
     */
    private function validateStatus(
        string $status
    ): void {
        if (
            !in_array(
                $status,
                self::STATUSES,
                true
            )
        ) {
            throw new InvalidArgumentException(
                'Invalid technician status.'
            );
        }
    }
}