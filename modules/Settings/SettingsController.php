<?php

declare(strict_types=1);

namespace Modules\Settings;

use Core\Controller;
use Core\Http\Response;

final class SettingsController extends Controller
{
    private SettingsService $service;


    public function __construct(
        SettingsService $service
    ) {
        $this->service = $service;
    }


    /**
     * Display application settings.
     */
    public function index(): Response
    {
        return $this->view(
            'settings/index',
            [
                'title' => 'Settings',
                'settings' => $this->service->all()
            ]
        );
    }
}