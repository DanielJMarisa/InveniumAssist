<?php

declare(strict_types=1);

namespace Core\Database;

use Core\Config\Config;
use PDO;
use PDOException;
use Core\Exceptions\DatabaseException;

final class Database
{
    /**
     * Singleton PDO instance.
     */
    private static ?PDO $connection = null;

    /**
     * Prevent instantiation.
     */
    private function __construct()
    {
    }

    /**
     * Get the PDO connection.
     *
     * @throws RuntimeException
     */
    public static function connection(): PDO
    {
        if (self::$connection instanceof PDO) {

            return self::$connection;

        }

        $driver = Config::get('database.driver');

        $host = Config::get('database.host');

        $port = Config::get('database.port');

        $database = Config::get('database.database');

        $charset = Config::get('database.charset', 'utf8mb4');

        $username = Config::get('database.username');

        $password = Config::get('database.password');

        $options = Config::get('database.options', []);

        $dsn = sprintf(

            '%s:host=%s;port=%d;dbname=%s;charset=%s',

            $driver,

            $host,

            $port,

            $database,

            $charset

        );

        try {

            self::$connection = new PDO(

                $dsn,

                $username,

                $password,

                $options

            );

        }

        catch (PDOException $exception) {

            throw new DatabaseException(

                'Database connection failed.',

                previous: $exception

            );

        }

        return self::$connection;
    }

    /**
     * Begin transaction.
     */
    public static function beginTransaction(): bool
    {
        return self::connection()->beginTransaction();
    }

    /**
     * Commit transaction.
     */
    public static function commit(): bool
    {
        return self::connection()->commit();
    }

    /**
     * Rollback transaction.
     */
    public static function rollback(): bool
    {
        if (self::connection()->inTransaction()) {

            return self::connection()->rollBack();

        }

        return false;
    }

    /**
     * Determine if a transaction is active.
     */
    public static function inTransaction(): bool
    {
        return self::connection()->inTransaction();
    }

    /**
     * Close the connection.
     * Primarily used during testing.
     */
    public static function disconnect(): void
    {
        self::$connection = null;
    }
}