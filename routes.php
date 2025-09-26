<?php

$router->get('/', 'Controllers\HomeController@show');


// --------- User ---------
$router->get('/login', 'Controllers\UserController@login');
$router->post('/login', 'Controllers\UserController@login');

$router->get('/register', 'Controllers\UserController@register');
$router->post('/register', 'Controllers\UserController@register');

$router->get('/logout', 'Controllers\UserController@logout');
$router->post('/logout', 'Controllers\UserController@logout');


// --------- Book ---------
$router->get('/books', 'Controllers\BookController@show');
$router->get('/books/create', 'Controllers\BookController@create');
$router->post('/books/create', 'Controllers\BookController@create');
$router->get('/books/{id}/edit', 'Controllers\BookController@edit');
$router->post('/books/{id}/edit', 'Controllers\BookController@edit');
$router->get('/books/{id}/delete', 'Controllers\BookController@delete');
$router->post('/books/{id}/delete', 'Controllers\BookController@delete');

// --------- Movie ---------
$router->get('/movies', 'Controllers\MovieController@show');
$router->get('/movies/create', 'Controllers\MovieController@create');
$router->post('/movies/create', 'Controllers\MovieController@create');
$router->get('/movies/{id}/edit', 'Controllers\MovieController@edit');
$router->post('/movies/{id}/edit', 'Controllers\MovieController@edit');
$router->get('/movies/{id}/delete', 'Controllers\MovieController@delete');
$router->post('/movies/{id}/delete', 'Controllers\MovieController@delete');

// --------- Album ---------
$router->get('/albums', 'Controllers\AlbumController@show');
$router->get('/albums/create', 'Controllers\AlbumController@create');
$router->post('/albums/create', 'Controllers\AlbumController@create');
$router->get('/albums/{id}/edit', 'Controllers\AlbumController@edit');
$router->post('/albums/{id}/edit', 'Controllers\AlbumController@edit');
$router->get('/albums/{id}/delete', 'Controllers\AlbumController@delete');
$router->post('/albums/{id}/delete', 'Controllers\AlbumController@delete');

$router->get('/albums/{id}/borrow', 'Controllers\AlbumController@borrow');
$router->post('/albums/{id}/borrow', 'Controllers\AlbumController@borrow');

// --------- Song ---------
$router->get('/songs', 'Controllers\SongController@show');
$router->get('/songs/create', 'Controllers\SongController@create');
$router->post('/songs/create', 'Controllers\SongController@create');
$router->get('/songs/{id}/edit', 'Controllers\SongController@edit');
$router->post('/songs/{id}/edit', 'Controllers\SongController@edit');
$router->get('/songs/{id}/delete', 'Controllers\SongController@delete');
$router->post('/songs/{id}/delete', 'Controllers\SongController@delete');
