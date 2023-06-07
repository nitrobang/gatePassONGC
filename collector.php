<?php
session_start();

// Include the database connection file
require_once "db_connection.php";

// SQL query to fetch fields from a table
$sql = "SELECT descrip, nop, deliverynote, remark FROM orders WHERE orderno = (SELECT MAX(orderno) FROM order_no)";

$result = $connection->query($sql);

if ($result->num_rows > 0) {
    echo "<style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        
        th, td {
            text-align: Center;
            padding: 8px;
            border: 1px solid black; /* Add black border */
        }
        
        th {
            background-color: #f2f2f2;
        }
        
        tr:nth-child(even) {
            text-align: Center;
            background-color: #f2f2f2;
        }
    </style>";

    echo "<table>";
    echo "<tr><th>Brief description</th><th>No of Packages</th><th>Delivery Note Or Dispatch Convey Note No OR Indent No</th><th>Remarks</th></tr>";

    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["descrip"] . "</td>";
        echo "<td>" . $row["nop"] . "</td>";
        echo "<td>" . $row["deliverynote"] . "</td>";
        echo "<td>" . $row["remark"] . "</td>";
        echo "</tr>";
    }

    echo "</table>";

    // Add form to input "Mode of Collection" and "Vehicle Number"
    echo '<form method="POST" action="">
            <label for="mode_of_collection">Mode of Collection:</label>
            <input type="text" id="mode_of_collection" name="mode_of_collection" required><br><br>
            
            <label for="vehicle_number">Vehicle Number:</label>
            <input type="text" id="vehicle_number" name="vehicle_number" required><br><br>
            
            <input type="submit" value="Submit">
          </form>';

} else {
    echo "No fields found in the table.";
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the input values from the form
    $mode_of_collection = $_POST["mode_of_collection"];
    $vehicle_number = $_POST["vehicle_number"];

    // Insert the values into the orders table
    $insert_sql = "UPDATE orders 
                   SET mode_of_collection = '$mode_of_collection', vehicle_number = '$vehicle_number' 
                   WHERE orderno = (SELECT MAX(orderno) FROM order_no)";

    if ($connection->query($insert_sql) === TRUE) {
        echo "New record inserted successfully.";
    } else {
        echo "Error: " . $insert_sql . "<br>" . $connection->error;
    }
}

// Close the connection
$connection->close();
?>
