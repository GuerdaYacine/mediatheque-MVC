<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Album</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/albums/albums.css">
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
                <h1><i class="fas fa-images"></i> Albums</h1>
                <?php if ($isLoggedIn) : ?>
                    <a href="/albums/create" class="add-btn">
                        <i class="fas fa-plus"></i>
                        Ajouter un album
                    </a>
                <?php endif; ?>
            </div>

            <?php if (empty($albums)) : ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-images"></i>
                    </div>
                    <h3>Aucun album disponible</h3>
                    <?php if ($isLoggedIn) : ?>
                        <p>Commencez par ajouter votre premier album à la médiathèque.</p>
                        <a href="/albums/create" class="empty-cta">
                            <i class="fas fa-plus"></i>
                            Ajouter un album
                        </a>
                    <?php endif; ?>

                </div>
            <?php else : ?>
                <div class="albums-grid">
                    <?php foreach ($albums as $album) : ?>
                        <div class="album-card">
                            <div class="album-image">
                                <img src="<?= $album['image'] ?>" alt="<?= $album['title'] ?>">
                                <div class="availability-badge <?= $album['available'] ? 'available' : 'unavailable' ?>">
                                    <i class="fas <?= $album['available'] ? 'fa-check' : 'fa-times' ?>"></i>
                                    <?= $album['available'] ? 'Disponible' : 'Indisponible' ?>
                                </div>
                            </div>

                            <div class="album-info">
                                <h3 class="album-title"><?= $album['title'] ?></h3>
                                <p class="album-author">
                                    <i class="fas fa-user"></i>
                                    <?= $album['author'] ?>
                                </p>
                                <p class="album-editor">
                                    <i class="fas fa-building"></i>
                                    <?= $album['editor'] ?>
                                </p>
                                <p class="album-tracks">
                                    <i class="fas fa-music"></i>
                                    <?= $album['track_number'] ?> piste<?= $album['track_number'] > 1 ? 's' : '' ?>
                                </p>
                            </div>
                            <?php if ($isLoggedIn) : ?>
                                <div class="album-actions">
                                    <a href="/albums/<?= $album['id'] ?>/borrow" class="action-btn view">
                                        <i class="fas fa-eye"></i>
                                        Emprunter
                                    </a>
                                    <a href="/albums/<?= $album['id'] ?>/edit" class="action-btn edit">
                                        <i class="fas fa-edit"></i>
                                        Modifer
                                    </a>
                                    <a href="/albums/<?= $album['id'] ?>/delete" class="action-btn delete">
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