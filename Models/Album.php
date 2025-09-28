<?php

namespace Models;

use \Database\Database;

class Album extends Media
{
    private ?int $trackNumber = null;

    public function __construct(int $id, string $title, string $author, int $available, string $image, private string $editor)
    {
        parent::__construct($id, $title, $author, $available, $image);
    }

    public function getEditor(): string
    {
        return $this->editor;
    }

    public function setEditor(string $editor): void
    {
        $this->editor = $editor;
    }

    public static function getTrackNumber(int $albumId): int
    {
        $db = new Database();
        $connexion = $db->connect();
        $statementCountTracks = $connexion->prepare(
            "SELECT COUNT(*) 
         FROM song 
         WHERE album_id = :id AND available = 1"
        );
        $statementCountTracks->bindParam(':id', $albumId);
        $statementCountTracks->execute();
        return (int) $statementCountTracks->fetchColumn();
    }

    public function setTrackNumber(int $trackNumber): void
    {
        $this->trackNumber = $trackNumber;
    }


    public static function getAllAlbums(): array
    {
        $db = new Database();
        $connexion = $db->connect();
        $statementReadAllAlbums = $connexion->prepare(
            "SELECT m.id, m.title, m.author, m.available, m.image, a.editor
              FROM media m
              JOIN album a USING(id)"
        );
        $statementReadAllAlbums->execute();
        $albumsDB = $statementReadAllAlbums->fetchAll();
        $albums = [];
        foreach ($albumsDB as $album) {
            $albumInst = new Album($album['id'], $album['title'], $album['author'], $album['available'], $album['image'], $album['editor']);
            array_push($albums, $albumInst);
        }
        return $albums;
    }

    public static function createAlbum(string $title, string $author, int $available, string $image, string $editor): bool
    {
        $db = new Database();
        $connexion = $db->connect();

        $statementCreateAlbum = $connexion->prepare(
            "INSERT INTO media (title, author, available, image) 
            VALUES (:title, :author, :available, :image)"
        );

        $statementCreateAlbulIntoAlbum = $connexion->prepare(
            "INSERT INTO album (id, editor)
            VALUES (:id, :editor)"
        );

        $statementCreateAlbum->bindParam(':title', $title);
        $statementCreateAlbum->bindParam(':author', $author);
        $statementCreateAlbum->bindParam(':available', $available);
        $statementCreateAlbum->bindParam(':image', $image);

        $success = $statementCreateAlbum->execute();
        if (!$success) {
            return false;
        }

        $id = $connexion->lastInsertId();

        $statementCreateAlbulIntoAlbum->bindParam(':id', $id);
        $statementCreateAlbulIntoAlbum->bindParam(':editor', $editor);

        return $statementCreateAlbulIntoAlbum->execute();
    }

    public static function updateAlbum(int $id, string $title, string $author, int $available, string $image, string $editor): bool
    {
        $db = new Database();
        $connexion = $db->connect();

        $statementUpdateAlbum = $connexion->prepare(
            "UPDATE media m
              JOIN album a USING(id)
              SET m.title = :title, m.author = :author, m.available = :available, m.image = :image, a.editor = :editor
              WHERE m.id = :id"
        );

        $statementUpdateAlbum->bindParam(':id', $id);
        $statementUpdateAlbum->bindParam(':title', $title);
        $statementUpdateAlbum->bindParam(':author', $author);
        $statementUpdateAlbum->bindParam(':available', $available);
        $statementUpdateAlbum->bindParam(':image', $image);
        $statementUpdateAlbum->bindParam(':editor', $editor);

        return $statementUpdateAlbum->execute();
    }

    public static function getOneAlbum(int $id): Album
    {
        $db = new Database();
        $connexion = $db->connect();

        $statementReadOneAlbum = $connexion->prepare(
            "SELECT m.id, m.title, m.author, m.available, m.image, a.editor
              FROM media m
              JOIN album a USING(id)
              WHERE m.id = :id"
        );

        $statementReadOneAlbum->bindParam(':id', $id);
        $statementReadOneAlbum->execute();
        $album = $statementReadOneAlbum->fetch();

        $album = new Album($album['id'], $album['title'], $album['author'], $album['available'], $album['image'], $album['editor']);

        return $album;
    }

    public static function deleteAlbum(int $id): bool
    {
        $db = new Database();
        $connexion = $db->connect();

        $statementDeleteAlbum = $connexion->prepare(
            "DELETE m, a
            FROM media m
            JOIN album a USING(id)
            WHERE m.id = :id"
        );
        $statementDeleteAlbum->bindParam(':id', $id);
        $success = $statementDeleteAlbum->execute();
        return $success;
    }

    public static function getThreeAvailableRandomAlbums(): array
    {
        $db = new Database();
        $connexion = $db->connect();

        $statementGetThreeAvailableRandomAlbums = $connexion->prepare(
            "SELECT m.id, m.title, m.author, m.available, m.image, a.editor
             FROM media m 
             JOIN album a USING(id)
             WHERE m.available = 1
             ORDER BY RAND()
             LIMIT 3"
        );
        $statementGetThreeAvailableRandomAlbums->execute();
        $albumsDB = $statementGetThreeAvailableRandomAlbums->fetchAll();
        $albums = [];
        foreach ($albumsDB as $album) {
            $albumInst = new Album($album['id'], $album['title'], $album['author'], $album['available'], $album['image'], $album['editor']);
            array_push($albums, $albumInst);
        }

        return $albums;
    }

    public static function getAvailableAlbums(): array
    {
        $db = new Database();
        $connexion = $db->connect();

        $statementGetAvailableAlbums = $connexion->prepare(
            "SELECT m.id, m.title, m.author, m.available, m.image, a.editor
             FROM media m 
             JOIN album a USING(id)
             WHERE m.available = 1
             ORDER BY RAND()
             LIMIT 3"
        );
        $statementGetAvailableAlbums->execute();
        $albumsDB = $statementGetAvailableAlbums->fetchAll();
        $albums = [];
        foreach ($albumsDB as $album) {
            $albumInst = new Album($album['id'], $album['title'], $album['author'], $album['available'], $album['image'], $album['editor']);
            array_push($albums, $albumInst);
        }

        return $albums;
    }

    public static function searchAlbums(array $albums, string $search): array
    {
        $search = mb_strtolower(trim($search));

        return array_values(
            array_filter($albums, function (Album $album) use ($search) {
                $title  = mb_strtolower($album->getTitle() ?? '');
                $author = mb_strtolower($album->getAuthor() ?? '');

                return str_contains($title, $search) ||
                    str_contains($author, $search);
            })
        );
    }
}
