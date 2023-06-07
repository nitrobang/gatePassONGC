<?php
session_start();

// Include the database connection file
require_once "db_connection.php";

// SQL query to fetch fields from a table
$sql = "SELECT descrip, nop, deliverynote, remark, moc, vehno FROM orders WHERE orderno = (SELECT MAX(orderno) FROM order_no)";

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
    echo "<tr><th>Brief description</th><th>No of Packages</th><th>Delivery Note Or Dispatch Convey Note No OR Indent No</th><th>Remarks</th><th>Mode of Collection</th><th>Vehicle Number</th></tr>";

    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["descrip"] . "</td>";
        echo "<td>" . $row["nop"] . "</td>";
        echo "<td>" . $row["deliverynote"] . "</td>";
        echo "<td>" . $row["remark"] . "</td>";
        echo "<td>" . $row["moc"] . "</td>";
        echo "<td>" . $row["vehno"] . "</td>";
        echo "</tr>";
    }

    echo "</table>";

    // Add form to input "Mode of Collection" and "Vehicle Number"
    echo '<form method="POST" action="">
            <label for="securityn">Name:</label>
            <input type="text" id="securityn" name="securityn" required><br><br>

            <input type="submit" name= "revert" value="Revert">
            <input type="submit" name= "approve" value="Approve">
          </form>';

} else {
    echo "No fields found in the table.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the input values from the form
    $securityn = $_POST["securityn"];

    // Insert the values into the orders table
    $insert_sql = "UPDATE order_no 
                   SET securityn = '$securityn'
                   WHERE orderno = (SELECT MAX(orderno) FROM order_no)";

    if ($connection->query($insert_sql) === TRUE) 
    {
    } else {
        echo "Error: " . $insert_sql . "<br>" . $connection->error;
    }
    if (isset($_POST["revert"])) {
        // Code to handle revert button
        // Perform necessary actions when the revert button is clicked

        echo "Request Reverted";
    } elseif (isset($_POST["approve"])) {
        // Code to handle approve button
        // Perform necessary actions when the approve button is clicked

        echo "Request Approved";
    }
}

$connection->close();
?>
