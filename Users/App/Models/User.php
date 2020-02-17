<?php declare(strict_types=1);

namespace App\Models;

class User
{
    /** @var int $id */
    public $id;

    /** @var string $username */
    public $username;

    /** @var string $password */
    public $password;

    /** @var string $createDate */
    public $createDate;

    /**
     * User constructor.
     * @param array $databaseRecord
     */
    public function __construct(array $databaseRecord)
    {
        $this->id = $databaseRecord['id'];
        $this->username = $databaseRecord['username'];
        $this->password = $databaseRecord['password'];
        $this->createDate = $databaseRecord['created_on'];
    }
}
