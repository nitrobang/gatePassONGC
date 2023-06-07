<?php
session_start();

// Include the database connection file
require_once "db_connection.php";

// Check if the user is already logged in
if (isset($_SESSION["username"])) {
    header("Location: form.php");
    exit();
}

// Define variables and set to empty values
$cpf_no = $username = $password = "";
$errorMessage = "";

// Form submission handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    if (empty($_POST["cpf_no"])) {
        $errorMessage = "CPF Number is required";
    } else {
        $cpf_no = test_input($_POST["cpf_no"]);
    }

    // Validate password
    if (empty($_POST["password"])) {
        $errorMessage = "Password is required";
    } else {
        $password = test_input($_POST["password"]);
    }

    // Proceed with login if there are no validation errors
    if (empty($errorMessage)) {
        // Query the database to check if the user exists
        $query = "SELECT * FROM users WHERE cpfno = '$cpf_no'";
        $result = mysqli_query($connection, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            //get the designation of the user
            $query2 = "SELECT * FROM employee WHERE cpfno = '$cpf_no'";
            $result2 = mysqli_query($connection, $query2);
            if (!$result2 || mysqli_num_rows($result2) == 0) {
                header("Location: login.php");
                exit();
            }
            $user2 = mysqli_fetch_assoc($result2);
            $designation = $user2["designation"];   
            // Verify the password
            if (password_verify($password, $user["password"])) {
                // Password is correct, create a session and redirect to the dashboard
                $_SESSION["username"] = $user["username"];
                $_SESSION["cpf_no"] = $cpf_no;
                $_SESSION['designation'] = $designation;
                header("Location: skdash.php");
                exit();
            } else {
                $errorMessage = "Invalid password";
            }
        } else {
            $errorMessage = "Invalid username";
        }
        $conn = $connection;
    if(isset($_SESSION["cpf_no"])){
        $cpf_no = $_SESSION["cpf_no"];
        }
    }
}

// Function to sanitize form inputs
function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
</head>

<body>
    <div class="container">
    <div class="lbox">
        <div class="lmod">
            <img src="assets/images.png" class="logo" alt="ONGC Logo" />
            <h1>Gate Pass Portal for ONGCians</h1>
            <h2>Login</h2>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <input type="text" id="cpf_no" name="cpf_no" class="form-control" placeholder="Your CPF number" required>
                </div>
                <div class="form-group">

                    <input type="password" id="password" name="password" class="form-control" placeholder="password" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Sign in</button>
                </div>
                <?php if (!empty($errorMessage)): ?>
                        <div class="error-message"><?php echo $errorMessage; ?></div>
                <?php endif; ?>
            </form>
            <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
    
        </div>
    </div>
    </div>
</body>

</html>