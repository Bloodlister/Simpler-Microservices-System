<?php declare(strict_types=1);

namespace App\WebSocket;

abstract class Message
{
    public const TYPE = '';

    abstract public function unpack(): array;
}
