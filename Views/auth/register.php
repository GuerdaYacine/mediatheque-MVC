<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Médiathèque</title>
    <link rel="stylesheet" href="/../assets/css/auth/authentication.css">
</head>

<body>
    <div class="container">
        <div class="form-wrapper">
            <h1>Créer un compte</h1>

            <form action="" method="POST" class="form">
                <div class="field-group">
                    <label for="username">Nom d'utilisateur</label>
                    <input type="text" name="username" id="username" value="<?= htmlspecialchars($username ?? '') ?>" class="<?= $errors['username'] ? 'error-input' : '' ?>">
                    <div class=" error-message" id="username-error">
                        <?php if ($errors['username']): ?>
                            <p><?= $errors['username'] ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="field-group">
                    <label for="email">Adresse email</label>
                    <input type="email" name="email" id="email" value="<?= htmlspecialchars($email ?? '') ?>" class="<?= $errors['email'] ? 'error-input' : '' ?>">
                    <div class="error-message" id="email-error">
                        <?php if ($errors['email']): ?>
                            <p><?= $errors['email'] ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="field-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" name="password" id="password" class="<?= $errors['password'] ? 'error-input' : '' ?>">
                    <div class="error-message" id="password-error">
                        <?php if ($errors['password']): ?>
                            <p><?= $errors['password'] ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <button type="submit" class="submit-btn">S'inscrire</button>
            </form>

            <p>Déjà inscrit ? <a href="/login">Se connecter</a></p>
        </div>
    </div>
</body>

</html>