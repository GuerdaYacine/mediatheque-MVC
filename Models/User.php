<?php

namespace Models;

use PDO;
use PDOStatement;
use Database;

class User
{
    private $username;
    private $email;
    private $password;
    private $createdAt;
    private $updatedAt;
    private PDO $pdo;
    private PDOStatement $statementCreateUser;
    private PDOStatement $statementUpdateUser;
    private PDOStatement $statementReadUserByEmail;
    private PDOStatement $statementReadUserById;


    public function __construct(Database $db)
    {
        $this->pdo = $db->connection;

        $this->statementCreateUser = $this->pdo->prepare(
            "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)"
        );

        $this->statementUpdateUser = $this->pdo->prepare(
            "UPDATE users SET username = :username, email = :email, password = :password WHERE id = :id"
        );

        $this->statementReadUserByEmail = $this->pdo->prepare(
            "SELECT * FROM users WHERE email = :email"
        );
        $this->statementReadUserById = $this->pdo->prepare(
            "SELECT * FROM users WHERE id = :id"
        );
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = password_hash($password, PASSWORD_ARGON2ID);
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(string $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function createUser(string $username, string $email, string $password): bool
    {
        $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);

        $this->statementCreateUser->bindParam(':username', $username);
        $this->statementCreateUser->bindParam(':email', $email);
        $this->statementCreateUser->bindParam(':password', $hashedPassword);

        return $this->statementCreateUser->execute();
    }

    public function updateUser(int $id, string $username, string $email, string $password): bool
    {
        $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);

        $this->statementUpdateUser->bindParam(':id', $id);
        $this->statementUpdateUser->bindParam(':username', $username);
        $this->statementUpdateUser->bindParam(':email', $email);
        $this->statementUpdateUser->bindParam(':password', $hashedPassword);
        return $this->statementUpdateUser->execute();
    }

    public function getUserById(int $id): array|false
    {
        $this->statementReadUserById->bindParam(':id', $id);
        $this->statementReadUserById->execute();
        return $this->statementReadUserById->fetch();
    }

    public function getUserByEmail(string $email): array|false
    {
        $this->statementReadUserByEmail->bindParam(':email', $email);
        $this->statementReadUserByEmail->execute();
        return $this->statementReadUserByEmail->fetch();
    }
}
