<?php

namespace Models;

use Database;
use PDOStatement;
use PDO;

class Song
{
    private string $title;
    private float $duration;
    private int $note;
    private int $albumId;
    private PDO $pdo;

    private PDOStatement $statementReadOneSong;
    private PDOStatement $statementReadAllSongs;
    private PDOStatement $statementUpdateSong;
    private PDOStatement $statementDeleteSong;
    private PDOStatement $statementCreateSong;

    public function __construct(Database $db)
    {
        $this->pdo = $db->connection;

        $this->statementReadAllSongs = $this->pdo->prepare(
            "SELECT s.*, a.id AS album_id, m.title AS album_title 
                FROM song s 
                LEFT JOIN album a ON s.album_id = a.id 
                LEFT JOIN media m ON a.id = m.id"
        );

        $this->statementReadOneSong = $this->pdo->prepare(
            "SELECT s.*, a.id AS album_id, m.title AS album_title 
                FROM song s 
                LEFT JOIN album a ON s.album_id = a.id 
                LEFT JOIN media m ON a.id = m.id 
                WHERE s.id = :id"
        );


        $this->statementUpdateSong = $this->pdo->prepare(
            "UPDATE song
             SET album_id = :album_id, title = :title, author = :author, available = :available, image = :image, duration = :duration, note = :note
             WHERE id = :id"
        );

        $this->statementDeleteSong = $this->pdo->prepare(
            "DELETE FROM song
             WHERE id = :id"
        );

        $this->statementCreateSong = $this->pdo->prepare(
            "INSERT INTO song (album_id, title, author, available, image, duration, note)
             VALUES (:album_id, :title, :author, :available, :image, :duration, :note)"
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
    public function getDuration(): float
    {
        return $this->duration;
    }
    public function setDuration(float $duration): void
    {
        $this->duration = $duration;
    }
    public function getNote(): int
    {
        return $this->note;
    }
    public function setNote(int $note): void
    {
        $this->note = max(0, min(5, $note));
    }
    public function getAlbumId(): int
    {
        return $this->albumId;
    }
    public function setAlbumId(int $albumId): void
    {
        $this->albumId = $albumId;
    }

    public function getAllSongs(): array
    {
        $this->statementReadAllSongs->execute();
        return $this->statementReadAllSongs->fetchAll();
    }

    public function getAllSongsByAlbum(int $albumId): array
    {
        $this->statementReadAllSongs->bindParam(':album_id', $albumId);
        $this->statementReadAllSongs->execute();
        return $this->statementReadAllSongs->fetchAll();
    }

    public function getOneSong(int $id): array|false
    {
        $this->statementReadOneSong->bindParam(':id', $id);
        $this->statementReadOneSong->execute();
        return $this->statementReadOneSong->fetch();
    }

    public function updateSong(int $id, ?int $albumId, string $title, string $author, int $available, string $image, float $duration, int $note): bool
    {
        $this->statementUpdateSong->bindParam(':id', $id);
        $this->statementUpdateSong->bindParam(':album_id', $albumId);
        $this->statementUpdateSong->bindParam(':title', $title);
        $this->statementUpdateSong->bindParam(':author', $author);
        $this->statementUpdateSong->bindParam(':available', $available);
        $this->statementUpdateSong->bindParam(':image', $image);
        $this->statementUpdateSong->bindParam(':duration', $duration);
        $this->statementUpdateSong->bindParam(':note', $note);
        return $this->statementUpdateSong->execute();
    }

    public function deleteSong(int $id): bool
    {
        $this->statementDeleteSong->bindParam(':id', $id);
        return $this->statementDeleteSong->execute();
    }

    public function createSong(?int $albumId, string $title, string $author, int $available, string $image, float $duration, int $note): bool
    {
        $this->statementCreateSong->bindParam(':album_id', $albumId);
        $this->statementCreateSong->bindParam(':title', $title);
        $this->statementCreateSong->bindParam(':author', $author);
        $this->statementCreateSong->bindParam(':available', $available);
        $this->statementCreateSong->bindParam(':image', $image);
        $this->statementCreateSong->bindParam(':duration', $duration);
        $this->statementCreateSong->bindParam(':note', $note);
        return $this->statementCreateSong->execute();
    }
}
