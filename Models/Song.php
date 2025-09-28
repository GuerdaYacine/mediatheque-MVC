<?php

namespace Models;

use \Database\Database;

class Song
{
    private ?string $albumTitle;

    public function __construct(private int $id, private ?int $albumId, private string $title, private string $author, private int $available, private string $image, private int $duration, private int $note) {}

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
    public function getDuration(): int
    {
        return $this->duration;
    }
    public function setDuration(int $duration): void
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
    public function getAlbumId(): ?int
    {
        return $this->albumId;
    }
    public function setAlbumId(int $albumId): void
    {
        $this->albumId = $albumId;
    }

    public function getAlbumTitle(): ?string
    {
        return $this->albumTitle;
    }

    public function setAlbumTitle(?string $albumTitle): void
    {
        $this->albumTitle = $albumTitle;
    }

    public static function getAllSongs(): array
    {
        $db = new Database();
        $connexion = $db->connect();
        $statementReadAllSongs = $connexion->prepare(
            "SELECT s.*, a.id AS album_id, m.title AS album_title 
             FROM song s 
             LEFT JOIN album a ON s.album_id = a.id 
             LEFT JOIN media m ON a.id = m.id"
        );
        $statementReadAllSongs->execute();
        $songsDB = $statementReadAllSongs->fetchAll();
        $songs = [];
        foreach ($songsDB as $song) {
            $songInst = new Song($song['id'], $song['album_id'], $song['title'], $song['author'], $song['available'], $song['image'], $song['duration'], $song['note']);
            $songInst->setAlbumTitle($song['album_title'] ?? null);
            array_push($songs, $songInst);
        }
        return $songs;
    }

    public static function createSong(?int $albumId, string $title, string $author, int $available, string $image, int $duration, int $note): bool
    {
        $db = new Database();
        $connexion = $db->connect();

        $statementCreateSong = $connexion->prepare(
            "INSERT INTO song (album_id, title, author, available, image, duration, note)
             VALUES (:album_id, :title, :author, :available, :image, :duration, :note)"
        );

        $statementCreateSong->bindParam(':album_id', $albumId);
        $statementCreateSong->bindParam(':title', $title);
        $statementCreateSong->bindParam(':author', $author);
        $statementCreateSong->bindParam(':available', $available);
        $statementCreateSong->bindParam(':image', $image);
        $statementCreateSong->bindParam(':duration', $duration);
        $statementCreateSong->bindParam(':note', $note);

        return $statementCreateSong->execute();
    }

    public static function updateSong(int $id, ?int $albumId, string $title, string $author, int $available, string $image, int $duration, int $note): bool
    {
        $db = new Database();
        $connexion = $db->connect();

        $statementUpdateSong = $connexion->prepare(
            "UPDATE song
          SET album_id = :album_id, title = :title, author = :author, available = :available, image = :image, duration = :duration, note = :note
          WHERE id = :id"
        );

        $statementUpdateSong->bindParam(':id', $id);
        $statementUpdateSong->bindParam(':album_id', $albumId);
        $statementUpdateSong->bindParam(':title', $title);
        $statementUpdateSong->bindParam(':author', $author);
        $statementUpdateSong->bindParam(':available', $available);
        $statementUpdateSong->bindParam(':image', $image);
        $statementUpdateSong->bindParam(':duration', $duration);
        $statementUpdateSong->bindParam(':note', $note);

        return $statementUpdateSong->execute();
    }

    public static function getOneSong(int $id): Song
    {
        $db = new Database();
        $connexion = $db->connect();

        $statementReadOneSong = $connexion->prepare(
            "SELECT s.*, a.id AS album_id, m.title AS album_title 
             FROM song s 
             LEFT JOIN album a ON s.album_id = a.id 
             LEFT JOIN media m ON a.id = m.id 
             WHERE s.id = :id"
        );

        $statementReadOneSong->bindParam(':id', $id);
        $statementReadOneSong->execute();
        $song = $statementReadOneSong->fetch();

        $song = new Song($song['id'], $song['album_id'], $song['title'], $song['author'], $song['available'], $song['image'], $song['duration'], $song['note']);

        return $song;
    }

    public static function deleteSong(int $id): bool
    {
        $db = new Database();
        $connexion = $db->connect();

        $statementDeleteSong = $connexion->prepare(
            "DELETE FROM song
          WHERE id = :id"
        );
        $statementDeleteSong->bindParam(':id', $id);
        $success = $statementDeleteSong->execute();
        return $success;
    }

    public static function getAvailableSongs(): array
    {
        $db = new Database();
        $connexion = $db->connect();
        $stmt = $connexion->prepare(
            "SELECT s.*, a.id AS album_id, m.title AS album_title 
            FROM song s
            LEFT JOIN album a ON s.album_id = a.id 
            LEFT JOIN media m ON a.id = m.id
            WHERE s.available = 1"
        );
        $stmt->execute();
        $songsDB = $stmt->fetchAll();

        $songs = [];
        foreach ($songsDB as $song) {
            $songInst = new Song($song['id'], $song['album_id'], $song['title'], $song['author'], $song['available'], $song['image'], $song['duration'], $song['note']);
            $songInst->setAlbumTitle($song['album_title'] ?? null);
            array_push($songs, $songInst);
        }

        return $songs;
    }

    public static function searchSongs(array $songs, string $search): array
    {
        $search = mb_strtolower(trim($search));
        $tolerance = 2;

        return array_values(
            array_filter($songs, function (Song $song) use ($search, $tolerance) {
                $title  = mb_strtolower($song->getTitle() ?? '');
                $author = mb_strtolower($song->getAuthor() ?? '');
                $album  = mb_strtolower($song->getAlbumTitle() ?? '');

                return str_contains($title, $search) ||
                    str_contains($author, $search) ||
                    str_contains($album, $search);
            })
        );
    }

}
