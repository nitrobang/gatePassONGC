<?php
require_once 'db_connection.php';

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
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
</head>
<body>
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
                <th>created_at</th>
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
                    { "data": "created_at" },
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
