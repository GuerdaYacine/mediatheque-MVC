<?php

namespace Models;

use Database;
use PDOStatement;
use PDO;

class Book extends Media
{
    private int $pageNumber;
    private PDOStatement $statementReadOneBook;
    private PDOStatement $statementReadAllBook;
    private PDOStatement $statementUpdateBook;
    private PDOStatement $statementDeleteBook;
    private PDOStatement $statementCreateBook;
    private PDOStatement $statementCreateBookIntoBook;
    private PDOStatement $statementGetThreeRandomBooks;
    private PDOStatement $statementGetAvailableBooks;
    private PDO $pdo;

    public function __construct(Database $db)
    {
        $this->pdo = $db->connection;

        $this->statementReadAllBook = $this->pdo->prepare(
            "SELECT m.id, m.title, m.author, m.available, m.image, b.page_number
        FROM media m
        JOIN book b USING(id)
        WHERE m.media_type = 'book'"
        );

        $this->statementReadOneBook = $this->pdo->prepare(
            "SELECT m.id, m.title, m.author, m.available, m.image, b.page_number
            FROM media m
            JOIN book b USING(id)
            WHERE m.id = :id AND m.media_type = 'book'"
        );

        $this->statementUpdateBook = $this->pdo->prepare(
            "UPDATE media m
            JOIN book b USING(id)
            SET m.title = :title, m.author = :author, m.available = :available, m.image = :image, b.page_number = :page_number
            WHERE m.id = :id"
        );

        $this->statementDeleteBook = $this->pdo->prepare(
            "DELETE m, b
            FROM media m
            JOIN book b USING(id)
            WHERE m.id = :id"
        );

        $this->statementCreateBook = $this->pdo->prepare(
            "INSERT INTO media (title, author, available, image, media_type) VALUES (:title, :author, :available, :image, 'book')"
        );

        $this->statementCreateBookIntoBook = $this->pdo->prepare(
            "INSERT INTO book (id, page_number) VALUES (:id, :page_number)"
        );
        $this->statementGetThreeRandomBooks = $this->pdo->prepare(
            "SELECT m.id, m.title, m.author, m.available, m.image, b.page_number
            FROM media m
            JOIN book b USING(id)
            WHERE m.available = 1
            ORDER BY RAND()
            LIMIT 3
            "
        );

        $this->statementGetAvailableBooks = $this->pdo->prepare(
            "SELECT m.id, m.title, m.author, m.available, m.image, b.page_number
            FROM media m
            JOIN book b USING(id)
            WHERE m.available = 1
            "
        );
    }

    public function getPageNumber(): int
    {
        return $this->pageNumber;
    }

    public function setPageNumber(int $pageNumber): void
    {
        $this->pageNumber = $pageNumber;
    }

    public function getAllBooks(): array
    {
        $this->statementReadAllBook->execute();
        return $this->statementReadAllBook->fetchAll();
    }

    public function getOneBook(int $id): array|false
    {
        $this->statementReadOneBook->bindParam(':id', $id);
        $this->statementReadOneBook->execute();
        return $this->statementReadOneBook->fetch();
    }

    public function updateBook(int $id, string $title, string $author, int $pageNumber, int $available, string $image): bool
    {
        $this->statementUpdateBook->bindParam(':id', $id);
        $this->statementUpdateBook->bindParam(':title', $title);
        $this->statementUpdateBook->bindParam(':author', $author);
        $this->statementUpdateBook->bindParam(':page_number', $pageNumber);
        $this->statementUpdateBook->bindParam(':available', $available);
        $this->statementUpdateBook->bindParam(':image', $image);
        return $this->statementUpdateBook->execute();
    }

    public function deleteBook(int $id): bool
    {
        $this->statementDeleteBook->bindParam(':id', $id);
        return $this->statementDeleteBook->execute();
    }

    public function createBook(string $title, string $author, int $pageNumber, int $available, string $image): bool
    {
        $this->statementCreateBook->bindParam(':title', $title);
        $this->statementCreateBook->bindParam(':author', $author);
        $this->statementCreateBook->bindParam(':available', $available);
        $this->statementCreateBook->bindParam(':image', $image);
        $success = $this->statementCreateBook->execute();

        if (!$success) {
            return false;
        }

        $id = $this->pdo->lastInsertId();

        $this->statementCreateBookIntoBook->bindParam(':id', $id);
        $this->statementCreateBookIntoBook->bindParam(':page_number', $pageNumber);
        return $this->statementCreateBookIntoBook->execute();
    }

    public function getThreeRandomBooks(): array
    {
        $this->statementGetThreeRandomBooks->execute();
        return $this->statementGetThreeRandomBooks->fetchAll();
    }

    public function getAvailableBooks():array
    {
        $this->statementGetAvailableBooks->execute();
        return $this->statementGetAvailableBooks->fetchAll();
    }
}
