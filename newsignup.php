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
$username = $password = $email = "";
$successMessage = $errorMessage = "";

// Form submission handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    if (empty($_POST["username"])) {
        $errorMessage = "Username is required";
    } else {
        $username = test_input($_POST["username"]);
    
        // Check if the username already exists
        $existingUserQuery = "SELECT * FROM `external_users` WHERE username = '$username'";
        $existingUserResult = mysqli_query($connection, $existingUserQuery);
        $existingUserCount = mysqli_num_rows($existingUserResult);
    
        if ($existingUserCount >= 1) {
            $errorMessage = "Username already exists";
        } else {
            $username = test_input($_POST["username"]);
        }
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

    
    if (isset($_POST['role'])) {
        $role = $_POST['role'];
    } else {
        $errorMessage = "No Designation Selected";
    }
      

    // Proceed with signup if there are no validation errors
    if (empty($errorMessage)) {
        // Check if the username or email already exists in the database
        $query = "SELECT * FROM external_users WHERE username = '$username' OR email = '$email'";
        $result = mysqli_query($connection, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $errorMessage = "User already exists";
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert the user into the database
            $insertQuery = "INSERT INTO external_users (username, password, email) VALUES ('$username', '$hashedPassword', '$email')";
            $insertResult = mysqli_query($connection, $insertQuery);

            if ($insertResult) {
                $successMessage = "Signup successful! You can now <a href='login.php'>Login</a>.";
                $u_id = mysqli_insert_id($connection);
                // Insert the user_to_group into the database
                if ($role==4){
                    $insertQuery = "INSERT INTO user_to_groups(user_id, group_id) values('$u_id' , 4)";
                }else{
                    $insertQuery = "INSERT INTO user_to_groups(user_id, group_id) values('$u_id' , 5)";
                }
                
                $insertResult = mysqli_query($connection, $insertQuery);
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
    <style>
        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .btn-primary {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-primary:hover {
            background-color: #45a049;
        }

        .success-message {
            color: #4CAF50;
            margin-top: 10px;
        }

        .error-message {
            color: #f44336;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="lbox">
            <h2>Sign Up</h2>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="username">Name:</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="role">Role:</label>
                    <label><input type="radio" name="role" value="4" required> Collector</label>
                    <label><input type="radio" name="role" value="5" required> Security Guard</label>
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
            <p>Already have an account? <a href="login.php">Login</a></p>
        </div>
    </div>
</body>
</html>

