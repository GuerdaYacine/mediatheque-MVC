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
                                <img src="<?= $song['image'] ?>"
                                    alt="Image de la musique <?= $song['title'] ?>"
                                    onerror="this.src='/assets/images/song-placeholder.jpg'">

                                <div class="availability-badge <?= $song['available'] ? 'available' : 'unavailable' ?>">
                                    <i class="fas <?= $song['available'] ? 'fa-check' : 'fa-times' ?>"></i>
                                    <?= $song['available'] ? 'Disponible' : 'Indisponible' ?>
                                </div>

                                <?php
                                $minutes = floor($song['duration'] / 60);
                                $seconds = $song['duration'] % 60;
                                ?>
                                <div class="duration-badge">
                                    <i class="fas fa-clock"></i>
                                    <?= sprintf("%d:%02d", $minutes, $seconds) ?>
                                </div>
                            </div>

                            <div class="song-info">
                                <h3 class="song-title"><?= $song['title'] ?></h3>

                                <p class="song-author">
                                    <i class="fas fa-microphone"></i>
                                    <?= $song['author'] ?>
                                </p>

                                <p class="song-album">
                                    <i class="fas fa-compact-disc"></i>
                                    Album : <a href="/albums" class="album-link"><?= $song['album_title'] ?? 'N/A' ?></a>
                                </p>

                                <div class="song-note">
                                    <i class="fas fa-star"></i>
                                    <?= $song['note'] ?>
                                </div>
                            </div>

                            <?php if ($isLoggedIn) : ?>
                                <div class="song-actions">
                                    <a href="#" class="action-btn edit">
                                        <i class="fas fa-eye"></i>
                                        Emprunter
                                    </a>
                                    <a href="/songs/<?= $song['id'] ?>/edit" class="action-btn edit">
                                        <i class="fas fa-edit"></i>
                                        Modifier
                                    </a>
                                    <a href="/songs/<?= $song['id'] ?>/delete" class="action-btn delete">
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