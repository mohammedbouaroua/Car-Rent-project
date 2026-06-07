<?php
define('host','localhost');
define('user','root');
define('pass','');
define('port','3306');
define('db','car_rental');

$connection = mysqli_connect(host, user,pass,db);
if($connection == false){echo "pb de connection";exit(1);}
// else echo "bonne connection"
?>