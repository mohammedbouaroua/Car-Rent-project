<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #667eea;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            width: 350px;
        }
        h2 { text-align: center; }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .error {
            background: #fee;
            color: #c33;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }
        .success {
            background: #efe;
            color: #3c3;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }
        a { color: #667eea; text-decoration: none; }
        .link { text-align: center; margin-top: 15px; }
    </style>
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