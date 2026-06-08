<?php
require 'checksession.php';
require 'dbconnection.php';

$rental_id = intval($_GET['id']);

// 1. Get car_id from rental
$query = "SELECT car_id FROM rentals WHERE id = $rental_id";
$result = mysqli_query($connection, $query);
$row = mysqli_fetch_array($result);
if(!$row){
    header("Location: allrentals.php?error=not_found");
    exit();
}

$car_id = $row[0];

// 2. Update rental → cancelled
mysqli_query($connection, "
    UPDATE rentals 
    SET status = 'cancelled'
    WHERE id = $rental_id
");

// 3. Free the car
mysqli_query($connection, "
    UPDATE cars 
    SET status = 'available'
    WHERE id = $car_id
");

header("Location: allrentals.php?success=cancelled");
exit();
?>