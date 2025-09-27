<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'album</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/albums/edit.css">
    <link rel="stylesheet" href="/assets/css/partials/header.css">
    <link rel="stylesheet" href="/assets/css/partials/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php require_once __DIR__ . '/../partials/_header.php'; ?>

    <main class="main-content">
        <div class="content-container">
            <div class="page-header">
                <h1><i class="fas fa-edit"></i> Modifier l'album</h1>
            </div>

            <div class="form-container">
                <?php if (isset($error)) : ?>
                    <div class="general-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <form action="" method="post" enctype="multipart/form-data" class="edit-form">

                    <div class="form-group">
                        <label for="title">
                            <i class="fas fa-music"></i>
                            Titre de l'album
                        </label>
                        <input type="text"
                            name="title"
                            id="title"
                            class="form-input <?= $errors['title'] ? 'error' : '' ?>"
                            value="<?= $title ?? $album->getTitle() ?>">
                        <?php if ($errors['title']) : ?>
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                <?= $errors['title'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="author">
                            <i class="fas fa-user"></i>
                            Auteur
                        </label>
                        <input type="text"
                            name="author"
                            id="author"
                            class="form-input <?= $errors['author'] ? 'error' : '' ?>"
                            value="<?= $author ?? $album->getAuthor() ?>">
                        <?php if ($errors['author']) : ?>
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                <?= $errors['author'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="track_number">
                            <i class="fas fa-list-ol"></i>
                            Nombre de pistes
                        </label>
                        <input type="number"
                            disabled
                            name="track_number"
                            id="track_number"
                            class="form-input disabled <?= $errors['track_number'] ? 'error' : '' ?>"
                            value="<?= isset($trackNumber) ?? $album->getTrackNumber($album->getId()) ?>">
                        <?php if ($errors['track_number']) : ?>
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                <?= $errors['track_number'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="editor">
                            <i class="fas fa-building"></i>
                            Éditeur
                        </label>
                        <input type="text"
                            name="editor"
                            id="editor"
                            class="form-input <?= $errors['editor'] ? 'error' : '' ?>"
                            value="<?= $editor ?? $album->getEditor() ?>">
                        <?php if ($errors['editor']) : ?>
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                <?= $errors['editor'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="image">
                            <i class="fas fa-image"></i>
                            Nouvelle image (optionnel)
                        </label>
                        <input type="file"
                            name="image"
                            id="image"
                            class="file-input <?= isset($errors['image']) && $errors['image'] ? 'error' : '' ?>">
                        <div class="file-help">
                            Formats acceptés : JPG, PNG, WEBP
                        </div>

                        <div class="current-image-section">
                            <span class="current-image-label">Image actuelle :</span>
                            <img src="<?= $album->getImage() ?>"
                                alt="Image de l'album <?= $album->getTitle() ?>"
                                class="current-image">
                        </div>

                        <?php if (isset($errors['image']) && $errors['image']) : ?>
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                <?= $errors['image'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-check-circle"></i>
                            Disponibilité
                        </label>
                        <div class="radio-group <?= $errors['available'] ? 'error' : '' ?>">
                            <div class="radio-option">
                                <input type="radio"
                                    name="available"
                                    value="1"
                                    id="available_yes"
                                    <?= (isset($available) ? ($available == 1) : $album->getAvailable() == 1) ? 'checked' : '' ?>>
                                <label for="available_yes">Disponible</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio"
                                    name="available"
                                    value="0"
                                    id="available_no"
                                    <?= (isset($available) ? ($available == 0) : $album->getAvailable() == 0) ? 'checked' : '' ?>>
                                <label for="available_no">Indisponible</label>
                            </div>
                        </div>
                        <?php if (isset($errors['available']) && $errors['available']) : ?>
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                <?= $errors['available'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-actions">
                        <a href="/albums" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Modifier
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php require_once __DIR__ . '/../partials/_footer.php'; ?>
</body>

</html>