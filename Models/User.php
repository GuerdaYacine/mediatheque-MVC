<?php

namespace Models;

use Controllers\UserController;
use Database\Database;

/**
 * Class User
 *
 * Représente un utilisateur de l'application.
 * Fournit des méthodes pour créer, récupérer et gérer les utilisateurs en base de données.
 */
class User
{
    /**
     * Constructeur de la classe User
     *
     * @param int $id Identifiant unique de l'utilisateur
     * @param string $username Nom d'utilisateur
     * @param string $email Adresse email de l'utilisateur
     * @param string $password Mot de passe de l'utilisateur (haché)
     * @param string|null $createdAt Date de création de l'utilisateur (format datetime ou null)
     */
    public function __construct(
        private int $id,
        private string $username,
        private string $email,
        private string $password,
        private ?string $createdAt
    ) {}

    /**
     * Retourne l'identifiant de l'utilisateur
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Modifie l'identifiant de l'utilisateur
     *
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Retourne le nom d'utilisateur
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Modifie le nom d'utilisateur
     *
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * Retourne l'adresse email de l'utilisateur
     *
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Modifie l'adresse email de l'utilisateur
     *
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * Retourne le mot de passe de l'utilisateur (haché)
     *
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Modifie le mot de passe de l'utilisateur et le hache avec Argon2id
     *
     * @param string $password Mot de passe en clair
     */
    public function setPassword(string $password): void
    {
        $this->password = password_hash($password, PASSWORD_ARGON2ID);
    }

    /**
     * Retourne la date de création de l'utilisateur
     *
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    /**
     * Modifie la date de création de l'utilisateur
     *
     * @param string $createdAt
     */
    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Crée un nouvel utilisateur en base de données
     *
     * @param string $username Nom d'utilisateur
     * @param string $email Email de l'utilisateur
     * @param string $password Mot de passe en clair
     * @return bool True si l'insertion a réussi, false sinon
     */
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

    /**
     * Récupère un utilisateur par son identifiant
     *
     * @param int $id Identifiant de l'utilisateur
     * @return User|null Retourne un objet User ou null si l'utilisateur n'existe pas
     */
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

        return new User($user['id'], $user['username'], $user['email'], $user['password'], $user['created_at']);
    }

    /**
     * Récupère un utilisateur par son email
     *
     * @param string $email Email de l'utilisateur
     * @return User|null Retourne un objet User ou null si l'utilisateur n'existe pas
     */
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

        return new User($user['id'], $user['username'], $user['email'], $user['password'], $user['created_at']);
    }
}
