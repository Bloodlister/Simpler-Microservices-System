<?php declare(strict_types=1);

namespace App\Services;

use App\Connection;
use App\Models\User;
use App\Services\Payloads\UserRegisterPayload;

class UserRegisterService extends Service
{
    public const CHANNEL = 'onUserRegisterRequest';

    public const PAYLOAD_CLASS = UserRegisterPayload::class;

    /**
     * @param UserRegisterPayload $payload
     * @return mixed
     */
    public function run(UserRegisterPayload $payload): void
    {
        try {
            $this->registerUser($payload->username, $payload->password);
            $user = $this->getUser($payload->username);
        } catch (\Exception $exception) {

        }
    }

    private function registerUser(string $username, string $password): void
    {
        $db = Connection::connect();
        $stmt = $db->prepare('INSERT INTO `users`(`username`, `password`, `created_on`) VALUES (:username, :password, NOW())');
        $stmt->execute([
            'username' => $username,
            'password' => password_hash($password, PASSWORD_BCRYPT)
        ]);
    }

    private function getUser(string $username): User
    {
        $db = Connection::connect();
        $stmt = $db->prepare('SELECT * FROM `users` WHERE username = :username');
        $stmt->execute([
            'username' => $username
        ]);

        return new User($stmt->fetch(\PDO::FETCH_ASSOC));
    }

}