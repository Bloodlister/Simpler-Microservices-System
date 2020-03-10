<?php declare(strict_types=1);

namespace App\Database;

interface DatabaseInterface {

    public static function createConnection(string $host, string $database, string $username, string $password): void;

    public static function getConnection(): \PDO;
}
