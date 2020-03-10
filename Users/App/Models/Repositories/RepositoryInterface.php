<?php declare(strict_types=1);

namespace App\Models\Repositories;

interface RepositoryInterface
{
    public function __construct(\PDO $database);
}
