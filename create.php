<?php
include 'databaseConnections.php';
if(isset($_POST['submit'])){
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $gender = $_POST['gender'];
    $sql = "INSERT INTO users(firstname, lastname, email, password, gender) VALUE (
    $firstname, $lastname, $email, $password, $gender
    )";
    $result = $connection->query($sql);
    if(!$result === true ){
        echo "New Record created successfully";
    } else {
        echo ""
    }
    sql_close($connection);
}
?>
<?php
include 'databaseConnections.php';
if (isset($_POST['submit'])) {
$first_name = $_POST['firstname'];
$last_name = $_POST['lastname'];
$email = $_POST['email'];
$password = sha1($_POST['password']);
$gender = $_POST['gender'];
$sql = "INSERT INTO users(Fname,Lname,Email,Password,Gender) VALUES
('$first_name','$last_name','$email','$password','$gender')";
$result = $connection->query($sql);
if ($result === true) {
echo 'New record created successfully.';
} else {
echo 'Error:'.$sql.'<br>'.$connection->error;
}
$connection->close();
}

?>
<html>
<a class="btn btn-info" href="signup.html"><br><br>Back</a>
<a class="btn btn-info" href="read.php"><br><br>View record from database</a>
</html>