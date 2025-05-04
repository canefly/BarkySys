<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "barksys_db";

// Create a database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>
