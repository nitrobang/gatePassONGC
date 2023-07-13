<?php
session_start();
require_once "db_connection.php";
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
$conn=$connection;
// Check if the orderno is set in the session
if (isset($_SESSION['orderno'])) {
    $orderno = $_SESSION['orderno'];

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

    // Retrieve values from the orders table
    $query = "SELECT n.order_dest, n.issue_dep, n.placeoi, n.issueto, o.descrip, o.nop, o.deliverynote, o.remark, n.moc, n.vehno, n.guard_name
              FROM orders o
              JOIN order_no n ON o.orderno = n.orderno
              WHERE o.orderno = $orderno";
    $result = mysqli_query($connection, $query);
    // Retrieve securityn value from the order_no table if it exists
    $securityn = $_SESSION['cpf_no'];
    $fetchQuery = "SELECT securityn FROM order_no WHERE orderno = $orderno";
    $fetchResult = mysqli_query($connection, $fetchQuery);
    if (mysqli_num_rows($fetchResult) > 0) {
        $row = mysqli_fetch_assoc($fetchResult);
    }

    // Handle form submission to update security_approval and remarks in the order_no table
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
        // Get the input values from the form
        $new_remarks = $_POST["new_remarks"];

        // Update the order_no table with security_approval = 1 and remarks
        $updateQuery = "UPDATE order_no SET securityn='$securityn',security_approval = 1,comp_approval=-1, new_remarks = '$new_remarks' WHERE orderno = $orderno";
        $updateResult = mysqli_query($connection, $updateQuery);

        if ($updateResult) {
            // Redirect to the dashboard or a success page
            $_SESSION['asuccess'] = true; // Using session variable
            // Redirect to the next page
            header("Location: skdash.php");
            exit();
        } else {
            // Handle the error, display a message, or redirect to an error page
            echo "Error: " . mysqli_error($connection);
        }
    }

    // Handle form submission to Revert the order
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["deny"])) {
        // Get the input values from the form
        $new_remarks = $_POST["new_remarks"];
        if (empty($new_remarks)) {
            echo '<script>alert("Please Enter Remarks");</script>';
        }
        else{
             // Update the order_no table with security_approval = -1 and remarks
        $updateQuery = "UPDATE order_no SET  securityn='$securityn',security_approval = -1, guard_approval = 0, coll_approval = 0,sign_approval=0, new_remarks = '$new_remarks' WHERE orderno = $orderno";
        $updateResult = mysqli_query($connection, $updateQuery);

        if ($updateResult) {
            // Redirect to the dashboard or a success page
            $_SESSION['rsuccess'] = true; // Using session variable
            // Redirect to the next page
            header("Location: skdash.php");
            exit();
        } else {
            // Handle the error, display a message, or redirect to an error page
            echo"Error: " . mysqli_error($connection);
        }
        }
       
    }
?>

<!-- HTML Section -->
<!DOCTYPE html>
<html>
<head>
  <title>Order Details</title>
  <link rel="stylesheet" href="css/styles.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
</head>
<body>
<button class="btn btn-secondary" id="gb" onclick="window.location.href = 'skdash.php'">Go Back</button>

    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <button type="submit" id="lo" class="btn btn-outline-danger" name="logout">Logout</button>
    </form>

    <?php  echo "<button class='btn btn-primary' id='printer' name='printer' onclick='printFunction()'>Print</button>";?>
    <div id="print">
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
    <h2 class="wlc">Welcome, <?php echo $_SESSION["username"]; ?>!</h2><br>
    
    
    <h5>Order Number:<?php echo $orderno;?></h5>
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
                    <th>Action</th>
                </tr>
                <?php foreach ($orderItems as $index => $item) { ?>
                    <tr>
                        <td><input type='hidden' name='serial_number[]'> <?php echo $index + 1; ?></input></td>
                        <td><input type="text" name="description[]" value="<?php echo $item['descrip']; ?>" required readonly></td>
                        <td><input type="text" name="num[]" value="<?php echo $item['nop']; ?>" required readonly></td>
                        <td><input type="text" name="dispatchnotes[]" value="<?php echo $item['deliverynote']; ?>" required readonly></td>
                        <td><input type="text" name="remarks[]" value="<?php echo $item['remark']; ?>" required readonly></td>
                        <td> </td>
                    </tr>

                <?php }
                ?>
            </table>
            <br>
            <div id="returnDateForm" style="display: none;">
                <label for="returnDate">Return Date:</label>
                <input type="date" name="returnDate" id="returnDate"value="<?php echo $orderData['returndate']; ?>">
            </div>
                     
        </form><?php
            } else {
                // Handle the case where no rows were found
                echo "No order data found.";
            }
                ?>

  <!-- Display the input fields for securityn and remarks -->
  <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <input type="hidden" name="orderno" value="<?php echo $orderno; ?>">
    <!-- <label for="securityn">Security Name:</label>
    <input type="text" name="securityn" value="<?php echo $securityn; ?>"> -->
    <label for="new_remarks">Remarks:</label>
    <input type="text" name="new_remarks" value="<?php echo isset($new_remarks) ? $new_remarks : ''; ?>">
    <?php if (isset($error)) { ?>
      <p class="error"><?php echo $error; ?></p>
    <?php } ?>
    <button type="submit" class="btn btn-danger" name="deny">Revert</button>
<button type="submit" class="btn btn-primary" name="submit">Submit and Approve</button>
  </form>
    </div>
    </div>
</body>
</html>

<?php
} else {
    echo 'No orderno set in the session.';
}
?>