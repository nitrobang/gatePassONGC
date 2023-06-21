<?php
session_start();
require_once "db_connection.php";

// echo "hello".$_SESSION['orderno'];
// Check if the user is not logged in
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
if ($_SESSION['isedit']==1){

$orderno = $_SESSION['orderno'];
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
    header("Location: login.php");
    exit();
}

$conn = $connection;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_SESSION["isedit"] == 1) {
        $orderno = $_SESSION["orderno"];
        $delquery = "DELETE FROM orders WHERE orderno = '$orderno'";
        $conn->query($delquery);
    }
    // Escape user inputs to prevent SQL injection
    $returnable = $_POST["return"] == "1" ? 1 : 0;
    $issueDesc = mysqli_real_escape_string($conn, $_POST["issued"]);
    $placeOfIssue = mysqli_real_escape_string($conn, $_POST["placei"]);
    $issueTo = mysqli_real_escape_string($conn, $_POST["issuet"]);
    $placeOfDestination = mysqli_real_escape_string($conn, $_POST["pod"]);
    $forwardTo = mysqli_real_escape_string($conn, $_POST["fors"]);

    // Insert data into the 'order_no' table
    $insertOrderNoQuery = "INSERT INTO order_no (order_dest, issue_desc, placeoi, issueto, securityn, collectorid, returnable, forwarded_to) 
                           VALUES ('$placeOfDestination', '$issueDesc', '$placeOfIssue', '$issueTo', '', '', $returnable, '$forwardTo')";

    if (mysqli_query($conn, $insertOrderNoQuery)) {
        $orderNo = mysqli_insert_id($conn); // Get the auto-generated order ID

        //*****************/ Insert data into the 'orders' table ************************

        // Retrieve the form data
        $serialNumbers = $_POST['serial_number'];
        $description = $_POST['description'];
        $num = $_POST['num'];
        $dispatchnotes = $_POST['dispatchnotes'];
        $remarks = $_POST['remarks'];


        // Create a PDO connection to the database
        $conn2 = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare the SQL statement for insertion
        $stmt = $conn2->prepare("INSERT INTO orders (descrip, nop, deliverynote, remark, orderno) VALUES (:descrip, :nop, :deliverynote, :remark, :orderno)");

        // Iterate over the rows and insert them into the database
        for ($i = 0; $i < count($serialNumbers); $i++) {
            $stmt->bindParam(':descrip', $description[$i]);
            $stmt->bindParam(':nop', $num[$i]);
            $stmt->bindParam(':deliverynote', $dispatchnotes[$i]);
            $stmt->bindParam(':remark', $remarks[$i]);
            $stmt->bindParam(':orderno', $orderNo);

            $stmt->execute();
        }

        /****************************** Done **********************************/

        // Redirect to a success page or display a success message
        header("Location: form.php");
        exit();
    } else {
        // Handle the case where the insertion failed
        echo "Error: " . mysqli_error($conn);
    }

    // Close the database connection
    mysqli_close($conn);
    $conn2 = null;
}

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
        <button type="submit" id="lo" class="btn btn-secondary" name="logout">Logout</button>
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

    <h2 class="wlc">Welcome, <?php echo $_SESSION["username"]; ?>!</h2>
    <?php
    if ($_SESSION["isedit"] == 1) {
        // Retrieve data from the orders table based on the orderno
        $orderno = $_SESSION["orderno"];
        $query = "SELECT * FROM orders WHERE orderno = '$orderno'";

        // Execute the query
        // Make sure you have established a database connection before executing the query
        $result = $conn->query($query);
        $query = "SELECT * FROM order_no WHERE orderno = '$orderno'";
        $res = $conn->query($query);
        $col = $res->fetch_assoc();
        if ($result->num_rows > 0) {
            // Display the retrieved data as editable form fields
            while ($row = $result->fetch_assoc()) {
    ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <div class="pos">
                        <!-- Display the Returnable/Non-Returnable options -->
                        <label for="return">Returnable</label>
                        <input type="radio" class="form-group" name="return" value="1" required <?php echo ($col['returnable'] == 1) ? 'checked' : ''; ?>>
                        <label for="nreturn">Non-Returnable</label>
                        <input type="radio" class="form-group" name="return" value="0" <?php echo ($col['returnable'] == 0) ? 'checked' : ''; ?>><br>

                        <table class="postt">
                            <tr>
                                <td>
                                    <label for="issued">Issuing department/Office</label>
                                    <input type="text" class="form-group" name="issued" value="<?php echo $col['issue_desc']; ?>" required><br>
                                </td>
                                <td>
                                    <label for="issuet">Issue To</label>
                                    <input type="text" class="form-group" name="issuet" value="<?php echo $col['issueto']; ?>" required><br>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="placei">Place of Issue</label>
                                    <input type="text" class="form-group" name="placei" value="<?php echo $col['placeoi']; ?>" required><br>
                                </td>
                                <td>
                                    <label for="pod">Place of Destination</label>
                                    <input type="text" class="form-group" name="pod" value="<?php echo $col['order_dest']; ?>" required>
                                </td>
                            </tr>
                        </table>

                        <!-- Display the table of dynamic rows -->
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
                        <?php
                        // Display the rows from the orders table as editable form fields
                        $serialNumber = 1;
                        while ($row = $result->fetch_assoc()) {
                        
                            echo '<tr>';
                            echo '<td><input type="hidden" name="serial_number[]" >' . $serialNumber . '.</td>';
                            echo '<td><input type="text" name="description[]" value="' . $row['descrip'] . '" required></td>';
                            echo '<td><input type="text" name="num[]" value="' . $row['nop'] . '" required></td>';
                            echo '<td><input type="text" name="dispatchnotes[]" value="' . $row['deliverynote'] . '" required></td>';
                            echo '<td><input type="text" name="remarks[]" value="' . $row['remark'] . '" required></td>';
                            echo '<td></td>';
                            echo '</tr>';
                            echo '<script>';
                            echo 'addRow();'; // Call the JavaScript function
                            echo '</script>';                              
                            $serialNumber++;
                        }
                        ?>
                    </table>
                    <br>
                    <button type="button" onclick="addRow()">Add Row</button>
                    <br><br>
                    <div class="sugg">
                        <div class="result">
                            <p>Forwarded To:</p>
                        </div>
                        <input type="text" name="fors" oninput="findet(this.value)">
                        <ul class="autocomplete-list"></ul>
                        <div class="clear"></div>
                    </div>
                    <br>
                    <br>
                    <input type="submit" name="submit" id="submitButton" value="Submit" disabled>
                </form>
        <?php
            }
        } else {
            echo "No data found for the specified orderno.";
        }
    } else {
        // Display the default form if isedit is not equal to 1
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <!-- Default form content -->
            <div class="pos">
                <label for="return">Returnable</label>
                <input type="radio" class="form-group" name="return" value="1" required>
                <label for="nreturn">Non Returnable</label>
                <input type="radio" class="form-group" name="return" value="0"><br>
                <table class="postt">
                    <tr>
                        <td><label for="issued">Issuing department/Office</label>
                            <input type="text" class="form-group" name="issued" required><br>
                        </td>
                        <td><label for="issuet">Issue To</label>
                            <input type="text" class="form-group" name="issuet" required><br>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="placei">Place of Issue</label>
                            <input type="text" class="form-group" name="placei" required><br>
                        </td>
                        <td><label for="pod">Place of Destination</label>
                            <input type="text" class="form-group" name="pod" required>
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
                <tr>
                    <td><input type="hidden" name="serial_number[]">1. </td>
                    <td><input type="text" name="description[]" required></td>
                    <td><input type="text" name="num[]" required></td>
                    <td><input type="text" name="dispatchnotes[]" required></td>
                    <td><input type="text" name="remarks[]" required></td>
                    <td> </td>
                </tr>
            </table>
            <br>
            <button type="button" onclick="addRow()">Add Row</button>
            <br><br>
            <div class="sugg">
                <div class="result">
                    <p>Forwarded To:</p>
                </div>
                <input type="text" name="fors" oninput="findet(this.value)">
                <ul class="autocomplete-list"></ul>
                <div class="clear"></div>
            </div>
            <br>
            <br>
            <input type="submit" name="submit" id="submitButton" value="Submit" disabled>
        </form>
    <?php
    }
    ?>

    <script type="text/javascript" src="form.js"></script>
</body>

</html>