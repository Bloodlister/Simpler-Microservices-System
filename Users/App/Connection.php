<?php declare(strict_types=1);

namespace App;

use PDO;

class Connection
{
    /**
     * @var PDO $pdo
     */
    private static $pdo;

    public static function connect(): PDO
    {
        if (!static::$pdo) {
            $databaseConfig = Config::get('database');

            static::$pdo = new PDO(sprintf('pgsql:host=%1$s;dbname=%2$s;user=%3$s;password=%4$s',
                $databaseConfig['host'],
                $databaseConfig['database'],
                $databaseConfig['username'],
                $databaseConfig['password']
            ));
        }

        return static::$pdo;
    }
}