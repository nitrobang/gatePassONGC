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
// echo "hello".$_SESSION['orderno'];
// Check if the user is not logged in
if (!isset($_SESSION["username"])) {
    header("Location: newlogin.php");
    exit();
}

//check if right person(store keeper) is accessing the forms page   
if ($_SESSION["designation"] != "E") {
    header("Location: skdash.php");
    exit();
}

// Logout handling
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["logout"])) {
    // Destroy the session and redirect to the login page
    session_destroy();
    header("Location: newlogin.php");
    exit();
}
$orderno=$_SESSION['orderno'];
$conn = $connection;
// if (isset($_GET['orderno'])) {
//     $orderno = $_GET['orderno'];

//     $checkquery = "SELECT coll_approval,security_approval,guard_approval  FROM order_no WHERE orderno = '$orderno'";
//     $result = mysqli_query($conn, $checkquery);

//     if ($result) {
//         $row = mysqli_fetch_assoc($result);
//         $collApproval = $row['coll_approval'];
//         $sApproval = $row['security_approval'];
//         $gApproval = $row['guard_approval'];
//         // Check if the 'coll_approval' value is not equal to -1
//         if ($collApproval != -1 && $sApproval != -1 && $gApproval != -1) {
//             $_SESSION['cantedit'] = true; // Using session variable

//             // Redirect to the next page
//             header("Location: skdash.php");
//             exit();
//         }
//     }
// }


// Function to get employee names and CPF numbers based on designation
function getEmployeesByDesignation($designation)
{
    global $connection;
    $query = "SELECT empname, cpfno FROM employee WHERE designation = '$designation'";
    $result = mysqli_query($connection, $query);
    $employees = array();
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $employee = array(
                'empname' => $row['empname'],
                'cpfno' => $row['cpfno']
            );
            $employees[] = $employee;
        }
    }
    return $employees;
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

// Handle form submission to update security_approval and remarks in the order_no table
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    // Get the input values from the form
    $new_remarks = $_POST["new_remarks"];

    // Update the order_no table with security_approval = 1 and remarks
    $updateQuery = "UPDATE order_no SET sign_approval=1, security_approval = 0, comp_approval=0, new_remarks = '$new_remarks' WHERE orderno = $orderno";
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
    $updateQuery = "UPDATE order_no SET  sign_approval=-1,security_approval = 0, guard_approval = 0, coll_approval = 0,sign_approval=0, new_remarks = '$new_remarks' WHERE orderno = $orderno";
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

<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>New Order</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script type="text/javascript" src="form.js"></script>
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
                <input type="radio" class="form-group" name="return" value="1" <?php echo $orderData['returnable'] == 1 ? 'checked' : ''; ?> readonly required>
                <label for="nreturn">Non Returnable</label>
                <input type="radio" class="form-group" name="return" value="0" <?php echo $orderData['returnable'] == 0 ? 'checked' : ''; ?> readonly><br>
                <table class="postt">
                    <tr>
                        <td><label for="issued">Issuing department/Office</label>
                            <input type="text" class="form-group" name="issued" value="<?php echo $orderData['issue_dep']; ?>" required readonly><br>
                        </td>
                        <td><label for="issuet">Issue To</label>
                            <input type="text" class="form-group" name="issuet" value="<?php echo $orderData['issueto']; ?>" required readonly><br>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="placei">Place of Issue</label>
                            <select class="form-group" name="placei" required readonly>
                                <option value="N" <?php if ($orderData['placeoi'] === 'N') echo 'selected'; ?>>NBP GREEN HEIGHTS</option>
                                <option value="V" <?php if ($orderData['placeoi'] === 'V') echo 'selected'; ?>>VASUNDHARA BHAVAN</option>
                                <option value="H" <?php if ($orderData['placeoi'] === 'H') echo 'selected'; ?>>11 HIGH</option>
                            </select>
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
                $returnDate = date('m-d-Y', strtotime($orderData['returndate']));
                if ($orderData['returnable']==1){
                        echo'<div id="returnDateForm">
                        <label for="returnDate">Return Date:</label>
                        <input type="date" name="returnDate" id="returnDate"value="<?php echo $returnDate; ?>">
                    </div>';
                }
            ?>
                     
        </form><?php
            } else {
                // Handle the case where no rows were found
                echo "No order data found.";
            }
                ?>
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
    <script type="text/javascript" src="form.js"></script>
</body>

</html>