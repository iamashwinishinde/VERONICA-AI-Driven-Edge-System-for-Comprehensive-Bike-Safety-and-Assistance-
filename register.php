<?php
// Show all errors for debugging (remove or comment out these two lines in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// === STEP A: Use the exact credentials from Hostinger ===
$servername = "localhost";
$db_username = "u433340098_Adityaswork";   // <-- your DB user
$db_password = "Hostinger030625";
$db_name     = "u433340098_Adityas";       // <-- your DB name

// Create a new MySQLi connection
$conn = new mysqli($servername, $db_username, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

// Sanitize and get form data
$firstName = htmlspecialchars(trim($_POST['firstName']));
$lastName = htmlspecialchars(trim($_POST['lastName']));
$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
$phone = htmlspecialchars(trim($_POST['phone']));
$passwordRaw = $_POST['password'];
$confirmPassword = $_POST['confirmPassword'];
$termsAccepted = isset($_POST['terms']) ? 1 : 0;
$createdAt = date('Y-m-d H:i:s');

// Check if email or phone already exists
$checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR phone = ?");
$checkStmt->bind_param("ss", $email, $phone);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    // Redirect back with error toast
    header("Location: register.html?error=user_exists");
    exit();
}
$checkStmt->close();

// Validate passwords match
if ($passwordRaw !== $confirmPassword) {
    header("Location: register.html?error=password_mismatch");
    exit();
}

// Hash the password
$passwordHash = password_hash($passwordRaw, PASSWORD_DEFAULT);

// Prepare statement to avoid SQL injection
$stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, phone, password, terms_accepted, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssis", $firstName, $lastName, $email, $phone, $passwordHash, $termsAccepted, $createdAt);

if ($stmt->execute()) {
    header("Location: index.html");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>