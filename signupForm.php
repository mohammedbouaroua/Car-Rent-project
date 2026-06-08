<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" type="text/css" href="cssfiles/signupForm.css"/>
</head>
<body>
    <div class="container">
        <h2>📝 Inscription</h2>
        
        <?php if(isset($_GET['error'])): ?>
            <?php switch($_GET['error']):
                case 'email_exists': ?>
                    <div class="error">❌ Cet email existe déjà</div>
                    <?php break; ?>
                <?php case 'password_mismatch': ?>
                    <div class="error">❌ Les mots de passe ne correspondent pas</div>
                    <?php break; ?>
                <?php case 'empty': ?>
                    <div class="error">❌ Veuillez remplir tous les champs</div>
                    <?php break; ?>
            <?php endswitch; ?>
        <?php endif; ?>
        
        <form action="checkSignup.php" method="POST">
            <div class="row">
                <input type="text" name="fullname" placeholder="Nom complet" required>
                <input type="text" name="phone" placeholder="Téléphone">
            </div>
            
            <input type="email" name="email" placeholder="Email" required>
            
            <div class="row">
                <input type="password" name="password" placeholder="Mot de passe" required>
                <input type="password" name="confirm_password" placeholder="Confirmer" required>
            </div>
            
            <button type="submit">S'inscrire</button>
        </form>
        
        <div class="link">
            Déjà un compte ? <a href="authForm.php">Se connecter</a>
        </div>
    </div>
</body>
</html>