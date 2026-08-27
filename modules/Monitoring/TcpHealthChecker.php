<?php

declare(strict_types=1);

namespace Modules\Monitoring;

final class TcpHealthChecker implements HealthChecker
{
    /**
     * Perform a TCP connectivity check.
     *
     * The device must provide either:
     *
     * - public_ip
     * - local_ip
     * - hostname
     *
     * A monitoring_port may optionally be supplied.
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
    ): array {
        $timeoutSeconds = max(
            1,
            $timeoutSeconds
        );

        $host = $this->resolveHost(
            $device
        );

        if ($host === null) {
            return [
                'status' => 'unknown',
                'latency_ms' => null,
                'error_code' => 'NO_ADDRESS',
                'error_message' =>
                    'No usable device address is available.'
            ];
        }

        $port = $this->resolvePort(
            $device
        );

        $start = microtime(true);

        $errno = 0;
        $error = '';

        $socket = @fsockopen(
            $host,
            $port,
            $errno,
            $error,
            $timeoutSeconds
        );

        $elapsed = microtime(true) - $start;

        $latencyMs = (int) round(
            $elapsed * 1000
        );

        if (is_resource($socket)) {

            fclose($socket);

            return [
                'status' => 'online',
                'latency_ms' => $latencyMs,
                'error_code' => null,
                'error_message' => null
            ];
        }

        return [
            'status' => 'offline',
            'latency_ms' => null,
            'error_code' =>
                $errno > 0
                    ? 'TCP_' . $errno
                    : 'CONNECTION_FAILED',
            'error_message' =>
                $error !== ''
                    ? $error
                    : 'Unable to establish TCP connection.'
        ];
    }


    /**
     * Determine the address to monitor.
     *
     * Prefer public IP, then local IP, then hostname.
     *
     * @param array<string,mixed> $device
     */
    private function resolveHost(
        array $device
    ): ?string {
        $addresses = [
            $device['public_ip'] ?? null,
            $device['local_ip'] ?? null,
            $device['hostname'] ?? null
        ];

        foreach ($addresses as $address) {

            $address = trim(
                (string) $address
            );

            if ($address !== '') {
                return $address;
            }
        }

        return null;
    }


    /**
     * Determine TCP monitoring port.
     *
     * Defaults to HTTPS.
     *
     * @param array<string,mixed> $device
     */
    private function resolvePort(
        array $device
    ): int {
        $port = $device['monitoring_port'] ?? null;

        if (
            $port !== null
            && ctype_digit((string) $port)
        ) {
            $port = (int) $port;

            if ($port >= 1 && $port <= 65535) {
                return $port;
            }
        }

        return 443;
    }
}