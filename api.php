<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
$host = 'localhost';
$db = 'user_app';
$user = 'root'; // Change according to your setup
$pass = '2010'; // Change according to your setup
// Connect to the database
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}
// Get the HTTP method
$method = $_SERVER['REQUEST_METHOD'];
// Switch based on the HTTP method
switch ($method) {
case 'GET':
if (isset($_GET['id'])) {
// Read a single user
$id = $_GET['id'];
$result = $conn->query("SELECT * FROM users WHERE
id = $id");
$user = $result->fetch_assoc();
echo json_encode($user);
} else {
// Read all users
$result = $conn->query("SELECT * FROM users");
$users = [];
while ($row = $result->fetch_assoc()) {
$users[] = $row;
}
echo json_encode($users);
}
break;
case 'POST':
// Create a new user
$data = json_decode(file_get_contents("php://input"),
true);
$name = $conn->real_escape_string($data['name']);
$email = $conn->real_escape_string($data['email']);
$conn->query("INSERT INTO users (name, email) VALUES
('$name', '$email')");
echo json_encode(["message" => "User created"]);
break;
case 'PUT':
// Update an existing user
$data = json_decode(file_get_contents("php://input"),
true);
$id = $data['id'];
$name = $conn->real_escape_string($data['name']);
$email = $conn->real_escape_string($data['email']);
$conn->query("UPDATE users SET name='$name',
email='$email' WHERE id=$id");
echo json_encode(["message" => "User updated"]);
break;
case 'DELETE':
// Delete a user
$id = $_GET['id'];
$conn->query("DELETE FROM users WHERE id=$id");
echo json_encode(["message" => "User deleted"]);
break;
default:
echo json_encode(["error" => "Invalid request method"]);
break;
}
$conn->close();
?>