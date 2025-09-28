<?php

namespace Controllers;

use Models\Song;
use Models\Album;

class SongController
{
    private function checkAuth(): void
    {
        if (!isset($_SESSION['username'])) {
            header("Location: /login");
            exit;
        }
    }

    private function isLoggedIn(): bool
    {
        return isset($_SESSION['username']);
    }

    public function show(): void
    {
        $isLoggedIn = $this->isLoggedIn();

        $search = $_GET['search'] ?? null;

        $onlyAvailable = isset($_GET['available']) && $_GET['available'] === 'on';

        $songs = $onlyAvailable ? Song::getAvailableSongs() : Song::getAllSongs();

        if ($search) {
            $songs = Song::searchSongs($songs, $search);
        }

        require_once __DIR__ . '/../Views/songs/index.php';
    }

    public function create(): void
    {
        $this->checkAuth();
        $albums = Album::getAllAlbums();

        $errors = [
            'title' => '',
            'author' => '',
            'available' => '',
            'image' => '',
            'duration' => '',
            'note' => '',
            'album' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $_POST = filter_input_array(INPUT_POST, [
                'title' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'author' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'available' => FILTER_DEFAULT,
                'duration' => FILTER_SANITIZE_NUMBER_FLOAT,
                'note' => FILTER_SANITIZE_NUMBER_INT,
                'album_id' => FILTER_SANITIZE_NUMBER_INT
            ]);

            $title = $_POST['title'] ?? '';
            $author = $_POST['author'] ?? '';
            $available = $_POST['available'] ?? null;
            $duration = $_POST['duration'] ?? '';
            $note = $_POST['note'] ?? '';
            $albumId = isset($_POST['album_id']) && $_POST['album_id'] !== '' ? (int)$_POST['album_id'] : null;

            if (!$title) {
                $errors['title'] = "Veuillez renseigner le titre";
            }
            if (!$author) {
                $errors['author'] = "Veuillez renseigner l'auteur";
            }
            if ($available === null) {
                $errors['available'] = "Veuillez renseigner la disponibilité";
            }
            if (!$duration) {
                $errors['duration'] = "Veuillez renseigner la durée";
            }
            if ($duration <= 0) {
                $errors['duration'] = "La durée doit être un nombre positif";
            }
            if ($note === '' || $note === null) {
                $errors['note'] = "Veuillez renseigner la note";
            }
            if ($note < 0 || $note > 5) {
                $errors['note'] = "La note doit être comprise entre 0 et 5";
            }

            $imagePath = '';

            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

                $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

                if (in_array($extension, $allowedExtensions)) {
                    $fileName = time() . '_' . $_FILES['image']['name'];
                    $uploadDir = __DIR__ . '/../assets/images/song/';

                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $destination = $uploadDir . $fileName;

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                        $imagePath = '/assets/images/song/' . $fileName;
                    }
                } else {
                    $errors['image'] = "Veuillez uploader une image au format jpg, jpeg, png ou webp";
                }
            } else {
                $errors['image'] = "Veuillez renseigner une image";
            }

            if (empty(array_filter($errors))) {
                $success = Song::createSong($albumId, $title, $author, (int)$available, $imagePath, $duration, (int)$note);
                if ($success) {
                    header('Location: /songs');
                    exit;
                } else {
                    echo "Erreur lors de la création de la musique.";
                }
            }
        }
        require_once __DIR__ . '/../Views/songs/create.php';
    }

    public function edit(int $id): void
    {
        $this->checkAuth();
        $song = Song::getOneSong($id);

        if (!$song) {
            header('Location: /songs');
            exit;
        }

        $albums = Album::getAllAlbums();

        $errors = [
            'title' => '',
            'author' => '',
            'available' => '',
            'image' => '',
            'duration' => '',
            'note' => '',
            'album' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $_POST = filter_input_array(INPUT_POST, [
                'title' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'author' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'available' => FILTER_DEFAULT,
                'duration' => FILTER_SANITIZE_NUMBER_INT,
                'note' => FILTER_SANITIZE_NUMBER_INT,
                'album_id' => FILTER_SANITIZE_NUMBER_INT
            ]);

            $title = $_POST['title'] ?? '';
            $author = $_POST['author'] ?? '';
            $available = $_POST['available'] ?? null;
            $duration = $_POST['duration'] ?? '';
            $note = $_POST['note'] ?? '';
            $albumId = isset($_POST['album_id']) && $_POST['album_id'] !== '' ? (int)$_POST['album_id'] : null;

            if (!$title) {
                $errors['title'] = "Veuillez renseigner le titre";
            }
            if (!$author) {
                $errors['author'] = "Veuillez renseigner l'auteur";
            }
            if ($available === null) {
                $errors['available'] = "Veuillez renseigner la disponibilité";
            }
            if (!$duration) {
                $errors['duration'] = "Veuillez renseigner la durée";
            }
            if ($duration <= 0) {
                $errors['duration'] = "La durée doit être un nombre positif";
            }
            if ($note === '' || $note === null) {
                $errors['note'] = "Veuillez renseigner la note";
            }
            if ($note < 0 || $note > 5) {
                $errors['note'] = "La note doit être comprise entre 0 et 5";
            }

            $imagePath = $song->getImage();
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

                $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

                if (in_array($extension, $allowedExtensions)) {
                    $fileName = time() . '_' . $_FILES['image']['name'];
                    $uploadDir = __DIR__ . '/../assets/images/song/';

                    $destination = $uploadDir . $fileName;

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                        $oldImagePath = __DIR__ . '/../' . $song->getImage();
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                        $imagePath = '/assets/images/song/' . $fileName;
                    }
                } else {
                    $errors['image'] = "Veuillez uploader une image au format jpg, jpeg, png ou webp";
                }
            }

            if (empty(array_filter($errors))) {
                $success = Song::updateSong($id, $albumId, $title, $author, (int)$available, $imagePath, $duration, $note);
                if ($success) {
                    header('Location: /songs');
                    exit;
                } else {
                    echo "Erreur lors de la modification de la musique.";
                }
            }
        }
        require_once __DIR__ . '/../Views/songs/edit.php';
    }

    public function delete(int $id): void
    {
        $this->checkAuth();
        $song = Song::getOneSong($id);

        if ($song) {

            $success = Song::deleteSong($id);

            if ($success) {
                if (!empty($song->getImage())) {
                    $oldImagePath = __DIR__ . '/../' . $song->getImage();
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                header('Location: /songs');
                exit;
            } else {
                $error = "Impossible de supprimer la musique";
            }
        } else {
            header('Location: /songs');
            exit;
        }
    }
}
