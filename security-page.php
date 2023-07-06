<?php
session_start();
require_once "db_connection.php";

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
    $query = "SELECT n.order_dest, n.issue_desc, n.placeoi, n.issueto, o.descrip, o.nop, o.deliverynote, o.remark, n.moc, n.vehno, n.securityn
              FROM orders o
              JOIN order_no n ON o.orderno = n.orderno
              WHERE o.orderno = $orderno";
    $result = mysqli_query($connection, $query);

    // Retrieve securityn value from the order_no table if it exists
    $securityn = '';
    $fetchQuery = "SELECT securityn FROM order_no WHERE orderno = $orderno";
    $fetchResult = mysqli_query($connection, $fetchQuery);
    if (mysqli_num_rows($fetchResult) > 0) {
        $row = mysqli_fetch_assoc($fetchResult);
        $securityn = $row['securityn'];
    }

    // Handle form submission to update security_approval and remarks in the order_no table
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
        // Get the input values from the form
        $new_remarks = $_POST["new_remarks"];

        // Update the order_no table with security_approval = 1 and remarks
        $updateQuery = "UPDATE order_no SET security_approval = 1,comp_approval=-1, new_remarks = '$new_remarks' WHERE orderno = $orderno";
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
            $error = "Remarks is required for revert.";
        }
        else{
             // Update the order_no table with security_approval = -1 and remarks
        $updateQuery = "UPDATE order_no SET security_approval = -1, guard_approval = 0, coll_approval = 0, new_remarks = '$new_remarks' WHERE orderno = $orderno";
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
<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <button type="submit" id="lo" class="btn btn-outline-danger" name="logout">Logout</button>
    </form>
    <button class="btn btn-secondary" id="gb" onclick="window.location.href = 'skdash.php'">Go Back</button>
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
    <h3>Security Page</h3>
  <table id='dynamic-table'>
    <tr>
      <th>Order Destination</th>
      <th>Issue Description</th>
      <th>Place Of Issue</th>
      <th>Issue TO</th>
      <th>Brief Description</th>
      <th>No of Packages</th>
      <th>Delivery Note Or Dispatch Convey Note No OR Indent No</th>
      <th>Remarks</th>
      <th>Mode Of Collection</th>
      <th>Vehicle Number</th>
      <th>Security Name</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
      <tr>
        <td><?php echo $row['order_dest']; ?></td>
        <td><?php echo $row['issue_desc']; ?></td>
        <td><?php echo $row['placeoi']; ?></td>
        <td><?php echo $row['issueto']; ?></td>
        <td><?php echo $row['descrip']; ?></td>
        <td><?php echo $row['nop']; ?></td>
        <td><?php echo $row['deliverynote']; ?></td>
        <td><?php echo $row['remark']; ?></td>
        <td><?php echo $row['moc']; ?></td>
        <td><?php echo $row['vehno']; ?></td>
        <td><?php echo $row['securityn']; ?></td>
      </tr>
    <?php } ?>
  </table>

  <!-- Display the input fields for securityn and remarks -->
  <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <input type="hidden" name="orderno" value="<?php echo $orderno; ?>">
    <label for="securityn">Security Name:</label>
    <input type="text" name="securityn" value="<?php echo $securityn; ?>">
    <label for="new_remarks">Remarks:</label>
    <input type="text" name="new_remarks" value="<?php echo isset($new_remarks) ? $new_remarks : ''; ?>">
    <?php if (isset($error)) { ?>
      <p class="error"><?php echo $error; ?></p>
    <?php } ?>
    <button type="submit" class="btn btn-danger" name="deny">Revert</button>
<button type="submit" class="btn btn-primary" name="submit">Submit and Approve</button>
  </form>

</body>
</html>

<?php
} else {
    echo 'No orderno set in the session.';
}
?>