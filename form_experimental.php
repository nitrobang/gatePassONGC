<?php
    // Assuming you have a database connection established
    // Replace the placeholders with your actual database credentials
    $servername = "localhost";
    $username = "root";
    $password = "Qweasdzxc@007";
    $dbname = "gate_pass";

    // Check if the form is submitted
    if (isset($_POST['submit'])) {
        // Retrieve the form data
        $serialNumbers = $_POST['serial_number'];
        $description = $_POST['description'];
        $num = $_POST['num'];
        $dispatchnotes = $_POST['dispatchnotes'];
        $remarks = $_POST['remarks'];


        // Create a PDO connection to the database
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Generate a random order number
        $orderNo = 12;

        // Prepare the SQL statement for insertion
        $stmt = $conn->prepare("INSERT INTO orders (descrip, nop, deliverynote, remark, orderno) VALUES (:descrip, :nop, :deliverynote, :remark, :orderno)");

        // Iterate over the rows and insert them into the database
        for ($i = 0; $i < count($serialNumbers); $i++) {
            $stmt->bindParam(':descrip', $description[$i]);
            $stmt->bindParam(':nop', $num[$i]);
            $stmt->bindParam(':deliverynote', $dispatchnotes[$i]);
            $stmt->bindParam(':remark', $remarks[$i]);
            $stmt->bindParam(':orderno', $orderNo);
            
            $stmt->execute();
        }

        echo "Rows inserted successfully.";

        // Close the database connection
        $conn = null;
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dynamically Add/Remove Rows from Database</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>
    <h2>Dynamically Add/Remove Rows from Database</h2>
    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
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
                <td>1. </td>
                <td><input type="text" name="description[]"></td>
                <td><input type="text" name="num[]"></td>
                <td><input type="text" name="dispatchnotes[]"></td>
                <td><input type="text" name="remarks[]"></td>
                <td> </td>
            </tr>
        </table>
        <br>
        <button type="button" onclick="addRow()">Add Row</button>
        <br><br>
        <input type="submit" name="submit" value="Submit">
    </form>

    <script type="text/javascript" src="form.js"></script>
</body>
</html>
