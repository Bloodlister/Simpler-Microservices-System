<?php declare(strict_types=1);

namespace App\Models\Repositories;

use App\Models\User;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function getUser(string $username): ?User;

    public function registerUser(string $username, string $password): bool;
}
