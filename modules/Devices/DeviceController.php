<?php

declare(strict_types=1);

namespace Modules\Devices;

use Core\Controller;
use Core\Http\Request;
use Core\Http\Response;
use Core\Session\Session;

final class DeviceController extends Controller
{
    private DeviceService $service;


    public function __construct(
        DeviceService $service
    ) {
        $this->service = $service;
    }


    /**
     * List devices.
     */
    public function index(): Response
    {
        return $this->view(
            'devices/index',
            [
                'title' => 'Devices',
                'devices' => $this->service->all(),
                'success' => Session::pull(
                    'devices.success'
                ),
                'error' => Session::pull(
                    'devices.error'
                )
            ]
        );
    }


    /**
     * Display create form.
     */
    public function create(): Response
    {
        return $this->view(
            'devices/create',
            [
                'title' => 'Create Device',
                'customers' => $this->service->customers(),
                'errors' => Session::pull(
                    'devices.errors'
                ),
                'old' => Session::pull(
                    'devices.old'
                )
            ]
        );
    }


    /**
     * Store device.
     */
    public function store(): Response
    {
        $input = Request::post();

        $result = $this->service->create(
            $input
        );

        if (!$result['success']) {

            Session::flash(
                'devices.errors',
                $result['errors']
            );

            Session::flash(
                'devices.old',
                $input
            );

            return $this->redirect(
                'devices/create'
            );
        }

        Session::flash(
            'devices.success',
            'Device created successfully.'
        );

        return $this->redirect(
            'devices'
        );
    }


    /**
     * Display device.
     */
    public function show(
        int $id
    ): Response {
        $device = $this->service->find($id);

        if ($device === null) {
            return Response::make(
                'Device not found.',
                404
            );
        }

        return $this->view(
            'devices/show',
            [
                'title' => 'Device Details',
                'device' => $device
            ]
        );
    }


    /**
     * Display edit form.
     */
    public function edit(
        int $id
    ): Response {
        $device = $this->service->find($id);

        if ($device === null) {
            return Response::make(
                'Device not found.',
                404
            );
        }

        return $this->view(
            'devices/edit',
            [
                'title' => 'Edit Device',
                'device' => $device,
                'customers' => $this->service->customers(),
                'errors' => Session::pull(
                    'devices.errors'
                )
            ]
        );
    }


    /**
     * Update device.
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
                'devices.errors',
                $result['errors']
            );

            return $this->redirect(
                'devices/' . $id . '/edit'
            );
        }

        Session::flash(
            'devices.success',
            'Device updated successfully.'
        );

        return $this->redirect(
            'devices/' . $id
        );
    }
}