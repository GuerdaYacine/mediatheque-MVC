<?php

namespace Models;

use \Database\Database;

/**
 * Classe Song
 * 
 * Représente une chanson du système avec ses propriétés et méthodes
 * pour la gestion des données des chansons en base de données.
 */
class Song
{
    /**
     * @var string|null Titre de l'album associé à la chanson
     */
    private ?string $albumTitle;

    /**
     * Constructeur de la classe Song
     * 
     * @param int $id Identifiant unique de la chanson
     * @param int|null $albumId Identifiant de l'album associé
     * @param string $title Titre de la chanson
     * @param string $author Auteur/artiste de la chanson
     * @param int $available Statut de disponibilité (0 ou 1)
     * @param string $image Chemin vers l'image de la chanson
     * @param int $duration Durée de la chanson en secondes
     * @param int $note Note attribuée à la chanson
     */
    public function __construct(private int $id, private ?int $albumId, private string $title, private string $author, private int $available, private string $image, private int $duration, private int $note) {}

    /**
     * Récupère l'identifiant de la chanson
     * 
     * @return int L'identifiant de la chanson
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Définit l'identifiant de la chanson
     * 
     * @param int $id Le nouvel identifiant
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Récupère le titre de la chanson
     * 
     * @return string Le titre de la chanson
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Définit le titre de la chanson
     * 
     * @param string $title Le nouveau titre
     * @return void
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Récupère l'auteur de la chanson
     * 
     * @return string L'auteur de la chanson
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * Définit l'auteur de la chanson
     * 
     * @param string $author Le nouvel auteur
     * @return void
     */
    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    /**
     * Récupère le statut de disponibilité
     * 
     * @return int Le statut de disponibilité
     */
    public function getAvailable(): int
    {
        return $this->available;
    }

    /**
     * Définit le statut de disponibilité
     * 
     * @param int $available Le nouveau statut de disponibilité
     * @return void
     */
    public function setAvailable(int $available): void
    {
        $this->available = $available;
    }

    /**
     * Récupère le chemin de l'image
     * 
     * @return string Le chemin vers l'image
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * Définit le chemin de l'image
     * 
     * @param string $image Le nouveau chemin de l'image
     * @return void
     */
    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    /**
     * Récupère la durée de la chanson
     * 
     * @return int La durée en secondes
     */
    public function getDuration(): int
    {
        return $this->duration;
    }

    /**
     * Définit la durée de la chanson
     * 
     * @param int $duration La nouvelle durée en secondes
     * @return void
     */
    public function setDuration(int $duration): void
    {
        $this->duration = $duration;
    }

    /**
     * Récupère la note de la chanson
     * 
     * @return int La note de la chanson
     */
    public function getNote(): int
    {
        return $this->note;
    }

    /**
     * Définit la note de la chanson avec limitation entre 0 et 5
     * 
     * @param int $note La nouvelle note (sera limitée entre 0 et 5)
     * @return void
     */
    public function setNote(int $note): void
    {
        $this->note = max(0, min(5, $note));
    }

    /**
     * Récupère l'identifiant de l'album
     * 
     * @return int|null L'identifiant de l'album ou null
     */
    public function getAlbumId(): ?int
    {
        return $this->albumId;
    }

    /**
     * Définit l'identifiant de l'album
     * 
     * @param int $albumId Le nouvel identifiant d'album
     * @return void
     */
    public function setAlbumId(int $albumId): void
    {
        $this->albumId = $albumId;
    }

    /**
     * Récupère le titre de l'album
     * 
     * @return string|null Le titre de l'album ou null
     */
    public function getAlbumTitle(): ?string
    {
        return $this->albumTitle;
    }

    /**
     * Définit le titre de l'album
     * 
     * @param string|null $albumTitle Le nouveau titre d'album
     * @return void
     */
    public function setAlbumTitle(?string $albumTitle): void
    {
        $this->albumTitle = $albumTitle;
    }

    /**
     * Récupère toutes les chansons avec les informations des albums
     * 
     * @return array Tableau d'instances Song
     */
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

    /**
     * Crée une nouvelle chanson en base de données
     * 
     * @param int|null $albumId L'identifiant de l'album
     * @param string $title Le titre de la chanson
     * @param string $author L'auteur de la chanson
     * @param int $available Le statut de disponibilité
     * @param string $image Le chemin de l'image
     * @param int $duration La durée en secondes
     * @param int $note La note de la chanson
     * @return bool True si la création réussit, false sinon
     */
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

    /**
     * Met à jour une chanson existante
     * 
     * @param int $id L'identifiant de la chanson à modifier
     * @param int|null $albumId L'identifiant de l'album
     * @param string $title Le nouveau titre
     * @param string $author Le nouvel auteur
     * @param int $available Le nouveau statut de disponibilité
     * @param string $image Le nouveau chemin d'image
     * @param int $duration La nouvelle durée
     * @param int $note La nouvelle note
     * @return bool True si la mise à jour réussit, false sinon
     */
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

    /**
     * Récupère une chanson par son identifiant
     * 
     * @param int $id L'identifiant de la chanson
     * @return Song L'instance Song correspondante
     */
    public static function getOneSong(int $id): ?Song
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

        if (!$song) {
            return null;
        }

        $song = new Song($song['id'], $song['album_id'], $song['title'], $song['author'], $song['available'], $song['image'], $song['duration'], $song['note']);

        return $song;
    }

    /**
     * Supprime une chanson de la base de données
     * 
     * @param int $id L'identifiant de la chanson à supprimer
     * @return bool True si la suppression réussit, false sinon
     */
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

    /**
     * Récupère toutes les chansons disponibles
     * 
     * @return array Tableau d'instances Song avec available = 1
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
            $songInst = new Song($song['id'], $song['album_id'], $song['title'], $song['author'], $song['available'], $song['image'], $song['duration'], $song['note']);
            $songInst->setAlbumTitle($song['album_title'] ?? null);
            array_push($songs, $songInst);
        }

        return $songs;
    }

    /**
     * Recherche des chansons dans un tableau selon un terme de recherche
     * 
     * @param array $songs Tableau d'instances Song à filtrer
     * @param string $search Terme de recherche
     * @return array Tableau filtré d'instances Song correspondant à la recherche
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
