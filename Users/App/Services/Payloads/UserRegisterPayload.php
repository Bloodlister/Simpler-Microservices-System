<?php declare(strict_types=1);

namespace App\Services\Payloads;

class UserRegisterPayload extends Payload
{
    protected $fields = [
        'username',
        'password'
    ];

    /** @var string $username */
    public $username;

    /** @var string $password */
    public $password;
}
