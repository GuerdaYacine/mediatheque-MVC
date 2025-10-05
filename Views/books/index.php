<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livres</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/books/books.css">
    <link rel="stylesheet" href="/assets/css/partials/header.css">
    <link rel="stylesheet" href="/assets/css/partials/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php require_once __DIR__ . '/../partials/_header.php'; ?>

    <main class="main-content">
        <div class="content-container">
            <div class="page-header">
                <h1><i class="fas fa-book"></i> Livres</h1>
                <?php if ($isLoggedIn) : ?>
                    <a href="/books/create" class="add-btn">
                        <i class="fas fa-plus"></i>
                        Ajouter un livre
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

            <?php if (empty($books)) : ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <h3>Aucun livre disponible</h3>
                    <?php if ($isLoggedIn) : ?>
                        <p>Commencez par ajouter votre premier livre à la médiathèque.</p>
                        <a href="/books/create" class="empty-cta">
                            <i class="fas fa-plus"></i>
                            Ajouter un livre
                        </a>
                    <?php endif; ?>
                </div>
            <?php else : ?>
                <div class="books-grid">
                    <?php foreach ($books as $book) : ?>
                        <div class="book-card">
                            <div class="book-image">
                                <img src="<?= $book->getImage() ?>"
                                    alt="Couverture du livre <?= $book->getTitle() ?>">
                                <div class="availability-badge <?= $book->getAvailable() == 1 ? 'available' : 'unavailable' ?>">
                                    <i class="fas <?= $book->getAvailable() == 1 ? 'fa-check' : 'fa-times' ?>"></i>
                                    <?= $book->getAvailable() == 1 ? 'Disponible' : 'Indisponible' ?>
                                </div>
                            </div>

                            <div class="book-info">
                                <h3 class="book-title"><?= $book->getTitle() ?></h3>
                                <p class="book-author">
                                    <i class="fas fa-user-edit"></i>
                                    <?= $book->getAuthor() ?>
                                </p>
                                <p class="book-pages">
                                    <i class="fas fa-file-alt"></i>
                                    <?= $book->getPageNumber() ?> page<?= $book->getPageNumber() > 1 ? 's' : '' ?>
                                </p>
                            </div>

                            <?php if ($isLoggedIn) : ?>
                                <div class="book-actions">
                                    <?php if ($book->getAvailable()) : ?>
                                        <a href="/books/<?= $book->getId() ?>/borrow" class="action-btn view">
                                            <i class="fas fa-eye"></i>
                                            Emprunter
                                        </a>
                                    <?php elseif ($book->getBorrowerId($book->getId()) === $_SESSION['user_id']) : ?>
                                        <a href="/books/<?= $book->getId() ?>/return" class="action-btn return">
                                            <i class="fas fa-undo"></i>
                                            Rendre
                                        </a>
                                    <?php else : ?>
                                        <button class="action-btn view" disabled>
                                            <i class="fas fa-eye"></i>
                                            Emprunter (indisponible)
                                        </button>
                                    <?php endif; ?>

                                    <a href="/books/<?= $book->getId() ?>/edit" class="action-btn edit">
                                        <i class="fas fa-edit"></i>
                                        Modifier
                                    </a>
                                    <a href="/books/<?= $book->getId() ?>/delete" class="action-btn delete">
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