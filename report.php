<?php
session_start();
require_once 'db_connection.php';
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

if (!isset($_SESSION["username"]) && !isset($_SESSION["phone_no"])) {
    header("Location: newlogin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["logout"])) {
    session_destroy();
    header("Location: newlogin.php");
    exit();
}

$startDate = $_POST['start_date'] ?? '';
$endDate = $_POST['end_date'] ?? '';

$columns = array(
    'created_at' => 'Created At',
    'orderno' => 'Order Number',
    'order_dest' => 'Order Destination',
    'issue_desc' => 'Issue Description',
    'placeoi' => 'Place of Issue',
    'issueto' => 'Issued To',
    'securityn' => 'Security Number',
    'guard_name' => 'Guard Name',
    'collector_name' => 'Collector Name',
    'returnable' => 'Returnable',
    'forwarded_to' => 'Forwarded To',
    'moc' => 'MOC',
    'vehno' => 'Vehicle Number',
    'created_by' => 'Created By',
    'issue_dep' => 'Issue Department'
);

$selectedColumns = isset($_POST['columns']) ? $_POST['columns'] : array();

if (!empty($startDate) && !empty($endDate)) {
    $selectedColumnsString = implode(", ", $selectedColumns);
    
    // Prepare the SQL statement according to the user
    if($_SESSION["designation"] == "E"){
        $query = "SELECT $selectedColumnsString FROM order_no WHERE created_at BETWEEN ? AND ? AND (forwarded_to = ? OR created_by = ?)";
        $statement = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($statement, "ssii", $startDate, $endDate, $_SESSION["cpf_no"], $_SESSION["cpf_no"]);
    }
    else if($_SESSION["designation"] == "G"){
        $query = "SELECT $selectedColumnsString FROM order_no WHERE created_at BETWEEN ? AND ? AND placeoi = ?";
        $statement = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($statement, "sss", $startDate, $endDate, $_SESSION["venue"]);
    } else{
        $query = "SELECT $selectedColumnsString FROM order_no WHERE created_at BETWEEN ? AND ?";
        $statement = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($statement, "ss", $startDate, $endDate);
    } 

    // $statement = mysqli_prepare($connection, $query);
    mysqli_stmt_execute($statement);

    $result = mysqli_stmt_get_result($statement);

    if (!$result) {
        die('Query failed: ' . mysqli_error($connection));
    }

    $data = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    
    // Close the statement
    mysqli_stmt_close($statement);
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Report</title>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/report.css">
</head>
<body>
    <div class="dash-heading">

        <div>
            <button class="btn btn-secondary" id="gb" onclick="window.location.href = 'skdash.php'">Go Back</button>
        </div>

        <div>
            <div>
                <table>
                    <tr>
                        <td><img src="assets/images.png" class="logo"></td>
                        <td>
                            <h2>Oil and Natural Gas Corporation</h2>
                            <h4>MUMBAI REGION- REGIONAL OFFICE- INFOCOM</h4>
                        </td>
                    </tr>
                </table>
                <h2 class="wlc"><?php echo $_SESSION["designation"] == "E" || $_SESSION["designation"] == "S" ? "Welcome, ".$_SESSION["username"] : "Welcome"; ?>!</h2>
            </div>    
        </div>
        
        <div>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <button type="submit" id="lo" class="btn btn-outline-danger" name="logout">Logout</button>
            </form>
        </div>

    </div>

    <div class="container">
        <div class="card">
            <div class="card-body">
                <h1 class="card-title">Report</h1>
                <form method="POST" action="">
                    <div class="form-group row">
                        <label for="startDateInput" class="col-sm-2 col-form-label">Start Date:</label>
                        <div class="col-sm-4">
                            <input type="date" class="form-control" name="start_date" id="startDateInput" value="<?php echo $startDate; ?>">
                        </div>
                        <label for="endDateInput" class="col-sm-2 col-form-label">End Date:</label>
                        <div class="col-sm-4">
                            <input type="date" class="form-control" name="end_date" id="endDateInput" value="<?php echo $endDate; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <h2>Select Columns to display</h2>
                        <?php foreach ($columns as $columnKey => $columnLabel) { ?>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="columns[]" value="<?php echo $columnKey; ?>" id="checkbox-<?php echo $columnKey; ?>" <?php if (in_array($columnKey, $selectedColumns)) echo 'checked'; ?>>
                                <label class="form-check-label" for="checkbox-<?php echo $columnKey; ?>"><?php echo $columnLabel; ?></label>
                            </div>
                        <?php } ?>
                    </div>                  
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>

        <?php if (!empty($data)) { ?>
            <div class="card">
                <div class="card-body">
                    <h2>Filter Values</h2>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="columnSelect">Column:</label>
                            <select id="columnSelect" class="form-control">
                                <?php foreach ($selectedColumns as $column) { ?>
                                    <option value="<?php echo $column; ?>"><?php echo $columns[$column]; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="filterInput">Filter:</label>
                            <input type="text" id="filterInput" class="form-control" placeholder="Enter filter value">
                        </div>
                        <div class="form-group col-md-4">
                            <label>&nbsp;</label>
                            <div>
                                <button id="applyFilterBtn" class="btn btn-primary">Apply</button>
                                <button id="removeFilterBtn" class="btn btn-secondary">Remove Filter</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <table id="orderTable" class="table table-striped">
                        <thead>
                            <tr>
                                <?php foreach ($selectedColumns as $columnKey) { ?>
                                    <th><?php echo $columns[$columnKey]; ?></th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data as $row) { ?>
                                <tr>
                                    <?php foreach ($selectedColumns as $columnKey) { ?>
                                        <td><?php
                                                if ($row[$columnKey] === null) echo "-";
                                                else if ($columnKey == 'returnable')
                                                    echo $row[$columnKey] == 1 ? "YES" : "NO";
                                                else
                                                    echo $row[$columnKey];
                                                ?>
                                        </td>
                                    <?php } ?>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php } ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#orderTable').DataTable();

            $('#applyFilterBtn').click(function() {
                table.search('').columns().search('').draw();
                var columnValue = $('#columnSelect').prop('selectedIndex');
                var filterValue = $('#filterInput').val().trim();
                table.column(columnValue).search(filterValue, true).draw();
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