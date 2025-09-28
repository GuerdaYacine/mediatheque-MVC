<?php

namespace Models;

use \Database\Database;

/**
 * Class Album
 *
 * Représente un album dans la médiathèque.
 * Hérite de Media et ajoute la propriété editor et le nombre de pistes.
 */
class Album extends Media
{
    /** @var string Nom de l'éditeur */
    private string $editor;

    /** @var int|null Nombre de pistes dans l'album */
    private ?int $trackNumber = null;

    /**
     * Constructeur de la classe Album
     *
     * @param int $id
     * @param string $title
     * @param string $author
     * @param int $available
     * @param string $image
     * @param string $editor
     */
    public function __construct(int $id, string $title, string $author, int $available, string $image, string $editor)
    {
        parent::__construct($id, $title, $author, $available, $image);
        $this->editor = $editor;
    }

    // ------------------- Getters et Setters -------------------

    public function getEditor(): string
    {
        return $this->editor;
    }
    public function setEditor(string $editor): void
    {
        $this->editor = $editor;
    }

    public function getTrackNumber(): ?int
    {
        return $this->trackNumber;
    }
    public function setTrackNumber(int $trackNumber): void
    {
        $this->trackNumber = $trackNumber;
    }

    /** Récupère le nombre de pistes disponibles pour un album donné */
    public static function countTracks(int $albumId): int
    {
        $db = new Database();
        $connexion = $db->connect();
        $stmt = $connexion->prepare(
            "SELECT COUNT(*) FROM song WHERE album_id = :id AND available = 1"
        );
        $stmt->bindParam(':id', $albumId);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    // ------------------- Méthodes CRUD -------------------

    /** Récupère tous les albums */
    public static function getAllAlbums(): array
    {
        $db = new Database();
        $connexion = $db->connect();

        $stmt = $connexion->prepare(
            "SELECT m.id, m.title, m.author, m.available, m.image, a.editor
             FROM media m
             JOIN album a USING(id)"
        );
        $stmt->execute();
        $albumsDB = $stmt->fetchAll();

        $albums = [];
        foreach ($albumsDB as $album) {
            $albums[] = new Album(
                $album['id'],
                $album['title'],
                $album['author'],
                $album['available'],
                $album['image'],
                $album['editor']
            );
        }

        return $albums;
    }

    /** Crée un nouvel album */
    public static function createAlbum(string $title, string $author, int $available, string $image, string $editor): bool
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

        if (!$stmtMedia->execute()) return false;

        $id = $connexion->lastInsertId();

        $stmtAlbum = $connexion->prepare(
            "INSERT INTO album (id, editor) VALUES (:id, :editor)"
        );
        $stmtAlbum->bindParam(':id', $id);
        $stmtAlbum->bindParam(':editor', $editor);

        return $stmtAlbum->execute();
    }

    /** Met à jour un album existant */
    public static function updateAlbum(int $id, string $title, string $author, int $available, string $image, string $editor): bool
    {
        $db = new Database();
        $connexion = $db->connect();

        $stmt = $connexion->prepare(
            "UPDATE media m
             JOIN album a USING(id)
             SET m.title = :title, m.author = :author, m.available = :available, m.image = :image, a.editor = :editor
             WHERE m.id = :id"
        );

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':author', $author);
        $stmt->bindParam(':available', $available);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':editor', $editor);

        return $stmt->execute();
    }

    /** Récupère un album spécifique */
    public static function getOneAlbum(int $id): Album
    {
        $db = new Database();
        $connexion = $db->connect();

        $stmt = $connexion->prepare(
            "SELECT m.id, m.title, m.author, m.available, m.image, a.editor
             FROM media m
             JOIN album a USING(id)
             WHERE m.id = :id"
        );
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $album = $stmt->fetch();

        return new Album(
            $album['id'],
            $album['title'],
            $album['author'],
            $album['available'],
            $album['image'],
            $album['editor']
        );
    }

    /** Supprime un album */
    public static function deleteAlbum(int $id): bool
    {
        $db = new Database();
        $connexion = $db->connect();

        $stmt = $connexion->prepare(
            "DELETE m, a
             FROM media m
             JOIN album a USING(id)
             WHERE m.id = :id"
        );
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    /** Récupère 3 albums disponibles aléatoires */
    public static function getThreeAvailableRandomAlbums(): array
    {
        $db = new Database();
        $connexion = $db->connect();

        $stmt = $connexion->prepare(
            "SELECT m.id, m.title, m.author, m.available, m.image, a.editor
             FROM media m
             JOIN album a USING(id)
             WHERE m.available = 1
             ORDER BY RAND()
             LIMIT 3"
        );
        $stmt->execute();
        $albumsDB = $stmt->fetchAll();

        $albums = [];
        foreach ($albumsDB as $album) {
            $albums[] = new Album(
                $album['id'],
                $album['title'],
                $album['author'],
                $album['available'],
                $album['image'],
                $album['editor']
            );
        }

        return $albums;
    }

    /** Récupère tous les albums disponibles */
    public static function getAvailableAlbums(): array
    {
        $db = new Database();
        $connexion = $db->connect();

        $stmt = $connexion->prepare(
            "SELECT m.id, m.title, m.author, m.available, m.image, a.editor
             FROM media m
             JOIN album a USING(id)
             WHERE m.available = 1"
        );
        $stmt->execute();
        $albumsDB = $stmt->fetchAll();

        $albums = [];
        foreach ($albumsDB as $album) {
            $albums[] = new Album(
                $album['id'],
                $album['title'],
                $album['author'],
                $album['available'],
                $album['image'],
                $album['editor']
            );
        }

        return $albums;
    }

    /** Recherche des albums par titre ou auteur */
    public static function searchAlbums(array $albums, string $search): array
    {
        $search = mb_strtolower(trim($search));

        return array_values(
            array_filter($albums, function (Album $album) use ($search) {
                $title  = mb_strtolower($album->getTitle() ?? '');
                $author = mb_strtolower($album->getAuthor() ?? '');

                return str_contains($title, $search) || str_contains($author, $search);
            })
        );
    }
}
