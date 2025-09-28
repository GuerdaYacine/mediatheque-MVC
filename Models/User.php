<?php

namespace Models;

use Database\Database;

/**
 * Classe User
 * 
 * Représente un utilisateur du système avec ses propriétés et méthodes
 * pour la gestion des données utilisateur en base de données.
 */
class User
{
    /**
     * Constructeur de la classe User
     * 
     * @param int $id Identifiant unique de l'utilisateur
     * @param string $username Nom d'utilisateur
     * @param string $email Adresse email de l'utilisateur
     * @param string $password Mot de passe de l'utilisateur
     * @param string|null $createdAt Date de création de l'utilisateur
     */
    public function __construct(private int $id, private string $username, private string $email, private string $password, private ?string $createdAt) {}

    /**
     * Récupère l'identifiant de l'utilisateur
     * 
     * @return int L'identifiant de l'utilisateur
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Définit l'identifiant de l'utilisateur
     * 
     * @param int $id Le nouvel identifiant
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Récupère le nom d'utilisateur
     * 
     * @return string Le nom d'utilisateur
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Définit le nom d'utilisateur
     * 
     * @param string $username Le nouveau nom d'utilisateur
     * @return void
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * Récupère l'adresse email
     * 
     * @return string L'adresse email
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Définit l'adresse email
     * 
     * @param string $email La nouvelle adresse email
     * @return void
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * Récupère le mot de passe
     * 
     * @return string Le mot de passe
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Définit le mot de passe avec hashage automatique
     * 
     * @param string $password Le nouveau mot de passe en clair
     * @return void
     */
    public function setPassword(string $password): void
    {
        $this->password = password_hash($password, PASSWORD_ARGON2ID);
    }

    /**
     * Récupère la date de création
     * 
     * @return string|null La date de création ou null
     */
    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    /**
     * Définit la date de création
     * 
     * @param string $createdAt La nouvelle date de création
     * @return void
     */
    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Crée un nouvel utilisateur en base de données
     * 
     * @param string $username Le nom d'utilisateur
     * @param string $email L'adresse email
     * @param string $password Le mot de passe en clair
     * @return bool True si la création réussit, false sinon
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
     * @param int $id L'identifiant de l'utilisateur
     * @return User|null L'instance User ou null si non trouvé
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

        $user = new User($user['id'], $user['username'], $user['email'], $user['password'], $user['created_at']);

        return $user;
    }

    /**
     * Récupère un utilisateur par son adresse email
     * 
     * @param string $email L'adresse email de l'utilisateur
     * @return User|null L'instance User ou null si non trouvé
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

        $user = new User($user['id'], $user['username'], $user['email'], $user['password'], $user['created_at']);

        return $user;
    }
}
