<?php

namespace Controllers;

use Models\Album;

class AlbumController
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

        $albums = $onlyAvailable ? Album::getAvailableAlbums() : Album::getAllAlbums();

        if ($search) {
            $albums = Album::searchAlbums($albums, $search);
        }

        foreach ($albums as $album) {
            $album->setTrackNumber(Album::getTrackNumber($album->getId()));
        }

        require_once __DIR__ . '/../Views/albums/index.php';
    }


    public function create(): void
    {
        $this->checkAuth();
        $errors = [
            'title' => '',
            'author' => '',
            'editor' => '',
            'available' => '',
            'image' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $_POST = filter_input_array(INPUT_POST, [
                'title' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'author' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'editor' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'available' => FILTER_DEFAULT
            ]);

            $imagePath = '';
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

                if (in_array($extension, $allowedExtensions)) {
                    $fileName = time() . '_' . $_FILES['image']['name'];
                    $uploadDir = __DIR__ . '/../assets/images/album/';

                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $destination = $uploadDir . $fileName;

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                        $imagePath = '/assets/images/album/' . $fileName;
                    }
                } else {
                    $errors['image'] = "Veuillez uploader une image au format jpg, jpeg, png ou webp";
                }
            } else {
                $errors['image'] = "Veuillez renseigner une image";
            }

            $title = $_POST['title'] ?? '';
            $author = $_POST['author'] ?? '';
            $editor = $_POST['editor'] ?? '';
            $available = $_POST['available'] ?? null;

            if (!$title) {
                $errors['title'] = "Veuillez renseigner le titre";
            }
            if (!$author) {
                $errors['author'] = "Veuillez renseigner l'auteur";
            }
            if (!$editor) {
                $errors['editor'] = "Veuillez renseigner l'éditeur";
            }
            if ($available === null) {
                $errors['available'] = "Veuillez renseigner la disponibilité";
            }

            if (empty(array_filter($errors))) {
                $success = Album::createAlbum($title, $author, (int)$available, $imagePath, $editor);

                if ($success) {
                    header('Location: /albums');
                    exit;
                } else {
                    $error = "Impossible de créer l'album";
                }
            }
        }

        require_once __DIR__ . '/../Views/albums/create.php';
    }

    public function edit(int $id): void
    {

        $this->checkAuth();
        $album = Album::getOneAlbum($id);

        if (!$album) {
            header('Location: /albums');
            exit;
        }

        $errors = [
            'title' => '',
            'author' => '',
            'track_number' => '',
            'editor' => '',
            'available' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $_POST = filter_input_array(INPUT_POST, [
                'title' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'author' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'track_number' => FILTER_SANITIZE_NUMBER_INT,
                'editor' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'available' => FILTER_DEFAULT
            ]);

            $imagePath = $album->getImage();
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

                if (in_array($extension, $allowedExtensions)) {
                    $fileName = time() . '_' . $_FILES['image']['name'];
                    $uploadDir = __DIR__ . '/../assets/images/album/';

                    $destination = $uploadDir . $fileName;

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                        $oldImagePath = __DIR__ . '/../' . $album->getImage();
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                        $imagePath = '/assets/images/album/' . $fileName;
                    }
                } else {
                    $errors['image'] = "Veuillez uploader une image au format jpg, jpeg, png ou webp";
                }
            }

            $title = $_POST['title'] ?? '';
            $author = $_POST['author'] ?? '';
            $editor = $_POST['editor'] ?? '';
            $available = $_POST['available'] ?? null;

            if (!$title) {
                $errors['title'] = "Veuillez renseigner le titre";
            }
            if (!$author) {
                $errors['author'] = "Veuillez renseigner l'auteur";
            }
            if (!$editor) {
                $errors['editor'] = "Veuillez renseigner l'éditeur";
            }
            if ($available === null) {
                $errors['available'] = "Veuillez renseigner la disponibilité";
            }

            if (empty(array_filter($errors))) {
                $success = Album::updateAlbum($id, $title, $author, (int)$available, $imagePath, $editor);
                if ($success) {
                    header('Location: /albums');
                    exit;
                } else {
                    $error = "Impossible de mettre à jour l'album";
                }
            }
        }

        require_once __DIR__ . '/../Views/albums/edit.php';
    }


    public function delete(int $id): void
    {
        $this->checkAuth();
        $album = Album::getOneAlbum($id);

        if ($album) {
            $success = Album::deleteAlbum($id);
            if ($success) {
                if (!empty($album->getImage())) {
                    $oldImagePath = __DIR__ . '/..' . $album->getImage();
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                header('Location: /albums');
                exit;
            } else {
                $error = "Impossible de supprimer l'album";
            }
        } else {
            header('Location: /albums');
            exit;
        }
    }

    public function borrow(int $id)
    {
        $this->checkAuth();
        $user_id = $_SESSION['user_id'] ?? null;

        $album = Album::getOneAlbum($id);

        if ($album) {
            $success = Album::borrow($user_id, $id);

            if ($success) {
                header('Location: /albums');
                exit;
            } else {
                $error = "Impossible de louer l'album.";
            }
        } else {
            header('Location: /albums');
            exit;
        }
    }

    public function returnMedia(int $id)
    {
        $this->checkAuth();
        $user_id = $_SESSION['user_id'] ?? null;

        $album = Album::getOneAlbum($id);

        if ($album) {
            $success = Album::returnMedia($user_id, $id);

            if ($success) {
                header('Location: /albums');
                exit;
            } else {
                $error = "Impossible de rendre l'album.";
            }
        } else {
            header('Location: /albums');
            exit;
        }
    }
}
