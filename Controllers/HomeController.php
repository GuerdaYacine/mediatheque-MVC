<?php

namespace Controllers;

use Models\Album;
use Models\Movie;
use Models\Book;

class HomeController
{
    private Album $albumModel;
    private Movie $movieModel;
    private Book $bookModel;

    public function __construct(Album $albumModel, Movie $movieModel, Book $bookModel){
        $this->albumModel = $albumModel;
        $this->movieModel = $movieModel;
        $this->bookModel = $bookModel;
    }

    public function show()
    {
        $albums = $this->albumModel->getThreeRandomAlbums();

        foreach ($albums as $key => $album) {
            $albums[$key]['track_number'] = $this->albumModel->getTrackNumber($album['id']);
        }

        $books = $this->bookModel->getThreeRandomBooks();
        $movies = $this->movieModel->getThreeRandomMovies();

        require_once __DIR__ . '/../Views/home.php';
    }
}