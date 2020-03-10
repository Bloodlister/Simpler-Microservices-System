<?php declare(strict_types=1);

namespace App\Services;

use App\Exception\DuplicateUserException;
use App\Exception\QueryException;
use App\Models\Repositories\UserRepositoryInterface;
use App\Services\Payloads\Payload;
use App\Services\Payloads\UserRegisterPayload;
use App\WebSocket\ToastrNotification;

class UserRegisterService extends Service
{
    public const CHANNEL = 'userRegisterRequest';

    public const PAYLOAD_CLASS = UserRegisterPayload::class;

    public const QUEUE_USER_REGISTER_RESULTS = 'userRegisterResult';
    public const QUEUE_WEBSOCKET = 'websocket';

    public function getWritingQueues(): array
    {
        return [
            static::QUEUE_USER_REGISTER_RESULTS,
            static::QUEUE_WEBSOCKET,
        ];
    }

    /**
     * @param Payload $payload
     * @param UserRepositoryInterface $userRepository
     * @return mixed
     * @throws DuplicateUserException
     * @throws QueryException
     */
    public function run(Payload $payload, UserRepositoryInterface $userRepository): void
    {
        $user = $userRepository->getUser($payload->username);
        if ($user) {
            throw new DuplicateUserException(sprintf('Username `%1$s` is already taken.', $payload->username));
        }

        $password = md5($payload->password);
        $result = $userRepository->registerUser($payload->username, $password);

        if (!$result) {
            throw new QueryException('Could not add user to database');
        }

        $user = $userRepository->getUser($payload->username);
        $this->broker->publish(static::QUEUE_USER_REGISTER_RESULTS, [
            'id' => $user->id,
            'username' => $payload->username,
            'password' => $password
        ]);

        $this->broker->websocketMessage($payload->issuer, new ToastrNotification(ToastrNotification::STATUS_SUCCESS, 'Registration Complete'));
    }

}
