<?php
session_start();
require_once "db_connection.php";
$conn=$connection;

// Get the input value from the query string
$cpfno = $_GET['cpfno'];

// Prepare and execute a SQL query to fetch the matching users
$stmt = $conn->prepare("SELECT u.username, u.email, u.cpfno FROM users u 
    WHERE u.cpfno LIKE ? AND u.id IN (
    SELECT ug.id FROM user_to_groups ug WHERE ug.group_id = 2
)");
$stmt->bind_param("s", $searchValue);
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
?>
