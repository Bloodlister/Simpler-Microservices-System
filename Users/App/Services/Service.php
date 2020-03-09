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
    /**
     * @var DatabaseInterface
     */
    protected $database;

    public function __construct(BrokerInterface $broker, DatabaseInterface $database)
    {
        $this->broker = $broker;
        $this->database = $database;
    }

    /**
     * @return mixed
     */
    final public function handler(): \Closure
    {
        return function (AMQPMessage $msg) {
            $payloadClass = static::PAYLOAD_CLASS;
            $payload = new $payloadClass(json_decode($msg->body, true));
            try {
                $this->run($payload);
            } catch (\Exception $exception) {
                $this->broker->websocketMessage($payload->issuer, new WebSocket\ToastrNotification(WebSocket\ToastrNotification::STATUS_ERROR, 'Something went wrong', $exception->getMessage()));
            }
        };
    }

    abstract function getWritingQueues(): array;

    abstract function run(Payload $payload): void;
}
