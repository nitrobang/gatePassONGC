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

// Check if the user is not logged in
if (!isset($_SESSION["phone_no"])) {
    header("Location: newlogin.php");
    exit();
}

$conn = $connection;
$orderno = $_SESSION['orderno'];

// Logout handling
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["logout"])) {
    // Destroy the session and redirect to the login page
    session_destroy();
    header("Location: newlogin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the input values from the form
    $phone_no = $_SESSION["phone_no"];

    // Insert the values into the orders table
    if (isset($_POST['approve'])) {
        $insert_sql = "UPDATE order_no 
                       SET guard_name = '$phone_no', guard_approval = 1
                       WHERE orderno = $orderno";
        $connection->query($insert_sql);
        $_SESSION['asuccess'] = true; // Using session variable

        // Redirect to the next page
        header("Location: skdash.php");
        exit();
    } elseif (isset($_POST['revert'])) {
        // Check if the new_remarks field is empty
        if (empty($_POST['new_remarks'])) {
            echo '<script>alert("Please Enter Remarks");</script>';
        } else {
            $new_remarks = $_POST['new_remarks'];
            $insert_sql = "UPDATE order_no 
                           SET guard_name = '$phone_no', guard_approval = -1, coll_approval = 0, security_approval = 0, new_remarks = '$new_remarks'
                           WHERE orderno = $orderno";
            $connection->query($insert_sql);
            header('Location: skdash.php');
            exit();
        }
    }
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
    <h3>Guard Page</h3>
    <h5>Order Number:<?php echo $orderno; ?></h5>
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
            <?php
            if ($orderData['returnable'] == 1) {
                echo '<div id="returnDateForm">
                    <label for="returnDate">Return Date:</label>
                    <input type="date" name="returnDate" id="returnDate" value="' . $orderData['returndate'] . '">
                </div>';
            }
            ?>

        </form><?php
             echo '<form method="POST" action="">
            
            
             <label for="new_remarks">Remarks:</label>
             <input type="text" id="new_remarks" name="new_remarks"><br><br>';
 
         echo '<input type="submit" class="btn btn-danger" name="revert" value="Revert">
             <input type="submit" class="btn btn-primary" name="approve" value="Approve">
           </form>';
            }
       
    else {
        echo "No fields found in the table.";
    }
    ?>
    </div>
</body>

</html>