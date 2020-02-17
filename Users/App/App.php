<?php declare(strict_types=1);

namespace App;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Throwable;

class App
{
    public static function run(): void
    {
        Config::init();

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
                return new AMQPStreamConnection(
                    $rabbitMQConfig['host'],
                    $rabbitMQConfig['port'],
                    $rabbitMQConfig['username'],
                    $rabbitMQConfig['password']
                );
            } catch (Throwable $exception) {
                trigger_error("Couldn't connect to RabbitMQ: " . $rabbitMQConfig['host'] . ':' . $rabbitMQConfig['port'] . ' Exception: ' . $exception->getMessage());
                sleep(3);
            }
        }
    }
}
