<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le film</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/movie/edit.css">
    <link rel="stylesheet" href="/assets/css/partials/header.css">
    <link rel="stylesheet" href="/assets/css/partials/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php require_once __DIR__ . '/../partials/_header.php'; ?>

    <main class="main-content">
        <div class="content-container">
            <div class="page-header">
                <h1><i class="fas fa-edit"></i> Modifier le film</h1>
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
                            <i class="fas fa-film"></i>
                            Titre du film
                        </label>
                        <input type="text"
                            name="title"
                            id="title"
                            class="form-input <?= !empty($errors['title']) ? 'error' : '' ?>"
                            value="<?= $title ?? $movie->getTitle() ?>">
                        <?php if (!empty($errors['title'])) : ?>
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                <?= $errors['title'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="author">
                            <i class="fas fa-user-tie"></i>
                            Réalisateur
                        </label>
                        <input type="text"
                            name="author"
                            id="author"
                            class="form-input <?= !empty($errors['author']) ? 'error' : '' ?>"
                            value="<?= $author ?? $movie->getAuthor() ?>">
                        <?php if (!empty($errors['author'])) : ?>
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                <?= $errors['author'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="genre">
                            <i class="fas fa-theater-masks"></i>
                            Genre
                        </label>
                        <select name="genre"
                            id="genre"
                            class="form-select <?= !empty($errors['genre']) ? 'error' : '' ?>">
                            <?php foreach (\Models\Genre::cases() as $g): ?>
                                <option value="<?= $g->value ?>" <?= ($movie->getGenre()->value === $g->value) ? 'selected' : '' ?>>
                                    <?= $g->value ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!empty($errors['genre'])) : ?>
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                <?= $errors['genre'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="duration">
                            <i class="fas fa-clock"></i>
                            Durée (minutes)
                        </label>
                        <input type="number"
                            name="duration"
                            id="duration"
                            min="1"
                            class="form-input <?= !empty($errors['duration']) ? 'error' : '' ?>"
                            value="<?= $duration ?? $movie->getDuration() ?>">
                        <?php if (!empty($errors['duration'])) : ?>
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                <?= $errors['duration'] ?>
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
                            class="file-input <?= !empty($errors['image']) ? 'error' : '' ?>">
                        <div class="file-help">
                            Formats acceptés : JPG, PNG, WEBP
                        </div>

                        <div class="current-image-section">
                            <span class="current-image-label">Image actuelle :</span>
                            <img src="<?= $movie->getImage() ?>"
                                alt="Image du film <?= $movie->getTitle() ?>"
                                class="current-image">
                        </div>

                        <?php if (!empty($errors['image'])) : ?>
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
                        <div class="radio-group <?= !empty($errors['available']) ? 'error' : '' ?>">
                            <div class="radio-option">
                                <input type="radio"
                                    name="available"
                                    value="1"
                                    id="available_yes"
                                    <?= $movie->getAvailable() == 1 ? 'checked' : '' ?>>
                                <label for="available_yes">Disponible</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio"
                                    name="available"
                                    value="0"
                                    id="available_no"
                                    <?= $movie->getAvailable() == 0 ? 'checked' : '' ?>>
                                <label for="available_no">Indisponible</label>
                            </div>
                        </div>
                        <?php if (!empty($errors['available'])) : ?>
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                <?= $errors['available'] ?>
                            </div>
                        <?php endif; ?>
                    </div>


                    <div class="form-actions">
                        <a href="/movies" class="btn btn-secondary">
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