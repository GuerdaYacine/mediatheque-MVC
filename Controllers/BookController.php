<?php

namespace Controllers;

use Models\Book;

class BookController
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

        $books = $onlyAvailable ? Book::getAvailableBooks() : Book::getAllBooks();

        if ($search) {
            $books = Book::searchBooks($books, $search);
        }

        require_once __DIR__ . '/../Views/books/index.php';
    }

    public function create(): void
    {
        $this->checkAuth();
        $errors = [
            'title' => '',
            'author' => '',
            'available' => '',
            'image' => '',
            'page_number' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, [
                'title' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'author' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'available' => FILTER_DEFAULT,
                'page_number' => FILTER_SANITIZE_NUMBER_INT
            ]);

            $imagePath = '';

            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

                $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

                if (in_array($extension, $allowedExtensions)) {
                    $fileName = time() . '_' . $_FILES['image']['name'];
                    $uploadDir = __DIR__ . '/../assets/images/book/';

                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $destination = $uploadDir . $fileName;

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                        $imagePath = '/assets/images/book/' . $fileName;
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
            $pageNumber = $_POST['page_number'] ?? '';

            if (!$title) {
                $errors['title'] = "Veuillez renseigner le titre";
            }
            if (!$author) {
                $errors['author'] = "Veuillez renseigner l'auteur";
            }
            if ($available === null) {
                $errors['available'] = "Veuillez renseigner la disponibilité";
            }
            if (!$pageNumber) {
                $errors['page_number'] = "Veuillez renseigner le nombre de pages";
            } elseif ($pageNumber <= 0) {
                $errors['page_number'] = "Le nombre de pages doit être un entier positif";
            }

            if (empty(array_filter($errors))) {
                $success = Book::createBook($title, $author, (int)$available, $imagePath, $pageNumber);

                if ($success) {
                    header('Location: /books');
                    exit;
                } else {
                    $error = "Impossible de créer le livre";
                }
            }
        }
        require_once __DIR__ . '/../Views/books/create.php';
    }

    public function edit(int $id): void
    {
        $this->checkAuth();
        $book = Book::getOneBook($id);

        if (!$book) {
            header('Location: /books');
            exit;
        }

        $errors = [
            'title' => '',
            'author' => '',
            'available' => '',
            'image' => '',
            'page_number' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, [
                'title' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'author' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'available' => FILTER_DEFAULT,
                'page_number' => FILTER_SANITIZE_NUMBER_INT,
            ]);

            $imagePath = $book->getImage();
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];


            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

                if (in_array($extension, $allowedExtensions)) {
                    $fileName = time() . '_' . $_FILES['image']['name'];
                    $uploadDir = __DIR__ . '/../assets/images/book/';

                    $destination = $uploadDir . $fileName;

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                        $oldImagePath = __DIR__ . '/../' . $book['image'];
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                        $imagePath = '/assets/images/book/' . $fileName;
                    }
                } else {
                    $errors['image'] = "Veuillez uploader une image au format jpg, jpeg, png ou webp";
                }
            }

            $title = $_POST['title'] ?? '';
            $author = $_POST['author'] ?? '';
            $available = $_POST['available'] ?? null;
            $pageNumber = $_POST['page_number'] ?? '';

            if (!$title) {
                $errors['title'] = "Veuillez renseigner le titre";
            }
            if (!$author) {
                $errors['author'] = "Veuillez renseigner l'auteur";
            }
            if ($available === null) {
                $errors['available'] = "Veuillez renseigner la disponibilité";
            }
            if (!$pageNumber) {
                $errors['page_number'] = "Veuillez renseigner le nombre de pages";
            } elseif ($pageNumber <= 0) {
                $errors['page_number'] = "Le nombre de pages doit être un entier positif";
            }

            if (empty(array_filter($errors))) {
                $success = Book::updateBook($id, $title, $author, (int)$available, $imagePath, $pageNumber);

                if ($success) {
                    header('Location: /books');
                    exit;
                } else {
                    $error = "Impossible de modifier le livre";
                }
            }
        }
        require_once __DIR__ . '/../Views/books/edit.php';
    }

    public function delete(int $id): void
    {
        $this->checkAuth();
        $book = Book::getOneBook($id);

        if ($book) {

            $success = Book::deleteBook($id);

            if ($success) {
                if (!empty($book->getImage())) {
                    $oldImagePath = __DIR__ . '/../' . $book->getImage();
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                header('Location: /books');
                exit;
            } else {
                $error = "Impossible de supprimer le livre";
            }
        } else {
            header('Location: /books');
            exit;
        }
    }

    public function borrow(int $id)
    {
        $this->checkAuth();
        $user_id = $_SESSION['user_id'] ?? null;

        $book = Book::getOneBook($id);

        if ($book) {
            $success = Book::borrow($user_id, $id);

            if ($success) {
                header('Location: /books');
                exit;
            } else {
                $error = "Impossible de louer le livre.";
            }
        } else {
            header('Location: /books');
            exit;
        }
    }

    public function returnMedia(int $id)
    {
        $this->checkAuth();
        $user_id = $_SESSION['user_id'] ?? null;

        $book = Book::getOneBook($id);

        if ($book) {
            $success = Book::returnMedia($user_id, $id);

            if ($success) {
                header('Location: /books');
                exit;
            } else {
                $error = "Impossible de rendre le livre.";
            }
        } else {
            header('Location: /books');
            exit;
        }
    }
}
