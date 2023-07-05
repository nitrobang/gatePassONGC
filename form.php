<?php
session_start();
require_once "db_connection.php";

// Check if the user is not logged in
if (!isset($_SESSION["username"])) {
    header("Location: newlogin.php");
    exit();
}

//check if right person (store keeper) is accessing the forms page
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

$conn = $connection;

$conn = $connection;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    var_dump($_POST);
    // Escape user inputs to prevent SQL injection
    $returnable = $_POST["return"] == "1" ? 1 : 0;
    $issueDesc = mysqli_real_escape_string($conn, $_POST["issued"]);
    $placeOfIssue = mysqli_real_escape_string($conn, $_POST["placei"]);
    $issueTo = mysqli_real_escape_string($conn, $_POST["issuet"]);
    $placeOfDestination = '';
    if ($_POST["pod"] == "other") {
        $placeOfDestination = mysqli_real_escape_string($conn, $_POST["otherOption"]);
    } else {
        $placeOfDestination = mysqli_real_escape_string($conn, $_POST["pod"]);
    }
    $forwardTo = mysqli_real_escape_string($conn, $_POST["fors"]);
    $collector_name = getEmployeesByCpf($forwardTo);
    $created_by = $_SESSION['cpf_no'];

    // Prepare the INSERT statement with bind parameters
    $insertOrderNoQuery = "INSERT INTO order_no (order_dest, issue_desc, placeoi, issueto, collector_name, returnable, forwarded_to, created_by)VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    // $UpdateOrderNoQuery = "UPDATE order_no SET order_dest = '$placeOfDestination',issue_desc = '$issueDesc',placeoi = '$placeOfIssue',issueto = '$issueTo',securityn = '',collector_name = '$collector_name',returnable = $returnable,	coll_approval='$coll_approval',security_approval='$security_approval',guard_approval='$guard_approval',forwarded_to = '$forwardTo',created_by = '$created_by' WHERE orderno = '$orderno'";

    // Prepare the statement
    $stmt = $conn->prepare($insertOrderNoQuery);


    // Bind the parameters
    $stmt->bind_param('sssssiii', $placeOfDestination, $issueDesc, $placeOfIssue, $issueTo, $collector_name, $returnable, $forwardTo, $created_by);

    // Execute the statement
    if ($stmt->execute()) {


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
        $_SESSION['fsuccess'] = true; // Using session variable

        // Redirect to the next page
        header("Location: skdash.php");
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
        <h2 class="wlc">Welcome, <?php echo $_SESSION["username"]; ?>!</h2>
    </div>

    
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
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
                        <select class="form-group" name="placei" required>
                            <option value="N">NBP GREEN HEIGHTS</option>
                            <option value="V">VASUNDHARA BHAVAN</option>
                            <option value="H">11 HIGH</option>
                        </select>
                    </td>
                    <td>
                    <label for="pod">Place of Destination</label>
                    <select name="pod" class="form-group" required onchange="showOtherOption(this)">

                        <option value="NBP Green Heights">NBP Green Heights</option>
                        <option value="Vasundhara Bhavan">Vasundhara Bhavan</option>
                        <option value="11 High">11 High</option>
                        <option value="other">Other</option>
                    </select>
                    <div id="otherOptionContainer" style="display: none;">
                        <input type="text" name="otherOption" placeholder="Specify other option">
                    </div>
                    </td>
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
    <script type="text/javascript" src="form.js"></script>
</body>

</html>