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

            <div>
                <h1>Filtrer</h1>
                <form action="" method="get">
                    <label for="filter">Afficher seulement ceux disponibles</label>
                    <input type="checkbox" name="filter" id="filter">
                    <button>Filtrer</button>
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
                                <img src="<?= $book['image'] ?>"
                                    alt="Couverture du livre <?= $book['title'] ?>"
                                    onerror="this.src='/assets/images/book-placeholder.jpg'">
                                <div class="availability-badge <?= $book['available'] ? 'available' : 'unavailable' ?>">
                                    <i class="fas <?= $book['available'] ? 'fa-check' : 'fa-times' ?>"></i>
                                    <?= $book['available'] ? 'Disponible' : 'Indisponible' ?>
                                </div>
                            </div>

                            <div class="book-info">
                                <h3 class="book-title"><?= $book['title'] ?></h3>
                                <p class="book-author">
                                    <i class="fas fa-user-edit"></i>
                                    <?= $book['author'] ?>
                                </p>
                                <p class="book-pages">
                                    <i class="fas fa-file-alt"></i>
                                    <?= $book['page_number'] ?> page<?= $book['page_number'] > 1 ? 's' : '' ?>
                                </p>
                            </div>

                            <?php if ($isLoggedIn) : ?>
                                <div class="book-actions">
                                    <a href="#" class="action-btn view">
                                        <i class="fas fa-eye"></i>
                                        Emprunter
                                    </a>
                                    <a href="/books/<?= $book['id'] ?>/edit" class="action-btn edit">
                                        <i class="fas fa-edit"></i>
                                        Modifier
                                    </a>
                                    <a href="/books/<?= $book['id'] ?>/delete" class="action-btn delete">
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