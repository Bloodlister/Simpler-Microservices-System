<?php declare(strict_types=1);

namespace App\Services;

use App\MessageBrokers\BrokerInterface;
use App\MessageBrokers\RabbitMQ;
use App\Services\Payloads\Payload;
use App\WebSocket;
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
            try {
                $this->run($payload);
            } catch (\Exception $exception) {
                WebSocket::send($payload->issuer, new WebSocket\ToastrNotification(WebSocket\ToastrNotification::STATUS_ERROR, 'Something went wrong', $exception->getMessage()));
            }
        };
    }

    abstract function getWritingQueues(): array;

    abstract function run(Payload $payload): void;
}
