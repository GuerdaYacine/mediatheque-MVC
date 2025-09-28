<?php

namespace Models;

use Database\Database;

class Media
{
    public function __construct(private int $id, private string $title, private string $author, private int $available, private string $image) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    public function getAvailable(): int
    {
        return $this->available;
    }

    public function setAvailable(int $available): void
    {
        $this->available = $available;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): void
    {
        $this->image = $image;
    }

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

        if($location){
            $statementUpdateLocation = $connexion->prepare(
                "UPDATE location 
                SET returned_at = NOW()
                WHERE id = :id");
            $statementUpdateLocation->bindParam(':id', $location['id']);
            $statementUpdateLocation->execute();

            $statementUpdateMedia = $connexion->prepare("UPDATE media SET available = 1 WHERE id = :id");
            $statementUpdateMedia->bindParam(':id', $mediaId);
            $statementUpdateMedia->execute();
            return true;
        }

        return false;
    }

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
