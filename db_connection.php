<?php
require_once 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Create a MySQL database connection
$servername = $_ENV["SERVERNAME"];
$username = $_ENV["USERNAME"];
$password = $_ENV["PASSWORD"];
$dbname = $_ENV["DBNAME"];
$connection = mysqli_connect($servername, $username, $password, $dbname);

// Check if the connection was successful
if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}
