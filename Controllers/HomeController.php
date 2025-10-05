<?php

namespace Controllers;

use Models\Album;
use Models\Movie;
use Models\Book;

class HomeController
{
    public function show()
    {
        $albums = Album::getThreeAvailableRandomAlbums();
        $books = Book::getThreeAvailableRandomBooks();
        $movies = Movie::getThreeAvailableRandomMovies();

        require_once __DIR__ . '/../Views/home.php';
    }
}
