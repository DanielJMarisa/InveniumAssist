<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../bootstrap/bootstrap.php';

use Core\Database\Database;
use Modules\Monitoring\MonitoringRepository;
use Modules\Monitoring\MonitoringService;


$db = Database::connection();

$repository = new MonitoringRepository();
$service = new MonitoringService($repository);

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

function assertSameValue(
    mixed $expected,
    mixed $actual,
    string $message
): void {
    if ($expected !== $actual) {
        fail(
            $message
            . " Expected: "
            . var_export($expected, true)
            . " Actual: "
            . var_export($actual, true)
        );
    }
}

try {

    /*
     * ---------------------------------------------------------
     * Transaction
     * ---------------------------------------------------------
     *
     * Everything performed by this test is rolled back.
     */
    $db->beginTransaction();


    /*
     * ---------------------------------------------------------
     * 1. Enable monitoring
     * ---------------------------------------------------------
     */

    $result = $service->enable(
        $deviceId,
        60,
        10
    );

    if (!$result['success']) {
        fail(
            'Unable to enable monitoring: '
            . implode(
                ', ',
                $result['errors']
            )
        );
    }

    pass('Monitoring enabled successfully.');


    /*
     * ---------------------------------------------------------
     * 2. Verify initial state
     * ---------------------------------------------------------
     */

    $monitoring =
        $service->find($deviceId);

    if ($monitoring === null) {
        fail('Monitoring configuration was not found.');
    }

    assertSameValue(
        'unknown',
        $monitoring['current_status'],
        'Initial monitoring state should be unknown.'
    );

    assertSameValue(
        0,
        (int) $monitoring['consecutive_failures'],
        'Initial failure count should be zero.'
    );

    assertSameValue(
        0,
        (int) $monitoring['consecutive_successes'],
        'Initial success count should be zero.'
    );

    pass('Initial monitoring state is correct.');


    /*
     * ---------------------------------------------------------
     * 3. UNKNOWN → ONLINE
     * ---------------------------------------------------------
     */

    $result =
        $service->processCheck(
            $deviceId,
            'online',
            25
        );

    assertSameValue(
        'unknown',
        $result['previous_status'],
        'First check should begin from unknown.'
    );

    assertSameValue(
        'online',
        $result['status'],
        'Successful first check should produce online state.'
    );

    assertSameValue(
        1,
        $result['consecutive_successes'],
        'First successful check should produce one success.'
    );

    assertSameValue(
        0,
        $result['consecutive_failures'],
        'Successful check should have zero failures.'
    );

    pass('UNKNOWN → ONLINE transition works.');


    /*
     * ---------------------------------------------------------
     * 4. Verify no incident was created
     * ---------------------------------------------------------
     */

    $incident =
        $repository->findOpenIncident(
            $deviceId
        );

    if ($incident !== null) {
        fail(
            'Online device should not have an open incident.'
        );
    }

    pass('Online state does not create an incident.');


    /*
     * ---------------------------------------------------------
     * 5. ONLINE → ONLINE
     * ---------------------------------------------------------
     */

    $result =
        $service->processCheck(
            $deviceId,
            'online',
            30
        );

    assertSameValue(
        'online',
        $result['previous_status'],
        'Second check should begin from online.'
    );

    assertSameValue(
        'online',
        $result['status'],
        'Second successful check should remain online.'
    );

    assertSameValue(
        2,
        $result['consecutive_successes'],
        'Successful checks should increment success count.'
    );

    pass('ONLINE → ONLINE transition works.');


    /*
     * ---------------------------------------------------------
     * 6. ONLINE → OFFLINE
     * ---------------------------------------------------------
     */

    $result =
        $service->processCheck(
            $deviceId,
            'offline',
            null,
            'TIMEOUT',
            'Device did not respond within timeout.'
        );

    assertSameValue(
        'online',
        $result['previous_status'],
        'Failure should begin from online.'
    );

    assertSameValue(
        'offline',
        $result['status'],
        'Failed check should produce offline state.'
    );

    assertSameValue(
        1,
        $result['consecutive_failures'],
        'First failure should produce one consecutive failure.'
    );

    assertSameValue(
        0,
        $result['consecutive_successes'],
        'Failure should reset consecutive successes.'
    );

    pass('ONLINE → OFFLINE transition works.');


    /*
     * ---------------------------------------------------------
     * 7. Verify incident creation
     * ---------------------------------------------------------
     */

    $incident =
        $repository->findOpenIncident(
            $deviceId
        );

    if ($incident === null) {
        fail(
            'Offline transition should create an open incident.'
        );
    }

    assertSameValue(
        'open',
        $incident['status'],
        'New incident should be open.'
    );

    pass('Offline transition creates an incident.');


    /*
     * ---------------------------------------------------------
     * 8. OFFLINE → OFFLINE
     * ---------------------------------------------------------
     */

    $result =
        $service->processCheck(
            $deviceId,
            'offline',
            null,
            'TIMEOUT',
            'Device still did not respond.'
        );

    assertSameValue(
        'offline',
        $result['previous_status'],
        'Repeated failure should begin from offline.'
    );

    assertSameValue(
        'offline',
        $result['status'],
        'Repeated failure should remain offline.'
    );

    assertSameValue(
        2,
        $result['consecutive_failures'],
        'Repeated failure should increment failure count.'
    );

    pass('OFFLINE → OFFLINE transition works.');


    /*
     * ---------------------------------------------------------
     * 9. Verify no duplicate incident
     * ---------------------------------------------------------
     */

    $incidents =
        $repository->incidents(
            $deviceId,
            50
        );

    if (count($incidents) !== 1) {
        fail(
            'Repeated offline checks must not create duplicate incidents.'
        );
    }

    pass('Repeated offline checks do not create duplicate incidents.');


    /*
     * ---------------------------------------------------------
     * 10. OFFLINE → ONLINE
     * ---------------------------------------------------------
     */

    $result =
        $service->processCheck(
            $deviceId,
            'online',
            22
        );

    assertSameValue(
        'offline',
        $result['previous_status'],
        'Recovery should begin from offline.'
    );

    assertSameValue(
        'online',
        $result['status'],
        'Successful recovery should produce online state.'
    );

    assertSameValue(
        1,
        $result['consecutive_successes'],
        'Recovery should reset and start success count.'
    );

    assertSameValue(
        0,
        $result['consecutive_failures'],
        'Recovery should reset failure count.'
    );

    pass('OFFLINE → ONLINE transition works.');


    /*
     * ---------------------------------------------------------
     * 11. Verify incident resolution
     * ---------------------------------------------------------
     */

    $openIncident =
        $repository->findOpenIncident(
            $deviceId
        );

    if ($openIncident !== null) {
        fail(
            'Recovered device should not have an open incident.'
        );
    }

    $incidents =
        $repository->incidents(
            $deviceId,
            50
        );

    if (count($incidents) !== 1) {
        fail(
            'Incident history should contain exactly one incident.'
        );
    }

    assertSameValue(
        'resolved',
        $incidents[0]['status'],
        'Recovered incident should be resolved.'
    );

    if ($incidents[0]['resolved_at'] === null) {
        fail(
            'Resolved incident must contain resolved_at.'
        );
    }

    if ($incidents[0]['duration_seconds'] === null) {
        fail(
            'Resolved incident must contain duration_seconds.'
        );
    }

    pass('Incident resolution works correctly.');


    /*
     * ---------------------------------------------------------
     * 12. Verify final monitoring state
     * ---------------------------------------------------------
     */

    $monitoring =
        $service->find($deviceId);

    if ($monitoring === null) {
        fail(
            'Final monitoring configuration could not be retrieved.'
        );
    }

    assertSameValue(
        'online',
        $monitoring['current_status'],
        'Final monitoring state should be online.'
    );

    assertSameValue(
        0,
        (int) $monitoring['consecutive_failures'],
        'Final failure count should be zero.'
    );

    assertSameValue(
        1,
        (int) $monitoring['consecutive_successes'],
        'Final success count should be one.'
    );

    pass('Final monitoring state is correct.');


    /*
     * ---------------------------------------------------------
     * 13. Verify device synchronization
     * ---------------------------------------------------------
     */

    $statement = $db->prepare("
        SELECT
            status,
            last_seen
        FROM devices
        WHERE id = :id
        LIMIT 1
    ");

    $statement->execute([
        'id' => $deviceId
    ]);

    $device =
        $statement->fetch(PDO::FETCH_ASSOC);

    if ($device === false) {
        fail(
            'Device could not be retrieved after monitoring.'
        );
    }

    assertSameValue(
        'online',
        $device['status'],
        'Device status should be synchronized to online.'
    );

    if ($device['last_seen'] === null) {
        fail(
            'Online monitoring check should update last_seen.'
        );
    }

    pass('Device status is synchronized correctly.');


    /*
     * ---------------------------------------------------------
     * Roll back test
     * ---------------------------------------------------------
     */

    $db->rollBack();

    echo PHP_EOL;
    echo "MonitoringServiceTest PASSED." . PHP_EOL;

} catch (Throwable $exception) {

    if ($db->inTransaction()) {
        $db->rollBack();
    }

    echo PHP_EOL;
    echo "MonitoringServiceTest FAILED." . PHP_EOL;
    echo $exception->getMessage() . PHP_EOL;

    exit(1);
}