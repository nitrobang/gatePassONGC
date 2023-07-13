$returnable = $_POST["return"] == "1" ? 1 : 0;
    $issueDesc = mysqli_real_escape_string($conn, $_POST["issued"]);
    if ($issueDesc == "Infocom") {
        $$issueDesc = "I";
    } elseif ($issueDesc == "Management") {
        $$issueDesc = "M";
    } elseif ($issueDesc == "Production") {
        $$issueDesc = "P";
    }
    $placeOfIssue = mysqli_real_escape_string($conn, $_POST["placei"]);
    $issueTo = mysqli_real_escape_string($conn, $_POST["issuet"]);
    $placeOfDestination = '';
    $returnDate = strtotime($_POST['returnDate']);
    $returnDate=date('Y-m-d', $returnDate);
    if ($_POST["pod"] == "other") {
        $placeOfDestination = mysqli_real_escape_string($conn, $_POST["otherOption"]);
    } else {
        $placeOfDestination = mysqli_real_escape_string($conn, $_POST["pod"]);
    }
    $forwardTo = mysqli_real_escape_string($conn, $_POST["fors"]);
    $signatory = mysqli_real_escape_string($conn, $_POST["signatory"]);
    $coll_approval = 0;
    $security_approval = 0;
    $guard_approval = 0;
    $sign_approval = 0;
    
        // Insert data into the 'order_no' table
        $UpdateOrderNoQuery = "UPDATE order_no SET order_dest = ?, issue_dep = ?, placeoi = ?, issueto = ?, securityn = '', returnable = ?, returndate= ?,coll_approval = ?, sign_approval=?,security_approval = ?, guard_approval = ?, forwarded_to = ?, signatory= ?, created_by = ? WHERE orderno = ?";

        // Prepare the statement
        $stmt = $conn->prepare($UpdateOrderNoQuery);

        // Bind the parameters
        $stmt->bind_param('ssssisiiiiiiii', $placeOfDestination, $issueDesc, $placeOfIssue, $issueTo, $returnable, $returnDate, $coll_approval,$sign_approval, $security_approval, $guard_approval, $forwardTo, $signatory, $created_by, $orderno);
        <div class="sugges">
                <div class="result1">
                    <p>Signatory Officer:</p>
                </div>
                <input type="text" name="signatory" oninput="findso(this.value)" value="<?php echo $orderData['signatory']; ?>">
                <ul class="autocomplete-list1"></ul>
                <div class="clear1"></div>
            </div>
            <div id="returnDateForm" style="display: none;">
                <label for="returnDate">Return Date:</label>
                <input type="date" name="returnDate" id="returnDate"value="<?php echo $orderData['returndate']; ?>">
            </div>