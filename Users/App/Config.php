<?php declare(strict_types=1);

namespace App;

class Config
{
    /** @var array $config */
    private static $config;

    public static function init(): void
    {
        static::$config = require(__DIR__ . '/../config.php');
    }

    /**
     * @param string $param
     * @return mixed
     */
    public static function get(string $param)
    {
        return static::$config[$param];
    }
}
