<?php

declare(strict_types=1);

namespace Modules\Sessions;

use Core\Service;

final class SessionService extends Service
{
    private SessionRepository $repository;


    public function __construct(
        SessionRepository $repository
    ) {
        parent::__construct();

        $this->repository = $repository;
    }


    /**
     * Return all sessions.
     *
     * @return array<int,array<string,mixed>>
     */
    public function all(): array
    {
        return $this->repository->all();
    }


    /**
     * Find a session.
     *
     * @return array<string,mixed>|null
     */
    public function find(
        int $id
    ): ?array {
        return $this->repository->find($id);
    }
}