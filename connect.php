<?php
$servername = 'localhost';
$username = 'root';
$password = '2010';
$dbname = 'y1c_db';

$mysqli = new mysqli($servername,$username,$password,$dbname);
if(!$mysqli){
    echo("Failed to connect:".mysqli_connect_error());
}
echo('Connected successfully.');

$stmt = $mysqli -> prepare("INSERT INTO users(fname,lname,gender,email,password) VALUES(?,?,?,?,?)");
$stmt->bind_param('ssssi', $firstname,$lastname,$gender,$email,$password);

$firstname = 'Sano';
$lastname = 'Chretien';
$gender = 'Male';
$email = 'sanochretien@gmail.com';
$password = 12345;

$stmt->execute();
echo "<br>Record was added successfully";
$stmt->close();
$mysqli->close();
?>