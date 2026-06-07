<?php
session_start();
unset($_SESSION['user']);
session_destroy();
header('Location: authForm.php?auth=again');
exit();
?>