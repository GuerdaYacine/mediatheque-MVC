<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un film</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/movie/create.css">
    <link rel="stylesheet" href="/assets/css/partials/header.css">
    <link rel="stylesheet" href="/assets/css/partials/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php require_once __DIR__ . '/../partials/_header.php'; ?>

    <main class="main-content">
        <div class="content-container">
            <div class="page-header">
                <h1><i class="fas fa-plus-circle"></i> Créer un film</h1>
            </div>

            <div class="form-container">
                <?php if (isset($error)) : ?>
                    <div class="general-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" action="" class="create-form">

                    <div class="form-group">
                        <label for="title">
                            <i class="fas fa-film"></i>
                            Titre du film
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
                            <i class="fas fa-user-tie"></i>
                            Réalisateur
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

                    <div class="form-group">
                        <label for="genre">
                            <i class="fas fa-theater-masks"></i>
                            Genre
                        </label>
                        <select name="genre"
                            id="genre"
                            class="form-select <?= $errors['genre'] ? 'error' : '' ?>">
                            <?php foreach (\Models\Genre::cases() as $g): ?>
                                <option value="<?= $g->value ?>" <?= isset($genre) && $genre === $g->value ? 'selected' : '' ?>>
                                    <?= $g->value ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($errors['genre']) : ?>
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
                            class="form-input <?= $errors['duration'] ? 'error' : '' ?>"
                            value="<?= $duration ?? '' ?>">
                        <?php if ($errors['duration']) : ?>
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                <?= $errors['duration'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="image">
                            <i class="fas fa-image"></i>
                            Image du film
                        </label>
                        <input type="file"
                            name="image"
                            id="image"
                            class="file-input <?= isset($errors['image']) ? 'error' : '' ?>"
                            accept="image/*">
                        <div class="file-help">
                            Formats acceptés : JPG, PNG, WEBP
                        </div>
                        <?php if (isset($errors['image'])) : ?>
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
                                    <?= (isset($available) && $available == 1) ? 'checked' : '' ?>>
                                <label for="available_yes">Disponible</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio"
                                    name="available"
                                    value="0"
                                    id="available_no"
                                    <?= (isset($available) && $available == 0) ? 'checked' : '' ?>>
                                <label for="available_no">Indisponible</label>
                            </div>
                        </div>
                        <?php if ($errors['available']) : ?>
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
                            <i class="fas fa-plus"></i>
                            Créer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php require_once __DIR__ . '/../partials/_footer.php'; ?>
</body>

</html>