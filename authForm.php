<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <?php require_once 'icon_helper.php'; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" type="text/css" href="cssfiles/authForm.css"/>
</head>
<body>
    <div class="container">
        <h2><?= ui_icon('lock') ?> Connexion</h2>

        <?php if (isset($_GET['auth'])): ?>
            <?php if ($_GET['auth'] === 'false'): ?>
                <div class="error"><?= ui_icon('x-circle') ?> Email ou mot de passe incorrect</div>
            <?php elseif ($_GET['auth'] === 'nonAuth'): ?>
                <div class="error"><?= ui_icon('lock-open') ?> Veuillez vous connecter</div>
            <?php elseif ($_GET['auth'] === 'again'): ?>
                <div class="success"><?= ui_icon('logout') ?> Deconnexion reussie</div>
            <?php elseif ($_GET['auth'] === 'registered'): ?>
                <div class="success"><?= ui_icon('check-circle') ?> Inscription reussie ! Connectez-vous</div>
            <?php elseif ($_GET['auth'] === 'access_denied'): ?>
                <div class="error"><?= ui_icon('ban') ?> Acces refuse. Connectez-vous en tant qu'administrateur.</div>
            <?php endif; ?>
        <?php endif; ?>

        <form action="checkAuth.php" method="POST">
            <input type="email" name="login" placeholder="Email" required>
            <input type="password" name="pass" placeholder="Mot de passe" required>
            <button type="submit">Se connecter</button>
        </form>

        <div class="link">
            <a href="signupForm.php">Creer un compte</a>
        </div>
    </div>
</body>
</html>