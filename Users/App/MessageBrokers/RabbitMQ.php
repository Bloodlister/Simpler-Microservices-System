<?php declare(strict_types=1);

namespace App\MessageBrokers;

use App\App;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQ implements BrokerInterface
{
    public const QUEUE_WEBSOCKET = 'websocket';

    public function publish(string $queue, array $data = [])
    {
        $connection = App::getConnection();
        $connection->channel()
            ->basic_publish(new AMQPMessage(json_encode($data)), '', $queue);
    }

    public function websocketMessage(string $issuer, \App\WebSocket\ToastrNotification $param)
    {
        $connection = App::getConnection();
        $connection->channel()
            ->basic_publish(new AMQPMessage(json_encode([
                'receiver' => $issuer,
                'data' => $param->unpack()
            ])), '', static::QUEUE_WEBSOCKET);
    }
}
