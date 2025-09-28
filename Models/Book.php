<?php

namespace Models;

use \Database\Database;

/**
 * Classe Book
 * 
 * Représente un livre héritant de la classe Media.
 * Ajoute la propriété spécifique aux livres : le nombre de pages.
 */
class Book extends Media
{
    /**
     * Constructeur de la classe Book
     * 
     * @param int $id Identifiant unique du livre
     * @param string $title Titre du livre
     * @param string $author Auteur du livre
     * @param int $available Statut de disponibilité (0 ou 1)
     * @param string $image Chemin vers l'image du livre
     * @param int $pageNumber Nombre de pages du livre
     */
    public function __construct(int $id, string $title, string $author, int $available, string $image, private int $pageNumber)
    {
        parent::__construct($id, $title, $author, $available, $image);
    }

    /**
     * Récupère le nombre de pages du livre
     * 
     * @return int Le nombre de pages
     */
    public function getPageNumber(): int
    {
        return $this->pageNumber;
    }

    /**
     * Définit le nombre de pages du livre
     * 
     * @param int $pageNumber Le nouveau nombre de pages
     * @return void
     */
    public function setPageNumber(int $pageNumber): void
    {
        $this->pageNumber = $pageNumber;
    }

    /**
     * Récupère tous les livres de la base de données
     * 
     * @return array Tableau d'instances Book
     */
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

    /**
     * Crée un nouveau livre dans la base de données
     * 
     * Insère d'abord dans la table media, puis dans la table book
     * avec l'ID généré automatiquement.
     * 
     * @param string $title Le titre du livre
     * @param string $author L'auteur du livre
     * @param int $available Le statut de disponibilité
     * @param string $image Le chemin de l'image
     * @param int $pageNumber Le nombre de pages
     * @return bool True si la création réussit, false sinon
     */
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

    /**
     * Met à jour un livre existant
     * 
     * Met à jour les données dans les tables media et book simultanément.
     * 
     * @param int $id L'identifiant du livre à modifier
     * @param string $title Le nouveau titre
     * @param string $author Le nouvel auteur
     * @param int $available Le nouveau statut de disponibilité
     * @param string $image Le nouveau chemin d'image
     * @param int $pageNumber Le nouveau nombre de pages
     * @return bool True si la mise à jour réussit, false sinon
     */
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

    /**
     * Récupère un livre par son identifiant
     * 
     * @param int $id L'identifiant du livre
     * @return Book L'instance Book correspondante
     */
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

    /**
     * Supprime un livre de la base de données
     * 
     * Supprime simultanément les données des tables media et book.
     * 
     * @param int $id L'identifiant du livre à supprimer
     * @return bool True si la suppression réussit, false sinon
     */
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

    /**
     * Récupère trois livres disponibles de manière aléatoire
     * 
     * @return array Tableau de 3 instances Book maximum, sélectionnées aléatoirement
     */
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

    /**
     * Récupère tous les livres disponibles
     * 
     * @return array Tableau d'instances Book avec available = 1
     */
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

    /**
     * Recherche des livres dans un tableau selon un terme de recherche
     * 
     * Effectue une recherche dans le titre et l'auteur du livre.
     * 
     * @param array $books Tableau d'instances Book à filtrer
     * @param string $search Terme de recherche
     * @return array Tableau filtré d'instances Book correspondant à la recherche
     */
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
