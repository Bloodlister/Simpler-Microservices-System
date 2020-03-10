<?php declare(strict_types=1);

namespace App\Services;

use App\Database\DatabaseInterface;
use App\DI;
use App\MessageBrokers\BrokerInterface;
use App\Services\Payloads\Payload;
use App\WebSocket\ToastrNotification;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class Service
 * @package App\Services
 * @method run(Payload $payload): void
 */
abstract class Service
{
    public const CHANNEL = '';

    public const PAYLOAD_CLASS = '';

    protected BrokerInterface $broker;

    protected DatabaseInterface $database;

    public function __construct(BrokerInterface $broker)
    {
        $this->broker = $broker;
    }

    /**
     * @return \Closure
     */
    final public function handler(): \Closure
    {
        return function (AMQPMessage $msg) {
            $payloadClass = static::PAYLOAD_CLASS;
            $payload = new $payloadClass(json_decode($msg->body, true));
            try {
                DI::executeService($this, $payload);
            } catch (\Exception $exception) {
                trigger_error($exception->getMessage(), E_USER_ERROR);
                $this->broker->websocketMessage($payload->issuer, new ToastrNotification(ToastrNotification::STATUS_ERROR, 'Something went wrong', $exception->getMessage()));
            }
        };
    }

    abstract function getWritingQueues(): array;
}
