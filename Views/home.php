<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Médiathèque</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/home.css">
    <link rel="stylesheet" href="/assets/css/partials/header.css">
    <link rel="stylesheet" href="/assets/css/partials/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php require_once __DIR__ . '/partials/_header.php'; ?>

    <main class="main-content">
        <div class="content-container">
            <div class="hero-section">
                <h1><i class="fas fa-home"></i> Bienvenue dans votre médiathèque</h1>
                <p>Découvrez notre collection de livres, films et albums musicaux</p>
            </div>

            <section class="media-section">
                <div class="section-header">
                    <h2><i class="fas fa-images"></i> Albums disponibles</h2>
                    <a href="/albums" class="view-all-btn">
                        Voir tous les albums
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <?php if (empty($albums)) : ?>
                    <div class="empty-message">
                        <i class="fas fa-images"></i>
                        <p>Aucun album disponible</p>
                    </div>
                <?php else : ?>
                    <div class="media-grid">
                        <?php foreach($albums as $album) : ?>
                            <div class="media-card album-card">
                                <div class="media-image">
                                    <img src="<?=$album['image'] ?>" alt="<?= $album['title'] ?>">
                                    <div class="availability-badge <?= $album['available'] ? 'available' : 'unavailable' ?>">
                                        <i class="fas <?= $album['available'] ? 'fa-check' : 'fa-times' ?>"></i>
                                        <?= $album['available'] ? 'Disponible' : 'Indisponible' ?>
                                    </div>
                                </div>
                                <div class="media-info">
                                    <h3 class="media-title"><?= $album['title'] ?></h3>
                                    <p class="media-detail">
                                        <i class="fas fa-user"></i>
                                        <?= $album['author'] ?>
                                    </p>
                                    <p class="media-detail">
                                        <i class="fas fa-building"></i>
                                        <?= $album['editor'] ?>
                                    </p>
                                    <p class="media-detail">
                                        <i class="fas fa-music"></i>
                                        <?= $album['track_number'] ?> piste<?= $album['track_number'] > 1 ? 's' : '' ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>
            </section>

            <section class="media-section">
                <div class="section-header">
                    <h2><i class="fas fa-film"></i> Films disponibles</h2>
                    <a href="/movies" class="view-all-btn">
                        Voir tous les films
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <?php if (empty($movies)) : ?>
                    <div class="empty-message">
                        <i class="fas fa-film"></i>
                        <p>Aucun film disponible</p>
                    </div>
                <?php else : ?>
                    <div class="media-grid">
                        <?php foreach($movies as $movie) : ?>
                            <div class="media-card movie-card">
                                <div class="media-image">
                                    <img src="<?= $movie['image'] ?>" alt="<?= $movie['title'] ?>">
                                    <div class="availability-badge <?= $movie['available'] ? 'available' : 'unavailable' ?>">
                                        <i class="fas <?= $movie['available'] ? 'fa-check' : 'fa-times' ?>"></i>
                                        <?= $movie['available'] ? 'Disponible' : 'Indisponible' ?>
                                    </div>
                                </div>
                                <div class="media-info">
                                    <h3 class="media-title"><?= $movie['title'] ?></h3>
                                    <p class="media-detail">
                                        <i class="fas fa-user-tie"></i>
                                        <?= $movie['author'] ?>
                                    </p>
                                    <p class="media-detail">
                                        <i class="fas fa-theater-masks"></i>
                                        <?= $movie['genre'] ?>
                                    </p>
                                    <p class="media-detail">
                                        <i class="fas fa-clock"></i>
                                        <?php
                                        $hours = intdiv($movie['duration'], 60);
                                        $minutes = $movie['duration'] % 60;
                                        ?>
                                        <?= $hours ?>h <?= $minutes ?>m
                                    </p>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>
            </section>

            <section class="media-section">
                <div class="section-header">
                    <h2><i class="fas fa-book"></i> Livres disponibles</h2>
                    <a href="/books" class="view-all-btn">
                        Voir tous les livres
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <?php if (empty($books)) : ?>
                    <div class="empty-message">
                        <i class="fas fa-book-open"></i>
                        <p>Aucun livre disponible</p>
                    </div>
                <?php else : ?>
                    <div class="media-grid">
                        <?php foreach($books as $book) : ?>
                            <div class="media-card book-card">
                                <div class="media-image">
                                    <img src="<?= $book['image'] ?>" 
                                         alt="Couverture du livre <?= $book['title'] ?>"
                                         onerror="this.src='/assets/images/book-placeholder.jpg'">
                                    <div class="availability-badge <?= $book['available'] ? 'available' : 'unavailable' ?>">
                                        <i class="fas <?= $book['available'] ? 'fa-check' : 'fa-times' ?>"></i>
                                        <?= $book['available'] ? 'Disponible' : 'Indisponible' ?>
                                    </div>
                                </div>
                                <div class="media-info">
                                    <h3 class="media-title"><?= $book['title'] ?></h3>
                                    <p class="media-detail">
                                        <i class="fas fa-user-edit"></i>
                                        <?= $book['author'] ?>
                                    </p>
                                    <p class="media-detail">
                                        <i class="fas fa-file-alt"></i>
                                        <?= $book['page_number'] ?> page<?= $book['page_number'] > 1 ? 's' : '' ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>
            </section>
        </div>
    </main>

    <?php require_once __DIR__ . '/partials/_footer.php'; ?>
</body>

</html>