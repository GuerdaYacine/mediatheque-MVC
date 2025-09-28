<?php

namespace Models;

use \Database\Database;

/**
 * Enum Genre
 *
 * Représente les différents genres possibles pour un film.
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
 * Class Movie
 *
 * Représente un film dans la médiathèque.
 * Hérite de la classe Media et ajoute des propriétés spécifiques comme la durée et le genre.
 */
class Movie extends Media
{
    /**
     * Constructeur de la classe Movie
     *
     * @param int $id Identifiant unique du film
     * @param string $title Titre du film
     * @param string $author Réalisateur ou auteur
     * @param int $available Disponibilité (1 = disponible, 0 = emprunté)
     * @param string $image Nom du fichier image associé
     * @param int $duration Durée du film en minutes
     * @param Genre $genre Genre du film
     */
    public function __construct(
        int $id,
        string $title,
        string $author,
        int $available,
        string $image,
        private int $duration,
        private Genre $genre
    ) {
        parent::__construct($id, $title, $author, $available, $image);
    }

    /**
     * Retourne la durée du film
     *
     * @return int
     */
    public function getDuration(): int
    {
        return $this->duration;
    }

    /**
     * Modifie la durée du film
     *
     * @param int $duration
     */
    public function setDuration(int $duration): void
    {
        $this->duration = $duration;
    }

    /**
     * Retourne le genre du film
     *
     * @return Genre
     */
    public function getGenre(): Genre
    {
        return $this->genre;
    }

    /**
     * Modifie le genre du film
     *
     * @param Genre $genre
     */
    public function setGenre(Genre $genre): void
    {
        $this->genre = $genre;
    }

    /**
     * Récupère tous les films de la base de données
     *
     * @return Movie[] Tableau d'objets Movie
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
            $movies[] = new Movie(
                $movie['id'],
                $movie['title'],
                $movie['author'],
                $movie['available'],
                $movie['image'],
                $movie['duration'],
                Genre::from($movie['genre'])
            );
        }

        return $movies;
    }

    /**
     * Crée un nouveau film dans la base de données
     *
     * @param string $title
     * @param string $author
     * @param int $available
     * @param string $image
     * @param int $duration
     * @param Genre $genre
     * @return bool True si l'insertion a réussi, false sinon
     */
    public static function createMovie(
        string $title,
        string $author,
        int $available,
        string $image,
        int $duration,
        Genre $genre
    ): bool {
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
        if (!$success) return false;

        $id = $connexion->lastInsertId();
        $statementCreateMovieIntoMovie->bindParam(':id', $id);
        $statementCreateMovieIntoMovie->bindParam(':duration', $duration);
        $statementCreateMovieIntoMovie->bindValue(':genre', $genre->value);

        return $statementCreateMovieIntoMovie->execute();
    }

    /**
     * Met à jour un film existant dans la base de données
     *
     * @param int $id
     * @param string $title
     * @param string $author
     * @param int $available
     * @param string $image
     * @param int $duration
     * @param Genre $genre
     * @return bool True si la mise à jour a réussi, false sinon
     */
    public static function updateMovie(
        int $id,
        string $title,
        string $author,
        int $available,
        string $image,
        int $duration,
        Genre $genre
    ): bool {
        $db = new Database();
        $connexion = $db->connect();

        $statementUpdateMovie = $connexion->prepare(
            "UPDATE media m
             JOIN movie mo USING(id)
             SET m.title = :title, m.author = :author, m.available = :available, m.image = :image,
                 mo.duration = :duration, mo.genre = :genre
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
     * Récupère un film spécifique par son identifiant
     *
     * @param int $id
     * @return Movie
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

        return new Movie(
            $movie['id'],
            $movie['title'],
            $movie['author'],
            $movie['available'],
            $movie['image'],
            $movie['duration'],
            Genre::from($movie['genre'])
        );
    }

    /**
     * Supprime un film et son entrée associée dans movie
     *
     * @param int $id
     * @return bool True si la suppression a réussi, false sinon
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

        return $statementDeleteMovie->execute();
    }

    /**
     * Récupère 3 films disponibles aléatoires
     *
     * @return Movie[] Tableau de 3 objets Movie
     */
    public static function getThreeAvailableRandomMovies(): array
    {
        $db = new Database();
        $connexion = $db->connect();

        $statement = $connexion->prepare(
            "SELECT m.id, m.title, m.author, m.available, m.image, mo.duration, mo.genre
             FROM media m
             JOIN movie mo USING(id)
             WHERE m.available = 1
             ORDER BY RAND()
             LIMIT 3"
        );
        $statement->execute();
        $moviesDB = $statement->fetchAll();

        $movies = [];
        foreach ($moviesDB as $movie) {
            $movies[] = new Movie(
                $movie['id'],
                $movie['title'],
                $movie['author'],
                $movie['available'],
                $movie['image'],
                $movie['duration'],
                Genre::from($movie['genre'])
            );
        }

        return $movies;
    }

    /**
     * Récupère tous les films disponibles
     *
     * @return Movie[]
     */
    public static function getAvailableMovies(): array
    {
        $db = new Database();
        $connexion = $db->connect();

        $statement = $connexion->prepare(
            "SELECT m.id, m.title, m.author, m.available, m.image, mo.duration, mo.genre
             FROM media m
             JOIN movie mo USING(id)
             WHERE m.available = 1"
        );
        $statement->execute();
        $moviesDB = $statement->fetchAll();

        $movies = [];
        foreach ($moviesDB as $movie) {
            $movies[] = new Movie(
                $movie['id'],
                $movie['title'],
                $movie['author'],
                $movie['available'],
                $movie['image'],
                $movie['duration'],
                Genre::from($movie['genre'])
            );
        }

        return $movies;
    }

    /**
     * Filtre un tableau de films en fonction d'une recherche sur le titre, l'auteur ou le genre
     *
     * @param Movie[] $movies
     * @param string $search
     * @return Movie[] Tableau filtré
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
