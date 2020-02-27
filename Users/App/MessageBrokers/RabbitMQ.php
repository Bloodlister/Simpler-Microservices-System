<?php declare(strict_types=1);

namespace App\MessageBrokers;

use App\App;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQ implements BrokerInterface
{
    public function publish(string $queue, array $data = [])
    {
        $connection = App::getConnection();
        $connection->channel()
            ->basic_publish(new AMQPMessage(json_encode($data)), '', $queue);
    }
}
