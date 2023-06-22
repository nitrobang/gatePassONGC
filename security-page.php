<?php
session_start();

// Include the database connection file
require_once "db_connection.php";

// Check if the user is not logged in
if (!isset($_SESSION["username"])) {
    header("Location: newlogin.php");
    exit();
}

// Logout handling
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["logout"])) {
    // Destroy the session and redirect to the login page
    session_destroy();
    header("Location: newlogin.php");
    exit();
}

// SQL query to fetch fields from a table
$sql = "SELECT o.descrip, o.nop, o.deliverynote, o.remark, n.moc, n.vehno
FROM orders o
JOIN order_no n ON o.orderno = n.orderno
WHERE o.orderno = " . $_SESSION['orderno'];

$result = $connection->query($sql);

if ($result->num_rows > 0) {
    echo "<style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        
        th, td {
            text-align: center;
            padding: 8px;
            border: 1px solid black;
        }
        
        th {
            background-color: #f2f2f2;
        }
        
        tr:nth-child(even) {
            text-align: center;
            background-color: #f2f2f2;
        }
    </style>";
    echo "<a href='skdash.php'>Go Back</a>";
    echo "<table>";
    echo "<tr><th>Brief description</th><th>No of Packages</th><th>Delivery Note Or Dispatch Convey Note No OR Indent No</th><th>Remarks</th><th>Mode Of Collection</th><th>Vehicle Number</th></tr>";

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

            <input type="submit" name="revert" value="Revert">
            <input type="submit" name="approve" value="Approve">
          </form>';

} else {
    echo "No fields found in the table.";
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the input values from the form
    $securityn = $_POST["securityn"];

    // Insert the values into the orders table
    if (isset($_POST['approve'])) {
        $insert_sql = "UPDATE order_no 
                SET securityn = '$securityn', security_approval = 1
                WHERE orderno =" . $_SESSION['orderno'];
        $connection->query($insert_sql);
        header('Location: skdash.php');
        exit();
    } else if (isset($_POST['revert'])) {
        $insert_sql = "UPDATE order_no 
                SET securityn = '$securityn', security_approval = -1
                WHERE orderno =" . $_SESSION['orderno'];
        $connection->query($insert_sql);
        header('Location: skdash.php');
        exit();
    }
}

$connection->close();
?>
