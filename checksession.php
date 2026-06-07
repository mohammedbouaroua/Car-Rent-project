<?php
require 'config.php';

// Démarrer la session
session_start();
// Vérifier si l'utilisateur est connecté
if(!isset($_SESSION['user'])) {
    header('Location: authForm.php?auth=nonAuth');
    exit();
}

// Vérifier l'expiration de la session
if(isset($_SESSION['LAT']) && (time() - $_SESSION['LAT'] > $ttl)) {
    header('Location: logout.php');
    exit();
}

// Mettre à jour le timestamp
$_SESSION['LAT'] = time();
?>