<?php
session_start();
require_once "db_connection.php";

// Check if the user is not logged in
if (!isset($_SESSION["username"]) && !isset($_SESSION["phone_no"])) {
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

$conn = $connection;
if (isset($_SESSION["cpf_no"])) {
    $cpf_no = $_SESSION["cpf_no"];
    
}

if(!isset($_SESSION['designation'])){
    //get the designation of the user
    $query = "SELECT * FROM employee WHERE cpfno = '$cpf_no'";
    $result = mysqli_query($connection, $query);
    if (!$result || mysqli_num_rows($result) == 0) {
        header("Location: skdash.php");
        exit();
    }
    $user = mysqli_fetch_assoc($result);
    $designation = $user["designation"];
} else $designation = $_SESSION["designation"];


// Check if the user clicked on the collector link
if (($designation == "E") && isset($_GET['orderno'])) {
    $_SESSION['orderno'] = $_GET['orderno'];
    header("Location: collector-page.php");
    exit();
}

//Check if the user clicked on the security link
if (($designation == "S") && isset($_GET['orderno'])) {
    $_SESSION['orderno'] = $_GET['orderno'];
    header("Location: security-page.php");
    exit();
}

//Check if the user clicked on the guard link
if (($designation == "G") && isset($_GET['orderno'])) {
    $_SESSION['orderno'] = $_GET['orderno'];
    header("Location: guard-page.php");
    exit();
}

// Set the session variable 'isEditable' and redirect to form.php for "New Order" button
if ($designation == "E" && isset($_POST['new_order'])) {
    $_SESSION['isedit'] = 0;
    header("Location: form.php");
    exit();
}

// Set the session variable 'isEditable' and redirect to form.php for "Edit" button
if ($designation == "E" && isset($_POST['edit_order'])) {
    $orderno = $_POST['edit_order'];
    header("Location: tempform.php?orderno=$orderno");
    exit();
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

$query = "SELECT created_at, orderno, order_dest, issue_desc, placeoi, issueto, securityn, guard_name, collector_name, returnable, forwarded_to, moc, vehno, created_by FROM order_no";
$result = mysqli_query($connection, $query);

if (!$result) {
    die('Query failed: ' . mysqli_error($connection));
}

$data = array();

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

$jsonData = array(
    'data' => $data
);

$columns = array(
    'created_at',
    'orderno',
    'order_dest',
    'issue_desc',
    'placeoi',
    'issueto',
    'securityn',
    'guard_name',
    'collector_name',
    'returnable',
    'forwarded_to',
    'moc',
    'vehno',
    'created_by'
);

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
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
</head>

<body>
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
    <h3>Dashboard</h3>
    <!--  -->


    <div>
        <label for="columnSelect">Column:</label>
        <select id="columnSelect">
            <?php foreach ($columns as $column) { ?>
                <option value="<?php echo $column; ?>"><?php echo $column; ?></option>
            <?php } ?>
        </select>
        <label for="filterInput">Filter:</label>
        <input type="text" id="filterInput" placeholder="Enter filter value">
        <button id="applyFilterBtn">Apply</button>
        <button id="removeFilterBtn">Remove Filter</button>
    </div>

    <table id="orderTable">
        <thead>
            <tr>
                
                <th>orderno</th>
                <th>order_dest</th>
                <th>issue_desc</th>
                <th>placeoi</th>
                <th>issueto</th>
                <th>securityn</th>
                <th>guard_name</th>
                <th>collector_name</th>
                <th>returnable</th>
                <th>forwarded_to</th>
                <th>moc</th>
                <th>vehno</th>
                <th>created_by</th>
            </tr>
        </thead>
    </table>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#orderTable').DataTable({
                "data": <?php echo json_encode($jsonData['data']); ?>,
                "columns": [
                    { "data": "orderno" },
                    { "data": "order_dest" },
                    { "data": "issue_desc" },
                    { "data": "placeoi" },
                    { "data": "issueto" },
                    { "data": "securityn" },
                    { "data": "guard_name" },
                    { "data": "collector_name" },
                    { "data": "returnable" },
                    { "data": "forwarded_to" },
                    { "data": "moc" },
                    { "data": "vehno" },
                    { "data": "created_by" }
                ]
            });

            $('#applyFilterBtn').click(function() {
                var columnValue = $('#columnSelect').prop('selectedIndex');
                var filterValue = $('#filterInput').val().trim();
                var table = $('#orderTable').DataTable();
                table.column(columnValue).search(filterValue).draw();
            });

            $('#removeFilterBtn').click(function() {
                table.search('').columns().search('').draw();
                $('#columnSelect').prop('selectedIndex', 0);
                $('#filterInput').val('');
            });
        });
    </script>
</body>

</html>