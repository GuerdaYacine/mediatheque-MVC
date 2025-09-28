<?php

namespace Models;

use Database\Database;

/**
 * Class Media
 *
 * Représente un média de la médiathèque.
 * Fournit des méthodes pour emprunter, retourner et gérer l'état des médias en base de données.
 */
class Media
{
    /**
     * Constructeur de la classe Media
     *
     * @param int $id Identifiant unique du média
     * @param string $title Titre du média
     * @param string $author Auteur ou créateur du média
     * @param int $available Disponibilité du média (1 = disponible, 0 = emprunté)
     * @param string $image Nom du fichier image associé au média
     */
    public function __construct(
        private int $id,
        private string $title,
        private string $author,
        private int $available,
        private string $image
    ) {}

    /**
     * Retourne l'identifiant du média
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Modifie l'identifiant du média
     *
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Retourne le titre du média
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Modifie le titre du média
     *
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Retourne l'auteur du média
     *
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * Modifie l'auteur du média
     *
     * @param string $author
     */
    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    /**
     * Retourne la disponibilité du média
     *
     * @return int 1 = disponible, 0 = emprunté
     */
    public function getAvailable(): int
    {
        return $this->available;
    }

    /**
     * Modifie la disponibilité du média
     *
     * @param int $available 1 = disponible, 0 = emprunté
     */
    public function setAvailable(int $available): void
    {
        $this->available = $available;
    }

    /**
     * Retourne le nom du fichier image associé au média
     *
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * Modifie le nom du fichier image associé au média
     *
     * @param string $image
     */
    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    /**
     * Permet à un utilisateur d'emprunter un média.
     *
     * @param int $userId Identifiant de l'utilisateur
     * @param int $mediaId Identifiant du média
     * @return bool True si l'emprunt a réussi, false sinon
     */
    public static function borrow(int $userId, int $mediaId): bool
    {
        $db = new Database();
        $connexion = $db->connect();

        // Vérifie si le média est disponible
        $statementIsAvailable = $connexion->prepare("SELECT available FROM media WHERE id = :id");
        $statementIsAvailable->bindParam(':id', $mediaId);
        $statementIsAvailable->execute();
        $available = $statementIsAvailable->fetchColumn();

        if ($available == 1) {
            // Crée une entrée dans la table location
            $statementInsertIntoLocation = $connexion->prepare(
                "INSERT INTO location (user_id, media_id) VALUES (:user_id, :media_id)"
            );
            $statementInsertIntoLocation->bindParam(':user_id', $userId);
            $statementInsertIntoLocation->bindParam(':media_id', $mediaId);
            $statementInsertIntoLocation->execute();

            // Met à jour la disponibilité du média
            $statementUpdateMedia = $connexion->prepare("UPDATE media SET available = 0 WHERE id = :id");
            $statementUpdateMedia->bindParam(':id', $mediaId);
            $statementUpdateMedia->execute();

            return true;
        }

        return false;
    }

    /**
     * Permet à un utilisateur de retourner un média emprunté.
     *
     * @param int $userId Identifiant de l'utilisateur
     * @param int $mediaId Identifiant du média
     * @return bool True si le retour a réussi, false sinon
     */
    public static function returnMedia(int $userId, int $mediaId): bool
    {
        $db = new Database();
        $connexion = $db->connect();

        // Vérifie si l'utilisateur a bien emprunté ce média et qu'il n'a pas encore été retourné
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
            // Met à jour la table location pour indiquer le retour
            $statementUpdateLocation = $connexion->prepare(
                "UPDATE location SET returned_at = NOW() WHERE id = :id"
            );
            $statementUpdateLocation->bindParam(':id', $location['id']);
            $statementUpdateLocation->execute();

            // Met à jour la disponibilité du média
            $statementUpdateMedia = $connexion->prepare("UPDATE media SET available = 1 WHERE id = :id");
            $statementUpdateMedia->bindParam(':id', $mediaId);
            $statementUpdateMedia->execute();

            return true;
        }

        return false;
    }

    /**
     * Retourne l'identifiant de l'utilisateur qui a actuellement emprunté le média
     *
     * @param int $mediaId Identifiant du média
     * @return int|null Identifiant de l'emprunteur ou null si le média est disponible
     */
    public static function getBorrowerId(int $mediaId): ?int
    {
        $db = new Database();
        $connexion = $db->connect();

        $stmt = $connexion->prepare(
            "SELECT user_id 
             FROM location 
             WHERE media_id = :media_id 
               AND returned_at IS NULL
             ORDER BY borrowed_at DESC
             LIMIT 1"
        );
        $stmt->bindParam(':media_id', $mediaId);
        $stmt->execute();

        $borrowerId = $stmt->fetchColumn();

        return $borrowerId !== false ? (int) $borrowerId : null;
    }
}
