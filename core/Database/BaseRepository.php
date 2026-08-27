<?php

declare(strict_types=1);

namespace Core\Database;

use PDO;

abstract class BaseRepository
{
    /**
     * Database connection.
     */
    protected PDO $db;

    /**
     * Repository constructor.
     */
    public function __construct()
    {
        $this->db = Database::connection();
    }
}