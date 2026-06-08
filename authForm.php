<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" type="text/css" href="cssfiles/authForm.css"/>
</head>
<body>
    <div class="container">
        <h2>🔐 Connexion</h2>
        
        <?php if(isset($_GET['auth'])): ?>
            <?php switch($_GET['auth']):
                case 'false': ?>
                    <div class="error">❌ Email ou mot de passe incorrect</div>
                    <?php break; ?>
                <?php case 'nonAuth': ?>
                    <div class="error">🔒 Veuillez vous connecter</div>
                    <?php break; ?>
                <?php case 'again': ?>
                    <div class="success">👋 Déconnexion réussie</div>
                    <?php break; ?>
                <?php case 'registered': ?>
                    <div class="success">✅ Inscription réussie ! Connectez-vous</div>
                    <?php break; ?>
                <?php case 'access_denied': ?>
                    <div class="error">⛔ Accès refusé. Connectez-vous en tant qu'administrateur.</div>
                <?php break; ?>
            <?php endswitch; ?>
        <?php endif; ?>
        
        <form action="checkAuth.php" method="POST">
            <input type="email" name="login" placeholder="Email" required>
            <input type="password" name="pass" placeholder="Mot de passe" required>
            <button type="submit">Se connecter</button>
        </form>
        
        <div class="link">
            <a href="signupForm.php">Créer un compte</a>
        </div>
    </div>
</body>
</html>