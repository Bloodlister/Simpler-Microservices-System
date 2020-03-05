<?php declare(strict_types=1);

namespace App;

use App\MessageBrokers\RabbitMQ;
use App\Services\Service;
use App\Services\UserRegisterService;
use PhpAmqpLib\Channel\AMQPChannel;

class ChannelBroker
{
    /**
     * @var Service[] $queues
     */
    private static array $queues = [
        UserRegisterService::CHANNEL => UserRegisterService::class
    ];

    public static function setup(AMQPChannel $channel): void
    {
        foreach (static::$queues as $queue => $handler) {
            trigger_error('Registered ' . $handler . ' To ' . $queue);

            /** @var Service $handlerInstance */
            $handlerInstance = new $handler();
            $channel->queue_declare($queue, false, false, false, false);
            $channel->basic_consume($queue, '', false, true, false, false, $handlerInstance->handler());

            $writingQueues = $handlerInstance->getWritingQueues();
            foreach ($writingQueues as $writingQueue) {
                $channel->queue_declare($writingQueue, false, false, false, false);
            }
        }
    }

    public static function connectToWebsocket(AMQPChannel $channel): void
    {
        $channel->queue_declare(RabbitMQ::QUEUE_WEBSOCKET, false, false, false, false);
        trigger_error('Writing to websocket channel');
    }
}
