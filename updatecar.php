<?php
// require 'checksession.php';
// if($_SESSION['role'] != 'AD') {
//     header('Location: authForm.php?auth=role');
//     exit();
// }

require 'dbconnection.php';

// Récupération des données
$id = $_POST['id'];
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
$photo = null;
if(isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
    require 'carphotoupload.php';
    if(isset($cible)) {
        $photo = $cible;
        
        // Supprimer l'ancienne photo si elle existe
        $query_old = "SELECT photo FROM cars WHERE id = $id";
        $result_old = mysqli_query($connection, $query_old);
        $old_car = mysqli_fetch_array($result_old);
        if($old_car['photo'] && file_exists($old_car['photo'])) {
            unlink($old_car['photo']);
        }
    }
}

// Construction de la requête UPDATE
if($photo) {
    $query = "UPDATE cars SET 
              brand_id = '$brand_id',
              model = '$model',
              year = '$year',
              license_plate = '$license_plate',
              price_per_day = '$price_per_day',
              seats = '$seats',
              transmission = '$transmission',
              fuel_type = '$fuel_type',
              status = '$status',
              description = '$description',
              photo = '$photo'
              WHERE id = $id";
} else {
    $query = "UPDATE cars SET 
              brand_id = '$brand_id',
              model = '$model',
              year = '$year',
              license_plate = '$license_plate',
              price_per_day = '$price_per_day',
              seats = '$seats',
              transmission = '$transmission',
              fuel_type = '$fuel_type',
              status = '$status',
              description = '$description'
              WHERE id = $id";
}

// Exécution de la requête
if(mysqli_query($connection, $query)) {
    mysqli_close($connection);
    header('Location: allcars.php?success=updated');
} else {
    echo "Erreur lors de la mise à jour : " . mysqli_error($connection);
}
?>