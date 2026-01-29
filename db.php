<?php
$host = "binarytradinghub.in"; // Usually localhost
$dbname = "u433340098_Adityas";
$username = "u433340098_Adityaswork";
$password = "Hostinger030625";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
