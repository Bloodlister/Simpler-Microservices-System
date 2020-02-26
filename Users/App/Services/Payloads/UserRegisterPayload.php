<?php declare(strict_types=1);

namespace App\Services\Payloads;

class UserRegisterPayload extends Payload
{
    protected $fields = [
        'senderId',
        'username',
        'password'
    ];

    /** @var string $sender_id */
    public $senderId;

    /** @var string $username */
    public $username;

    /** @var string $password */
    public $password;
}
