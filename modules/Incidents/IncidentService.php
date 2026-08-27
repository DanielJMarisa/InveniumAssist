<?php

declare(strict_types=1);

namespace Modules\Incidents;

use Core\Service;
use RuntimeException;

final class IncidentService extends Service
{
    private IncidentRepository $repository;


    public function __construct(
        IncidentRepository $repository
    ) {
        parent::__construct();

        $this->repository = $repository;
    }


    /**
     * Return all monitoring incidents.
     *
     * @return array<int,array<string,mixed>>
     */
    public function all(): array
    {
        return $this->repository->all();
    }


    /**
     * Find an incident by ID.
     *
     * @return array<string,mixed>|null
     */
    public function find(
        int $incidentId
    ): ?array {
        $this->validateIncidentId(
            $incidentId
        );

        return $this->repository->findById(
            $incidentId
        );
    }


    /**
     * Return currently open incidents.
     *
     * @return array<int,array<string,mixed>>
     */
    public function open(): array
    {
        return $this->repository->open();
    }


    /**
     * Return resolved incidents.
     *
     * @return array<int,array<string,mixed>>
     */
    public function resolved(): array
    {
        return $this->repository->resolved();
    }


    /**
     * Update incident notes.
     *
     * @return array{
     *     success: bool,
     *     errors: array<string,string>
     * }
     */
    public function updateNotes(
        int $incidentId,
        ?string $notes
    ): array {
        $errors = [];

        try {
            $this->validateIncidentId(
                $incidentId
            );
        } catch (RuntimeException $exception) {
            $errors['incident'] =
                $exception->getMessage();
        }


        if ($errors !== []) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }


        /*
         * Normalize empty notes to NULL.
         */
        if ($notes !== null) {
            $notes = trim($notes);

            if ($notes === '') {
                $notes = null;
            }
        }


        /*
         * Verify that the incident exists before
         * attempting to update it.
         */
        $incident =
            $this->repository->findById(
                $incidentId
            );

        if ($incident === null) {
            return [
                'success' => false,
                'errors' => [
                    'incident' =>
                        'Incident not found.'
                ]
            ];
        }


        $success =
            $this->repository->updateNotes(
                $incidentId,
                $notes
            );


        if (!$success) {
            return [
                'success' => false,
                'errors' => [
                    'notes' =>
                        'Unable to update incident notes.'
                ]
            ];
        }


        return [
            'success' => true,
            'errors' => []
        ];
    }


    /**
     * Validate an incident ID.
     */
    private function validateIncidentId(
        int $incidentId
    ): void {
        if ($incidentId <= 0) {
            throw new RuntimeException(
                'Invalid incident ID.'
            );
        }
    }
}