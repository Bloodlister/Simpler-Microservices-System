<?php declare(strict_types=1);

use App\Database\PostgreSQL;
use App\DI;
use App\Models\Repositories\UserRepository;
use App\Models\Repositories\UserRepositoryInterface;

DI::assign(UserRepositoryInterface::class, function() {
    return new UserRepository(PostgreSQL::getConnection());
});
