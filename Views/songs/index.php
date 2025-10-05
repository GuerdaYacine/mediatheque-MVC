<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Musiques</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/songs/songs.css">
    <link rel="stylesheet" href="/assets/css/partials/header.css">
    <link rel="stylesheet" href="/assets/css/partials/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php require_once __DIR__ . '/../partials/_header.php'; ?>

    <main class="main-content">
        <div class="content-container">
            <div class="page-header">
                <h1><i class="fas fa-music"></i> Musiques</h1>
                <?php if ($isLoggedIn) : ?>
                    <a href="/songs/create" class="add-btn">
                        <i class="fas fa-plus"></i>
                        Ajouter une musique
                    </a>
                <?php endif; ?>
            </div>

            <div class="filter-section">
                <h1>Filtrer</h1>
                <form action="" method="get" class="filter-form">
                    <div class="filter-group">
                        <label for="search">Recherche</label>
                        <input type="text" name="search" id="search" placeholder="Recherchez par titre ou auteur" class="filter-input">
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


            <?php if (empty($songs)) : ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-music"></i>
                    </div>
                    <h3>Aucune musique disponible</h3>
                    <?php if ($isLoggedIn) : ?>
                        <p>Commencez par ajouter votre première musique à la médiathèque.</p>
                        <a href="/songs/create" class="empty-cta">
                            <i class="fas fa-plus"></i>
                            Ajouter une musique
                        </a>
                    <?php endif; ?>
                </div>
            <?php else : ?>
                <div class="songs-grid">
                    <?php foreach ($songs as $song) : ?>
                        <div class="song-card">
                            <div class="song-image">
                                <img src="<?= $song->getImage() ?>"
                                    alt="Image de la musique <?= $song->getTitle() ?>">

                                <div class="availability-badge <?= $song->getAvailable() == 1 ? 'available' : 'unavailable' ?>">
                                    <i class="fas <?= $song->getAvailable() == 1 ? 'fa-check' : 'fa-times' ?>"></i>
                                    <?= $song->getAvailable() == 1 ? 'Disponible' : 'Indisponible' ?>
                                </div>

                                <?php
                                $minutes = floor($song->getDuration() / 60);
                                $seconds = $song->getDuration() % 60;
                                ?>
                                <div class="duration-badge">
                                    <i class="fas fa-clock"></i>
                                    <?= sprintf("%d:%02d", $minutes, $seconds) ?>
                                </div>
                            </div>

                            <div class="song-info">
                                <h3 class="song-title"><?= $song->getTitle() ?></h3>

                                <p class="song-author">
                                    <i class="fas fa-microphone"></i>
                                    <?= $song->getAuthor() ?>
                                </p>

                                <p class="song-album">
                                    <i class="fas fa-compact-disc"></i>
                                    Album : <a href="/albums" class="album-link"><?= $song->getAlbumTitle() ?? 'N/A' ?></a>
                                </p>

                                <div class="song-note">
                                    <i class="fas fa-star"></i>
                                    <?= $song->getNote() ?>
                                </div>
                            </div>

                            <?php if ($isLoggedIn) : ?>
                                <div class="song-actions">
                                    <a href="/songs/<?= $song->getId() ?>/edit" class="action-btn edit">
                                        <i class="fas fa-edit"></i>
                                        Modifier
                                    </a>
                                    <a href="/songs/<?= $song->getId() ?>/delete" class="action-btn delete">
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