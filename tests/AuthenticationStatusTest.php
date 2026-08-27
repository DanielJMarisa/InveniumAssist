<?php

declare(strict_types=1);

ob_start();

/*
|--------------------------------------------------------------------------
| Invenium Assist Authentication Status Tests
|--------------------------------------------------------------------------
|
| Verifies authentication behavior for the three supported account states:
|
|   active
|   inactive
|   locked
|
| Local development test only.
| Uses the existing Daniel test account and restores it to ACTIVE afterwards.
|
*/

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../bootstrap/bootstrap.php';

use Core\Database\Database;
use Modules\Auth\AuthRepository;
use Modules\Auth\AuthService;
use Modules\Auth\AuthValidator;


/*
|--------------------------------------------------------------------------
| Test Configuration
|--------------------------------------------------------------------------
*/

const TEST_USERNAME = 'daniel@inveniumtech.com';

const INVALID_PASSWORD = 'definitely-not-the-correct-password';


/*
|--------------------------------------------------------------------------
| Test Helpers
|--------------------------------------------------------------------------
*/

function failTest(string $message): never
{
    if (ob_get_level() > 0) {
        ob_end_clean();
    }

    throw new RuntimeException(
        PHP_EOL
        . 'FAIL: '
        . $message
        . PHP_EOL
    );
}


function passTest(string $message): void
{
    echo 'PASS: ' . $message . PHP_EOL;
}


/**
 * Fetch the test account.
 *
 * @return array<string,mixed>
 */
function getTestAccount(PDO $db): array
{
    $statement = $db->prepare(
        '
        SELECT
            id,
            username,
            email,
            status,
            failed_logins,
            locked_until
        FROM users
        WHERE username = :username
        LIMIT 1
        '
    );

    $statement->execute([
        'username' => TEST_USERNAME
    ]);

    $user = $statement->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        failTest(
            'Test account "' . TEST_USERNAME . '" was not found.'
        );
    }

    return $user;
}


/**
 * Set account status and clear temporary lockout state.
 */
function setAccountStatus(
    PDO $db,
    int $userId,
    string $status
): void {
    $allowedStatuses = [
        'active',
        'inactive',
        'locked'
    ];

    if (!in_array($status, $allowedStatuses, true)) {
        failTest(
            'Attempted to use unsupported account status: '
            . $status
        );
    }

    $statement = $db->prepare(
        '
        UPDATE users
        SET
            status = :status,
            failed_logins = 0,
            locked_until = NULL,
            updated_at = NOW()
        WHERE id = :id
        '
    );

    $statement->execute([
        'status' => $status,
        'id' => $userId
    ]);
}


/**
 * Restore test account to a known clean state.
 */
function restoreTestAccount(
    PDO $db,
    int $userId
): void {
    setAccountStatus(
        $db,
        $userId,
        'active'
    );
}


/**
 * Ensure authentication result is unsuccessful.
 *
 * @param array<string,mixed> $result
 */
function assertAuthenticationRejected(
    array $result,
    string $context
): void {
    if (($result['success'] ?? null) !== false) {
        failTest(
            $context
            . ' was unexpectedly authenticated.'
        );
    }
}


/**
 * Ensure the authentication message matches exactly.
 *
 * @param array<string,mixed> $result
 */
function assertAuthenticationMessage(
    array $result,
    string $expected,
    string $context
): void {
    $actual = $result['message'] ?? null;

    if ($actual !== $expected) {
        failTest(
            $context
            . ' returned unexpected authentication message.'
            . PHP_EOL
            . 'Expected: '
            . $expected
            . PHP_EOL
            . 'Received: '
            . ($actual ?? '[no message]')
        );
    }
}


/*
|--------------------------------------------------------------------------
| Initialise
|--------------------------------------------------------------------------
*/

$db = Database::connection();

$account = getTestAccount($db);

$userId = (int) $account['id'];

echo PHP_EOL;
echo 'Authentication status test account:' . PHP_EOL;
echo '  Username: ' . $account['username'] . PHP_EOL;
echo '  User ID: ' . $userId . PHP_EOL;
echo PHP_EOL;


/*
|--------------------------------------------------------------------------
| Authentication Service
|--------------------------------------------------------------------------
*/

$repository = new AuthRepository($db);

$validator = new AuthValidator();

$auth = new AuthService(
    $repository,
    $validator
);


/*
|--------------------------------------------------------------------------
| Clean Starting State
|--------------------------------------------------------------------------
*/

restoreTestAccount(
    $db,
    $userId
);

$account = getTestAccount($db);

if ($account['status'] !== 'active') {
    failTest(
        'Unable to establish active starting state.'
    );
}

passTest(
    'Test account starts in active status.'
);


/*
|--------------------------------------------------------------------------
| ACTIVE ACCOUNT
|--------------------------------------------------------------------------
|
| Active accounts should reach normal authentication processing.
|
*/

