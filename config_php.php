<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bnb_database";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Start session for user authentication
session_start();

// Function to check if user is logged in
function checkLogin() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
        header('Location: login.php');
        exit();
    }
}

// Function to sanitize input data
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to validate date format
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}
?>