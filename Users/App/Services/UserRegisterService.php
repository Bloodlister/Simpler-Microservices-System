<?php declare(strict_types=1);

namespace App\Services;

use App\Connection;
use App\Exception\DuplicateUserException;
use App\Exception\QueryException;
use App\Models\User;
use App\Services\Payloads\Payload;
use App\Services\Payloads\UserRegisterPayload;

class UserRegisterService extends Service
{
    public const CHANNEL = 'userRegisterRequest';

    public const PAYLOAD_CLASS = UserRegisterPayload::class;

    public const QUEUE_USER_REGISTER_RESULTS = 'userRegisterResult';

    public function getWritingQueues(): array
    {
        return [
            static::QUEUE_USER_REGISTER_RESULTS
        ];
    }

    /**
     * @param UserRegisterPayload $payload
     * @return mixed
     * @throws DuplicateUserException
     * @throws QueryException
     */
    public function run(Payload $payload): void
    {
        $user = $this->getUser($payload->username);
        if ($user) {
            throw new DuplicateUserException(sprintf('Username `%1$s` is already taken.', $payload->username));
        }

        $password = md5($payload->password);
        $result = $this->registerUser($payload->username, $password);
        if (!$result) {
            throw new QueryException('Could not add user to database');
        }

        $this->broker->publish(static::QUEUE_USER_REGISTER_RESULTS, [
            'id' => Connection::connect()->lastInsertId(),
            'username' => $payload->username,
            'password' => $password
        ]);
    }

    private function registerUser(string $username, string $password): bool
    {
        $db = Connection::connect();
        $stmt = $db->prepare('INSERT INTO "public"."users"(username, password, created_on) VALUES (:username, :password, NOW())');

        return $stmt->execute([
            'username' => $username,
            'password' => $password
        ]);
    }

    private function getUser(string $username): ?User
    {
        $db = Connection::connect();
        $stmt = $db->prepare('SELECT * FROM "users" WHERE username = :username');
        $stmt->execute([
            'username' => $username
        ]);
        $record = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($record) {
            return new User($record);
        }

        return null;
    }

}
