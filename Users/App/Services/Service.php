<?php declare(strict_types=1);

namespace App\Services;

use App\Services\Payloads\Payload;
use PhpAmqpLib\Message\AMQPMessage;

abstract class Service
{
    public const CHANNEL = '';

    public const PAYLOAD_CLASS = '';

    /** @var BrokerInterface $broker */
    protected $broker;

    public function setBroker(BrokerInterface $broker): void
    {
        $this->broker = $broker;
    }

    /**
     * @param array $payload
     * @return mixed
     */
    final public function handler(): \Closure
    {
        return function (AMQPMessage $msg) {
            $payloadClass = static::PAYLOAD_CLASS;
            $payload = new $payloadClass(json_decode($msg->body, true));

            $this->run($payload);
        };
    }
}
