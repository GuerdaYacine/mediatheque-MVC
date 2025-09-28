<?php

namespace Models;

use Database\Database;

/**
 * Classe Media
 * 
 * Représente un média du système avec ses propriétés et méthodes
 * pour la gestion des données des médias et des emprunts en base de données.
 */
class Media
{
    /**
     * Constructeur de la classe Media
     * 
     * @param int $id Identifiant unique du média
     * @param string $title Titre du média
     * @param string $author Auteur du média
     * @param int $available Statut de disponibilité (0 ou 1)
     * @param string $image Chemin vers l'image du média
     */
    public function __construct(private int $id, private string $title, private string $author, private int $available, private string $image) {}

    /**
     * Récupère l'identifiant du média
     * 
     * @return int L'identifiant du média
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Définit l'identifiant du média
     * 
     * @param int $id Le nouvel identifiant
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Récupère le titre du média
     * 
     * @return string Le titre du média
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Définit le titre du média
     * 
     * @param string $title Le nouveau titre
     * @return void
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Récupère l'auteur du média
     * 
     * @return string L'auteur du média
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * Définit l'auteur du média
     * 
     * @param string $author Le nouvel auteur
     * @return void
     */
    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    /**
     * Récupère le statut de disponibilité
     * 
     * @return int Le statut de disponibilité
     */
    public function getAvailable(): int
    {
        return $this->available;
    }

    /**
     * Définit le statut de disponibilité
     * 
     * @param int $available Le nouveau statut de disponibilité
     * @return void
     */
    public function setAvailable(int $available): void
    {
        $this->available = $available;
    }

    /**
     * Récupère le chemin de l'image
     * 
     * @return string Le chemin vers l'image
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * Définit le chemin de l'image
     * 
     * @param string $image Le nouveau chemin de l'image
     * @return void
     */
    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    /**
     * Emprunte un média pour un utilisateur
     * 
     * Vérifie la disponibilité du média, puis enregistre l'emprunt
     * et marque le média comme non disponible.
     * 
     * @param int $userId L'identifiant de l'utilisateur emprunteur
     * @param int $mediaId L'identifiant du média à emprunter
     * @return bool True si l'emprunt réussit, false sinon
     */
    public static function borrow(int $userId, int $mediaId): bool
    {
        $db = new Database();
        $connexion = $db->connect();

        $statementIsAvailable = $connexion->prepare("SELECT available FROM media WHERE id = :id");
        $statementIsAvailable->bindParam(':id', $mediaId);
        $statementIsAvailable->execute();
        $available = $statementIsAvailable->fetchColumn();

        if ($available == 1) {
            $statementInsertIntoLocation = $connexion->prepare("INSERT INTO location (user_id, media_id) VALUES (:user_id, :media_id)");
            $statementInsertIntoLocation->bindParam(':user_id', $userId);
            $statementInsertIntoLocation->bindParam(':media_id', $mediaId);
            $statementInsertIntoLocation->execute();

            $statementUpdateMedia = $connexion->prepare("UPDATE media SET available = 0 WHERE id = :id");
            $statementUpdateMedia->bindParam(':id', $mediaId);
            $statementUpdateMedia->execute();

            return true;
        }

        return false;
    }

    /**
     * Retourne un média emprunté par un utilisateur
     * 
     * Vérifie que l'utilisateur a bien emprunté le média,
     * met à jour la date de retour et marque le média comme disponible.
     * 
     * @param int $userId L'identifiant de l'utilisateur
     * @param int $mediaId L'identifiant du média à retourner
     * @return bool True si le retour réussit, false sinon
     */
    public static function returnMedia(int $userId, int $mediaId): bool
    {
        $db = new Database();
        $connexion = $db->connect();

        $statementCheckUser = $connexion->prepare(
            "SELECT id FROM location 
        WHERE user_id = :user_id 
          AND media_id = :media_id 
          AND returned_at IS NULL
        ORDER BY borrowed_at DESC
        LIMIT 1"
        );
        $statementCheckUser->bindParam(':user_id', $userId);
        $statementCheckUser->bindParam(':media_id', $mediaId);
        $statementCheckUser->execute();
        $location = $statementCheckUser->fetch();

        if ($location) {
            $statementUpdateLocation = $connexion->prepare(
                "UPDATE location 
                SET returned_at = NOW()
                WHERE id = :id"
            );
            $statementUpdateLocation->bindParam(':id', $location['id']);
            $statementUpdateLocation->execute();

            $statementUpdateMedia = $connexion->prepare("UPDATE media SET available = 1 WHERE id = :id");
            $statementUpdateMedia->bindParam(':id', $mediaId);
            $statementUpdateMedia->execute();
            return true;
        }

        return false;
    }

    /**
     * Récupère l'identifiant de l'utilisateur qui a emprunté le média
     * 
     * Recherche l'emprunt actuel (non retourné) du média spécifié.
     * 
     * @param int $mediaId L'identifiant du média
     * @return int|null L'identifiant de l'emprunteur ou null si non emprunté
     */
    public static function getBorrowerId(int $mediaId): ?int
    {
        $db = new Database();
        $connexion = $db->connect();

        $stmt = $connexion->prepare("
            SELECT user_id 
            FROM location 
            WHERE media_id = :media_id 
            AND returned_at IS NULL
            ORDER BY borrowed_at DESC
            LIMIT 1
        ");
        $stmt->bindParam(':media_id', $mediaId);
        $stmt->execute();

        $borrowerId = $stmt->fetchColumn();

        return $borrowerId !== false ? (int) $borrowerId : null;
    }
}
