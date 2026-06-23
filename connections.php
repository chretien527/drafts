<?php
$username = 'root';
$servername = 'localhost';
$password = "2010"
$dbname = "y1b_db";

$connection = myslqi_connect($servername.$username,$password,$dbname);

if(!$connection){
    echo("Failed to connect:".mysqli_connect_error());
}
echo("Connection succeded");

$stmt = $mysqli ->prepare("INSERT INTO users($Fname,$Lname,$Email,$Password,$Gender) VALUES(?,?,?,?,?)");
$stmt = bind_paramas($firstname,$lastname,$email,$Gender,$password)
;

?>

