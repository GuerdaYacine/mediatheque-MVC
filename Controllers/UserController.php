<?php

namespace Controllers;

use Models\User;


class UserController
{
    public function register(): void
    {
        $errors = [
            'username' => '',
            'email' => '',
            'password' => ''
        ];

        $user = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = filter_input_array(INPUT_POST, [
                'username' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'email' => FILTER_SANITIZE_EMAIL,
            ]);

            $username = $input['username'] ?? '';
            $email = $input['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $existingUser = User::getUserByEmail($email);

            if (!$username) {
                $errors['username'] = 'Veuillez saisir un nom d\'utilisateur';
            }
            if (!$email) {
                $errors['email'] = 'Veuillez saisir une adresse email';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Veuillez saisir une adresse email valide';
            } elseif ($existingUser) {
                $errors['email'] = 'Cette adresse email est déjà utilisée';
            }

            if (!$password) {
                $errors['password'] = 'Veuillez saisir un mot de passe';
            } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
                $errors['password'] = 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial';
            } elseif (str_contains(strtolower($password), strtolower($username))) {
                $errors['password'] = 'Le mot de passe ne doit pas contenir le nom d\'utilisateur';
            }


            if (empty(array_filter($errors))) {
                $user = User::createUser($username, $email, $password);
                if ($user) {
                    header("Location: /login");
                    exit;
                }
            }
        }
        require_once __DIR__ . '/../Views/auth/register.php';
    }

    public function login(): void
    {
        $errors = [
            'email' => '',
            'password' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = filter_input_array(INPUT_POST, [
                'email' => FILTER_SANITIZE_EMAIL,
            ]);

            $email = $input['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if (!$email) {
                $errors['email'] = 'Veuillez saisir une adresse email';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Veuillez saisir une adresse email valide';
            }

            if (!$password) {
                $errors['password'] = 'Veuillez saisir un mot de passe';
            }

            if (empty(array_filter($errors))) {
                $user = User::getUserByEmail($email);
                if ($user && password_verify($password, $user->getPassword())) {
                    session_start();
                    $_SESSION['user_id'] = $user->getId();
                    $_SESSION['username'] = $user->getUsername();
                    $_SESSION['email'] = $user->getEmail();
                    header("Location: /");
                    exit;
                } else {
                    $errors['password'] = 'Email ou mot de passe incorrect';
                }
            }
        }
        require_once __DIR__ . '/../Views/auth/login.php';
    }

    public function logout(): void
    {
        session_start();
        session_unset();
        session_destroy();
        header("Location: /login");
        exit;
    }
}
