<?php declare(strict_types=1);

namespace App\MessageBrokers;

interface BrokerInterface
{
    public function send(string $channel, array $data = []);
}
