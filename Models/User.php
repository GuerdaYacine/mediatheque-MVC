<?php

namespace Models;

use Controllers\UserController;
use PDO;
use PDOStatement;
use Database\Database;

class User
{
    private PDOStatement $statementCreateUser;
    private PDOStatement $statementUpdateUser;
    private PDOStatement $statementReadUserByEmail;
    private PDOStatement $statementReadUserById;


    public function __construct(private int $id, private string $username, private string $email, private string $password, private ?string $createdAt, private ?string $updatedAt) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
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

    public static function createUser(string $username, string $email, string $password): bool
    {
        $db = new Database();
        $connexion = $db->connect();

        $statementCreateUser = $connexion->prepare(
            "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)"
        );

        $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);

        $statementCreateUser->bindParam(':username', $username);
        $statementCreateUser->bindParam(':email', $email);
        $statementCreateUser->bindParam(':password', $hashedPassword);

        return $statementCreateUser->execute();
    }

    public static function getUserById(int $id): ?User
    {
        $db = new Database();
        $connexion = $db->connect();

        $statementGetUserById = $connexion->prepare(
            "SELECT * FROM users WHERE id = :id"
        );
        $statementGetUserById->bindParam(':id', $id);
        $statementGetUserById->execute();
        $user = $statementGetUserById->fetch();

        if (!$user) {
            return null;
        }

        $user = new User($user['id'], $user['username'], $user['email'], $user['password'], $user['created_at'], $user['updated_at']);

        return $user;
    }

    public static function getUserByEmail(string $email): ?User
    {
        $db = new Database();
        $connexion = $db->connect();

        $statementGetUserByEmail = $connexion->prepare(
            "SELECT * FROM users WHERE email = :email"
        );

        $statementGetUserByEmail->bindParam(':email', $email);
        $statementGetUserByEmail->execute();
        $user = $statementGetUserByEmail->fetch();

        if (!$user) {
            return null;
        }

        $user = new User($user['id'], $user['username'], $user['email'], $user['password'], $user['created_at'], $user['updated_at']);

        return $user;
    }
}
