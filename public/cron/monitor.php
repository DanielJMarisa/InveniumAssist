<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Forbidden');
}

require_once dirname(__DIR__, 2) . '/bootstrap/bootstrap.php';

use Core\Database\Database;
use Modules\Monitoring\MonitoringRepository;
use Modules\Monitoring\MonitoringScheduler;
use Modules\Monitoring\MonitoringService;
use Modules\Monitoring\TcpHealthChecker;


/*
|--------------------------------------------------------------------------
| Scheduler Lock
|--------------------------------------------------------------------------
|
| Prevent two monitoring scheduler processes from running at
| the same time.
|
| This is important once the scheduler is executed automatically.
|
*/

$lockPath = LOG_PATH . '/monitoring-scheduler.lock';

$lockHandle = fopen(
    $lockPath,
    'c'
);

if ($lockHandle === false) {

    fwrite(
        STDERR,
        sprintf(
            "[%s] Unable to create scheduler lock file.",
            date('Y-m-d H:i:s')
        ) . PHP_EOL
    );

    exit(1);
}


/*
 * Attempt to acquire an exclusive non-blocking lock.
 */
if (!flock($lockHandle, LOCK_EX | LOCK_NB)) {

    echo sprintf(
        "[%s] Monitoring scheduler already running. Skipping.",
        date('Y-m-d H:i:s')
    ) . PHP_EOL;

    fclose($lockHandle);

    exit(0);
}


/*
|--------------------------------------------------------------------------
| Logging
|--------------------------------------------------------------------------
*/

$logPath = LOG_PATH . '/monitoring-scheduler.log';


/**
 * Write a scheduler log entry.
 */
$log = static function (
    string $message
) use ($logPath): void {

    $line = sprintf(
        "[%s] %s%s",
        date('Y-m-d H:i:s'),
        $message,
        PHP_EOL
    );

    file_put_contents(
        $logPath,
        $line,
        FILE_APPEND | LOCK_EX
    );
};


$log(
    'Monitoring scheduler started.'
);


try {

    /*
    |--------------------------------------------------------------------------
    | Dependencies
    |--------------------------------------------------------------------------
    */

    $repository = new MonitoringRepository(
        Database::connection()
    );

    $service = new MonitoringService(
        $repository
    );

    $checker = new TcpHealthChecker();

    $scheduler = new MonitoringScheduler(
        $service,
        $checker
    );


    /*
    |--------------------------------------------------------------------------
    | Execute Scheduler
    |--------------------------------------------------------------------------
    */

    $results = $scheduler->run();


    $processed = count($results);
    $successful = 0;
    $failed = 0;


    foreach ($results as $result) {

        if (
            ($result['success'] ?? false) === true
        ) {

            $successful++;

        } else {

            $failed++;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Console Output
    |--------------------------------------------------------------------------
    */

    $message = sprintf(
        'Monitoring run completed. Processed: %d, Successful: %d, Failed: %d',
        $processed,
        $successful,
        $failed
    );


    echo sprintf(
        "[%s] %s",
        date('Y-m-d H:i:s'),
        $message
    ) . PHP_EOL;


    /*
    |--------------------------------------------------------------------------
    | Persistent Log
    |--------------------------------------------------------------------------
    */

    $log($message);


    /*
    |--------------------------------------------------------------------------
    | Successful Completion
    |--------------------------------------------------------------------------
    */

    $log(
        'Monitoring scheduler finished successfully.'
    );


} catch (\Throwable $exception) {

    $message = sprintf(
        'Monitoring run failed: %s',
        $exception->getMessage()
    );


    fwrite(
        STDERR,
        sprintf(
            "[%s] %s",
            date('Y-m-d H:i:s'),
            $message
        ) . PHP_EOL
    );


    $log($message);


    /*
     * Log the exception class as well.
     */
    $log(
        sprintf(
            'Exception: %s',
            get_class($exception)
        )
    );


    exit(1);


} finally {

    /*
    |--------------------------------------------------------------------------
    | Release Scheduler Lock
    |--------------------------------------------------------------------------
    */

    flock(
        $lockHandle,
        LOCK_UN
    );

    fclose(
        $lockHandle
    );
}