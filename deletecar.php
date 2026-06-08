<?php
require 'checksession.php';
if($_SESSION['role'] != 'AD') {
    header('Location: authForm.php?auth=access_denied');
}
require 'dbconnection.php';

$id = $_GET['id'];

// Récupérer le nom de la photo pour la supprimer
$photo_query = "SELECT photo FROM cars WHERE id = $id";
$photo_result = mysqli_query($connection, $photo_query);
$car = mysqli_fetch_array($photo_result);

// Supprimer la voiture
$query = "DELETE FROM cars WHERE id = $id";

if(mysqli_query($connection, $query)) {
    // Supprimer la photo du serveur
    if($car['photo'] && file_exists($car['photo'])) {
        unlink($car['photo']);
    }
    mysqli_close($connection);
    header('Location: allcars.php?success=deleted');
} else {
    echo "Erreur lors de la suppression : " . mysqli_error($connection);
}
?>