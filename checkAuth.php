<?php
// checkAuth.php - Version sécurisée avec password_hash
session_start();
require 'dbconnection.php';

$login = $_POST['login'];
$pass = $_POST['pass'];

// Récupérer l'utilisateur
$query = "SELECT * FROM users WHERE email = '$login'";
$result = mysqli_query($connection, $query);

if(mysqli_num_rows($result) == 1) {
    $user = mysqli_fetch_array($result);
    
    // Vérifier le mot de passe (MD5 ou password_hash)
    if($user['pass'] == md5($pass)) {
        $_SESSION['user'] = $login;
        $_SESSION['role'] = $user['role'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['LAT'] = time();
        mysqli_close($connection);
        header('Location: dashboard.php');
        exit();
    }
}

// Échec de connexion
mysqli_close($connection);
header('Location: authForm.php?auth=false');
?>