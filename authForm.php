<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Location de Voitures</title>
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
        .login-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            width: 350px;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
        input:focus {
            outline: none;
            border-color: #667eea;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background: #5a67d8;
        }
        .register-link {
            text-align: center;
            margin-top: 15px;
        }
        .register-link a {
            color: #667eea;
            text-decoration: none;
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
    </style>
</head>
<body>
    <div class="login-container">
        <h2>🔐 Connexion</h2>
        
        <?php
if(isset($_GET['auth'])) {

    switch($_GET['auth']) {

        case 'empty':
            echo '<div class="error">Veuillez remplir tous les champs</div>';
            break;

        case 'invalid':
            echo '<div class="error">Login ou mot de passe incorrect</div>';
            break;

        case 'inactive':
            echo '<div class="error">Compte désactivé</div>';
            break;

        case 'registered':
            echo '<div class="success">✅ Inscription réussie ! Vous pouvez maintenant vous connecter</div>';
            break;

        default:
            echo '<div class="error">Erreur inconnue</div>';
    }
}
?>
        
        <form action="checkAuth.php" method="POST">
            <div class="form-group">
                <label>📧 Email</label>
                <input type="email" name="login" required placeholder="admin@carrental.com">
            </div>
            
            <div class="form-group">
                <label>🔒 Mot de passe</label>
                <input type="password" name="pass" required placeholder="••••••">
            </div>
            
            <button type="submit">Se connecter</button>
        </form>
        
        <div class="register-link">
            <a href="register.php">Créer un compte</a>
        </div>
        
    
    </div>
</body>
</html>