<?php
// app/Repositories/UserRepository.php

class UserRepository
{
    public function __construct(private PDO $db) {}

    public function findActiveByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT id, name, email, password_hash, role
             FROM users
             WHERE email = :email AND status = 'active'
             LIMIT 1"
        );
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT id, name, email, role, status FROM users WHERE id = :id LIMIT 1"
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function updateLastLogin(int $id): void
    {
        $this->db->prepare("UPDATE users SET last_login = NOW() WHERE id = :id")
                 ->execute(['id' => $id]);
    }

    public function countAll(): int
    {
        return (int)$this->db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    }

    public function all(): array
    {
        return $this->db->query("SELECT id, name, role FROM users WHERE status='active' ORDER BY name")->fetchAll();
    }
}
