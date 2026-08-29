<?php

declare(strict_types=1);

namespace Modules\Audit;

use Core\Controller;
use Core\Http\Response;

final class AuditController extends Controller
{
    private AuditService $service;


    public function __construct(
        AuditService $service
    ) {
        $this->service = $service;
    }


    /**
     * List audit logs.
     */
    public function index(): Response
    {
        return $this->view(
            'audit/index',
            [
                'title' => 'Audit Logs',
                'auditLogs' => $this->service->all()
            ]
        );
    }


    /**
     * Display audit log details.
     */
    public function show(
        int $id
    ): Response {
        $audit = $this->service->find(
            $id
        );

        if ($audit === null) {
            return Response::make(
                'Audit log not found.',
                404
            );
        }

        return $this->view(
            'audit/show',
            [
                'title' => 'Audit Log Details',
                'audit' => $audit
            ]
        );
    }
}