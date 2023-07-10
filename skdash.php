<?php
session_start();
require_once "db_connection.php";

// Check if the user is not logged in
if (!isset($_SESSION["username"]) && !isset($_SESSION["phone_no"])) {
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

$conn = $connection;
if (isset($_SESSION["cpf_no"])) {
    $cpf_no = $_SESSION["cpf_no"];
}
if (isset($_SESSION['fsuccess'])) {
    if ($_SESSION['fsuccess']) {
        echo "<script>alert('Order Submitted Successfully');</script>";
        $_SESSION['fsuccess'] = false;
    }
}
if (isset($_SESSION['esuccess'])) {
    if ($_SESSION['esuccess']) {
        echo "<script>alert('Order Edited Successfully');</script>";
        $_SESSION['esuccess'] = false;
    }
}
if (isset($_SESSION['cantedit'])) {
    if ($_SESSION['cantedit']) {
        echo "<script>alert('Order Can't be Edited');</script>";
        $_SESSION['cantedit'] = false;
    }
}
if (isset($_SESSION['asuccess'])) {
    if ($_SESSION['asuccess']) {
        echo "<script>alert('Order Approved Successfully');</script>";
        $_SESSION['asuccess'] = false;
    }
}
if (isset($_SESSION['rsuccess'])) {
    if ($_SESSION['rsuccess']) {
        echo "<script>alert('Order Reverted Successfully');</script>";
        $_SESSION['rsuccess'] = false;
    }
}
if (isset($_SESSION['resuccess'])) {
    if ($_SESSION['resuccess']) {
        echo "<script>alert('Order Received Successfully');</script>";
        $_SESSION['resuccess'] = false;
    }
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
} else $designation = $_SESSION["designation"];


// Check if the user clicked on the collector link
if (($designation == "E") && isset($_GET['orderno']) && isset($_GET['sign'])) {
    $_SESSION['orderno'] = $_GET['orderno'];
    header("Location: signatory.php");
    exit();
} else if (($designation == "E") && isset($_GET['orderno'])) {
    $_SESSION['orderno'] = $_GET['orderno'];
    header("Location: collector-page.php");
    exit();
}

//Check if the user clicked on the security link
if (($designation == "S") && isset($_GET['orderno'])) {
    $_SESSION['orderno'] = $_GET['orderno'];
    header("Location: security-page.php");
    exit();
}

//Check if the user clicked on the guard link
if (($designation == "G"  && isset($_GET['orderno']))) {
    $_SESSION['orderno'] = $_GET['orderno'];
    header("Location: guard-page.php");
    exit();
}

//  redirect to receive.php for "receive" button
if (($designation == "G" || $designation == "E" ||$designation == "S") && isset($_POST['receive'])) {
    $orderno = $_POST['receive'];
    header("Location: receive.php?orderno=$orderno");
    exit();
}

// Set the session variable 'isEditable' and redirect to form.php for "Edit" button
if ($designation == "E" && isset($_POST['edit_order'])) {
    $_SESSION['orderno'] = $_POST['edit_order'];
    header("Location: tempform.php");
    exit();
}
if ($designation == "E" && isset($_POST['new_order'])) {
    header("Location: form.php");
    exit();
}
if (isset($_POST['reports'])) {
    header("Location: skdash_exp.php");
    exit();
}
function getEmployeesByCpf($cpf)
{
    global $connection;
    $query = "SELECT empname FROM employee WHERE cpfno = '$cpf'";
    $result = mysqli_query($connection, $query);
    $employee = null;
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $employee = $row['empname'];
    }
    return $employee;
}
function getEmployeesvenue($cpf)
{
    global $connection;
    $query = "SELECT venue FROM employee WHERE cpfno = '$cpf'";
    $result = mysqli_query($connection, $query);
    $employee = null;
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $venue = $row['venue'];
    }
    return $venue;
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
    <h3>Dashboard</h3>
    <?php echo "Designation " . $designation;
    if ($designation == "E") : ?>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <button type="submit" class="btn btn-primary" class="form-group" name="new_order">New Order</button>
        </form>
    <?php endif;
    echo "<br>"; ?>
    <?php if ($designation == "E" || $designation == "S" || $designation == "G") : ?>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <button type="submit" class="btn btn-primary" class="form-group" name="reports">Reports</button>
        </form>
    <?php endif; ?>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <?php

        // Retrieve data from the "order_no" table
        if ($designation == "E") {
            $query = "SELECT orderno, order_dest, issue_dep, placeoi, issueto,securityn,guard_name,collector_name, returnable, coll_approval, security_approval, comp_approval, guard_approval, sign_approval,forwarded_to, signatory, created_by FROM order_no WHERE coll_approval = 0 AND forwarded_to = ? OR created_by = ? OR signatory = ? ";

            // Prepare the statement
            $stmt = $conn->prepare($query);

            // Bind the parameters
            $stmt->bind_param('iii', $cpf_no, $cpf_no, $cpf_no);
        } else if ($designation == "S") {
            $ven = getEmployeesvenue($cpf_no);
            $query = "SELECT orderno, order_dest, issue_dep, placeoi, issueto, returnable, coll_approval,sign_approval, security_approval, comp_approval, guard_approval, forwarded_to, created_by FROM order_no WHERE placeoi = ? AND (security_approval = 0 AND coll_approval = 1 AND guard_approval = 1 AND sign_approval = 1) OR (comp_approval=1 AND returnable=1)";

            // Prepare the statement
            $stmt = $conn->prepare($query);

            // Bind the parameter
            $stmt->bind_param('s', $ven);
        } else {
            $query = "SELECT orderno, order_dest, issue_dep, placeoi, issueto, returnable, coll_approval,sign_approval, security_approval, comp_approval, guard_approval, forwarded_to, created_by FROM order_no WHERE placeoi = ? AND (coll_approval = 1 AND sign_approval = 1 AND guard_approval = 0) OR (comp_approval=-1 AND returnable=1) ";

            // Prepare the statement
            $stmt = $conn->prepare($query);

            // Bind the parameter
            $stmt->bind_param('s', $_SESSION["venue"]);
        }

        // Execute the statement
        $stmt->execute();

        // Get the result
        $result = $stmt->get_result();
        // Check if the query was successful
        if ($result && mysqli_num_rows($result) > 0) {
            // Display the data in a table
            echo "<table id='dynamic-table'>";
            echo "<tr><th>Order No</th><th>Created By</th><th>Order Destination</th><th>Issue Department</th><th>Place of Issue</th><th>Issue To</th><th>Returnable</th>";
            echo "<th>Action</th>";
            if ($designation == "E") echo "<th>Status</th></tr>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td><a href='vieworder.php?orderno=" . $row['orderno'] . "'>" . $row['orderno'] . "</td>";
                $creatorname = getEmployeesByCpf($row['created_by']);
                echo "<td>" . $creatorname . "</td>";
                echo "<td>" . $row['order_dest'] . "</td>";
                $placeoi = $row['issue_dep']; 
                if ($placeoi === 'I') {
                    $displayText = 'Infocom';
                } elseif ($placeoi === 'M') {
                    $displayText = 'Management';
                } elseif ($placeoi === 'P') {
                    $displayText = 'Production';
                }
                echo "<td>" . $displayText. "</td>";
                $placeoi = $row['placeoi']; // Assuming $row['placeoi'] contains the value
                $displayText = '';

                if ($placeoi === 'N') {
                    $displayText = 'NBP Green Heights';
                } elseif ($placeoi === 'V') {
                    $displayText = 'Vasundhara Bhavan';
                } elseif ($placeoi === 'H') {
                    $displayText = '11 HIGH';
                }
                echo "<td>" . $displayText . "</td>";

                echo "<td>" . $row['issueto'] . "</td>";
                $returnableValue = ($row['returnable'] ? 'Yes' : 'No');

                echo "<td>" . ($returnableValue) . "</td>";

                if ($designation == "E" && $row['forwarded_to'] == $cpf_no && $row['coll_approval'] == 0 && $row['security_approval'] == 0 && $row['guard_approval'] == 0)
                    echo "<td><a href='skdash.php?orderno=" . $row['orderno'] . "'>Collector Link</a></td>";

                else if ($designation == "E" && $row['signatory'] == $cpf_no && $row['coll_approval'] == 1 && $row['sign_approval'] == 0 && $row['security_approval'] == 0 && $row['guard_approval'] == 0)
                    echo "<td><a href='skdash.php?orderno=" . $row['orderno'] . "&sign=" . $row['signatory'] . "'>Signatory Link</a></td>";

                else if ($designation == "S" && $row['coll_approval'] == 1 && $row['sign_approval'] == 1 && $row['security_approval'] == 0 && $row['guard_approval'] == 1&& $row['comp_approval'] == 0)
                    echo "<td><a href='skdash.php?orderno=" . $row['orderno'] . "'>Security Link</a></td>";

                else if ($designation == "S" && $row['coll_approval'] == 1 && $row['sign_approval'] == 1 && $row['security_approval'] == 1 && $row['guard_approval'] == 1&& $row['comp_approval'] == 1 && $returnableValue == 'Yes'){
                    echo '<td>';
                    echo '<input type="hidden" name="Orderno" value="' . $row['orderno'] . '">';
                    echo '<button type="submit" name="receive" class="btn btn-outline-secondary" value="' . $row['orderno'] . '">Receive</button>';
                    echo '</td>';
                }
                else if ($designation == "G" && $row['coll_approval'] == 1 && $row['sign_approval'] == 1 && $row['security_approval'] == 0 && $row['guard_approval'] == 0&& $row['comp_approval'] == 1 && $returnableValue == 'Yes')
                    echo "<td><a href='skdash.php?orderno=" . $row['orderno'] . "'>Guard Link</a></td>";

                else if ($designation == "G" && $row['coll_approval'] == 1 && $row['security_approval'] == 1 && $row['guard_approval'] == 1 && $row['comp_approval'] == -1 && $returnableValue == 'Yes') {
                    echo '<td>';
                    echo '<input type="hidden" name="Orderno" value="' . $row['orderno'] . '">';
                    echo '<button type="submit" name="receive" class="btn btn-outline-secondary" value="' . $row['orderno'] . '">Receive</button>';
                    echo '</td>';
                } else if ($designation == "E" && $row['coll_approval'] == 1 && $row['security_approval'] == 1 && $row['guard_approval'] == 1 && $row['comp_approval'] == 2 && $returnableValue == 'Yes') {
                    echo '<td>';
                    echo '<input type="hidden" name="Orderno" value="' . $row['orderno'] . '">';
                    echo '<button type="submit" name="receive" class="btn btn-outline-secondary" value="' . $row['orderno'] . '">Final Receive</button>';
                    echo '</td>';
                } else if ($designation == "S" && $row['coll_approval'] == 1 && $row['security_approval'] == 1 && $row['guard_approval'] == 1 && $row['comp_approval'] == 2 && $returnableValue == 'Yes') {
                    echo '<td>';
                    echo '<input type="hidden" name="Orderno" value="' . $row['orderno'] . '">';
                    echo '<button type="submit" name="receive" class="btn btn-outline-secondary" value="' . $row['orderno'] . '">Receive</button>';
                    echo '</td>';
                } else if ($designation == "E" && $row['created_by'] == $cpf_no && ($row['coll_approval'] == -1 || $row['security_approval'] == -1 || $row['guard_approval'] == -1 || $row['sign_approval'] == -1)) {
                    echo '<td>';
                    echo '<input type="hidden" name="Orderno" value="' . $row['orderno'] . '">';
                    echo '<button type="submit" name="edit_order" class="btn btn-outline-secondary" value="' . $row['orderno'] . '">Edit</button>';
                    echo '</td>';

                    if ($row['coll_approval'] == -1) {
                        // Fetch collector name from order_no table

                        $collector_name_query = "SELECT username FROM users WHERE cpfno = " . $row['forwarded_to'];
                        $collector_name_result = $connection->query($collector_name_query);
                        $collector_name_row = $collector_name_result->fetch_assoc();

                        echo '<td>Order Reverted by Collector: ' . $collector_name_row['username'] . '-' . $row['forwarded_to'] . '</td>';
                    } elseif ($row['security_approval'] == -1) {
                        // Fetch security name from order_no table
                        $security_name_query = "SELECT username FROM users WHERE cpfno = " . $row['securityn'];
                        $security_name_result = $connection->query($security_name_query);
                        $security_name_row = $security_name_result->fetch_assoc();

                        echo '<td>Order Reverted by Security: ' . $security_name_row['username'] . '-' . $row['securityn'] . '</td>';
                    } elseif ($row['guard_approval'] == -1) {
                        // Fetch guard name from order_no table
                        $guard_name_query = "SELECT guard_name FROM security_guard WHERE phone_no = " . $row['guard_name'];
                        $guard_name_result = $connection->query($guard_name_query);
                        $guard_name_row = $guard_name_result->fetch_assoc();

                        echo '<td>Order Reverted by Guard: ' . $guard_name_row['guard_name'] . '-' . $row['guard_name'] . '</td>';
                    } elseif ($row['sign_approval'] == -1) {
                        // Fetch guard name from order_no table
                        $sign_name_query = "SELECT username FROM users WHERE cpfno = " . $row['signatory'];
                        $sign_name_result = $connection->query($sign_name_query);
                        $sign_name_row = $sign_name_result->fetch_assoc();

                        echo '<td>Order Reverted by Signatory Officer: ' . $sign_name_row['guard_name'] . '-' . $row['signatory'] . '</td>';
                    }
                } else
                    echo '<td>-</td>';

                if ($designation == "E" && $row['created_by'] == $cpf_no) {

                    if ($row['coll_approval'] == 1 && $row['security_approval'] == 0 && $row['guard_approval'] == 0 && $row['sign_approval'] == 0) {
                        // echo '<td>Approved by Collector</td>';
                        $collector_name_query = "SELECT username FROM users WHERE cpfno = " . $row['forwarded_to'];
                        $collector_name_result = $connection->query($collector_name_query);
                        $collector_name_row = $collector_name_result->fetch_assoc();

                        echo '<td>Order Approved by Collector: ' . $collector_name_row['username'] . '-' . $row['forwarded_to'] . '</td>';
                    } else if ($row['coll_approval'] == 1 && $row['security_approval'] == 0 && $row['guard_approval'] == 0 && $row['sign_approval'] == 1) {
                        // echo '<td>Approved by Collector</td>';
                        $sign_name_query = "SELECT username FROM users WHERE cpfno = " . $row['signatory'];
                        $sign_name_result = $connection->query($sign_name_query);
                        $sign_name_row = $sign_name_result->fetch_assoc();

                        echo '<td>Order Approved by Signatory Officer: ' . $sign_name_row['username'] . '-' . $row['signatory'] . '</td>';
                    } else if ($row['coll_approval'] == 1 && $row['sign_approval'] == 1 && $row['guard_approval'] == 1 && $row['security_approval'] == 0) {
                        // echo '<td>Approved by Guard</td>';
                        // Fetch guard name from order_no table
                        $guard_name_query = "SELECT guard_name FROM security_guard WHERE phone_no = " . $row['guard_name'];
                        $guard_name_result = $connection->query($guard_name_query);
                        $guard_name_row = $guard_name_result->fetch_assoc();

                        echo '<td>Order Approved by Guard: ' . $guard_name_row['guard_name'] . '-' . $row['guard_name'] . '</td>';
                    } else if ($row['coll_approval'] == 0 && $row['security_approval'] == 0 && $row['guard_approval'] == 0 && $row['comp_approval'] == 0)
                        echo '<td>Collector Approval Pending</td>';

                    else if ($row['coll_approval'] == 1 && $row['sign_approval'] == 0 && $row['security_approval'] == 0 && $row['guard_approval'] == 0 && $row['comp_approval'] == 0)
                        echo '<td>Signatory Officer Approval Pending</td>';

                    else if ($row['coll_approval'] == 1  && $row['sign_approval'] == 1 && $row['security_approval'] == 0 && $row['guard_approval'] == 1)
                        echo '<td>Security Approval Pending</td>';

                    if ($returnableValue == "Yes") {
                        if ($row['coll_approval'] == 1 && $row['sign_approval'] == 1 && $row['security_approval'] == 1 && $row['guard_approval'] == 1 && $row['comp_approval'] == -1)
                            echo '<td>Approved and Out</td>';
                        else if ($row['coll_approval'] == 1 && $row['security_approval'] == 1 && $row['guard_approval'] == 1 && $row['comp_approval'] == 1)
                            echo '<td>Order Received By Guard</td>';

                        else if ($row['coll_approval'] == 1 && $row['security_approval'] == 1 && $row['guard_approval'] == 1 && $row['comp_approval'] == 2)
                            echo '<td>Order Received By Security</td>';

                        else if ($row['coll_approval'] == 1 && $row['security_approval'] == 1 && $row['guard_approval'] == 1 && $row['comp_approval'] == 3)
                            echo '<td>Order Completed</td>';
                    } elseif ($returnableValue == "No") {
                        if ($row['coll_approval'] == 1 && $row['security_approval'] == 1 && $row['guard_approval'] == 1)
                            echo '<td>Order Completed</td>';
                    }
                }

                echo '</tr>';
            }

            echo "</table>";
        } else {
            echo "No records found.";
        }
        // Close the database connection
        mysqli_close($connection);
        ?>
    </form>
</body>

</html>