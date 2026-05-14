<?php

$servername = "localhost";
$dbname = "Notes";
$username = "root";
$password = "2010";

if(!$client){
    echo("connection failed:".mysql_connect_error());
}

echo("Connection succeded.");
mysql_close($client);

$connection = mysqli_connect($servername, $username, $password, $dbname);

//check connection
if(!$connection){
    echo('Connection failed:'.mysqli_connect_error());
}

echo("Connection succeded.");
mysql_close($connection);
?>