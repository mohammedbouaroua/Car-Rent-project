<?php
// require 'checksession.php';
require 'dbconnection.php';

$car_id = $_POST['car_id'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$total_price = $_POST['total_price'];
$customer_type = $_POST['customer_type'];

// Gestion du client
if($customer_type == 'existing') {
    $customer_email = $_POST['customer_email'];
    $customer_query = "SELECT id FROM customers WHERE email = '$customer_email'";
    $customer_result = mysqli_query($connection, $customer_query);
    
    if(mysqli_num_rows($customer_result) == 0) {
        header('Location: addrental.php?car_id=' . $car_id . '&error=customer_not_found');
        exit();
    }
    
    $customer = mysqli_fetch_array($customer_result);
    $customer_id = $customer['id'];
} else {
    $firstname = mysqli_real_escape_string($connection, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($connection, $_POST['lastname']);
    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $phone = mysqli_real_escape_string($connection, $_POST['phone']);
    $address = mysqli_real_escape_string($connection, $_POST['address']);
    $driver_license = mysqli_real_escape_string($connection, $_POST['driver_license']);
    
    $insert_customer = "INSERT INTO customers (firstname, lastname, email, phone, address, driver_license) 
                        VALUES ('$firstname', '$lastname', '$email', '$phone', '$address', '$driver_license')";
    
    if(mysqli_query($connection, $insert_customer)) {
        $customer_id = mysqli_insert_id($connection);
    } else {
        header('Location: addrental.php?car_id=' . $car_id . '&error=customer_insert_failed');
        exit();
    }
}

// Créer la location
$rental_query = "INSERT INTO rentals (car_id, customer_id, start_date, end_date, total_price, status) 
                 VALUES ('$car_id', '$customer_id', '$start_date', '$end_date', '$total_price', 'confirmed')";

if(mysqli_query($connection, $rental_query)) {
    // Mettre à jour le statut de la voiture
    $update_car = "UPDATE cars SET status = 'rented' WHERE id = $car_id";
    mysqli_query($connection, $update_car);
    
    mysqli_close($connection);
    header('Location: allrentals.php?success=added');
} else {
    echo "Erreur : " . mysqli_error($connection);
}
?>