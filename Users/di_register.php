<?php declare(strict_types=1);

use App\Database\PostgreSQL;
use App\ServiceDI;
use App\Models\Repositories\UserRepository;
use App\Models\Repositories\UserRepositoryInterface;

ServiceDI::assign(UserRepositoryInterface::class, function() {
    return new UserRepository(PostgreSQL::getConnection());
});
