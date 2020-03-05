<?php declare(strict_types=1);

namespace App;

use App\WebSocket\Message;
use WebSocket\Client;

class WebSocket
{
    /** @var Client $socket */
    private static $socket;

    public static function send(string $receiver, Message $message)
    {
        static::getClient()->send(json_encode(['receiver' => $receiver, 'data' => $message->unpack()]));
    }

    protected static function getClient(): Client
    {
        if (!static::$socket) {
            static::$socket = new Client('ws://websocket/?users_back');
        }

        return static::$socket;
    }
}
