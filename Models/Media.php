<?php

namespace Models;

use PDO;
use PDOStatement;
use Database;

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

    // public function borrow(int $userId, int $mediaId): bool
    // {
    //     if ($this->available) {
    //         $this->statementBorrowMedia->bindParam(':user_id', $userId);
    //         $this->statementBorrowMedia->bindParam(':media_id', $mediaId);
    //         $this->statementBorrowMedia->execute();
    //         $this->available = 0;
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }

    // public function return(int $userId, int $mediaId): void
    // {
    //     $this->statementReturnMedia->bindParam(':user_id', $userId);
    //     $this->statementReturnMedia->bindParam(':media_id', $mediaId);
    //     $this->statementReturnMedia->execute();
    //     $this->available = 1;
    // }
}
