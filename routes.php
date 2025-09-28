<?php

// Route pour la page d'accueil
$router->get('/', 'Controllers\HomeController@show');


// Routes concernant l'authentification
$router->get('/login', 'Controllers\UserController@login');
$router->post('/login', 'Controllers\UserController@login');

$router->get('/register', 'Controllers\UserController@register');
$router->post('/register', 'Controllers\UserController@register');

$router->get('/logout', 'Controllers\UserController@logout');

// Routes concernant les livres
$router->get('/books', 'Controllers\BookController@show');
$router->get('/books/create', 'Controllers\BookController@create');
$router->post('/books/create', 'Controllers\BookController@create');
$router->get('/books/{id}/edit', 'Controllers\BookController@edit');
$router->post('/books/{id}/edit', 'Controllers\BookController@edit');
$router->get('/books/{id}/delete', 'Controllers\BookController@delete');
$router->get('/books/{id}/borrow', 'Controllers\BookController@borrow');
$router->get('/books/{id}/return', 'Controllers\BookController@returnMedia');

// Routes concernant les films
$router->get('/movies', 'Controllers\MovieController@show');
$router->get('/movies/create', 'Controllers\MovieController@create');
$router->post('/movies/create', 'Controllers\MovieController@create');
$router->get('/movies/{id}/edit', 'Controllers\MovieController@edit');
$router->post('/movies/{id}/edit', 'Controllers\MovieController@edit');
$router->get('/movies/{id}/delete', 'Controllers\MovieController@delete');
$router->get('/movies/{id}/borrow', 'Controllers\MovieController@borrow');
$router->get('/movies/{id}/return', 'Controllers\MovieController@returnMedia');


// Routes concernants les albums
$router->get('/albums', 'Controllers\AlbumController@show');
$router->get('/albums/create', 'Controllers\AlbumController@create');
$router->post('/albums/create', 'Controllers\AlbumController@create');
$router->get('/albums/{id}/edit', 'Controllers\AlbumController@edit');
$router->post('/albums/{id}/edit', 'Controllers\AlbumController@edit');
$router->get('/albums/{id}/delete', 'Controllers\AlbumController@delete');
$router->get('/albums/{id}/borrow', 'Controllers\AlbumController@borrow');
$router->get('/albums/{id}/return', 'Controllers\AlbumController@returnMedia');

// Routes concernant les musqiques
$router->get('/songs', 'Controllers\SongController@show');
$router->get('/songs/create', 'Controllers\SongController@create');
$router->post('/songs/create', 'Controllers\SongController@create');
$router->get('/songs/{id}/edit', 'Controllers\SongController@edit');
$router->post('/songs/{id}/edit', 'Controllers\SongController@edit');
$router->get('/songs/{id}/delete', 'Controllers\SongController@delete');
