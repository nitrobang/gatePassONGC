<?php
session_start();
require_once "enc_dec.php";
$decryptedClientIP = decrypt($_SESSION['encrypted_client_ip'], $secretKey);
$decryptedUserAgent = decrypt($_SESSION['encrypted_user_agent'], $secretKey);
if ($_SERVER['REMOTE_ADDR'] != $decryptedClientIP && $_SESSION['user_agent'] !== $decryptedUserAgent){
    session_unset();
    session_destroy();
}
if (!isset($_SESSION['regenerated']) || ($_SESSION['regenerated'] + 30 * 60) < time()) {
    session_regenerate_id(true);
    $_SESSION['regenerated'] = time();
}
// Include the database connection file
require_once "db_connection.php";
$conn=$connection;
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
    <div class="tableclass">
    <?php
       
    
    $selectOrderNoQuery = "SELECT * FROM order_no WHERE orderno = '$orderno'";
    $result1 = mysqli_query($conn, $selectOrderNoQuery);
    $selectOrdersQuery = "SELECT * FROM orders WHERE orderno = '$orderno'";
    $result = mysqli_query($conn, $selectOrdersQuery);
    $orderItems = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $orderItems[] = $row;
    }

    if ($result1 && mysqli_num_rows($result1) > 0) {
        $orderData = mysqli_fetch_assoc($result1);
    ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="pos">
            <label for="return">Returnable</label>
                    <input type="radio" class="form-group" name="return" value="1" <?php echo $orderData['returnable'] == 1 ? 'checked' : ''; ?> <?php echo $orderData['returnable'] == 1 ? '' : 'hidden'; ?> readonly required>

                    <label for="nreturn">Non Returnable</label>
                    <input type="radio" class="form-group" name="return" value="0" <?php echo $orderData['returnable'] == 0 ? 'checked' : ''; ?> <?php echo $orderData['returnable'] == 0 ? '' : 'hidden'; ?> readonly><br>
                <table class="postt">
                    <tr>
                        <td><label for="issued">Issuing department/Office</label>
                            <input type="text" class="form-group" name="issued" value="<?php
                                                                                        if ($orderData['issue_dep'] === "I") {
                                                                                            $department = "Infocom";
                                                                                        } elseif ($orderData['issue_dep'] === "M") {
                                                                                            $department = "Management";
                                                                                        } elseif ($orderData['issue_dep'] === "P") {
                                                                                            $department = "Production";
                                                                                        }
                                                                                        echo $department; ?>" required readonly><br>
                        </td>
                        <td><label for="issuet">Issue To</label>
                            <input type="text" class="form-group" name="issuet" value="<?php echo $orderData['issueto']; ?>" required readonly><br>
                        </td>
                    </tr>
                    <tr>
                        <td>
                        <label for="placei">Place of Issue</label>
                            <input type="text" class="form-group" name="placei" value="<?php
                                                                                        if ($orderData['placeoi'] == "N") {
                                                                                            $venue = "NBP Green Heights";
                                                                                        } elseif ($orderData['placeoi'] == "V") {
                                                                                            $venue = "Vasundhara Bhavan";
                                                                                        } elseif ($orderData['placeoi'] == "H") {
                                                                                            $venue = "11 High";
                                                                                        }
                                                                                        echo $venue; ?>" readonly>
                        </td>
                        <td><label for="pod">Place of Destination</label>
                            <input type="text" class="form-group" name="pod" value="<?php echo $orderData['order_dest']; ?>" required readonly>
                        </td>
                    </tr>
                </table>

                <h4></h4>
            </div>
            <table id="dynamic-table">
                <tr>
                    <th>Sr No</th>
                    <th>Brief description</th>
                    <th>No of Packages</th>
                    <th>Deliver Note Or Dispatch convey note no OR Indent no</th>
                    <th>Remarks</th>
                </tr>
                <?php foreach ($orderItems as $index => $item) { ?>
                    <tr>
                        <td><input type='hidden' name='serial_number[]'> <?php echo $index + 1; ?></input></td>
                        <td><input type="text" name="description[]" value="<?php echo $item['descrip']; ?>" required readonly></td>
                        <td><input type="text" name="num[]" value="<?php echo $item['nop']; ?>" required readonly></td>
                        <td><input type="text" name="dispatchnotes[]" value="<?php echo $item['deliverynote']; ?>" required readonly></td>
                        <td><input type="text" name="remarks[]" value="<?php echo $item['remark']; ?>" required readonly></td>
                    </tr>

                <?php }
                ?>
            </table>
            <br>
            <div id="returnDateForm" style="display: none;">
                <label for="returnDate">Return Date:</label>
                <input type="date" name="returnDate" id="returnDate"value="<?php echo $orderData['returndate']; ?>">
            </div>
                     
        </form>
<?php 
        // Add form to input "Mode of Collection" and "Vehicle Number"
        echo '<form method="POST" action="">
            <input type="submit" class="btn btn-primary" name="receive" value="receive">
          </form>';
    } else {
        echo "No fields found in the table.";
    }
    ?>
    </div>
</body>

</html>