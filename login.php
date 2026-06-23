<!-- login.php -->
 <html>
    <head>
        <title>Login Page</title>
    </head>
    <body>
        <form action="" method="POST">
        <h2>Login Form</h2>
        <label>Username:</label>
        <input type="text" name="username"><br><br>
        <label>Password:</label>
        <input type="password" name="password"><br><br>
        <input type="submit" name="submit" value="login">
</form>
    </body>

    <?php
    if(isset($_POST['submit'])){
        $username = $_POST['username'];
        $password = $_POST['password'];

        if($username == 'admin' && $password == 'admin@123') {
            session_start();

            $_SESSION['username'] = $username;

            header('location: homepage.php');
    
        } else {
            echo '<script>alert("Invalid username or password.")</script>';
        }
    }
    ?>
 </html>