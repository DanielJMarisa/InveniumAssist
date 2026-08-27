<?php

declare(strict_types=1);

namespace Modules\Incidents;

use Core\Controller;
use Core\Http\Request;
use Core\Http\Response;
use Core\Session\Session;

final class IncidentController extends Controller
{
    private IncidentService $service;


    public function __construct(
        IncidentService $service
    ) {
        $this->service = $service;
    }


    /**
     * Display the incident dashboard.
     */
    public function index(): Response
    {
        return $this->view(
            'incidents/index',
            [
                'title' => 'Incidents',
                'incidents' =>
                    $this->service->all(),
                'success' =>
                    Session::pull(
                        'incidents.success'
                    ),
                'error' =>
                    Session::pull(
                        'incidents.error'
                    )
            ]
        );
    }


    /**
     * Display a single incident.
     */
    public function show(
        int $id
    ): Response {
        $incident =
            $this->service->find(
                $id
            );

        if ($incident === null) {
            return Response::make(
                'Incident not found.',
                404
            );
        }


        return $this->view(
            'incidents/show',
            [
                'title' => 'Incident',
                'incident' => $incident,
                'success' =>
                    Session::pull(
                        'incidents.success'
                    ),
                'error' =>
                    Session::pull(
                        'incidents.error'
                    )
            ]
        );
    }


    /**
     * Update incident notes.
     */
    public function updateNotes(
        int $id
    ): Response {
        $input = Request::post();


        $notes = null;


        if (
            isset($input['notes'])
            && is_string($input['notes'])
        ) {
            $notes = $input['notes'];
        }


        $result =
            $this->service->updateNotes(
                $id,
                $notes
            );


        if (!$result['success']) {

            Session::flash(
                'incidents.error',
                implode(
                    ' ',
                    $result['errors']
                )
            );

            return $this->redirect(
                'incidents/' . $id
            );
        }


        Session::flash(
            'incidents.success',
            'Incident notes updated successfully.'
        );


        return $this->redirect(
            'incidents/' . $id
        );
    }
}