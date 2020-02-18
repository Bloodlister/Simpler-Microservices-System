<?php declare(strict_types=1);

namespace App\Services;

use App\MessageBrokers\BrokerInterface;
use App\MessageBrokers\RabbitMQ;
use PhpAmqpLib\Message\AMQPMessage;

abstract class Service
{
    public const CHANNEL = '';

    public const PAYLOAD_CLASS = '';

    protected BrokerInterface $broker;

    public function setBroker(BrokerInterface $broker): void
    {
        $this->broker = $broker;
    }

    /**
     * @return mixed
     */
    final public function handler(): \Closure
    {
        return function (AMQPMessage $msg) {
            $payloadClass = static::PAYLOAD_CLASS;
            $payload = new $payloadClass(json_decode($msg->body, true));
            $this->setBroker(new RabbitMQ());

            $this->run($payload);
        };
    }

    abstract function getWritingQueues(): array;
}
