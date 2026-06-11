<?php

$servername = 'localhost';
$username = 'root';
$password = 2010;
$db_name = 'student_db';
$mysqli = new mysqli($servername,$username,$password,$db_name);
if(!mysqli){
    exit("Connection failed: ".mysqli_connect_error());
}
echo "Connection succeded";
$stmt = $mysqli -> prepare("INSERT INTO users(firstname,lastname,email,gender,password) values (?,?,?,?,?)");
$stmt = bind_param("ssssi",$firstname,$lastname,$email,$gender,$password);

$firstnamee = 'John';
$lastname = 'Doe';
$email = "John@example.com";
$gender = 'Male';
$password = 1234
$stmt -> execute();

echo "New records created succesfully";
$stmt -> close();
$mysqli -> close();
?>