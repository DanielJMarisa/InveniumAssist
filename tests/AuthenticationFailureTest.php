<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Authentication Failure Tests
|--------------------------------------------------------------------------
|
| These tests exercise failed authentication against the local database.
|
| Development-only test account:
| - Username: daniel@inveniumtech.com
|
| The test restores failed_logins and locked_until when complete.
|
*/

require_once __DIR__ . '/../vendor/autoload.php';

ob_start();

use Core\Config\Config;
use Core\Database\Database;
use Core\Session\Session;
use Modules\Auth\AuthRepository;
use Modules\Auth\AuthService;
use Modules\Auth\AuthValidator;


/*
|--------------------------------------------------------------------------
| Bootstrap
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../bootstrap/constants.php';

Config::load(CONFIG_PATH);

date_default_timezone_set('Africa/Johannesburg');

$db = Database::connection();


/*
|--------------------------------------------------------------------------
| Test Account
|--------------------------------------------------------------------------
*/

$testUsername = 'daniel@inveniumtech.com';

$invalidPassword = 'definitely-invalid-password-for-test';


/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

/**
 * Retrieve the test user's authentication state.
 *
 * @return array<string,mixed>
 */
function getTestUser(
    PDO $db,
    string $username
): array {
    $statement = $db->prepare(
        "
        SELECT
            id,
            username,
            email,
            status,
            failed_logins,
            locked_until,
            last_login
        FROM users
        WHERE username = :username
        LIMIT 1
        "
    );

    $statement->execute([
        'username' => $username
    ]);

    $user = $statement->fetch(PDO::FETCH_ASSOC);

    if ($user === false) {
        throw new RuntimeException(
            "Test user '{$username}' was not found."
        );
    }

    return $user;
}


/**
 * Reset authentication failure state.
 */
function resetTestUser(
    PDO $db,
    int $userId
): void {
    $statement = $db->prepare(
        "
        UPDATE users
        SET
            failed_logins = 0,
            locked_until = NULL,
            updated_at = NOW()
        WHERE id = :id
        "
    );

    $statement->execute([
        'id' => $userId
    ]);
}


/**
 * Assert condition.
 */
function assertTrue(
    bool $condition,
    string $message
): void {
    if (!$condition) {
        throw new RuntimeException($message);
    }

    echo "PASS: {$message}" . PHP_EOL;
}


/*
|--------------------------------------------------------------------------
| Locate Test User
|--------------------------------------------------------------------------
*/

$user = getTestUser(
    $db,
    $testUsername
);

$userId = (int) $user['id'];

echo PHP_EOL;
echo "Authentication failure test account:" . PHP_EOL;
echo "  Username: {$user['username']}" . PHP_EOL;
echo "  User ID: {$userId}" . PHP_EOL;
echo PHP_EOL;


/*
|--------------------------------------------------------------------------
| Preserve Original State
|--------------------------------------------------------------------------
*/

$originalFailedLogins = (int) $user['failed_logins'];
$originalLockedUntil = $user['locked_until'];


/*
|--------------------------------------------------------------------------
| Always Restore Test Account
|--------------------------------------------------------------------------
*/

register_shutdown_function(
    function () use (
        $db,
        $userId
    ): void {
        resetTestUser(
            $db,
            $userId
        );

        Session::flush();
    }
);


/*
|--------------------------------------------------------------------------
| Establish Clean Test State
|--------------------------------------------------------------------------
*/

resetTestUser(
    $db,
    $userId
);

Session::flush();


/*
|--------------------------------------------------------------------------
| Create Authentication Service
|--------------------------------------------------------------------------
*/

$repository = new AuthRepository(
    $db
);

$validator = new AuthValidator();

$service = new AuthService(
    $repository,
    $validator
);


/*
|--------------------------------------------------------------------------
| Test 1 — Invalid Password Is Rejected
|--------------------------------------------------------------------------
*/

$result = $service->authenticate([
    'username' => $testUsername,
    'password' => $invalidPassword
]);

assertTrue(
    $result['success'] === false,
    'Invalid password is rejected.'
);


/*
|--------------------------------------------------------------------------
| Test 2 — Generic Authentication Error
|--------------------------------------------------------------------------
*/

assertTrue(
    ($result['message'] ?? null)
        === 'Invalid username or password.',
    'Invalid password returns the generic authentication message.'
);


/*
|--------------------------------------------------------------------------
| Test 3 — No Authentication Session Created
|--------------------------------------------------------------------------
*/

assertTrue(
    !Session::has('auth.user_id'),
    'Failed authentication does not create an authenticated session.'
);


/*
|--------------------------------------------------------------------------
| Test 4 — Failed Login Counter Increments
|--------------------------------------------------------------------------
*/

$user = getTestUser(
    $db,
    $testUsername
);

assertTrue(
    (int) $user['failed_logins'] === 1,
    'Failed login counter increments to 1.'
);


/*
|--------------------------------------------------------------------------
| Test 5 — Account Is Not Locked After One Failure
|--------------------------------------------------------------------------
*/

assertTrue(
    $user['locked_until'] === null,
    'Account is not locked after a single failed login.'
);



/*
|--------------------------------------------------------------------------
| Test 7 — Unknown Username Uses Generic Response
|--------------------------------------------------------------------------
*/

Session::flush();

$unknownResult = $service->authenticate([
    'username' => 'nonexistent-user@invalid.local',
    'password' => $invalidPassword
]);

assertTrue(
    $unknownResult['success'] === false,
    'Unknown username is rejected.'
);

assertTrue(
    ($unknownResult['message'] ?? null)
        === 'Invalid username or password.',
    'Unknown username returns the same generic authentication message.'
);


/*
|--------------------------------------------------------------------------
| Test 8 — No User Enumeration
|--------------------------------------------------------------------------
*/

assertTrue(
    ($result['message'] ?? null)
        === ($unknownResult['message'] ?? null),
    'Known-user and unknown-user failures return identical authentication messages.'
);


/*
|--------------------------------------------------------------------------
| Final State
|--------------------------------------------------------------------------
*/

resetTestUser(
    $db,
    $userId
);

Session::flush();

$finalUser = getTestUser(
    $db,
    $testUsername
);

assertTrue(
    (int) $finalUser['failed_logins'] === 0,
    'Test account failed-login counter is restored to zero.'
);

assertTrue(
    $finalUser['locked_until'] === null,
    'Test account lock state is cleared after the test.'
);


echo PHP_EOL;
echo "Authentication failure tests passed successfully."
    . PHP_EOL;

ob_end_flush();