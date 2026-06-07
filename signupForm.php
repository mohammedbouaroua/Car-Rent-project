<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
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
            width: 400px;
        }
        h2 { text-align: center; }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
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
        a { color: #667eea; text-decoration: none; }
        .link { text-align: center; margin-top: 15px; }
        .row {
            display: flex;
            gap: 10px;
        }
        .row input {
            flex: 1;
        }
    </style>
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