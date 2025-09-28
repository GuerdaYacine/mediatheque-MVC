<?php

namespace Models;

use \Database\Database;

enum Genre: string
{
    case Action = "Action";
    case Comedie = "Comédie";
    case Drame = "Drame";
    case Horreur = "Horreur";
    case Autre = "Autre";
}

class Movie extends Media
{

    public function __construct(int $id, string $title, string $author, int $available, string $image, private int $duration, private Genre $genre)
    {
        parent::__construct($id, $title, $author, $available, $image);
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): void
    {
        $this->duration = $duration;
    }

    public function getGenre(): Genre
    {
        return $this->genre;
    }

    public function setGenre(Genre $genre): void
    {
        $this->genre = $genre;
    }

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

    public static function getAvailableMovies(){;
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



    // public function getAvailableMovies(?string $search = null): array
    // {
    //     if ($search) {
    //         $this->statementSearchMovies->execute(['search' => '%' . $search . '%']);
    //         return array_filter(
    //             $this->statementSearchMovies->fetchAll(),
    //             fn($movie) => $movie['available'] == 1
    //         );
    //     } else {
    //         $this->statementGetAvailableMovies->execute();
    //         return $this->statementGetAvailableMovies->fetchAll();
    //     }
    // }


    // public function searchMovies(string $search): array
    // {
    //     $this->statementSearchMovies->execute(['search' => '%' . $search . '%']);
    //     return $this->statementSearchMovies->fetchAll();
    // }
}
