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
    $query = "SELECT descrip, nop, deliverynote, remark FROM orders WHERE orderno = $orderno";
    $result = mysqli_query($connection, $query);

    // Retrieve moc and vehno values from the order_no table if they exist
    $moc = '';
    $vehno = '';
    $fetchQuery = "SELECT moc, vehno FROM order_no WHERE orderno = $orderno";
    $fetchResult = mysqli_query($connection, $fetchQuery);
    if (mysqli_num_rows($fetchResult) > 0) {
        $row = mysqli_fetch_assoc($fetchResult);
        $moc = $row['moc'];
        $vehno = $row['vehno'];
    }

    // Handle form submission to insert moc and vehno into the order_no table
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
        $moc = $_POST["moc"];
        $vehno = $_POST["vehno"];

        // Update the order_no table with moc and vehno values
        $updateQuery = "UPDATE order_no SET moc = '$moc', vehno = '$vehno', coll_approval = 1 WHERE orderno = $orderno";
        $updateResult = mysqli_query($connection, $updateQuery);

        if ($updateResult) {
            // Redirect to the dashboard or a success page
            header("Location: skdash.php");
            exit();
        } else {
            // Handle the error, display a message, or redirect to an error page
            echo "Error: " . mysqli_error($connection);
        }
    }

    // Handle form submission to Revert the order
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["deny"])) {
        // Update the order_no table with coll_approval = -1 to indicate denial
        $updateQuery = "UPDATE order_no SET coll_approval = -1,security_approval = 0,guard_approval = 0 WHERE orderno = $orderno";
        $updateResult = mysqli_query($connection, $updateQuery);

        if ($updateResult) {
            // Redirect to the dashboard or a success page
            header("Location: skdash.php");
            exit();
        } else {
            // Handle the error, display a message, or redirect to an error page
            echo "Error: " . mysqli_error($connection);
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
    <h3>Collector Page</h3>
  <table id='dynamic-table'>
    <tr>
      <th>Description</th>
      <th>NOP</th>
      <th>Delivery Note</th>
      <th>Remark</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
      <tr>
        <td><?php echo $row['descrip']; ?></td>
        <td><?php echo $row['nop']; ?></td>
        <td><?php echo $row['deliverynote']; ?></td>
        <td><?php echo $row['remark']; ?></td>
      </tr>
    <?php } ?>
  </table>

  <!-- Display the input fields for moc and vehno -->
  <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <input type="hidden" name="orderno" value="<?php echo $orderno; ?>">
    <label for="moc">MOC:</label>
    <input type="text" name="moc" value="<?php echo $moc; ?>">
    <label for="vehno">Vehno:</label>
    <input type="text" name="vehno" value="<?php echo $vehno; ?>">
    <button type="submit" name="submit">Submit and Approve</button>
    <button type="submit" name="deny">Revert</button>
  </form>

</body>
</html>

<?php
} else {
    echo 'No orderno set in the session.';
}
?>
