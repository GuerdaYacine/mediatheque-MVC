<?php

namespace Models;

use \Database\Database;

class Book extends Media
{

    public function __construct(int $id, string $title, string $author, int $available, string $image, private int $pageNumber)
    {
        parent::__construct($id, $title, $author, $available, $image);
    }

    public function getPageNumber(): int
    {
        return $this->pageNumber;
    }

    public function setPageNumber(int $pageNumber): void
    {
        $this->pageNumber = $pageNumber;
    }

    public static function getAllBooks(): array
    {
        $db = new Database();
        $connexion = $db->connect();
        $statementReadAllBooks = $connexion->prepare(
            "SELECT m.id, m.title, m.author, m.available, m.image, b.page_number
            FROM media m
            JOIN book b USING(id)"
        );
        $statementReadAllBooks->execute();
        $booksDB = $statementReadAllBooks->fetchAll();
        $books = [];
        foreach ($booksDB as $book) {
            $bookInst = new Book($book['id'], $book['title'], $book['author'], $book['available'], $book['image'], $book['page_number']);
            array_push($books, $bookInst);
        }

        return $books;
    }

    public static function createBook(string $title, string $author, int $available, string $image, int $pageNumber): bool
    {
        $db = new Database();
        $connexion = $db->connect();

        $statementCreateBook = $connexion->prepare(
            "INSERT INTO media (title, author, available, image) 
            VALUES (:title, :author, :available, :image)"
        );

        $statementCreateBookIntoBook = $connexion->prepare(
            "INSERT INTO book (id, page_number) 
            VALUES (:id, :page_number)"
        );

        $statementCreateBook->bindParam(':title', $title);
        $statementCreateBook->bindParam(':author', $author);
        $statementCreateBook->bindParam(':available', $available);
        $statementCreateBook->bindParam(':image', $image);

        $success = $statementCreateBook->execute();
        if (!$success) {
            return false;
        }

        $id = $connexion->lastInsertId();

        $statementCreateBookIntoBook->bindParam(':id', $id);
        $statementCreateBookIntoBook->bindParam(':page_number', $pageNumber);

        return $statementCreateBookIntoBook->execute();
    }

    public static function updateBook(int $id, string $title, string $author, int $available, string $image, int $pageNumber): bool
    {
        $db = new Database();
        $connexion = $db->connect();

        $statementUpdateBook = $connexion->prepare(
            "UPDATE media m
            JOIN book b USING(id)
            SET m.title = :title, m.author = :author, m.available = :available, m.image = :image, b.page_number = :page_number
            WHERE m.id = :id"
        );

        $statementUpdateBook->bindParam(':id', $id);
        $statementUpdateBook->bindParam(':title', $title);
        $statementUpdateBook->bindParam(':author', $author);
        $statementUpdateBook->bindParam(':available', $available);
        $statementUpdateBook->bindParam(':image', $image);
        $statementUpdateBook->bindParam(':page_number', $pageNumber);

        return $statementUpdateBook->execute();
    }

    public static function getOneBook(int $id): Book
    {
        $db = new Database();
        $connexion = $db->connect();

        $statementReadOneBook = $connexion->prepare(
            "SELECT m.id, m.title, m.author, m.available, m.image, b.page_number
            FROM media m
            JOIN book b USING(id)
            WHERE m.id = :id"
        );

        $statementReadOneBook->bindParam(':id', $id);
        $statementReadOneBook->execute();
        $book = $statementReadOneBook->fetch();

        $book = new Book($book['id'], $book['title'], $book['author'], $book['available'], $book['image'], $book['page_number']);

        return $book;
    }

    public static function deleteBook(int $id): bool
    {
        $db = new Database();
        $connexion = $db->connect();

        $statementDeleteBook = $connexion->prepare(
            "DELETE m, b
            FROM media m
            JOIN book b USING(id)
            WHERE m.id = :id"
        );
        $statementDeleteBook->bindParam(':id', $id);
        $success = $statementDeleteBook->execute();
        return $success;
    }

    public static function getThreeAvailableRandomBooks(): array
    {
        $db = new Database();
        $connexion = $db->connect();

        $statementGetThreeAvailableRandomBooks = $connexion->prepare(
            "SELECT m.id, m.title, m.author, m.available, m.image, b.page_number
            FROM media m
            JOIN book b USING(id)
            WHERE m.available = 1
            ORDER BY RAND()
            LIMIT 3
            "
        );
        $statementGetThreeAvailableRandomBooks->execute();
        $booksDB = $statementGetThreeAvailableRandomBooks->fetchAll();
        $books = [];
        foreach ($booksDB as $book) {
            $bookInst = new Book($book['id'], $book['title'], $book['author'], $book['available'], $book['image'], $book['page_number']);
            array_push($books, $bookInst);
        }

        return $books;
    }

    public static function getAvailableBooks(): array
    {
        $db = new Database();
        $connexion = $db->connect();

        $statementGetAvailableBooks = $connexion->prepare(
            "SELECT m.id, m.title, m.author, m.available, m.image, b.page_number
            FROM media m
            JOIN book b USING(id)
            WHERE m.available = 1"
        );
        $statementGetAvailableBooks->execute();
        $booksDB = $statementGetAvailableBooks->fetchAll();
        $books = [];
        foreach ($booksDB as $book) {
            $bookInst = new Book($book['id'], $book['title'], $book['author'], $book['available'], $book['image'], $book['page_number']);
            array_push($books, $bookInst);
        }

        return $books;
    }

        public static function searchBooks(array $books, string $search): array
    {
        $search = mb_strtolower(trim($search));

        return array_values(
            array_filter($books, function (Book $books) use ($search) {
                $title  = mb_strtolower($books->getTitle() ?? '');
                $author = mb_strtolower($books->getAuthor() ?? '');

                return str_contains($title, $search) ||
                    str_contains($author, $search);
            })
        );
    }


}
