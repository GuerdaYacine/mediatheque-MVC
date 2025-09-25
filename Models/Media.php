<?php

namespace Models;

use PDO;
use PDOStatement;
use Database;

class Media
{

    private string $title;
    private string $author;
    private ?int $available = null;
    private string $image;
    private PDOStatement $statementBorrowMedia;
    private PDOStatement $statementReturnMedia;
    private PDO $pdo;


    public function __construct(string $title, string $author, string $image, int $available, Database $db)
    {
        $this->title = $title;
        $this->author = $author;
        $this->available = $available;
        $this->image = $image;
        $this->pdo = $db->connection;

        $this->statementBorrowMedia = $this->pdo->prepare(
            "INSERT INTO location (user_id, media_id) VALUES (:user_id, :media_id)"
        );
        $this->statementReturnMedia = $this->pdo->prepare(
            "UPDATE location SET return_date = NOW() WHERE user_id = :user_id AND media_id = :media_id"
        );
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

    public function isAvailable(): ?bool
    {
        return $this->available;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    public function borrow(int $userId, int $mediaId): bool
    {
        if ($this->available) {
            $this->statementBorrowMedia->bindParam(':user_id', $userId);
            $this->statementBorrowMedia->bindParam(':media_id', $mediaId);
            $this->statementBorrowMedia->execute();
            $this->available = 0;
            return true;
        } else {
            return false;
        }
    }

    public function return(int $userId, int $mediaId): void
    {
        $this->statementReturnMedia->bindParam(':user_id', $userId);
        $this->statementReturnMedia->bindParam(':media_id', $mediaId);
        $this->statementReturnMedia->execute();
        $this->available = 1;
    }
}
