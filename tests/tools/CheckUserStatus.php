<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../bootstrap/bootstrap.php';

use Core\Database\Database;

$db = Database::connection();

$statement = $db->prepare("
    SELECT
        id,
        username,
        email,
        status,
        failed_logins,
        locked_until
    FROM users
    WHERE id = :id
    LIMIT 1
");

$statement->execute([
    'id' => 1
]);

$user = $statement->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    throw new RuntimeException(
        'User ID 1 was not found.'
    );
}

print_r($user);

echo PHP_EOL;
echo 'Current status: ' . $user['status'] . PHP_EOL;