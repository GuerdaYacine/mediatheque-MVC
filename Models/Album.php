<?php

namespace Models;

use \Database\Database;

/**
 * Classe Album
 * 
 * Représente un album musical héritant de la classe Media.
 * Ajoute les propriétés spécifiques aux albums comme l'éditeur
 * et gère le nombre de pistes associées.
 */
class Album extends Media
{
    /**
     * @var int|null Nombre de pistes dans l'album
     */
    private ?int $trackNumber = null;

    /**
     * Constructeur de la classe Album
     * 
     * @param int $id Identifiant unique de l'album
     * @param string $title Titre de l'album
     * @param string $author Artiste de l'album
     * @param int $available Statut de disponibilité (0 ou 1)
     * @param string $image Chemin vers l'image de l'album
     * @param string $editor Éditeur/label de l'album
     */
    public function __construct(int $id, string $title, string $author, int $available, string $image, private string $editor)
    {
        parent::__construct($id, $title, $author, $available, $image);
    }

    /**
     * Récupère l'éditeur de l'album
     * 
     * @return string L'éditeur de l'album
     */
    public function getEditor(): string
    {
        return $this->editor;
    }

    /**
     * Définit l'éditeur de l'album
     * 
     * @param string $editor Le nouvel éditeur
     * @return void
     */
    public function setEditor(string $editor): void
    {
        $this->editor = $editor;
    }

    /**
     * Compte le nombre de pistes disponibles dans un album
     * 
     * @param int $albumId L'identifiant de l'album
     * @return int Le nombre de pistes disponibles
     */
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

    /**
     * Définit le nombre de pistes de l'album
     * 
     * @param int $trackNumber Le nombre de pistes
     * @return void
     */
    public function setTrackNumber(int $trackNumber): void
    {
        $this->trackNumber = $trackNumber;
    }

    /**
     * Récupère tous les albums de la base de données
     * 
     * @return array Tableau d'instances Album
     */
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

    /**
     * Crée un nouvel album dans la base de données
     * 
     * Insère d'abord dans la table media, puis dans la table album
     * avec l'ID généré automatiquement.
     * 
     * @param string $title Le titre de l'album
     * @param string $author L'artiste de l'album
     * @param int $available Le statut de disponibilité
     * @param string $image Le chemin de l'image
     * @param string $editor L'éditeur de l'album
     * @return bool True si la création réussit, false sinon
     */
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

    /**
     * Met à jour un album existant
     * 
     * Met à jour les données dans les tables media et album simultanément.
     * 
     * @param int $id L'identifiant de l'album à modifier
     * @param string $title Le nouveau titre
     * @param string $author Le nouvel artiste
     * @param int $available Le nouveau statut de disponibilité
     * @param string $image Le nouveau chemin d'image
     * @param string $editor Le nouvel éditeur
     * @return bool True si la mise à jour réussit, false sinon
     */
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

    /**
     * Récupère un album par son identifiant
     * 
     * @param int $id L'identifiant de l'album
     * @return Album L'instance Album correspondante
     */
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

    /**
     * Supprime un album de la base de données
     * 
     * Supprime simultanément les données des tables media et album.
     * 
     * @param int $id L'identifiant de l'album à supprimer
     * @return bool True si la suppression réussit, false sinon
     */
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

    /**
     * Récupère trois albums disponibles de manière aléatoire
     * 
     * @return array Tableau de 3 instances Album maximum, sélectionnées aléatoirement
     */
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

    /**
     * Récupère trois albums disponibles de manière aléatoire
     * 
     * Note: Cette méthode semble identique à getThreeAvailableRandomAlbums()
     * mais avec un nom différent suggérant qu'elle devrait retourner tous les albums disponibles.
     * 
     * @return array Tableau de 3 instances Album maximum, sélectionnées aléatoirement
     */
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

    /**
     * Recherche des albums dans un tableau selon un terme de recherche
     * 
     * Effectue une recherche dans le titre et l'auteur de l'album.
     * 
     * @param array $albums Tableau d'instances Album à filtrer
     * @param string $search Terme de recherche
     * @return array Tableau filtré d'instances Album correspondant à la recherche
     */
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
