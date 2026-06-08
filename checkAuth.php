<?php
session_start();
require 'dbconnection.php';

$login = $_POST['login'];
$pass = md5($_POST['pass']);

$query = "SELECT * FROM users WHERE email='$login' AND pass='$pass'";
$result = mysqli_query($connection, $query);

if(mysqli_num_rows($result) == 1) {
    $user = mysqli_fetch_array($result);
    $_SESSION['user'] = $login;
    $_SESSION['role'] = $user['role'];
    $_SESSION['fullname'] = $user['fullname'];
    if($_SESSION['role'] == 'AD') {
        header('Location: dashboard.php');
    } else {
        header('Location: allcars.php');
    }
} else {
    header('Location: authForm.php?auth=false');
}

mysqli_close($connection);
?>