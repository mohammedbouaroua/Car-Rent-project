<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <?php require_once 'icon_helper.php'; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" type="text/css" href="cssfiles/signupForm.css"/>
</head>
<body>
    <div class="container">
        <h2><?= ui_icon('pencil-square') ?> Inscription</h2>

        <?php if (isset($_GET['error'])): ?>
            <?php if ($_GET['error'] === 'email_exists'): ?>
                <div class="error"><?= ui_icon('x-circle') ?> Cet email existe deja</div>
            <?php elseif ($_GET['error'] === 'password_mismatch'): ?>
                <div class="error"><?= ui_icon('x-circle') ?> Les mots de passe ne correspondent pas</div>
            <?php elseif ($_GET['error'] === 'empty'): ?>
                <div class="error"><?= ui_icon('x-circle') ?> Veuillez remplir tous les champs</div>
            <?php endif; ?>
        <?php endif; ?>

        <form action="checkSignup.php" method="POST">
            <div class="row">
                <input type="text" name="fullname" placeholder="Nom complet" required>
                <input type="text" name="phone" placeholder="Telephone">
            </div>

            <input type="email" name="email" placeholder="Email" required>

            <div class="row">
                <input type="password" name="password" placeholder="Mot de passe" required>
                <input type="password" name="confirm_password" placeholder="Confirmer" required>
            </div>

            <button type="submit">S'inscrire</button>
        </form>

        <div class="link">
            Deja un compte ? <a href="authForm.php">Se connecter</a>
        </div>
    </div>
</body>
</html>