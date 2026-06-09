<?php

$servername='localhost';
$password='2010';
$dbname='student_db';
$username='root';

$conn = new mysqli($servername,$username,$password,$dbname);

if($conn->connect_error){
    exit("Connection failed:".$conn->connect_error);
} else{
    echo "Connection succeded";
}
?>