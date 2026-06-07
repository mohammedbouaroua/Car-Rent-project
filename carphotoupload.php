<?php
require 'config.php';


print_r($_FILES['photo']);

if($_FILES['photo']['type']!= 'image/jpeg' && $_FILES['photo']['type']!= 'image/jpg' )
{
    header('location:ProductForm.php?error=typephoto');
}
if($_FILES['photo']['size'] > $maxsizefile)
{
    header('location:ProductForm.php?error=sizephoto');
}
$source = $_FILES['photo']['tmp_name'];
$cible = "photos/" . uniqid() . ".jpeg";
move_uploaded_file($source, $cible);
?>