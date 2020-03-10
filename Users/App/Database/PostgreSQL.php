<?php declare(strict_types=1);

namespace App\Database;

use PDO;

class PostgreSQL implements DatabaseInterface
{
    /** @var PDO $connection */
    private static PDO $connection;

    private function __construct() {}

    public static function createConnection(string $host, string $database, string $username, string $password): void
    {
        static::$connection = new PDO(sprintf('pgsql:host=%1$s;dbname=%2$s;user=%3$s;password=%4$s',
            $host,
            $database,
            $username,
            $password
        ));
    }

    public static function getConnection(): \PDO
    {
        return static::$connection;
    }
}
