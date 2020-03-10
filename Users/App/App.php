<?php declare(strict_types=1);

namespace App;

use App\Database\PostgreSQL;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Throwable;

class App
{
    /**
     * @var AMQPStreamConnection
     */
    private static AMQPStreamConnection $connection;

    public static function run(): void
    {
        Config::init();
        PostgreSQL::createConnection(...array_values(Config::get('database')));
        $rabbitMQConnection = static::connect();

        trigger_error('Connected to RabbitMQ');

        $channel = $rabbitMQConnection->channel();

        ChannelBroker::setup($channel);

        while ($channel->is_consuming()) {
            $channel->wait();
        }
    }

    private static function connect(): AMQPStreamConnection
    {
        $rabbitMQConfig = Config::get('rabbit_mq');

        $connected = false;
        while (!$connected) {
            try {
                static::$connection = new AMQPStreamConnection(
                    $rabbitMQConfig['host'],
                    $rabbitMQConfig['port'],
                    $rabbitMQConfig['username'],
                    $rabbitMQConfig['password']
                );

                return static::$connection;
            } catch (Throwable $exception) {
                trigger_error("Couldn't connect to RabbitMQ: " . $rabbitMQConfig['host'] . ':' . $rabbitMQConfig['port'] . ' Exception: ' . $exception->getMessage());
                sleep(3);
            }
        }
    }

    public static function getConnection(): AMQPStreamConnection
    {
        return static::$connection;
    }
}
