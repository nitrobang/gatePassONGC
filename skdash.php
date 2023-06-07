<?php
session_start();
require_once "db_connection.php";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["logout"])) {
    // Destroy the session and redirect to the login page
    session_destroy();
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Store Keeper Dashboard</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/styles.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
</head>
    </head>
    <body>
    <div class="container">
    <div class="upar">
        <img src="assets/imagetrans.png" class="logo" alt="ongc logo" width="150px"height="150px" />
    <div class="compname">
        
        <p>Oil and Natural Gas Corporation</p><br>
        <p>MUMBAI REGION- REGIONAL OFFICE- INFOCOM</p>
        
        
    </div>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <div class="button1">
    <a href="form.php"><button type="button" class="btn btn-light">New order</button></a>
    
    </div>
    <div class="button2">
        <button type="button" name="logout" class="btn btn-light">Logout</button><br>
    </div>
    </div>
    </div>    
    
</body> 

</html>