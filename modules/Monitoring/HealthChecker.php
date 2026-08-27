<?php

declare(strict_types=1);

namespace Modules\Monitoring;

interface HealthChecker
{
    /**
     * Check whether a device is reachable.
     *
     * @param array<string,mixed> $device
     *
     * @return array{
     *     status: string,
     *     latency_ms: int|null,
     *     error_code: string|null,
     *     error_message: string|null
     * }
     */
    public function check(
        array $device,
        int $timeoutSeconds
    ): array;
}