<?php
require 'checksession.php';
require 'dbconnection.php';

$car_id = intval($_POST['car_id']);
$user_id = $_SESSION['user_id'];
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
      $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $driver_license = $_POST['driver_license'];


    $insert_customer = "INSERT INTO customers (user_id, firstname, lastname, email, phone, address, driver_license) 
                        VALUES ('$user_id', '$firstname', '$lastname', '$email', '$phone', '$address', '$driver_license')";
    
    if(mysqli_query($connection, $insert_customer)) {
        $customer_id = mysqli_insert_id($connection);
    } else {
        header('Location: addrental.php?car_id=' . $car_id . '&error=customer_insert_failed');
        exit();
    }
}



// Créer la location
$rental_query = "INSERT INTO rentals (car_id, customer_id, start_date, end_date, total_price, status) 
                 VALUES ('$car_id', '$customer_id', '$start_date', '$end_date', '$total_price', 'active')";

if(mysqli_query($connection, $rental_query)) {
    // Mettre à jour le statut de la voiture
$update_car = "UPDATE cars SET status = 'rented' WHERE id = $car_id AND '$end_date' >= CURDATE()";    mysqli_query($connection, $update_car);
    
    mysqli_close($connection);
    if ($_SESSION['role'] == 'AD') {
        header('Location: allrentals.php?success=added');
    } else {
        header('Location: allcars.php?success=added');
    }
} else {
    echo "Erreur : " . mysqli_error($connection);
}

