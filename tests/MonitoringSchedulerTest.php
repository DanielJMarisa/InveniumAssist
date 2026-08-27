<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/bootstrap.php';

use Core\Database\Database;
use Modules\Monitoring\MonitoringRepository;
use Modules\Monitoring\MonitoringScheduler;
use Modules\Monitoring\MonitoringService;
use Modules\Monitoring\TcpHealthChecker;

$db = Database::connection();

$repository = new MonitoringRepository(
    $db
);

$service = new MonitoringService(
    $repository
);

$checker = new TcpHealthChecker();

$scheduler = new MonitoringScheduler(
    $service,
    $checker
);

$results = $scheduler->run();

echo PHP_EOL;

echo 'Monitoring scheduler executed.';
echo PHP_EOL;

echo 'Devices processed: ';
echo count($results);
echo PHP_EOL;

foreach ($results as $result) {

    echo PHP_EOL;

    echo 'Device ID: ';
    echo $result['device_id'];
    echo PHP_EOL;

    echo 'Success: ';
    echo $result['success']
        ? 'Yes'
        : 'No';
    echo PHP_EOL;

    echo 'Status: ';
    echo $result['status'];
    echo PHP_EOL;

    if (!$result['success']) {

        echo 'Error: ';
        echo $result['error'];
        echo PHP_EOL;
    }
}