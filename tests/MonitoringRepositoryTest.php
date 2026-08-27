<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../bootstrap/bootstrap.php';

use Core\Database\Database;
use Modules\Monitoring\MonitoringRepository;

$db = Database::connection();

$repository = new MonitoringRepository();

$deviceId = 1;

function pass(string $message): void
{
    echo "PASS: {$message}" . PHP_EOL;
}

function fail(string $message): void
{
    echo "FAIL: {$message}" . PHP_EOL;
    exit(1);
}

try {
    /*
     * Use a transaction so this test leaves the
     * production/development database unchanged.
     */
    $db->beginTransaction();


    /*
     * ---------------------------------------------------------
     * 1. Create monitoring configuration
     * ---------------------------------------------------------
     */

    $monitoringId = $repository->create(
        $deviceId,
        60,
        10
    );

    if ($monitoringId <= 0) {
        fail('Monitoring configuration was not created.');
    }

    pass('Monitoring configuration created.');


    /*
     * ---------------------------------------------------------
     * 2. Retrieve monitoring configuration
     * ---------------------------------------------------------
     */

    $monitoring = $repository->findByDeviceId(
        $deviceId
    );

    if ($monitoring === null) {
        fail('Monitoring configuration could not be retrieved.');
    }

    if ((int) $monitoring['device_id'] !== $deviceId) {
        fail('Monitoring configuration returned incorrect device ID.');
    }

    if ((int) $monitoring['enabled'] !== 1) {
        fail('Monitoring configuration is not enabled.');
    }

    if ((int) $monitoring['interval_seconds'] !== 60) {
        fail('Monitoring interval is incorrect.');
    }

    if ((int) $monitoring['timeout_seconds'] !== 10) {
        fail('Monitoring timeout is incorrect.');
    }

    if ($monitoring['current_status'] !== 'unknown') {
        fail('New monitoring configuration does not start as unknown.');
    }

    pass('Monitoring configuration retrieved correctly.');


    /*
     * ---------------------------------------------------------
     * 3. Verify device appears as due for checking
     * ---------------------------------------------------------
     */

    $dueDevices = $repository->dueDevices();

    $foundDevice = false;

    foreach ($dueDevices as $dueDevice) {
        if ((int) $dueDevice['device_id'] === $deviceId) {
            $foundDevice = true;
            break;
        }
    }

    if (!$foundDevice) {
        fail('New monitoring device was not identified as due.');
    }

    pass('Due-device detection works.');


    /*
     * ---------------------------------------------------------
     * 4. Record a successful monitoring check
     * ---------------------------------------------------------
     */

    $checkedAt = date(
        'Y-m-d H:i:s'
    );

    $checkId = $repository->recordCheck(
        $deviceId,
        $checkedAt,
        'online',
        25,
        null,
        null
    );

    if ($checkId <= 0) {
        fail('Monitoring check was not recorded.');
    }

    pass('Monitoring check recorded.');


    /*
     * ---------------------------------------------------------
     * 5. Update monitoring state
     * ---------------------------------------------------------
     */

    $nextCheckAt = date(
        'Y-m-d H:i:s',
        time() + 60
    );

    $stateUpdated = $repository->updateState(
        $deviceId,
        'online',
        25,
        0,
        1,
        $checkedAt,
        $nextCheckAt,
        null
    );

    if (!$stateUpdated) {
        fail('Monitoring state could not be updated.');
    }

    $monitoring = $repository->findByDeviceId(
        $deviceId
    );

    if ($monitoring === null) {
        fail('Monitoring configuration disappeared after state update.');
    }

    if ($monitoring['current_status'] !== 'online') {
        fail('Monitoring status was not updated to online.');
    }

    if ((int) $monitoring['current_latency_ms'] !== 25) {
        fail('Monitoring latency was not updated.');
    }

    if ((int) $monitoring['consecutive_successes'] !== 1) {
        fail('Consecutive success count was not updated.');
    }

    if ((int) $monitoring['consecutive_failures'] !== 0) {
        fail('Consecutive failure count was not reset.');
    }

    pass('Monitoring state updated correctly.');


    /*
     * ---------------------------------------------------------
     * 6. Synchronize device status
     * ---------------------------------------------------------
     */

    $deviceUpdated = $repository->updateDeviceStatus(
        $deviceId,
        'online',
        $checkedAt
    );

    if (!$deviceUpdated) {
        fail('Device status could not be updated.');
    }

    pass('Device status synchronization works.');


    /*
     * ---------------------------------------------------------
     * 7. Create monitoring incident
     * ---------------------------------------------------------
     */

    $incidentStartedAt = date(
        'Y-m-d H:i:s'
    );

    $incidentId = $repository->createIncident(
        $deviceId,
        $incidentStartedAt,
        'Monitoring test outage'
    );

    if ($incidentId <= 0) {
        fail('Monitoring incident was not created.');
    }

    pass('Monitoring incident created.');


    /*
     * ---------------------------------------------------------
     * 8. Find open incident
     * ---------------------------------------------------------
     */

    $incident = $repository->findOpenIncident(
        $deviceId
    );

    if ($incident === null) {
        fail('Open monitoring incident could not be found.');
    }

    if ((int) $incident['id'] !== $incidentId) {
        fail('Incorrect open incident was returned.');
    }

    if ($incident['status'] !== 'open') {
        fail('Incident is not marked as open.');
    }

    pass('Open monitoring incident retrieved correctly.');


    /*
     * ---------------------------------------------------------
     * 9. Resolve incident
     * ---------------------------------------------------------
     */

    $resolvedAt = date(
        'Y-m-d H:i:s',
        time() + 120
    );

    $resolved = $repository->resolveIncident(
        $incidentId,
        $resolvedAt,
        120
    );

    if (!$resolved) {
        fail('Monitoring incident could not be resolved.');
    }

    pass('Monitoring incident resolved.');


    /*
     * ---------------------------------------------------------
     * 10. Verify recent checks
     * ---------------------------------------------------------
     */

    $checks = $repository->recentChecks(
        $deviceId,
        10
    );

    if ($checks === []) {
        fail('Recent monitoring checks returned no results.');
    }

    if ((int) $checks[0]['device_id'] !== $deviceId) {
        fail('Recent check returned incorrect device.');
    }

    pass('Recent monitoring checks retrieved correctly.');


    /*
     * ---------------------------------------------------------
     * 11. Verify incidents
     * ---------------------------------------------------------
     */

    $incidents = $repository->incidents(
        $deviceId,
        10
    );

    if ($incidents === []) {
        fail('Monitoring incidents returned no results.');
    }

    if ((int) $incidents[0]['id'] !== $incidentId) {
        fail('Incident history returned incorrect incident.');
    }

    if ($incidents[0]['status'] !== 'resolved') {
        fail('Resolved incident is not marked as resolved.');
    }

    pass('Monitoring incident history retrieved correctly.');


    /*
     * ---------------------------------------------------------
     * Roll back everything.
     * ---------------------------------------------------------
     */

    $db->rollBack();

    echo PHP_EOL;
    echo "MonitoringRepositoryTest PASSED." . PHP_EOL;

} catch (Throwable $exception) {

    if ($db->inTransaction()) {
        $db->rollBack();
    }

    echo PHP_EOL;
    echo "MonitoringRepositoryTest FAILED." . PHP_EOL;
    echo $exception->getMessage() . PHP_EOL;

    exit(1);
}