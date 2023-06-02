<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Create a MySQL database connection
$connection = mysqli_connect("localhost", "root", "Qweasdzxc@007", "gate_pass");

// Check if the connection was successful
if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}
