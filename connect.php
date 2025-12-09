<?php
$host="localhost";
$username="root";
$pass="";
$db="project";
$conn=mysqli_connect($host,$username,$pass,$db);
if(!$conn)
{
    echo die("Database connection failed".mysqli_connect_error($conn));
}
?>