CREATE DATABASE IF NOT EXISTS mediatheque;
USE mediatheque;

DROP TABLE IF EXISTS `media`;
CREATE TABLE `media` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `author` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `available` tinyint(1) NOT NULL,
    `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `media_type` enum('book', 'album', 'movie') COLLATE utf8mb4_unicode_ci NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `album`;
CREATE TABLE `album` (
    `id` int unsigned NOT NULL,
    `editor` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `album_ibfk_1` FOREIGN KEY (`id`) REFERENCES `media` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `book`;
CREATE TABLE `book` (
    `id` int unsigned NOT NULL,
    `page_number` int NOT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `book_ibfk_1` FOREIGN KEY (`id`) REFERENCES `media` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `movie`;
CREATE TABLE `movie` (
    `id` int unsigned NOT NULL,
    `duration` double NOT NULL,
    `genre` enum('Action','Comédie','Drame','Horreur','Autre') COLLATE utf8mb4_unicode_ci NOT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `movie_ibfk_1` FOREIGN KEY (`id`) REFERENCES `media` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `song`;
CREATE TABLE `song` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `album_id` int unsigned DEFAULT NULL,
    `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `author` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `available` tinyint(1) NOT NULL,
    `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `duration` float NOT NULL,
    `note` int unsigned NOT NULL,
    PRIMARY KEY (`id`),
    KEY `song_ibfk_1` (`album_id`),
    CONSTRAINT `song_ibfk_1` FOREIGN KEY (`album_id`) REFERENCES `album` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `location`;
CREATE TABLE `location` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int unsigned NOT NULL,
    `media_id` int unsigned NOT NULL,
    `borrowed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `returned_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `location_ibfk_2` (`media_id`),
    CONSTRAINT `location_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `location_ibfk_2` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
