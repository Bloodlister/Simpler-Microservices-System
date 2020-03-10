<?php declare(strict_types=1);

namespace Tests\UserRegisterService;

use App\Exception\DuplicateUserException;
use App\Exception\QueryException;
use App\MessageBrokers\RabbitMQ;
use App\Models\Repositories\UserRepositoryInterface;
use App\Models\User;
use App\Services\Payloads\UserRegisterPayload;
use App\Services\UserRegisterService;
use PHPUnit\Framework\TestCase;

class RegisterUserTest extends TestCase
{

    /**
     * @test
     */
    public function creating_user_which_is_already_registered_throws_a_duplication_exception()
    {
        $this->expectException(DuplicateUserException::class);

        $rabbitmqMock = $this->createMock(RabbitMQ::class);
        $userRegisterService = new UserRegisterService($rabbitmqMock);
        $userRepoMock = $this->createMock(UserRepositoryInterface::class);
        $userRepoMock->expects($this->once())
            ->method('getUser')
            ->with('Foo')
            ->willReturn(new User([]));

        $servicePayload = new UserRegisterPayload(['issuer' => 'test', 'username' => 'Foo', 'password' => 'password']);
        $userRegisterService->run($servicePayload, $userRepoMock);
    }

    /**
     * @test
     */
    public function failing_to_create_user_throws_a_query_exception()
    {
        $this->expectException(QueryException::class);
        $payloadData = ['issuer' => 'test', 'username' => 'Foo', 'password' => 'password'];

        $rabbitmqMock = $this->createMock(RabbitMQ::class);
        $userRegisterService = new UserRegisterService($rabbitmqMock);
        $userRepoMock = $this->createMock(UserRepositoryInterface::class);
        $userRepoMock->expects($this->once())->method('getUser')->willReturn(null);
        $userRepoMock->expects($this->once())
            ->method('registerUser')
            ->with($payloadData['username'], md5($payloadData['password']))
            ->willReturn(false);

        $servicePayload = new UserRegisterPayload($payloadData);
        $userRegisterService->run($servicePayload, $userRepoMock);
    }

}
