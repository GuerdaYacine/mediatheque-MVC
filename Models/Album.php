<?php

namespace Models;

use Database;
use PDOStatement;
use PDO;

class Album extends Media
{
    private string $editor;
    private array $songs = [];
    private PDO $pdo;

    private PDOStatement $statementReadOneAlbum;
    private PDOStatement $statementReadAllAlbums;
    private PDOStatement $statementUpdateAlbum;
    private PDOStatement $statementDeleteAlbum;
    private PDOStatement $statementCreateAlbum;
    private PDOStatement $statementCreateAlbumIntoAlbum;
    private PDOStatement $statementCountTracks;

    public function __construct(Database $db)
    {
        $this->pdo = $db->connection;

        $this->statementReadAllAlbums = $this->pdo->prepare(
            "SELECT m.id, m.title, m.author, m.available, m.image, a.editor
             FROM media m
             JOIN album a USING(id)
             WHERE m.media_type = 'album'"
        );

        $this->statementReadOneAlbum = $this->pdo->prepare(
            "SELECT m.id, m.title, m.author, m.available, m.image, a.editor
             FROM media m
             JOIN album a USING(id)
             WHERE m.id = :id AND m.media_type = 'album'"
        );

        $this->statementUpdateAlbum = $this->pdo->prepare(
            "UPDATE media m
             JOIN album a USING(id)
             SET m.title = :title, m.author = :author, m.available = :available, m.image = :image, a.editor = :editor
             WHERE m.id = :id"
        );

        $this->statementDeleteAlbum = $this->pdo->prepare(
            "DELETE m, a
             FROM media m
             JOIN album a USING(id)
             WHERE m.id = :id"
        );

        $this->statementCreateAlbum = $this->pdo->prepare(
            "INSERT INTO media (title, author, available, image, media_type) VALUES (:title, :author, :available, :image, 'album')"
        );

        $this->statementCreateAlbumIntoAlbum = $this->pdo->prepare(
            "INSERT INTO album (id, editor) VALUES (:id, :editor)"
        );

        $this->statementCountTracks = $this->pdo->prepare(
            "SELECT COUNT(*) FROM song WHERE album_id = :id"
        );
    }

    public function getEditor(): string
    {
        return $this->editor;
    }

    public function setEditor(string $editor): void
    {
        $this->editor = $editor;
    }

    public function addSong(Song $song): void
    {
        $this->songs[] = $song;
    }

    public function getSongs(): array
    {
        return $this->songs;
    }

    public function getTrackNumber(int $albumId): int
    {
        $this->statementCountTracks->bindParam(':id', $albumId);
        $this->statementCountTracks->execute();
        return (int)$this->statementCountTracks->fetchColumn();
    }

    public function getAllAlbums(): array
    {
        $this->statementReadAllAlbums->execute();
        return $this->statementReadAllAlbums->fetchAll();
    }

    public function getOneAlbum(int $id): array|false
    {
        $this->statementReadOneAlbum->bindParam(':id', $id);
        $this->statementReadOneAlbum->execute();
        return $this->statementReadOneAlbum->fetch();
    }

    public function updateAlbum(int $id, string $title, string $author, string $editor, int $available, string $image): bool
    {
        $this->statementUpdateAlbum->bindParam(':id', $id);
        $this->statementUpdateAlbum->bindParam(':title', $title);
        $this->statementUpdateAlbum->bindParam(':author', $author);
        $this->statementUpdateAlbum->bindParam(':editor', $editor);
        $this->statementUpdateAlbum->bindParam(':available', $available);
        $this->statementUpdateAlbum->bindParam(':image', $image);
        return $this->statementUpdateAlbum->execute();
    }

    public function deleteAlbum(int $id): bool
    {
        $this->statementDeleteAlbum->bindParam(':id', $id);
        return $this->statementDeleteAlbum->execute();
    }

    public function createAlbum(string $title, string $author, string $editor, int $available, string $image): bool
    {
        $this->statementCreateAlbum->bindParam(':title', $title);
        $this->statementCreateAlbum->bindParam(':author', $author);
        $this->statementCreateAlbum->bindParam(':available', $available);
        $this->statementCreateAlbum->bindParam(':image', $image);

        $success = $this->statementCreateAlbum->execute();
        if (!$success) return false;

        $id = $this->pdo->lastInsertId();
        $this->statementCreateAlbumIntoAlbum->bindParam(':id', $id);
        $this->statementCreateAlbumIntoAlbum->bindParam(':editor', $editor);
        return $this->statementCreateAlbumIntoAlbum->execute();
    }
}
