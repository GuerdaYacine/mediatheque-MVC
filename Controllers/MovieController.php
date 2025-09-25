<?php

namespace Controllers;

use Models\Movie;

class MovieController
{
    private Movie $movieModel;

    public function __construct(Movie $movieModel)
    {
        $this->movieModel = $movieModel;
    }

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

        $movies = $this->movieModel->getAllMovies();

        require_once __DIR__ . '/../Views/movies/index.php';
    }

    public function create(): void
    {
        $this->checkAuth();
        $errors = [
            'title' => '',
            'author' => '',
            'available' => '',
            'duration' => '',
            'genre' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, [
                'title' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'author' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'available' => FILTER_DEFAULT,
                'duration' => FILTER_SANITIZE_NUMBER_INT,
                'genre' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            ]);
            $imagePath = '';
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

                if (in_array($extension, $allowedExtensions)) {
                    $fileName = time() . '_' . $_FILES['image']['name'];
                    $uploadDir = __DIR__ . '/../assets/images/movie/';

                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $destination = $uploadDir . $fileName;

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                        $imagePath = '/assets/images/movie/' . $fileName;
                    }
                } else {
                    $errors['image'] = "Veuillez uploader une image au format jpg, jpeg, png ou webp";
                }
            } else {
                $errors['image'] = "Veuillez renseigner une image";
            }

            $title = $_POST['title'] ?? '';
            $author = $_POST['author'] ?? '';
            $available = $_POST['available'] ?? null;
            $duration = $_POST['duration'] ?? '';
            $genre = $_POST['genre'] ?? '';

            if (!$title) {
                $errors['title'] = "Veuillez renseigner le titre";
            }
            if (!$author) {
                $errors['author'] = "Veuillez renseigner l'auteur";
            }
            if (!$duration) {
                $errors['duration'] = "Veuillez renseigner la durée";
            }
            if ($duration <= 0) {
                $errors['duration'] = "La durée doit être un nombre positif";
            }
            if (!$genre) {
                $errors['genre'] = "Veuillez renseigner le genre";
            }
            if ($available === null) {
                $errors['available'] = "Veuillez renseigner la disponibilité";
            }

            if (empty(array_filter($errors))) {
                $genreEnum = \Models\Genre::from($genre);
                $success = $this->movieModel->createMovie($title, $author, (int)$available, $imagePath, $duration, $genreEnum);

                if ($success) {
                    header("Location: /movies");
                    exit;
                } else {
                    $error = "Erreur lors de la création du film.";
                }
            }
        }
        require_once __DIR__ . '/../Views/movies/create.php';
    }

    public function edit(int $id): void
    {
        $this->checkAuth();

        $movie = $this->movieModel->getOneMovie($id);

        if (!$movie) {
            header('Location: /books');
            exit;
        }

        $errors = [
            'title' => '',
            'author' => '',
            'available' => '',
            'duration' => '',
            'genre' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, [
                'title' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'author' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'available' => FILTER_DEFAULT,
                'duration' => FILTER_SANITIZE_NUMBER_INT,
                'genre' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            ]);

            $imagePath = $movie['image'];
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

                if (in_array($extension, $allowedExtensions)) {
                    $fileName = time() . '_' . $_FILES['image']['name'];
                    $uploadDir = __DIR__ . '/../assets/images/movie/';

                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $destination = $uploadDir . $fileName;

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                        $imagePath = '/assets/images/movie/' . $fileName;
                    }
                } else {
                    $errors['image'] = "Veuillez uploader une image au format jpg, jpeg, png ou webp";
                }
            }

            $title = $_POST['title'] ?? '';
            $author = $_POST['author'] ?? '';
            $available = $_POST['available'] ?? null;
            $duration = $_POST['duration'] ?? '';
            $genre = $_POST['genre'] ?? '';

            if (!$title) {
                $errors['title'] = "Veuillez renseigner le titre";
            }
            if (!$author) {
                $errors['author'] = "Veuillez renseigner l'auteur";
            }
            if (!$duration) {
                $errors['duration'] = "Veuillez renseigner la durée";
            }
            if ($duration <= 0) {
                $errors['duration'] = "La durée doit être un nombre positif";
            }
            if (!$genre) {
                $errors['genre'] = "Veuillez renseigner le genre";
            }
            if ($available === null) {
                $errors['available'] = "Veuillez renseigner la disponibilité";
            }

            if (empty(array_filter($errors))) {
                $genreEnum = \Models\Genre::from($genre);
                $success = $this->movieModel->updateMovie($id, $title, $author, (int)$available, $imagePath, $duration, $genreEnum);

                if ($success) {
                    header("Location: /movies");
                    exit;
                } else {
                    $error = "Erreur lors de la modification du film.";
                }
            }
        }
        require_once __DIR__ . '/../Views/movies/edit.php';
    }

    public function delete(int $id): void
    {
        $this->checkAuth();
        $movie = $this->movieModel->getOneMovie($id);

        if ($movie) {

            $success = $this->movieModel->deleteMovie($id);

            if ($success) {
                if (!empty($movie['image'])) {
                    $oldImagePath = __DIR__ . '/../' . $movie['image'];
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                header('Location: /movies');
                exit;
            } else {
                $error = "Impossible de supprimer le film";
            }
        } else {
            header('Location: /movies');
            exit;
        }
    }
}
