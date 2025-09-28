<?php

namespace Models;

use \Database\Database;

/**
 * Class Book
 *
 * Représente un livre dans la médiathèque.
 * Hérite de Media et ajoute la propriété pageNumber.
 */
class Book extends Media
{
    /** @var int Nombre de pages du livre */
    private int $pageNumber;

    /**
     * Constructeur de la classe Book
     *
     * @param int $id
     * @param string $title
     * @param string $author
     * @param int $available
     * @param string $image
     * @param int $pageNumber
     */
    public function __construct(int $id, string $title, string $author, int $available, string $image, int $pageNumber)
    {
        parent::__construct($id, $title, $author, $available, $image);
        $this->pageNumber = $pageNumber;
    }

    // ------------------- Getters et Setters -------------------

    public function getPageNumber(): int
    {
        return $this->pageNumber;
    }
    public function setPageNumber(int $pageNumber): void
    {
        $this->pageNumber = $pageNumber;
    }

    // ------------------- Méthodes statiques -------------------

    /** Récupère tous les livres */
    public static function getAllBooks(): array
    {
        $db = new Database();
        $connexion = $db->connect();
        $stmt = $connexion->prepare(
            "SELECT m.id, m.title, m.author, m.available, m.image, b.page_number
             FROM media m
             JOIN book b USING(id)"
        );
        $stmt->execute();
        $booksDB = $stmt->fetchAll();

        $books = [];
        foreach ($booksDB as $book) {
            $books[] = new Book(
                $book['id'],
                $book['title'],
                $book['author'],
                $book['available'],
                $book['image'],
                $book['page_number']
            );
        }

        return $books;
    }

    /** Crée un nouveau livre */
    public static function createBook(string $title, string $author, int $available, string $image, int $pageNumber): bool
    {
        $db = new Database();
        $connexion = $db->connect();

        $stmtMedia = $connexion->prepare(
            "INSERT INTO media (title, author, available, image) VALUES (:title, :author, :available, :image)"
        );
        $stmtMedia->bindParam(':title', $title);
        $stmtMedia->bindParam(':author', $author);
        $stmtMedia->bindParam(':available', $available);
        $stmtMedia->bindParam(':image', $image);

        if (!$stmtMedia->execute()) {
            return false;
        }

        $id = $connexion->lastInsertId();

        $stmtBook = $connexion->prepare(
            "INSERT INTO book (id, page_number) VALUES (:id, :page_number)"
        );
        $stmtBook->bindParam(':id', $id);
        $stmtBook->bindParam(':page_number', $pageNumber);

        return $stmtBook->execute();
    }

    /** Met à jour un livre existant */
    public static function updateBook(int $id, string $title, string $author, int $available, string $image, int $pageNumber): bool
    {
        $db = new Database();
        $connexion = $db->connect();

        $stmt = $connexion->prepare(
            "UPDATE media m
             JOIN book b USING(id)
             SET m.title = :title, m.author = :author, m.available = :available, m.image = :image, b.page_number = :page_number
             WHERE m.id = :id"
        );

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':author', $author);
        $stmt->bindParam(':available', $available);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':page_number', $pageNumber);

        return $stmt->execute();
    }

    /** Récupère un livre spécifique */
    public static function getOneBook(int $id): Book
    {
        $db = new Database();
        $connexion = $db->connect();

        $stmt = $connexion->prepare(
            "SELECT m.id, m.title, m.author, m.available, m.image, b.page_number
             FROM media m
             JOIN book b USING(id)
             WHERE m.id = :id"
        );
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $book = $stmt->fetch();

        return new Book(
            $book['id'],
            $book['title'],
            $book['author'],
            $book['available'],
            $book['image'],
            $book['page_number']
        );
    }

    /** Supprime un livre */
    public static function deleteBook(int $id): bool
    {
        $db = new Database();
        $connexion = $db->connect();

        $stmt = $connexion->prepare(
            "DELETE m, b
             FROM media m
             JOIN book b USING(id)
             WHERE m.id = :id"
        );
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    /** Récupère 3 livres disponibles aléatoires */
    public static function getThreeAvailableRandomBooks(): array
    {
        $db = new Database();
        $connexion = $db->connect();

        $stmt = $connexion->prepare(
            "SELECT m.id, m.title, m.author, m.available, m.image, b.page_number
             FROM media m
             JOIN book b USING(id)
             WHERE m.available = 1
             ORDER BY RAND()
             LIMIT 3"
        );
        $stmt->execute();
        $booksDB = $stmt->fetchAll();

        $books = [];
        foreach ($booksDB as $book) {
            $books[] = new Book(
                $book['id'],
                $book['title'],
                $book['author'],
                $book['available'],
                $book['image'],
                $book['page_number']
            );
        }

        return $books;
    }

    /** Récupère tous les livres disponibles */
    public static function getAvailableBooks(): array
    {
        $db = new Database();
        $connexion = $db->connect();

        $stmt = $connexion->prepare(
            "SELECT m.id, m.title, m.author, m.available, m.image, b.page_number
             FROM media m
             JOIN book b USING(id)
             WHERE m.available = 1"
        );
        $stmt->execute();
        $booksDB = $stmt->fetchAll();

        $books = [];
        foreach ($booksDB as $book) {
            $books[] = new Book(
                $book['id'],
                $book['title'],
                $book['author'],
                $book['available'],
                $book['image'],
                $book['page_number']
            );
        }

        return $books;
    }

    /** Recherche des livres par titre ou auteur */
    public static function searchBooks(array $books, string $search): array
    {
        $search = mb_strtolower(trim($search));

        return array_values(
            array_filter($books, function (Book $book) use ($search) {
                $title  = mb_strtolower($book->getTitle() ?? '');
                $author = mb_strtolower($book->getAuthor() ?? '');

                return str_contains($title, $search) || str_contains($author, $search);
            })
        );
    }
}
