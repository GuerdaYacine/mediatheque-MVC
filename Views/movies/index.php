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

                        <div>
                <h1>Filtrer</h1>
                <form action="" method="get">
                    <label for="filter">Afficher seulement ceux disponibles</label>
                    <input type="checkbox" name="filter" id="filter">

                    <input type="text" placeholder="Recherchez par titre" name="search">
                    <button>Filtrer</button>
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
                                <img src="<?= $movie['image'] ?>" alt="<?= $movie['title'] ?>">
                                <div class="availability-badge <?= $movie['available'] ? 'available' : 'unavailable' ?>">
                                    <i class="fas <?= $movie['available'] ? 'fa-check' : 'fa-times' ?>"></i>
                                    <?= $movie['available'] ? 'Disponible' : 'Indisponible' ?>
                                </div>
                            </div>

                            <div class="movie-info">
                                <h3 class="movie-title"><?= $movie['title'] ?></h3>
                                <p class="movie-author">
                                    <i class="fas fa-user-tie"></i>
                                    <?= $movie['author'] ?>
                                </p>
                                <p class="movie-genre">
                                    <i class="fas fa-theater-masks"></i>
                                    <?= $movie['genre'] ?>
                                </p>
                                <p class="movie-duration">
                                    <i class="fas fa-clock"></i>
                                    <?php
                                    $hours = intdiv($movie['duration'], 60);
                                    $minutes = $movie['duration'] % 60;
                                    ?>
                                    <?= $hours ?>h <?= $minutes ?>m
                                </p>
                            </div>
                            <?php if ($isLoggedIn) : ?>
                                <div class="movie-actions">
                                    <a href="#" class="action-btn view">
                                        <i class="fas fa-eye"></i>
                                        Emprunter
                                    </a>
                                    <a href="/movies/<?= $movie['id'] ?>/edit" class="action-btn edit">
                                        <i class="fas fa-edit"></i>
                                        Modifer
                                    </a>
                                    <a href="/movies/<?= $movie['id'] ?>/delete" class="action-btn delete">
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