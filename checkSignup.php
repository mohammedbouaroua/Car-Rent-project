<?php
require 'dbconnection.php';

$fullname = $_POST['fullname'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirm = $_POST['confirm_password'];

// Vérifications
if(empty($fullname) || empty($email) || empty($password)) {
    header('Location: signupForm.php?error=empty');
    exit();
}

if($password !== $confirm) {
    header('Location: signupForm.php?error=password_mismatch');
    exit();
}

// Vérifier si email existe
$check = "SELECT id FROM users WHERE email='$email'";
$result = mysqli_query($connection, $check);

if(mysqli_num_rows($result) > 0) {
    header('Location: signupForm.php?error=email_exists');
    exit();
}

// Insérer l'utilisateur
$hashed_pass = md5($password);
$query = "INSERT INTO users (email, pass, role, fullname, phone) 
          VALUES ('$email', '$hashed_pass', 'US', '$fullname', '$phone')";

if(mysqli_query($connection, $query)) {
    header('Location: authForm.php?auth=registered');
} else {
    header('Location: signupForm.php?error=insert_failed');
}

mysqli_close($connection);
?>