<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/bootstrap.php';

use Modules\Monitoring\TcpHealthChecker;

$checker = new TcpHealthChecker();

$result = $checker->check(
    [
        'public_ip' => 'aurabloomcollection.com',
        'local_ip' => null,
        'hostname' => null
    ],
    5
);

echo PHP_EOL;

echo "Status: ";
echo $result['status'];
echo PHP_EOL;

echo "Latency: ";
echo $result['latency_ms'] ?? 'N/A';
echo " ms";
echo PHP_EOL;

echo "Error Code: ";
echo $result['error_code'] ?? 'None';
echo PHP_EOL;

echo "Error Message: ";
echo $result['error_message'] ?? 'None';
echo PHP_EOL;