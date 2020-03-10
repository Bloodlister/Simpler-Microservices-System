<?php declare(strict_types=1);

namespace App\Models\Repositories;

use App\Models\User;
use PDO;

class UserRepository implements UserRepositoryInterface
{

    /** @var PDO $database */
    protected PDO $database;

    public function __construct(PDO $database)
    {
        $this->database = $database;
    }

    public function getUser(string $username): ?User
    {
        $stmt = $this->database->prepare('SELECT * FROM "users" WHERE username = :username');
        $stmt->execute([
            'username' => $username
        ]);
        $record = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$record) {
            return null;
        }

        return new User($record);
    }

    public function registerUser(string $username, string $password): bool
    {
        $stmt = $this->database->prepare('INSERT INTO "public"."users"(username, password, created_on) VALUES (:username, :password, NOW())');

        return $stmt->execute([
            'username' => $username,
            'password' => $password
        ]);
    }
}
