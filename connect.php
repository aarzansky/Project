<?php
$host = "localhost";
$username = "root";
$password = "";  
$db = "project";

$conn = mysqli_connect($host, $username, $password, $db,);

if(!$conn){
    die("Database connection failed: " . mysqli_connect_error());
}
?>
