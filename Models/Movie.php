<?php

namespace Models;

use \Database\Database;

/**
 * Énumération Genre
 * 
 * Définit les différents genres de films disponibles dans le système.
 */
enum Genre: string
{
    case Action = "Action";
    case Comedie = "Comédie";
    case Drame = "Drame";
    case Horreur = "Horreur";
    case Autre = "Autre";
}

/**
 * Classe Movie
 * 
 * Représente un film héritant de la classe Media.
 * Ajoute les propriétés spécifiques aux films comme la durée et le genre.
 */
class Movie extends Media
{
    /**
     * Constructeur de la classe Movie
     * 
     * @param int $id Identifiant unique du film
     * @param string $title Titre du film
     * @param string $author Réalisateur ou auteur du film
     * @param int $available Statut de disponibilité (0 ou 1)
     * @param string $image Chemin vers l'image du film
     * @param int $duration Durée du film en minutes
     * @param Genre $genre Genre du film
     */
    public function __construct(int $id, string $title, string $author, int $available, string $image, private int $duration, private Genre $genre)
    {
        parent::__construct($id, $title, $author, $available, $image);
    }

    /**
     * Récupère la durée du film
     * 
     * @return int La durée en minutes
     */
    public function getDuration(): int
    {
        return $this->duration;
    }

    /**
     * Définit la durée du film
     * 
     * @param int $duration La nouvelle durée en minutes
     * @return void
     */
    public function setDuration(int $duration): void
    {
        $this->duration = $duration;
    }

    /**
     * Récupère le genre du film
     * 
     * @return Genre Le genre du film
     */
    public function getGenre(): Genre
    {
        return $this->genre;
    }

    /**
     * Définit le genre du film
     * 
     * @param Genre $genre Le nouveau genre
     * @return void
     */
    public function setGenre(Genre $genre): void
    {
        $this->genre = $genre;
    }

    /**
     * Récupère tous les films de la base de données
     * 
     * @return array Tableau d'instances Movie
     */
    public static function getAllMovies(): array
    {
        $db = new Database();
        $connexion = $db->connect();
        $statementReadAllMovies = $connexion->prepare(
            "SELECT m.id, m.title, m.author, m.available, m.image, mo.duration, mo.genre
            FROM media m
            JOIN movie mo USING(id)"
        );
        $statementReadAllMovies->execute();
        $moviesDB = $statementReadAllMovies->fetchAll();
        $movies = [];
        foreach ($moviesDB as $movie) {
            $movieInst = new Movie($movie['id'], $movie['title'], $movie['author'], $movie['available'], $movie['image'], $movie['duration'], Genre::from($movie['genre']));
            array_push($movies, $movieInst);
        }

        return $movies;
    }

    /**
     * Crée un nouveau film dans la base de données
     * 
     * Insère d'abord dans la table media, puis dans la table movie
     * avec l'ID généré automatiquement.
     * 
     * @param string $title Le titre du film
     * @param string $author Le réalisateur du film
     * @param int $available Le statut de disponibilité
     * @param string $image Le chemin de l'image
     * @param int $duration La durée en minutes
     * @param Genre $genre Le genre du film
     * @return bool True si la création réussit, false sinon
     */
    public static function createMovie(string $title, string $author, int $available, string $image, int $duration, Genre $genre): bool
    {
        $db = new Database();
        $connexion = $db->connect();

        $statementCreateMovie = $connexion->prepare(
            "INSERT INTO media (title, author, available, image) 
            VALUES (:title, :author, :available, :image)"
        );

        $statementCreateMovieIntoMovie = $connexion->prepare(
            "INSERT INTO movie (id, duration, genre) 
            VALUES (:id, :duration, :genre)"
        );

        $statementCreateMovie->bindParam(':title', $title);
        $statementCreateMovie->bindParam(':author', $author);
        $statementCreateMovie->bindParam(':available', $available);
        $statementCreateMovie->bindParam(':image', $image);

        $success = $statementCreateMovie->execute();
        if (!$success) {
            return false;
        }

        $id = $connexion->lastInsertId();

        $statementCreateMovieIntoMovie->bindParam(':id', $id);
        $statementCreateMovieIntoMovie->bindParam(':duration', $duration);
        $statementCreateMovieIntoMovie->bindValue(':genre', $genre->value);

        return $statementCreateMovieIntoMovie->execute();
    }

    /**
     * Met à jour un film existant
     * 
     * Met à jour les données dans les tables media et movie simultanément.
     * 
     * @param int $id L'identifiant du film à modifier
     * @param string $title Le nouveau titre
     * @param string $author Le nouveau réalisateur
     * @param int $available Le nouveau statut de disponibilité
     * @param string $image Le nouveau chemin d'image
     * @param int $duration La nouvelle durée
     * @param Genre $genre Le nouveau genre
     * @return bool True si la mise à jour réussit, false sinon
     */
    public static function updateMovie(int $id, string $title, string $author, int $available, string $image, int $duration, Genre $genre): bool
    {
        $db = new Database();
        $connexion = $db->connect();

        $statementUpdateMovie = $connexion->prepare(
            "UPDATE media m
            JOIN movie mo USING(id)
            SET m.title = :title, m.author = :author, m.available = :available, m.image = :image, mo.duration = :duration, mo.genre = :genre
            WHERE m.id = :id"
        );

        $statementUpdateMovie->bindParam(':id', $id);
        $statementUpdateMovie->bindParam(':title', $title);
        $statementUpdateMovie->bindParam(':author', $author);
        $statementUpdateMovie->bindParam(':available', $available);
        $statementUpdateMovie->bindParam(':image', $image);
        $statementUpdateMovie->bindParam(':duration', $duration);
        $statementUpdateMovie->bindValue(':genre', $genre->value);

        return $statementUpdateMovie->execute();
    }

