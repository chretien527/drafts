<?php
// ── Database Configuration ──
// Fill in your MySQL credentials below

define('DB_HOST', 'localhost');
define('DB_USER', 'root');         // your MySQL username
define('DB_PASS', '2010');             // ← PUT YOUR PASSWORD HERE
define('DB_NAME', 'user_app');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$conn->query("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
$conn->select_db(DB_NAME);

// Create users table if it doesn't exist
$conn->query("
    CREATE TABLE IF NOT EXISTS users (
        id           INT AUTO_INCREMENT PRIMARY KEY,
        name         VARCHAR(100)  NOT NULL,
        email        VARCHAR(150)  NOT NULL UNIQUE,
        gender       ENUM('Male','Female','Other') NOT NULL,
        password     VARCHAR(255)  NOT NULL,
        created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        last_login   TIMESTAMP NULL DEFAULT NULL,
        login_count  INT DEFAULT 0
    )
");

// Add last_login column if it doesn't exist (compatible with older MySQL)
$col_check = $conn->query("SHOW COLUMNS FROM users LIKE 'last_login'");
if ($col_check->num_rows === 0) {
    $conn->query("ALTER TABLE users ADD COLUMN last_login TIMESTAMP NULL DEFAULT NULL");
}

// Add login_count column if it doesn't exist
$col_check2 = $conn->query("SHOW COLUMNS FROM users LIKE 'login_count'");
if ($col_check2->num_rows === 0) {
    $conn->query("ALTER TABLE users ADD COLUMN login_count INT DEFAULT 0");
}

// ── Admin credentials (hardcoded, not stored in users table) ──
define('ADMIN_EMAIL',    'chretiensano@gmail.com');
define('ADMIN_PASSWORD', 'Admin@2024');   // change this to your preferred admin password
?>
