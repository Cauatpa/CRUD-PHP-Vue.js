<?php

namespace Src\Repositories;

use PDO;
use Src\Models\User;
use Src\Database\Connection;

class UserRepository
{
    private PDO $pdo;
    private string $table = 'crud';

    public function __construct()
    {
        $this->pdo = Connection::getInstance();
    }

    /** @return User[] */
    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT id, name, email, phone FROM {$this->table} ORDER BY id DESC");
        $rows = $stmt->fetchAll();
        return array_map(fn($r) => new User((int)$r['id'], $r['name'], $r['email'], $r['phone']), $rows);
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->pdo->prepare("SELECT id, name, email, phone FROM {$this->table} WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $r = $stmt->fetch();
        return $r ? new User((int)$r['id'], $r['name'], $r['email'], $r['phone']) : null;
    }

    public function create(User $user): int
    {
        $sql = "INSERT INTO {$this->table} (name, email, phone) VALUES (:name, :email, :phone)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':name'  => $user->name,
            ':email' => $user->email,
            ':phone' => $user->phone,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(User $user): bool
    {
        $sql = "UPDATE {$this->table}
                   SET name = :name, email = :email, phone = :phone
                 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':name'  => $user->name,
            ':email' => $user->email,
            ':phone' => $user->phone,
            ':id'    => $user->id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function emailExists(string $email, ?int $ignoreId = null): bool
    {
        $sql = "SELECT COUNT(1) AS qty FROM {$this->table} WHERE email = :email";
        $params = [':email' => $email];

        if ($ignoreId !== null) {
            $sql .= " AND id <> :id";
            $params[':id'] = $ignoreId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }
}
