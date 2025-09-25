<?php

namespace Models;

use Database;
use PDOStatement;
use PDO;

enum Genre: string
{
    case Action = "Action";
    case Comedie = "Comedie";
    case Drame = "Drame";
    case Horreur = "Horreur";
    case Autre = "Autre";
}

class Movie extends Media
{
    private int $duration;
    private Genre $genre;

    private PDOStatement $statementReadOneMovie;
    private PDOStatement $statementReadAllMovie;
    private PDOStatement $statementUpdateMovie;
    private PDOStatement $statementDeleteMovie;
    private PDOStatement $statementCreateMovie;
    private PDOStatement $statementCreateMovieIntoMovie;
    private PDO $pdo;

    public function __construct(Database $db)
    {
        $this->pdo = $db->connection;

        $this->statementReadAllMovie = $this->pdo->prepare(
            "SELECT m.id, m.title, m.author, m.available, m.image, mo.duration, mo.genre
            FROM media m
            JOIN movie mo USING(id)
            WHERE m.media_type = 'movie'"
        );

        $this->statementReadOneMovie = $this->pdo->prepare(
            "SELECT m.id, m.title, m.author, m.available, m.image, mo.duration, mo.genre
            FROM media m
            JOIN movie mo USING(id)
            WHERE m.id = :id AND m.media_type = 'movie'"
        );

        $this->statementUpdateMovie = $this->pdo->prepare(
            "UPDATE media m
            JOIN movie mo USING(id)
            SET m.title = :title, m.author = :author, m.available = :available, m.image = :image, mo.duration = :duration, mo.genre = :genre
            WHERE m.id = :id"
        );

        $this->statementDeleteMovie = $this->pdo->prepare(
            "DELETE m, mo
            FROM media m
            JOIN movie mo USING(id)
            WHERE m.id = :id"
        );

        $this->statementCreateMovie = $this->pdo->prepare(
            "INSERT INTO media (title, author, available, image, media_type) VALUES (:title, :author, :available, :image, 'movie')"
        );

        $this->statementCreateMovieIntoMovie = $this->pdo->prepare(
            "INSERT INTO movie (id, duration, genre) VALUES (:id, :duration, :genre)"
        );
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

    public function getAllMovies(): array
    {
        $this->statementReadAllMovie->execute();
        return $this->statementReadAllMovie->fetchAll();
    }

    public function getOneMovie(int $id): array|false
    {
        $this->statementReadOneMovie->bindParam(':id', $id);
        $this->statementReadOneMovie->execute();
        return $this->statementReadOneMovie->fetch();
    }

    public function updateMovie(int $id, string $title, string $author, int $available, string $image, int $duration, Genre $genre): bool
    {
        $this->statementUpdateMovie->bindParam(':id', $id);
        $this->statementUpdateMovie->bindParam(':title', $title);
        $this->statementUpdateMovie->bindParam(':author', $author);
        $this->statementUpdateMovie->bindParam(':available', $available);
        $this->statementUpdateMovie->bindParam(':image', $image);
        $this->statementUpdateMovie->bindParam(':duration', $duration);
        $this->statementUpdateMovie->bindValue(':genre', $genre->value);
        return $this->statementUpdateMovie->execute();
    }

    public function deleteMovie(int $id): bool
    {
        $this->statementDeleteMovie->bindParam(':id', $id);
        return $this->statementDeleteMovie->execute();
    }

    public function createMovie(string $title, string $author, int $available, string $image, int $duration, Genre $genre): bool
    {
        $this->statementCreateMovie->bindParam(':title', $title);
        $this->statementCreateMovie->bindParam(':author', $author);
        $this->statementCreateMovie->bindParam(':available', $available);
        $this->statementCreateMovie->bindParam(':image', $image);

        $success = $this->statementCreateMovie->execute();
        if (!$success) return false;

        $id = $this->pdo->lastInsertId();

        $this->statementCreateMovieIntoMovie->bindParam(':id', $id);
        $this->statementCreateMovieIntoMovie->bindParam(':duration', $duration);
        $this->statementCreateMovieIntoMovie->bindValue(':genre', $genre->value);

        return $this->statementCreateMovieIntoMovie->execute();
    }
}
