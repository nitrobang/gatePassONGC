<?php
session_start();

// Include the database connection file
require_once "db_connection.php";

// Check if the user is already logged in
if (isset($_SESSION["guard_name"])) {
    header("Location: form.php");
    exit();
}

// Define variables and set to empty values
$guard_name = $password = $phone = $location = "";
$successMessage = $errorMessage = "";

// Form submission handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate guard name
    if (empty($_POST["guard_name"])) {
        $errorMessage = "Username is required";
    } else {
        $guard_name = test_input($_POST["guard_name"]);
    }

    // Validate password
    if (empty($_POST["password"])) {
        $errorMessage = "Password is required";
    } else {
        $password = test_input($_POST["password"]);
    }

    // Validate phone
    if (empty($_POST["phone"])) {
        $errorMessage = "Phone number is required";
    } else {
        $phone = test_input($_POST["phone"]);

        // Check if the phone number already exists
        $existingPhoneQuery = "SELECT * FROM `security_guard` WHERE phone_no = '$phone'";
        $existingPhoneResult = mysqli_query($connection, $existingPhoneQuery);
        $existingPhoneCount = mysqli_num_rows($existingPhoneResult);

        if ($existingPhoneCount >= 1) {
            $errorMessage = "Phone number already exists";
        }
    }

    // Validate location
    if (empty($_POST["location"])) {
        $errorMessage = "Location is required";
    } else {
        $location = test_input($_POST["location"]);
    }

    // Proceed with signup if there are no validation errors
    if (empty($errorMessage)) {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert the user into the database
        $insertQuery = "INSERT INTO security_guard (guard_name, password, phone_no, venue) VALUES ('$guard_name', '$hashedPassword', '$phone', '$location')";
        $insertResult = mysqli_query($connection, $insertQuery);

        if ($insertResult) {
            $successMessage = "Signup successful! You can now <a href='login.php'>Login</a>.";
        } else {
            $errorMessage = "Error creating user";
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
    <title>Guard Sign Up</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <div class="container">
        <div class="lbox">
            <h2>Guard Sign Up</h2>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="guard_name">Name:</label>
                    <input type="text" id="guard_name" name="guard_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone:</label>
                    <input type="phone" id="phone" name="phone" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="location">Your Location:</label>
                    <label><input type="radio" name="location" value="N" required> NBP Green Heights</label>
                    <label><input type="radio" name="location" value="V" required> Vasundara Bhavan</label>
                    <label><input type="radio" name="location" value="H" required> 11 High</label>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Sign Up</button>
                </div>
                <?php if (!empty($successMessage)) : ?>
                    <div class="success-message"><?php echo $successMessage; ?></div>
                <?php endif; ?>
                <?php if (!empty($errorMessage)) : ?>
                    <div class="error-message"><?php echo $errorMessage; ?></div>
                <?php endif; ?>
            </form>
            <p>Already have an account? <a href="newlogin.php">Login</a></p>
        </div>
    </div>
</body>

</html>
