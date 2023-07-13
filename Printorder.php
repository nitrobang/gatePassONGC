<?php
session_start();
require_once "db_connection.php";

// echo "hello".$_SESSION['orderno'];
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
$orderno = $_GET['orderno'];
$conn = $connection;
// Function to get employee names and CPF numbers based on designation
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
    <style>
    body {
    background-color: transparent;
    color: #000;
    font-family: "Open Sans", sans-serif !important;
    font-size: 14px;
    -moz-osx-font-smoothing: grayscale;
    -webkit-font-smoothing: antialiased;
    font-weight: var(--font-weight-normal, 400);
    line-height: 1.5;
    margin: 0 auto;
    -ms-overflow-style: scrollbar;
    text-align: inherit;
    position: relative;
    height: 100%;
    width: 100%;
    overflow-x: hidden;

    background-position-x: center;
    background-position-y: top;
    background-size: cover;
    background-attachment: fixed;
    background-origin: initial;
    background-clip: initial;
    overflow-y: auto;
}

.container {
    width: 100vh;
    top: 0px;
    margin: 0 auto;
    background-color: white;
    padding: 20px;
    padding-top: 0px;
    border-radius: 5px;

}
h2 {
    text-align: center;
}
.container table {
    width: 75%;
    height: 100px;
}
.container table td {
    width:fit-content;
}
.logo{
    width: 100px;
    height: 100px;
}
.form-group {
    margin-bottom: 15px;
}

label {
    display: block;
    margin-bottom: 5px;
}
select,
input[type="text"],
input[type="password"],
input[type="email"] {
    width: 100%;
    padding: 5px;
}

input[type="date"]{
    padding: 5px;
}
.clear,
.clear1{
    clear:both;
    margin-top: 20px;
}

.autocomplete-list,
.autocomplete-list1{
    width: 250px;
    position: relative;
}
.autocomplete-list ,
.autocomplete-list1{
    list-style: none;
    padding: 0px;
    width: 100%;
    position: absolute;
    margin: 0;
    background: white;
}

.autocomplete-list  li
,.autocomplete-list1  li{
    background: #EDCB96;
    padding: 4px;
    margin-bottom: 1px;
}
.sugg input[type=text],
.sugges input[type=text]{
    padding: 5px;
    width: 100%;
    letter-spacing: 1px;
}
 table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
    margin-top: 20px;
    
}


#dynamic-table th,#orderTable th, td {

    padding: 8px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}


#dynamic-table th,#orderTable th {
    background-color: #98C1D9;
}

#dynamic-table  tr:hover,#orderTable tr:hover { 
    background-color: #f5f5f5;
}
select{
    width: 100%;
    border: 1px solid #ccc;
    padding-left: 5px;
    padding: 5px;
    font-size: 18px;
    font-family: 'Open Sans', sans-serif;
    color: #555;
    background-color: rgb(255, 255, 255);
    background-image: none;
    border: 1px solid rgb(41, 18, 18);
}
select>option{
    font-size: 18px;
    font-family: 'Open Sans', sans-serif;
    color: #555;
    background-color: rgb(247, 247, 247);
    background-image: none;
    font-size: 18px;

    padding: 15px;
    border: 1px solid rgb(41, 18, 18);


}
.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

input[type="text"],
input[type="password"],
input[type="phone"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

.btn-primary {
    background-color: #4CAF50;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
}

.btn-primary:hover {
    background-color: #45a049;
}

.success-message {
    color: #4CAF50;
    margin-top: 10px;
}

.error-message {
    color: #f44336;
    margin-top: 10px;
}
    </style>
    <script>
        window.onload = function() {
            // Trigger the print functionality
            window.print();
        };
    </script>
</head>

<body>
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
                <table>
                    <tr>
                        <td><label for="securityn">Security ID</label>
                        <input type="text" class="form-group" name="createdby" value="<?php echo $orderData['created_by']; ?>" required readonly><br></td>
                        <td><label for="collectorn">Collector-ID</label>
                    <input type="text" class="form-group" name="issuet" value="<?php echo $orderData['forwarded_to']; ?>" required readonly><br></td>
                        <td> <label for="collectorn">Collector-ID</label>
                    <input type="text" class="form-group" name="issuet" value="<?php echo $orderData['forwarded_to']; ?>" required readonly><br></td>
                    </tr>
                </table>
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
                    <input type="date" name="returnDate" id="returnDate" value="<?php echo $orderData['returndate']; ?>">
                </div>
    </div>
    </form>
                    </div><?php
        } else {
            // Handle the case where no rows were found
            echo "No order data found.";
        }
            ?>
</body>

</html>