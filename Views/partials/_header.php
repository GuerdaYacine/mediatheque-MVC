<header class="main-header">
    <div class="header-container">
        <div class="logo">
            <h2><i class="fas fa-book-open"></i> Médiathèque</h2>
        </div>

        <nav class="main-nav">
            <ul class="nav-list">
                <li class="nav-item">
                    <a href="/" class="nav-link">
                        <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/albums" class="nav-link">
                        <i class="fas fa-images"></i>
                        <span>Albums</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/movies" class="nav-link">
                        <i class="fas fa-film"></i>
                        <span>Movies</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/books" class="nav-link">
                        <i class="fas fa-book"></i>
                        <span>Books</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/songs" class="nav-link">
                        <i class="fas fa-music"></i>
                        <span>Songs</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="user-menu">
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="user-info">
                    <span class="welcome-text">Bonjour, <?= htmlspecialchars($_SESSION['username'] ?? 'Utilisateur') ?></span>
                    <a href="/logout" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        Déconnexion
                    </a>
                </div>
            <?php else: ?>
                <div class="auth-links">
                    <a href="/login" class="auth-link">
                        <i class="fas fa-sign-in-alt"></i>
                        Connexion
                    </a>
                    <a href="/register" class="auth-link register">
                        <i class="fas fa-user-plus"></i>
                        Inscription
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <button class="mobile-menu-toggle" id="mobile-menu-toggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>
</header>