$result = $auth->authenticate([
    'username' => TEST_USERNAME,
    'password' => INVALID_PASSWORD
]);

assertAuthenticationRejected(
    $result,
    'Active account with invalid credentials'
);

assertAuthenticationMessage(
    $result,
    'Invalid username or password.',
    'Active account'
);

passTest(
    'Active account processes authentication normally.'
);


/*
|--------------------------------------------------------------------------
| INACTIVE ACCOUNT
|--------------------------------------------------------------------------
*/

setAccountStatus(
    $db,
    $userId,
    'inactive'
);

$account = getTestAccount($db);

if ($account['status'] !== 'inactive') {
    failTest(
        'Unable to place test account into inactive status.'
    );
}

passTest(
    'Test account successfully changed to inactive status.'
);


/*
|--------------------------------------------------------------------------
| Inactive Authentication
|--------------------------------------------------------------------------
*/

$result = $auth->authenticate([
    'username' => TEST_USERNAME,
    'password' => INVALID_PASSWORD
]);

assertAuthenticationRejected(
    $result,
    'Inactive account'
);

assertAuthenticationMessage(
    $result,
    'Account is inactive.',
    'Inactive account'
);

passTest(
    'Inactive account authentication is rejected.'
);


/*
|--------------------------------------------------------------------------
| Inactive Account Must Not Accumulate Failed Attempts
|--------------------------------------------------------------------------
*/

$account = getTestAccount($db);

if ((int) $account['failed_logins'] !== 0) {
    failTest(
        'Inactive account incremented failed-login counter.'
    );
}

passTest(
    'Inactive account does not increment failed-login counter.'
);


/*
|--------------------------------------------------------------------------
| Inactive Account Must Not Enter Lockout
|--------------------------------------------------------------------------
*/

if ($account['locked_until'] !== null) {
    failTest(
        'Inactive account unexpectedly received a temporary lockout.'
    );
}

passTest(
    'Inactive account does not enter temporary lockout state.'
);


/*
|--------------------------------------------------------------------------
| LOCKED ACCOUNT
|--------------------------------------------------------------------------
*/

setAccountStatus(
    $db,
    $userId,
    'locked'
);

$account = getTestAccount($db);

if ($account['status'] !== 'locked') {
    failTest(
        'Unable to place test account into locked status.'
    );
}

passTest(
    'Test account successfully changed to locked status.'
);


/*
|--------------------------------------------------------------------------
| Locked Authentication
|--------------------------------------------------------------------------
*/

$result = $auth->authenticate([
    'username' => TEST_USERNAME,
    'password' => INVALID_PASSWORD
]);

assertAuthenticationRejected(
    $result,
    'Locked account'
);

assertAuthenticationMessage(
    $result,
    'Account is locked.',
    'Locked account'
);

passTest(
    'Locked account authentication is rejected.'
);


/*
|--------------------------------------------------------------------------
| Locked Account Must Not Accumulate Failed Attempts
|--------------------------------------------------------------------------
*/

$account = getTestAccount($db);

if ((int) $account['failed_logins'] !== 0) {
    failTest(
        'Locked account incremented failed-login counter.'
    );
}

passTest(
    'Locked account does not increment failed-login counter.'
);


/*
|--------------------------------------------------------------------------
| Locked Account Must Not Receive Temporary Lockout Timestamp
|--------------------------------------------------------------------------
|
| The account state itself is "locked".
| The temporary lockout mechanism tested separately by
| AuthenticationLockoutTest.php should not be mixed into this test.
|
*/

if ($account['locked_until'] !== null) {
    failTest(
        'Locked account unexpectedly received a temporary lockout timestamp.'
    );
}

passTest(
    'Locked account does not require a temporary lockout timestamp.'
);


/*
|--------------------------------------------------------------------------
| RESTORE ACCOUNT
|--------------------------------------------------------------------------
*/

restoreTestAccount(
    $db,
    $userId
);

$account = getTestAccount($db);

if ($account['status'] !== 'active') {
    failTest(
        'Test account could not be restored to active status.'
    );
}

if ((int) $account['failed_logins'] !== 0) {
    failTest(
        'Test account failed-login counter was not restored to zero.'
    );
}

if ($account['locked_until'] !== null) {
    failTest(
        'Test account lockout timestamp was not cleared.'
    );
}

passTest(
    'Test account restored to active status.'
);

passTest(
    'Test account failed-login counter restored to zero.'
);

passTest(
    'Test account temporary lockout state cleared.'
);


/*
|--------------------------------------------------------------------------
| Test Complete
|--------------------------------------------------------------------------
*/

if (ob_get_level() > 0) {
    ob_end_flush();
}

echo PHP_EOL;
echo 'Authentication status tests passed successfully.'
    . PHP_EOL;