<?php declare(strict_types=1);

namespace App\MessageBrokers;

interface BrokerInterface
{
    public function publish(string $channel, array $data = []);

    public function websocketMessage(string $issuer, \App\WebSocket\ToastrNotification $param);
}
