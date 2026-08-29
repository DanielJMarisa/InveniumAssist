<?php

declare(strict_types=1);

namespace Modules\Audit;

final class AuditService
{
    private AuditRepository $repository;

    public function __construct(
        AuditRepository $repository
    ) {
        $this->repository = $repository;
    }


    /**
     * Return all audit logs.
     */
    public function all(): array
    {
        return $this->repository->all();
    }


    /**
     * Find an audit log.
     */
    public function find(
        int $id
    ): ?array {
        return $this->repository->find(
            $id
        );
    }
}