<?php
session_start();
require_once "db_connection.php";
$conn = $connection;

// Get the input value from the query string
$cpfno = $_GET['cpfno'];
$searchno = $_GET['searchno'];
$venue= $_SESSION['venue'];

if ($searchno == 1) {
    // Prepare and execute a SQL query to fetch the matching users
    $stmt = $conn->prepare("SELECT * FROM employee WHERE cpfno LIKE ? AND designation = 'E' and venue = ?");
    $stmt->bind_param("ss", $searchValue, $venue);
    $searchValue = $cpfno . '%';
    $stmt->execute();
    $result = $stmt->get_result();

    // Store the results in an array
    $users = array();
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    // Close the database connection
    $conn->close();

    echo json_encode($users);
} else if ($searchno == 2) {
    $department = $_SESSION['department'];
    $stmt = $conn->prepare("SELECT * FROM employee WHERE cpfno LIKE ? AND designation = 'E' AND department = ? AND venue = ? AND signatory = 1 ");
    $stmt->bind_param("sss", $searchValue, $department,$venue);
    $searchValue = $cpfno . '%';
    $stmt->execute();
    $result = $stmt->get_result();

    // Store the results in an array
    $users = array();
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    // Close the database connection
    $conn->close();

    // Convert the results to JSON and send the response
    echo json_encode($users);
}
?>
