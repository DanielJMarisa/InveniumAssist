<?php

declare(strict_types=1);

namespace Modules\Monitoring;

use Core\Controller;
use Core\Http\Request;
use Core\Http\Response;
use Core\Session\Session;

final class MonitoringController extends Controller
{
    private MonitoringService $service;

    public function __construct(
        MonitoringService $service
    ) {
        $this->service = $service;
    }


    /**
     * Display the monitoring dashboard.
     */
    public function index(): Response
    {
        return $this->view(
            'monitoring/index',
            [
                'title' => 'Monitor',
                'devices' => $this->service->devices()
            ]
        );
    }


    /**
     * Return current monitoring state for the Monitor UI.
     */
    public function status(): Response
    {
        return Response::json([
            'success' => true,
            'devices' => $this->service->devices()
        ]);
    }

    /**
     * Display monitoring details for a device.
     */
    public function show(
        int $id
    ): Response {
        $monitoring = $this->service->details($id);

        if ($monitoring === null) {
            return Response::make(
                'Monitoring configuration not found.',
                404
            );
        }

        return $this->view(
            'monitoring/show',
            [
                'title' => 'Device Monitor',
                'monitoring' => $monitoring,
                'checks' => $this->service->recentChecks($id),
                'incidents' => $this->service->incidents($id),
                'success' => Session::pull(
                    'monitoring.success'
                ),
                'error' => Session::pull(
                    'monitoring.error'
                )
            ]
        );
    }


    /**
     * Enable or configure monitoring.
     */
    public function enable(
        int $id
    ): Response {
        $input = Request::post();

        $intervalSeconds = (int) (
            $input['interval_seconds'] ?? 60
        );

        $timeoutSeconds = (int) (
            $input['timeout_seconds'] ?? 10
        );

        $result = $this->service->enable(
            $id,
            $intervalSeconds,
            $timeoutSeconds
        );

        if (!$result['success']) {
            Session::flash(
                'monitoring.error',
                implode(' ', $result['errors'])
            );

            return $this->redirect(
                'monitor/' . $id
            );
        }

        Session::flash(
            'monitoring.success',
            'Monitoring enabled successfully.'
        );

        return $this->redirect(
            'monitor/' . $id
        );
    }


    /**
     * Disable monitoring.
     */
    public function disable(
        int $id
    ): Response {
        $result = $this->service->disable($id);

        if (!$result['success']) {
            Session::flash(
                'monitoring.error',
                implode(' ', $result['errors'])
            );

            return $this->redirect(
                'monitor/' . $id
            );
        }

        Session::flash(
            'monitoring.success',
            'Monitoring disabled successfully.'
        );

        return $this->redirect(
            'monitor/' . $id
        );
    }
}