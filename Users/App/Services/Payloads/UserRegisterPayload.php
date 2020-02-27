<?php declare(strict_types=1);

namespace App\Services\Payloads;

class UserRegisterPayload extends Payload
{
    protected $fields = [
        'issuer',
        'username',
        'password'
    ];

    /** @var string $issuer */
    public string $issuer;

    /** @var string $username */
    public string $username;

    /** @var string $password */
    public string $password;
}
