<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Invenium Assist Authentication Lockout Tests
|--------------------------------------------------------------------------
|
| These tests verify the failed-login lockout mechanism.
|
| IMPORTANT:
| - Local development only.
| - Uses the existing Daniel test account.
| - The account is restored to a clean state after the test.
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

const MAX_FAILED_ATTEMPTS = 5;


/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

function failTest(string $message): never
{
    throw new RuntimeException(
        PHP_EOL . 'FAIL: ' . $message . PHP_EOL
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
            failed_logins,
            locked_until,
            status
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
 * Reset the test account to a clean authentication state.
 */
function resetTestAccount(PDO $db, int $userId): void
{
    $statement = $db->prepare(
        '
        UPDATE users
        SET
            failed_logins = 0,
            locked_until = NULL,
            updated_at = NOW()
        WHERE id = :id
        '
    );

    $statement->execute([
        'id' => $userId
    ]);
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
echo 'Authentication lockout test account:' . PHP_EOL;
echo '  Username: ' . $account['username'] . PHP_EOL;
echo '  User ID: ' . $userId . PHP_EOL;
echo PHP_EOL;


/*
|--------------------------------------------------------------------------
| Clean Starting State
|--------------------------------------------------------------------------
*/

resetTestAccount(
    $db,
    $userId
);

passTest(
    'Test account starts with a clean lockout state.'
);


/*
|--------------------------------------------------------------------------
| Create Authentication Service
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
| Failed Attempts 1-4
|--------------------------------------------------------------------------
*/

for (
    $attempt = 1;
    $attempt < MAX_FAILED_ATTEMPTS;
    $attempt++
) {

    $result = $auth->authenticate([
        'username' => TEST_USERNAME,
        'password' => INVALID_PASSWORD
    ]);

    if (($result['success'] ?? null) !== false) {

        failTest(
            "Authentication unexpectedly succeeded on failed attempt {$attempt}."
        );
    }

    if (
        ($result['message'] ?? null)
        !==
        'Invalid username or password.'
    ) {

        failTest(
            "Attempt {$attempt} returned an unexpected authentication message."
        );
    }

    $account = getTestAccount($db);

    $failedLogins = (int) $account['failed_logins'];

    if ($failedLogins !== $attempt) {

        failTest(
            "Expected failed_logins={$attempt}, received {$failedLogins}."
        );
    }

    if ($account['locked_until'] !== null) {

        failTest(
            "Account became locked prematurely on attempt {$attempt}."
        );
    }

    passTest(
        "Failed attempt {$attempt} increments counter without locking account."
    );
}


/*
|--------------------------------------------------------------------------
| Fifth Failed Attempt
|--------------------------------------------------------------------------
*/

$result = $auth->authenticate([
    'username' => TEST_USERNAME,
    'password' => INVALID_PASSWORD
]);

if (($result['success'] ?? null) !== false) {

    failTest(
        'Authentication unexpectedly succeeded on the fifth failed attempt.'
    );
}

if (
    ($result['message'] ?? null)
    !==
    'Invalid username or password.'
) {

    failTest(
        'Fifth failed attempt returned an unexpected authentication message.'
    );
}

$account = getTestAccount($db);

$failedLogins = (int) $account['failed_logins'];

if ($failedLogins !== MAX_FAILED_ATTEMPTS) {

    failTest(
        'Expected failed_logins='
        . MAX_FAILED_ATTEMPTS
        . ', received '
        . $failedLogins
        . '.'
    );
}

passTest(
    'Fifth failed attempt reaches the maximum failed-login threshold.'
);


/*
|--------------------------------------------------------------------------
| Verify Account Is Locked
|--------------------------------------------------------------------------
*/

if ($account['locked_until'] === null) {

    failTest(
        'Account was not locked after the maximum failed-login threshold.'
    );
}

$lockedUntil = strtotime(
    (string) $account['locked_until']
);

if ($lockedUntil === false) {

    failTest(
        'Account locked_until value could not be parsed.'
    );
}

if ($lockedUntil <= time()) {

    failTest(
        'Account locked_until timestamp is not in the future.'
    );
}

passTest(
    'Account is locked after five consecutive failed attempts.'
);

passTest(
    'Account locked_until timestamp is in the future.'
);


/*
|--------------------------------------------------------------------------
| Verify Repository Lock Detection
|--------------------------------------------------------------------------
*/

if (!$repository->isLocked($userId)) {

    failTest(
        'AuthRepository::isLocked() did not recognise the locked account.'
    );
}

passTest(
    'AuthRepository correctly identifies the account as locked.'
);


/*
|--------------------------------------------------------------------------
| Attempt Authentication While Locked
|--------------------------------------------------------------------------
|
| We intentionally use an invalid password here.
|
| The important assertion is that the account remains rejected because
| the lock check happens before password verification.
|
*/

$result = $auth->authenticate([
    'username' => TEST_USERNAME,
    'password' => INVALID_PASSWORD
]);

if (($result['success'] ?? null) !== false) {

    failTest(
        'Locked account was unexpectedly authenticated.'
    );
}

if (
    ($result['message'] ?? null)
    !==
    'Account temporarily locked.'
) {

    failTest(
        'Locked account did not return the expected lockout message.'
        . PHP_EOL
        . 'Received: '
        . ($result['message'] ?? '[no message]')
    );
}

passTest(
    'Authentication is rejected while the account is locked.'
);

passTest(
    'Locked authentication returns the expected lockout response.'
);


/*
|--------------------------------------------------------------------------
| Verify Failed Counter Does Not Increase While Locked
|--------------------------------------------------------------------------
*/

$accountAfterLockAttempt = getTestAccount($db);

if (
    (int) $accountAfterLockAttempt['failed_logins']
    !==
    MAX_FAILED_ATTEMPTS
) {

    failTest(
        'Failed-login counter changed while account was locked.'
    );
}

passTest(
    'Failed-login counter remains unchanged while account is locked.'
);


/*
|--------------------------------------------------------------------------
| Cleanup
|--------------------------------------------------------------------------
*/

resetTestAccount(
    $db,
    $userId
);

$account = getTestAccount($db);

if ((int) $account['failed_logins'] !== 0) {

    failTest(
        'Test cleanup failed to reset failed-login counter.'
    );
}

if ($account['locked_until'] !== null) {

    failTest(
        'Test cleanup failed to clear locked_until.'
    );
}

passTest(
    'Test account failed-login counter restored to zero.'
);

passTest(
    'Test account lock state cleared after test.'
);


echo PHP_EOL;
echo 'Authentication lockout tests passed successfully.'
    . PHP_EOL;