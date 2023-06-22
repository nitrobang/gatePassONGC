<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Hello, World!</title>
</head>
<body>

  <?php
    $_SESSION["designation"] = 'G';
    $_SESSION["phone_no"] = '1010101010';
    header('Location: skdash.php');
  ?>

</body>
</html>
