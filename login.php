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

            // Verify the password
            if (password_verify($password, $user["password"])) {
                // Password is correct, create a session and redirect to the dashboard
                $_SESSION["username"] = $user["username"];
                $_SESSION["cpf_no"] = $cpf_no;
                header("Location: form.php");
                exit();
            } else {
                $errorMessage = "Invalid password";
            }

        } else {
            $errorMessage = "Invalid username";
        }
    }
}

// Function to sanitize form inputs
function test_input($data) {
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
</head>
<body>
    <div class="bg">
    <div class="container">
        <h2>Login</h2>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="username">CPF Number:</label>
                <input type="text" id="cpf_no" name="cpf_no" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <button type="submit" class="submit-btn">Login</button>
            </div>
            <?php if (!empty($errorMessage)) : ?>
                <div class="error-message"><?php echo $errorMessage; ?></div>
            <?php endif; ?>
        </form>
        <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
    </div>
    </div>
</body>
</html>
