<?php
$username = "root";
$servername = "localhost";
$password = "2010";
$dbname = "y1b_db";

$connection = mysqli_connect($servername, $username, $password, $dbname);
if(!$connection){
    echo('Failed to connect to Database'.mysqli_connect_err0r());
}
echo "Connected to Database successfully";
//mysqli_close($connection);
?>
