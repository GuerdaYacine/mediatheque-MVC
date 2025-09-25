<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer une musique</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/songs/create.css">
    <link rel="stylesheet" href="/assets/css/partials/header.css">
    <link rel="stylesheet" href="/assets/css/partials/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php require_once __DIR__ . '/../partials/_header.php'; ?>

    <main class="main-content">
        <div class="content-container">
            <div class="page-header">
                <h1><i class="fas fa-plus-circle"></i> Créer une musique</h1>
            </div>

            <div class="form-container">
                <?php if (isset($error)) : ?>
                    <div class="general-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <form action="" method="post" enctype="multipart/form-data" class="create-form">

                    <div class="form-group">
                        <label for="title">
                            <i class="fas fa-music"></i>
                            Titre de la musique
                        </label>
                        <input type="text"
                            name="title"
                            id="title"
                            class="form-input <?= $errors['title'] ? 'error' : '' ?>"
                            value="<?= $title ?? '' ?>">
                        <?php if ($errors['title']) : ?>
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                <?= $errors['title'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="author">
                            <i class="fas fa-microphone"></i>
                            Artiste
                        </label>
                        <input type="text"
                            name="author"
                            id="author"
                            class="form-input <?= $errors['author'] ? 'error' : '' ?>"
                            value="<?= $author ?? '' ?>">
                        <?php if ($errors['author']) : ?>
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                <?= $errors['author'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="duration">
                                <i class="fas fa-clock"></i>
                                Durée (en secondes)
                            </label>
                            <input type="number"
                                name="duration"
                                id="duration"
                                class="form-input <?= $errors['duration'] ? 'error' : '' ?>"
                                value="<?= $duration ?? '' ?>"
                                min="1"
                                step="1">
                            <div class="duration-help">
                                Exemple: 180 pour 3 minutes
                            </div>
                            <?php if ($errors['duration']) : ?>
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <?= $errors['duration'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="note">
                                <i class="fas fa-star"></i>
                                Note
                            </label>
                            <input type="number"
                                name="note"
                                id="note"
                                class="form-input <?= $errors['note'] ? 'error' : '' ?>"
                                value="<?= $note ?? '' ?>"
                                min="0"
                                max="5">
                            <div class="note-help">
                                Note de 0 à 5
                            </div>
                            <?php if ($errors['note']) : ?>
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <?= $errors['note'] ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="album_id">
                            <i class="fas fa-compact-disc"></i>
                            Album
                        </label>
                        <select name="album_id"
                            id="album_id"
                            class="form-select <?= isset($errors['album']) && $errors['album'] ? 'error' : '' ?>">
                            <option value="">Sélectionner un album (laissez pour vide)</option>
                            <?php foreach ($albums as $album) : ?>
                                <option value="<?= $album['id'] ?>"
                                    <?= (isset($album_id) && $album_id == $album['id']) ? 'selected' : '' ?>>
                                    <?= $album['title'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['album']) && $errors['album']) : ?>
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                <?= $errors['album'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="image">
                            <i class="fas fa-image"></i>
                            Image de la musique
                        </label>
                        <input type="file"
                            name="image"
                            id="image"
                            class="file-input <?= isset($errors['image']) && $errors['image'] ? 'error' : '' ?>"
                            accept="image/*">
                        <div class="file-help">
                            Formats acceptés : JPG, PNG, WEBP (max. 5MB)
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
                        <div class="radio-group <?= isset($errors['available']) && $errors['available'] ? 'error' : '' ?>">
                            <div class="radio-option">
                                <input type="radio"
                                    name="available"
                                    value="1"
                                    id="available_yes"
                                    <?= (isset($available) && $available == '1') ? 'checked' : '' ?>>
                                <label for="available_yes">Disponible</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio"
                                    name="available"
                                    value="0"
                                    id="available_no"
                                    <?= (isset($available) && $available == '0') ? 'checked' : '' ?>>
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
                        <a href="/songs" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Créer la musique
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php require_once __DIR__ . '/../partials/_footer.php'; ?>
</body>

</html>