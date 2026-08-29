<?php

declare(strict_types=1);

namespace Modules\Settings;

final class SettingsService
{
    private SettingsRepository $repository;


    public function __construct(
        SettingsRepository $repository
    ) {
        $this->repository = $repository;
    }


    /**
     * Return all settings.
     */
    public function all(): array
    {
        return $this->repository->all();
    }


    /**
     * Get a setting value.
     */
    public function get(
        string $key,
        ?string $default = null
    ): ?string {
        $setting = $this->repository->findByKey(
            $key
        );

        if ($setting === null) {
            return $default;
        }

        return (string) $setting['setting_value'];
    }


    /**
     * Set a setting value.
     */
    public function set(
        string $key,
        string $value
    ): bool {
        return $this->repository->set(
            $key,
            $value
        );
    }


    /**
     * Delete a setting.
     */
    public function delete(
        string $key
    ): bool {
        return $this->repository->delete(
            $key
        );
    }
}