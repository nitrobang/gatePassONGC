<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Create a MySQL database connection
$servername = "localhost";
$username = "root";
$password = "Qweasdzxc@007";
$dbname = "gate_pass";
$connection = mysqli_connect($servername, $username, $password, $dbname);

// Check if the connection was successful
if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}