    /**
     * Récupère un film par son identifiant
     * 
     * @param int $id L'identifiant du film
     * @return Movie L'instance Movie correspondante
     */
    public static function getOneMovie(int $id): Movie
    {
        $db = new Database();
        $connexion = $db->connect();

        $statementReadOneMovie = $connexion->prepare(
            "SELECT m.id, m.title, m.author, m.available, m.image, mo.duration, mo.genre
            FROM media m
            JOIN movie mo USING(id)
            WHERE m.id = :id"
        );

        $statementReadOneMovie->bindParam(':id', $id);
        $statementReadOneMovie->execute();
        $movie = $statementReadOneMovie->fetch();

        $movie = new Movie($movie['id'], $movie['title'], $movie['author'], $movie['available'], $movie['image'], $movie['duration'], Genre::from($movie['genre']));

        return $movie;
    }

    /**
     * Supprime un film de la base de données
     * 
     * Supprime simultanément les données des tables media et movie.
     * 
     * @param int $id L'identifiant du film à supprimer
     * @return bool True si la suppression réussit, false sinon
     */
    public static function deleteMovie(int $id): bool
    {
        $db = new Database();
        $connexion = $db->connect();

        $statementDeleteMovie = $connexion->prepare(
            "DELETE m, mo
            FROM media m
            JOIN movie mo USING(id)
            WHERE m.id = :id"
        );
        $statementDeleteMovie->bindParam(':id', $id);
        $success = $statementDeleteMovie->execute();
        return $success;
    }

    /**
     * Récupère trois films disponibles de manière aléatoire
     * 
     * @return array Tableau de 3 instances Movie maximum, sélectionnées aléatoirement
     */
    public static function getThreeAvailableRandomMovies(): array
    {
        $db = new Database();
        $connexion = $db->connect();

        $statementGetThreeAvailableRandomMovies = $connexion->prepare(
            "SELECT m.id, m.title, m.author, m.available, m.image, mo.duration, mo.genre
            FROM media m
            JOIN movie mo USING(id)
            WHERE m.available = 1
            ORDER BY RAND()
            LIMIT 3
            "
        );
        $statementGetThreeAvailableRandomMovies->execute();
        $moviesDB = $statementGetThreeAvailableRandomMovies->fetchAll();
        $movies = [];
        foreach ($moviesDB as $movie) {
            $movieInst = new Movie($movie['id'], $movie['title'], $movie['author'], $movie['available'], $movie['image'], $movie['duration'], Genre::from($movie['genre']));
            array_push($movies, $movieInst);
        }

        return $movies;
    }

    /**
     * Récupère tous les films disponibles
     * 
     * @return array Tableau d'instances Movie avec available = 1
     */
    public static function getAvailableMovies()
    {;
        $db = new Database();
        $connexion = $db->connect();

        $statementGetAvailableRandomMovies = $connexion->prepare(
            "SELECT m.id, m.title, m.author, m.available, m.image, mo.duration, mo.genre
            FROM media m
            JOIN movie mo USING(id)
            WHERE m.available = 1"
        );
        $statementGetAvailableRandomMovies->execute();
        $moviesDB = $statementGetAvailableRandomMovies->fetchAll();
        $movies = [];
        foreach ($moviesDB as $movie) {
            $movieInst = new Movie($movie['id'], $movie['title'], $movie['author'], $movie['available'], $movie['image'], $movie['duration'], Genre::from($movie['genre']));
            array_push($movies, $movieInst);
        }

        return $movies;
    }

    /**
     * Recherche des films dans un tableau selon un terme de recherche
     * 
     * Effectue une recherche dans le titre, l'auteur et le genre du film.
     * 
     * @param array $movies Tableau d'instances Movie à filtrer
     * @param string $search Terme de recherche
     * @return array Tableau filtré d'instances Movie correspondant à la recherche
     */
    public static function searchMovies(array $movies, string $search): array
    {
        $search = mb_strtolower(trim($search));

        return array_values(
            array_filter($movies, function (Movie $movie) use ($search) {
                $title  = mb_strtolower($movie->getTitle() ?? '');
                $author = mb_strtolower($movie->getAuthor() ?? '');
                $genre  = mb_strtolower($movie->getGenre()->value ?? '');

                return str_contains($title, $search) ||
                    str_contains($author, $search) ||
                    str_contains($genre, $search);
            })
        );
    }
}
