<?php
session_start();

// Include the database connection file
require_once "db_con.php";

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $return= $_POST["return"];
    $issued= $_POST["issued"];
    $placei= $_POST["placei"];
    $issuet= $_POST["issuet"];
    $pod= $_POST["pod"];
    $bdesc= $_POST["description"];
    $num= $_POST["num"];
    $dnote= $_POST["dispatchnotes"];
    $remark= $_POST["remarks"];
    $sql = "SELECT MAX(orderno) FROM order_no";
    $result = $connection->query($sql);
    $orderno = ($result) ? $result : 1;
    $sql = "INSERT INTO order_no (orderno,order_dest,issue_desc,placeoi,issueto,securityn,collectorid,returnable) VALUES ('$orderno','$pod','$issued','$placei','$issuet','$securityn','$collector','$return')";

    $sql1 = "INSERT INTO order (descrip,nop,deliverynote,remark,orderno) VALUES ('$bdesc','$num','$dnote','$remark','$orderno')";

      if ($connection->query($sql) === TRUE && $connection->query($sql1) === TRUE) {
        echo "Order Created Successfully";
      
    }
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
    </head>
    <body>
    <div class="top">
    <table>
        <tr>
        <td><img src="images.png"></td>
        <td><h1>Oil and Natural Gas Corporation</h1>
        <h3>MUMBAI REGION- REGIONAL OFFICE- INFOCOM</h3></td>
        </tr>
    </table>
    </div>
    <div class="form">
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
        <tr>
            <td></td>
            <td><input type="text" name="description"></td>
            <td><input type="text"name="num"></td>
            <td><input type="text" name="dispatchnotes"></td>
            <td><input type="text" name="remarks"></td>
        </tr>
       </table> 
       <input type="button" value="add row"><br>
       <label for="fors">Forward To</label>
       <select>
        <option></option>
       </select><br>
       <label for="ColN">Collector Name</label>
       <select>
        <option></option>
       </select><br>    
        <input type="submit" value="Place Order">
       </form>
      </div>    
        
    </body>
</html>