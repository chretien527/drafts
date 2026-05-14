<?php
$username = 'root';
$servername = 'localhost';
$password = '2010';
$dbname = 'y1b_db';

$connection = mysqli_connect($servername,$username,$password,$dbname);

if(!$connection){
    alert("Connection failed:".mysqli_connect_error);
}
echo("Connection succeded");
mysqli_close($connection);
?>
<?php
$username = 'root';
$servername = 'localhost';
$password = '2010';
$dbname = 'y1b_db';

$connection = new mysqli($servername.$username,$password,$dbname);

if($connection->connect_error){
    alert("Connection failed:".$connection->connect_error);
} else {
    echo("Connection succeded.");
}
?>