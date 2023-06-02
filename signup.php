<?php
session_start();

// Include the database connection file
require_once "db_connection.php";

// Check if the user is already logged in
if (isset($_SESSION["username"])) {
    header("Location: dashboard.php");
    exit();
}

// Define variables and set to empty values
$username = $password = $email = "";
$successMessage = $errorMessage = "";

// Form submission handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    if (empty($_POST["username"])) {
        $errorMessage = "Username is required";
    } else {
        $username = test_input($_POST["username"]);
    }

    // Validate password
    if (empty($_POST["password"])) {
        $errorMessage = "Password is required";
    } else {
        $password = test_input($_POST["password"]);
    }

    // Validate email
    if (empty($_POST["email"])) {
        $errorMessage = "Email is required";
    } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Invalid email format";
    } else {
        $email = test_input($_POST["email"]);
    }

    // Proceed with signup if there are no validation errors
    if (empty($errorMessage)) {
        // Check if the username or email already exists in the database
        $query = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
        $result = mysqli_query($connection, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $errorMessage = "Username or email already exists";
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert the user into the database
            $insertQuery = "INSERT INTO users (username, password, email) VALUES ('$username', '$hashedPassword', '$email')";
            $insertResult = mysqli_query($connection, $insertQuery);

            if ($insertResult) {
                $successMessage = "Signup successful! You can now <a href='login.php'>Login</a>.";
            } else {
                $errorMessage = "Error creating user";
            }
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
    <title>Sign Up</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h2>Sign Up</h2>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <button type="submit" class="submit-btn">Sign Up</button>
            </div>
            <?php if (!empty($successMessage)) : ?>
                <div class="success-message"><?php echo $successMessage; ?></div>
            <?php endif; ?>
            <?php if (!empty($errorMessage)) : ?>
                <div class="error-message"><?php echo $errorMessage; ?></div>
            <?php endif; ?>
        </form>
        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
</body>
</html>
