<?php
include 'connections.php';
if(isset($_POST['submit'])){
    $firstname = $_POST['fname'];
    $lastname = $_POST['lname'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $password = sha1($_POST['password']);
    $sql = "INSERT INTO users(fname,lname,email,gender,password) VALUES 
    ('$firstname','$lastname','$email','$gender','$password')";
    $result = $conn->query($sql);
    if($result === true){
        echo("Record added successfully.");
    } else {
        echo "Error".$sql."<br>".$conn->error;
    }
    $conn->close();
}
?>
<html>
<a href='form.html'><br><br>Go back</a>
<a href='read.php'><br><br>View data records<a/>
</html>
