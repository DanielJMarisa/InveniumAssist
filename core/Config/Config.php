<?php

declare(strict_types=1);

namespace Core\Config;

use RuntimeException;

final class Config
{
    /**
     * Loaded configuration.
     *
     * @var array<string, array<string, mixed>>
     */
    private static array $config = [];

    /**
     * Prevent instantiation.
     */
    private function __construct()
    {
    }

    /**
     * Load all configuration files.
     *
     * @throws RuntimeException
     */
    public static function load(string $configPath): void
    {
        if (!empty(self::$config)) {
            return;
        }

        if (!is_dir($configPath)) {

            throw new RuntimeException(

                "Configuration directory not found: {$configPath}"

            );

        }

        $files = glob($configPath . DIRECTORY_SEPARATOR . '*.php');

        if ($files === false) {

            throw new RuntimeException(

                'Unable to read configuration directory.'

            );

        }

        foreach ($files as $file) {

            $key = pathinfo($file, PATHINFO_FILENAME);

            $config = require $file;

            if (!is_array($config)) {

                throw new RuntimeException(

                    "Configuration file [{$key}] must return an array."

                );

            }

            self::$config[$key] = $config;

        }

    }

    /**
     * Retrieve a configuration value.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $segments = explode('.', $key);

        $value = self::$config;

        foreach ($segments as $segment) {

            if (!is_array($value) || !array_key_exists($segment, $value)) {

                return $default;

            }

            $value = $value[$segment];

        }

        return $value;

    }

    /**
     * Determine if a configuration key exists.
     */
    public static function has(string $key): bool
    {
        return self::get($key, '__missing__') !== '__missing__';
    }

    /**
     * Return all loaded configuration.
     *
     * @return array<string, mixed>
     */
    public static function all(): array
    {
        return self::$config;
    }

    /**
     * Set or override a configuration value at runtime.
     */
    public static function set(string $key, mixed $value): void
    {
        $segments = explode('.', $key);

        $config = &self::$config;

        foreach ($segments as $segment) {

            if (!isset($config[$segment]) || !is_array($config[$segment])) {

                $config[$segment] = [];

            }

            $config = &$config[$segment];

        }

        $config = $value;

    }

    /**
     * Remove all loaded configuration.
     * Useful for testing.
     */
    public static function clear(): void
    {
        self::$config = [];
    }
}