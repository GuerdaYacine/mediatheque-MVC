<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un livre</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/books/create.css">
    <link rel="stylesheet" href="/assets/css/partials/header.css">
    <link rel="stylesheet" href="/assets/css/partials/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php require_once __DIR__ . '/../partials/_header.php'; ?>

    <main class="main-content">
        <div class="content-container">
            <div class="page-header">
                <h1><i class="fas fa-book-medical"></i> Créer un livre</h1>
            </div>

            <div class="form-container">
                <?php if (isset($error)) : ?>
                    <div class="general-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form action="" method="POST" enctype="multipart/form-data" class="create-form">

                    <div class="form-group">
                        <label for="title">
                            <i class="fas fa-heading"></i>
                            Titre du livre
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
                            <i class="fas fa-user-edit"></i>
                            Auteur
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
                        <label for="page_number">
                            <i class="fas fa-file-alt"></i>
                            Nombre de pages
                        </label>
                        <input type="number"
                            name="page_number"
                            id="page_number"
                            class="form-input <?= $errors['page_number'] ? 'error' : '' ?>"
                            value="<?= $pageNumber ?? '' ?>"
                            min="1">
                        <?php if ($errors['page_number']) : ?>
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                <?= $errors['page_number'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="image">
                            <i class="fas fa-image"></i>
                            Couverture du livre
                        </label>
                        <input type="file"
                            name="image"
                            id="image"
                            class="file-input <?= isset($errors['image']) && $errors['image'] ? 'error' : '' ?>">
                        <div class="file-help">
                            Formats acceptés : JPG, PNG, WEBP
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
                        <a href="/books" class="btn btn-secondary">
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