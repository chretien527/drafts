<?php
$servername = 'localhost';
$username = 'root';
$password = '2010';

try {
    $conn = new PDO("mysql:host=$servername;dbname=y1b_db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully";
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
