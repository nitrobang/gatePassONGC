<?php
session_start();

// Include the database connection file
require_once "db_connection.php";

// Check if the user is not logged in
if (!isset($_SESSION["username"]) && !isset($_SESSION["phone_no"])) {
    header("Location: newlogin.php");
    exit();
}
if (!isset($_SESSION['designation'])) {
    //get the designation of the user
    $query = "SELECT * FROM employee WHERE cpfno = '$cpf_no'";
    $result = mysqli_query($connection, $query);
    if (!$result || mysqli_num_rows($result) == 0) {
        header("Location: skdash.php");
        exit();
    }
    $user = mysqli_fetch_assoc($result);
    $designation = $user["designation"];
} else {
    $designation = $_SESSION["designation"];
}
if (isset($_GET['orderno'])) {
    $orderno = $_GET['orderno'];

    $checkquery = "SELECT returnable,coll_approval,security_approval,guard_approval,comp_approval  FROM order_no WHERE orderno = '$orderno'";
    $result = mysqli_query($connection, $checkquery);
    if ($designation == "E") {
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $collApproval = $row['coll_approval'];
            $sApproval = $row['security_approval'];
            $gApproval = $row['guard_approval'];
            $compapproval = $row['comp_approval'];
            $return = $row['returnable'];
            // Check if the 'comp_approval' value is not equal to -1
            if ($collApproval != 1 || $sApproval != 1 || $gApproval != 1 || $compapproval != 2 || $return != 1) {
                header("Location: skdash.php");
                exit();
            }
        }
    } elseif ($designation == "G") {
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $collApproval = $row['coll_approval'];
            $sApproval = $row['security_approval'];
            $gApproval = $row['guard_approval'];
            $compapproval = $row['comp_approval'];
            $return = $row['returnable'];
            // Check if the 'comp_approval' value is not equal to -1
            if ($collApproval != 1 || $sApproval != 1 || $gApproval != 1 || $compapproval != -1 || $return != 1) {
                header("Location: skdash.php");
                exit();
            }
        }
    }
    elseif ($designation == "S") {
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $collApproval = $row['coll_approval'];
            $sApproval = $row['security_approval'];
            $gApproval = $row['guard_approval'];
            $compapproval = $row['comp_approval'];
            $return = $row['returnable'];
            // Check if the 'comp_approval' value is not equal to -1
            if ($collApproval != 1 || $sApproval != 1 || $gApproval != 1 || $compapproval != 1 || $return != 1) {
                header("Location: skdash.php");
                exit();
            }
        }
    }
}
// Logout handling
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["logout"])) {
    // Destroy the session and redirect to the login page
    session_destroy();
    header("Location: newlogin.php");
    exit();
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Dashboard</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
</head>

<body>
    <button class="btn btn-secondary" id="gb" onclick="window.location.href = 'skdash.php'">Go Back</button>

    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <button type="submit" id="lo" class="btn btn-outline-danger" name="logout">Logout</button>
    </form>

    <div class="container">
        <table>
            <tr>
                <td><img src="assets/images.png" class="logo"></td>
                <td>
                    <h1>Oil and Natural Gas Corporation</h1>
                    <h3>MUMBAI REGION- REGIONAL OFFICE- INFOCOM</h3>
                </td>
            </tr>
        </table>

    </div>
    <h3>Guard Page</h3>
    <?php
    // SQL query to fetch fields from a table
    $sql = "SELECT o.descrip, o.nop, o.deliverynote, o.remark, n.moc, n.vehno
FROM orders o
JOIN order_no n ON o.orderno = n.orderno
WHERE o.orderno = " . $orderno;

    $result = $connection->query($sql);

    if ($result->num_rows > 0) {
        echo "<table id='dynamic-table'>";
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
            <input type="submit" class="btn btn-primary" name="receive" value="receive">
          </form>';
    } else {
        echo "No fields found in the table.";
    }


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if ($designation == "G") {
            if (isset($_POST['receive'])) {
                $insert_sql = "UPDATE order_no 
                    SET  comp_approval = 1
                    WHERE orderno =" . $orderno;
                $connection->query($insert_sql);
                $_SESSION['resuccess'] = true; // Using session variable
                // Redirect to the next page
                header("Location: skdash.php");
                exit();
            }
        } else if ($designation =="E") {
            if (isset($_POST['receive'])) {
                $insert_sql = "UPDATE order_no 
                    SET  comp_approval = 3
                    WHERE orderno =" . $orderno;
                $connection->query($insert_sql);
                $_SESSION['resuccess'] = true; // Using session variable
                // Redirect to the next page
                header("Location: skdash.php");
                exit();
            }
        }
        else if ($designation =="S") {
            if (isset($_POST['receive'])) {
                $insert_sql = "UPDATE order_no 
                    SET  comp_approval = 2
                    WHERE orderno =" . $orderno;
                $connection->query($insert_sql);
                $_SESSION['resuccess'] = true; // Using session variable
                // Redirect to the next page
                header("Location: skdash.php");
                exit();
            }
        }
        // Insert the values into the orders table

    }
    ?>
</body>

</html>