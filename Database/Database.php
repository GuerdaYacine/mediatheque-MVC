<?php

namespace Database;

final readonly class Database
{

    public static function connect(): \PDO
    {
        try {
            $user = 'root';
            $pass = '123456';
            $dbName = 'mediatheque';
            $dbHost = 'localhost';

            $connexion = new \PDO("mysql:host=$dbHost;dbname=$dbName;charset=UTF8", $user, $pass, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]);
        } catch (\Exception $exception) {
            echo 'Erreur lors de la connexion à la base de données. : ' . $exception->getMessage();
            exit;
        }
        return $connexion;
    }
}
