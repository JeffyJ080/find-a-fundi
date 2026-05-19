<?php
$host = "your_database_host";
$dbname = "your_database_name";
$username = "your_database_username";
$password = "your_database_password";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>