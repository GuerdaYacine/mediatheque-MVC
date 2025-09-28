<?php

namespace Models;

use \Database\Database;

/**
 * Class Song
 *
 * Représente une chanson dans la médiathèque.
 * Chaque chanson peut appartenir à un album et possède des propriétés spécifiques comme la durée et la note.
 */
class Song
{
    /** @var ?string Titre de l'album associé (optionnel) */
    private ?string $albumTitle;

    /**
     * Constructeur de la classe Song
     *
     * @param int $id Identifiant unique de la chanson
     * @param ?int $albumId Identifiant de l'album associé (nullable)
     * @param string $title Titre de la chanson
     * @param string $author Auteur ou artiste de la chanson
     * @param int $available Disponibilité (1 = disponible, 0 = emprunté)
     * @param string $image Nom du fichier image associé
     * @param int $duration Durée de la chanson en secondes
     * @param int $note Note attribuée à la chanson (0 à 5)
     */
    public function __construct(
        private int $id,
        private ?int $albumId,
        private string $title,
        private string $author,
        private int $available,
        private string $image,
        private int $duration,
        private int $note
    ) {}

    // ------------------- Getters et Setters -------------------

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

    // ------------------- Méthodes statiques -------------------

    /**
     * Récupère toutes les chansons de la base de données
     *
     * @return Song[] Tableau d'objets Song
     */
    public static function getAllSongs(): array
    {
        $db = new Database();
        $connexion = $db->connect();
        $stmt = $connexion->prepare(
            "SELECT s.*, a.id AS album_id, m.title AS album_title 
             FROM song s 
             LEFT JOIN album a ON s.album_id = a.id 
             LEFT JOIN media m ON a.id = m.id"
        );
        $stmt->execute();
        $songsDB = $stmt->fetchAll();

        $songs = [];
        foreach ($songsDB as $song) {
            $songInst = new Song(
                $song['id'],
                $song['album_id'],
                $song['title'],
                $song['author'],
                $song['available'],
                $song['image'],
                $song['duration'],
                $song['note']
            );
            $songInst->setAlbumTitle($song['album_title'] ?? null);
            $songs[] = $songInst;
        }

        return $songs;
    }

    /**
     * Crée une nouvelle chanson dans la base de données
     *
     * @param ?int $albumId
     * @param string $title
     * @param string $author
     * @param int $available
     * @param string $image
     * @param int $duration
     * @param int $note
     * @return bool True si l'insertion a réussi, false sinon
     */
    public static function createSong(?int $albumId, string $title, string $author, int $available, string $image, int $duration, int $note): bool
    {
        $db = new Database();
        $connexion = $db->connect();

        $stmt = $connexion->prepare(
            "INSERT INTO song (album_id, title, author, available, image, duration, note)
             VALUES (:album_id, :title, :author, :available, :image, :duration, :note)"
        );

        $stmt->bindParam(':album_id', $albumId);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':author', $author);
        $stmt->bindParam(':available', $available);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':duration', $duration);
        $stmt->bindParam(':note', $note);

        return $stmt->execute();
    }

    /**
     * Met à jour une chanson existante
     *
     * @param int $id
     * @param ?int $albumId
     * @param string $title
     * @param string $author
     * @param int $available
     * @param string $image
     * @param int $duration
     * @param int $note
     * @return bool
     */
    public static function updateSong(int $id, ?int $albumId, string $title, string $author, int $available, string $image, int $duration, int $note): bool
    {
        $db = new Database();
        $connexion = $db->connect();

        $stmt = $connexion->prepare(
            "UPDATE song
             SET album_id = :album_id, title = :title, author = :author, available = :available, image = :image, duration = :duration, note = :note
             WHERE id = :id"
        );

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':album_id', $albumId);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':author', $author);
        $stmt->bindParam(':available', $available);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':duration', $duration);
        $stmt->bindParam(':note', $note);

        return $stmt->execute();
    }

    /**
     * Récupère une chanson spécifique par son identifiant
     *
     * @param int $id
     * @return Song
     */
    public static function getOneSong(int $id): Song
    {
        $db = new Database();
        $connexion = $db->connect();

        $stmt = $connexion->prepare(
            "SELECT s.*, a.id AS album_id, m.title AS album_title 
             FROM song s 
             LEFT JOIN album a ON s.album_id = a.id 
             LEFT JOIN media m ON a.id = m.id 
             WHERE s.id = :id"
        );
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $song = $stmt->fetch();

        $songObj = new Song(
            $song['id'],
            $song['album_id'],
            $song['title'],
            $song['author'],
            $song['available'],
            $song['image'],
            $song['duration'],
            $song['note']
        );
        $songObj->setAlbumTitle($song['album_title'] ?? null);

        return $songObj;
    }

    /**
     * Supprime une chanson
     *
     * @param int $id
     * @return bool
     */
    public static function deleteSong(int $id): bool
    {
        $db = new Database();
        $connexion = $db->connect();

        $stmt = $connexion->prepare(
            "DELETE FROM song WHERE id = :id"
        );
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    /**
     * Récupère toutes les chansons disponibles
     *
     * @return Song[]
     */
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
            $songInst = new Song(
                $song['id'],
                $song['album_id'],
                $song['title'],
                $song['author'],
                $song['available'],
                $song['image'],
                $song['duration'],
                $song['note']
            );
            $songInst->setAlbumTitle($song['album_title'] ?? null);
            $songs[] = $songInst;
        }

        return $songs;
    }

    /**
     * Recherche dans un tableau de chansons selon le titre, l'auteur ou le nom de l'album
     *
     * @param Song[] $songs
     * @param string $search
     * @return Song[] Tableau filtré
     */
    public static function searchSongs(array $songs, string $search): array
    {
        $search = mb_strtolower(trim($search));

        return array_values(
            array_filter($songs, function (Song $song) use ($search) {
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
