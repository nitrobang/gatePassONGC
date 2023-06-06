<?php
session_start();

// Include the database connection file
require_once "db_connection.php";

// SQL query to fetch fields from a table
$sql = "SELECT descrip,nop,deliverynote,remark FROM orders WHERE orderno = (SELECT MAX(orderno) FROM order_no)";

$result = $connection->query($sql);

if ($result->num_rows > 0) {
    echo "<style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        
        th, td {
            text-align: Centre;
            padding: 8px;
            border: 1px solid black; /* Add black border */
        }
        
        th {
            background-color: #f2f2f2;
        }
        
        tr:nth-child(even) {
            text-align:Centre;
            background-color: #f2f2f2;
        }
    </style>";

    echo "<table>";
    echo "<tr><th>Brief description</th><th>No of Packages</th><th>Deliver Note Or Dispatch convey note no OR Indent no</th><th>Remarks</th></tr>";

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
} else {
    echo "No fields found in the table.";
}

// Close the connection
$connection->close();
?>
