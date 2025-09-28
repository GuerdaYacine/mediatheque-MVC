<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Films</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/movie/movies.css">
    <link rel="stylesheet" href="/assets/css/partials/header.css">
    <link rel="stylesheet" href="/assets/css/partials/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head>

<body>
    <?php
    require_once __DIR__ . '/../partials/_header.php'; ?>

    <main class="main-content">
        <div class="content-container">
            <div class="page-header">
                <h1><i class="fas fa-film"></i> Films</h1>
                <?php if ($isLoggedIn) : ?>
                    <a href="/movies/create" class="add-btn">
                        <i class="fas fa-plus"></i>
                        Ajouter un film
                    </a>
                <?php endif; ?>
            </div>

            <div class="filter-section">
                <h1>Filtrer</h1>
                <form action="" method="get" class="filter-form">
                    <div class="filter-group">
                        <label for="search">Recherche</label>
                        <input type="text" name="search" id="search" placeholder="Recherchez par titre, auteur, genre" class="filter-input">
                    </div>

                    <div class="filter-actions">
                        <div class="filter-group checkbox">
                            <input type="checkbox" name="available" id="filter" class="filter-checkbox">
                            <label for="filter">Afficher seulement ceux disponibles</label>
                        </div>

                        <div style="display: flex; gap: 12px;">
                            <button type="submit" class="filter-btn">Filtrer</button>

                            <button type="button" class="filter-reset" onclick="document.querySelector('.filter-form').reset(); window.location.href = window.location.pathname;">
                                Effacer
                            </button>
                        </div>
                    </div>
                </form>
            </div>


            <?php if (empty($movies)) : ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-film"></i>
                    </div>
                    <h3>Aucun film disponible</h3>
                    <?php if ($isLoggedIn) : ?>
                        <p>Commencez par ajouter votre premier film à la médiathèque.</p>
                        <a href="/movies/create" class="empty-cta">
                            <i class="fas fa-plus"></i>
                            Ajouter un film
                        </a>
                    <?php endif; ?>

                </div>
            <?php else : ?>
                <div class="movies-grid">
                    <?php foreach ($movies as $movie) : ?>
                        <div class="movie-card">
                            <div class="movie-image">
                                <img src="<?= $movie->getImage() ?>" alt="<?= $movie->getTitle() ?>">
                                <div class="availability-badge <?= $movie->getAvailable() ? 'available' : 'unavailable' ?>">
                                    <i class="fas <?= $movie->getAvailable() ? 'fa-check' : 'fa-times' ?>"></i>
                                    <?= $movie->getAvailable() ? 'Disponible' : 'Indisponible' ?>
                                </div>
                            </div>

                            <div class="movie-info">
                                <h3 class="movie-title"><?= $movie->getTitle() ?></h3>
                                <p class="movie-author">
                                    <i class="fas fa-user-tie"></i>
                                    <?= $movie->getAuthor() ?>
                                </p>
                                <p class="movie-genre">
                                    <i class="fas fa-theater-masks"></i>
                                    <?= $movie->getGenre()->value ?>
                                </p>
                                <p class="movie-duration">
                                    <i class="fas fa-clock"></i>
                                    <?php
                                    $hours = intdiv($movie->getDuration(), 60);
                                    $minutes = $movie->getDuration() % 60;
                                    ?>
                                    <?= $hours ?>h <?= $minutes ?>m
                                </p>
                            </div>

                            <?php if ($isLoggedIn) : ?>
                                <div class="movie-actions">
                                    <?php if ($movie->getAvailable()) : ?>
                                        <a href="/movies/<?= $movie->getId() ?>/borrow" class="action-btn view">
                                            <i class="fas fa-eye"></i>
                                            Emprunter
                                        </a>
                                    <?php elseif ($movie->getBorrowerId($movie->getId()) === $_SESSION['user_id']) : ?>
                                        <a href="/movies/<?= $movie->getId() ?>/return" class="action-btn return">
                                            <i class="fas fa-undo"></i>
                                            Rendre
                                        </a>
                                    <?php else : ?>
                                        <button class="action-btn view" disabled>
                                            <i class="fas fa-eye"></i>
                                            Emprunter (indisponible)
                                        </button>
                                    <?php endif; ?>

                                    <a href="/movies/<?= $movie->getId() ?>/edit" class="action-btn edit">
                                        <i class="fas fa-edit"></i>
                                        Modifier
                                    </a>
                                    <a href="/movies/<?= $movie->getId() ?>/delete" class="action-btn delete">
                                        <i class="fas fa-trash"></i>
                                        Supprimer
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <?php require_once __DIR__ . '/../partials/_footer.php'; ?>
</body>

</html>