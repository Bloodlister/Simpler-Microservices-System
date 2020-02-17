<?php declare(strict_types=1);

namespace App\MessageBrokers;

class RabbitMQ implements BrokerInterface
{
    public function send(string $channel, array $data = [])
    {

    }
}
