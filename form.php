<?php
session_start();
require_once "db_connection.php";

// Check if the user is not logged in
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

// Logout handling
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["logout"])) {
    // Destroy the session and redirect to the login page
    session_destroy();
    header("Location: login.php");
    exit();
}

$conn = $connection;
if(isset($_SESSION["cpf_no"])){
    $cpf_no = $_SESSION["cpf_no"];
}
//get the designation of the user
$query = "SELECT * FROM employee WHERE cpfno = '$cpf_no'";
$result = mysqli_query($connection, $query);
if (!$result || mysqli_num_rows($result) == 0) {
    header("Location: form.php");
    exit();
}
$user2 = mysqli_fetch_assoc($result);
$designation = $user2["designation"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Escape user inputs to prevent SQL injection
    $returnable = $_POST["return"] == "1" ? 1 : 0;
    $issueDesc = mysqli_real_escape_string($conn, $_POST["issued"]);
    $placeOfIssue = mysqli_real_escape_string($conn, $_POST["placei"]);
    $issueTo = mysqli_real_escape_string($conn, $_POST["issuet"]);
    $placeOfDestination = mysqli_real_escape_string($conn, $_POST["pod"]);
    $forwardTo = mysqli_real_escape_string($conn, $_POST["fors"]);
    $collectorName = mysqli_real_escape_string($conn, $_POST["coln"]);

    // Insert data into the 'order_no' table
    $insertOrderNoQuery = "INSERT INTO order_no (order_dest, issue_desc, placeoi, issueto, securityn, collectorid, returnable) 
                           VALUES ('$placeOfDestination', '$issueDesc', '$placeOfIssue', '$issueTo', '', '$collectorName', $returnable)";

    if (mysqli_query($conn, $insertOrderNoQuery)) {
        $orderId = mysqli_insert_id($conn); // Get the auto-generated order ID

        // Insert data into the 'orders' table
        $rowCount = count($_POST["srno"]);
        for ($i = 0; $i < $rowCount; $i++) {
            $srNo = mysqli_real_escape_string($conn, $_POST["srno"][$i]);
            $description = mysqli_real_escape_string($conn, $_POST["description"][$i]);
            $numOfPackages = mysqli_real_escape_string($conn, $_POST["num"][$i]);
            $dispatchNotes = mysqli_real_escape_string($conn, $_POST["dispatchnotes"][$i]);
            $remarks = mysqli_real_escape_string($conn, $_POST["remarks"][$i]);

            $insertOrdersQuery = "INSERT INTO orders (descrip, nop, deliverynote, remark, orderno) 
                                  VALUES ('$description', $numOfPackages, '$dispatchNotes', '$remarks', $orderId)";
            mysqli_query($conn, $insertOrdersQuery);
        }

        // Redirect to a success page or display a success message
        header("Location: form.php");
        exit();
    } else {
        // Handle the case where the insertion failed
        echo "Error: " . mysqli_error($conn);
    }

    // Close the database connection
    mysqli_close($conn);
}
?>

<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>New Order</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="">
        <script type="text/javascript" src="form.js"></script>
    </head>
    <body>
    <?php if ($designation == "collector") : ?>
            <a href="collector-page.php">Collector Link</a>
        <?php endif; ?>
        <?php if ($designation == "security") : ?>
            <a href="security-page.php">Security Link</a>
    <?php endif; ?>

    <div class="container">
        <h2>Welcome, <?php echo $_SESSION["username"]; ?>!</h2>
        <p>This is your dashboard.</p>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <button type="submit" name="logout">Logout</button>
        </form>
    </div>
    <table>
        <tr>
        <td><img src="assets/images.png"></td>
        <td><h1>Oil and Natural Gas Corporation</h1>
        <h3>MUMBAI REGION- REGIONAL OFFICE- INFOCOM</h3></td>
        </tr>
    </table>
       <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <label for="return">Returnable</label>
        <input type="radio" name="return" value="1">
        <label for="nreturn">Non Returnable</label>
        <input type="radio" name="return" value="0"><br>
        <label for="issued">Issue description</label>
        <input type="text" name="issued"><br>
        <label for="placei">Place of Issue</label>
        <input type="text" name="placei"><br>
        <label for="issuet">Issue To</label>
        <input type="text" name="issuet"><br>
        <label for="pod">Place of Destination</label>
        <input type="text" name="pod">
        <h4></h4>
       <table border="5px">
        <tr>
            <td>Sr No</td>
            <td>Brief description</td>
            <td>No of Packages</td>
            <td>Deliver Note Or Dispatch convey note no OR Indent no</td>
            <td>Remarks</td>
        </tr>
        <div id="readform">
        <tr>
            <td><input type="text" name="srno[]"></td>
            <td><input type="text" name="description[]"></td>
            <td><input type="text"name="num[]"></td>
            <td><input type="text" name="dispatchnotes[]"></td>
            <td><input type="text" name="remarks[]"></td>
        </tr>
        <div id="writeform"></div>
    </div>
       </table> 
       <input type="button" value="add row" id="addrow"><br>
       <label for="fors">Forward To</label>
       <select name="fors">
        <option>default</option>
       </select><br>
       <label for="coln">Collector Name</label>
       <select name="coln">
        <option>defualt</option>
       </select><br>    
        <input type="submit" value="Place Order">
       </form>
        
        
    </body>
</html>