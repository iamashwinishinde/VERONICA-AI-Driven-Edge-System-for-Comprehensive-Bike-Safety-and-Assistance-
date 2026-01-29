<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// DB connection
$servername = "localhost";
$db_username = "u433340098_Adityaswork";
$db_password = "Hostinger030625";
$db_name     = "u433340098_Adityas";

$conn = new mysqli($servername, $db_username, $db_password, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Check if user exists
$stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 1) {
    $stmt->bind_result($id, $hashed_password);
    $stmt->fetch();

    if (password_verify($password, $hashed_password)) {
        // Successful login
        $_SESSION['user_id'] = $id;
        header("Location: index.html?success=1");
        exit();
    } else {
        // Password incorrect
        header("Location: index.html?error=1");
        exit();
    }
} else {
    // Email not found
    header("Location: index.html?error=1");
    exit();
}

$stmt->close();
$conn->close();
?>