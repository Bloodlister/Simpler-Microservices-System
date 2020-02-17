<?php declare(strict_types=1);

namespace App;

use App\Services\Service;
use App\Services\UserRegisterService;
use PhpAmqpLib\Channel\AMQPChannel;

class ChannelBroker
{
    /**
     * @var Service[] $queues
     */
    private static $queues = [
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
        }
    }
}
