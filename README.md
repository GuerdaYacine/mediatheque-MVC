# Médiathèque MVC

Une application web de gestion de médiathèque développée en PHP orienté objet avec une architecture MVC native from scratch.

## Description

La Médiathèque est une application web complète permettant de gérer une collection de médias diversifiés (livres, films, albums musicaux). Elle offre un système complet de gestion avec authentification utilisateur, CRUD complet des médias, et un système de location/retour.

### Fonctionnalités

- Authentification utilisateur - Système de connexion/inscription sécurisé
- Gestion des livres - Ajout, modification, suppression avec nombre de pages
- Gestion des films - Gestion complète avec genres et durée
- Gestion des albums - Albums musicaux avec éditeur et pistes associées
- Gestion des chansons - Pistes individuelles avec notes et durée
- Système de location - Emprunt et retour de médias
- Recherche et filtrage - Recherche textuelle dans tous les types de médias
- Gestion des disponibilités - Suivi en temps réel des médias disponibles

## Prérequis

PHP 8.0+

Composer

MySQL/MariaDB

## Installation 

### Cloner le projet

```

https://github.com/GuerdaYacine/mediatheque-MVC.git

```
### Installer les dépendances 

```

composer install

```

### Créer la base de données

Récuperez le fichier sql fournit dans le répertoire et créez la base de données depuis celui-ci

### Configuration

Rendez-vous dans le dossier Database puis dans Database.php modifiez les informations nécéssaires.

### Lancer le serveur 

```

php -S localhost:3000

```

### Accéder à l'application

Rendez vous sur http://localhost:3000 et naviguez sur l'application.

## Problèmes

Si vous rencontrez le moindre problème n'hésitez pas à me contacter : guerda.yacine60100@gmail.com
