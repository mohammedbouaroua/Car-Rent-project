<?php
// require 'checksession.php';
// if($_SESSION['role'] != 'AD') {
//     header('Location: authForm.php?auth=role');
//     exit();
// }

require 'carphotoupload.php'; 
require 'dbconnection.php';

// Récupération des données
$brand_id = $_POST['brand_id'];
$model = mysqli_real_escape_string($connection, $_POST['model']);
$year = $_POST['year'];
$license_plate = mysqli_real_escape_string($connection, $_POST['license_plate']);
$price_per_day = $_POST['price_per_day'];
$seats = $_POST['seats'] ?? 5;
$transmission = $_POST['transmission'];
$fuel_type = $_POST['fuel_type'];
$status = $_POST['status'];
$description = mysqli_real_escape_string($connection, $_POST['description']);

// Gestion de la photo
$photo = isset($cible) ? $cible : NULL;

// Préparation de la requête
$query = "INSERT INTO cars (brand_id, model, year, license_plate, price_per_day, 
          seats, transmission, fuel_type, status, description, photo) 
          VALUES ('$brand_id', '$model', '$year', '$license_plate', '$price_per_day',
          '$seats', '$transmission', '$fuel_type', '$status', '$description', '$photo')";

if(mysqli_query($connection, $query)) {
    mysqli_close($connection);
    header('location: allcars.php?success=added');
} else {
    echo "Erreur: " . mysqli_error($connection);
}
?>