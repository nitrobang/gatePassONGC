<?php
session_start();

// Include the database connection file
require_once "db_connection.php";

// Check if the user is already logged in
if (isset($_SESSION["username"])) {
    header("Location: skdash.php");
    exit();
}

// Define variables and set to empty values
$cpf_no = $phone_no = $password = "";
$errorMessage = "";

// Form submission handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate login type selection
    if (empty($_POST["login_type"])) {
        $errorMessage = "Please select a login type";
    } else {
        $loginType = test_input($_POST["login_type"]);
    }

    // Validate CPF number or phone number based on login type
    if ($loginType === "ongc") {
        if (empty($_POST["cpf_no"])) {
            $errorMessage = "CPF Number is required";
        } else {
            $cpf_no = test_input($_POST["cpf_no"]);
        }
    } elseif ($loginType === "guard") {
        if (empty($_POST["phone_no"])) {
            $errorMessage = "Phone Number is required";
        } else {
            $phone_no = test_input($_POST["phone_no"]);
        }
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
        if ($loginType === "ongc") {
            $query = "SELECT * FROM users WHERE cpfno = '$cpf_no'";
        } elseif ($loginType === "guard") {
            $query = "SELECT * FROM security_guard WHERE phone_no = '$phone_no'";
        }

        $result = mysqli_query($connection, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            //get the designation of the user
            $query2 = "SELECT * FROM employee WHERE cpfno = '$cpf_no'";
            $result2 = mysqli_query($connection, $query2);
            if (!$result2 || mysqli_num_rows($result2) == 0) {
                header("Location: newlogin.php");
                exit();
            }
            $user2 = mysqli_fetch_assoc($result2);
            $designation = $user2["designation"]; 

            if ($loginType === "ongc") {
                // Verify the password for ONGC login
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
            } elseif ($loginType === "guard") {
                // Verify the password for guard login
                $hashedPassword = $user["password"]; // Retrieve the hashed password from the database
                echo $password;
                if (password_verify($password, $hashedPassword)) {
                    // Password is correct, create a session and redirect to the guard dashboard
                    $_SESSION["phone_no"] = $phone_no;
                    $_SESSION["designation"] = 'G';
                    header("Location: test.php");
                } else {
                    $errorMessage = "Invalid password";
                }
            }
        } else {
            $errorMessage = "Invalid username";
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
    <script src="form.js"></script>
</head>

<body>
    <div class="container">
        <div class="lbox">
            <div class="lmod">
                <img src="assets/images.png" class="logo" alt="ONGC Logo" />
                <h1>Gate Pass Portal for ONGCians</h1>
                <h2>Login</h2>
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" >
                    <div class="form-group">
                        <label>Select Login Type:</label>
                        <div>
                            <label for="ongc_login">
                                <input type="radio" id="ongc_login" name="login_type" value="ongc" checked required>
                                ONGC Login
                            </label>
                            <label for="guard_login">
                                <input type="radio" id="guard_login" name="login_type" value="guard" required>
                                Guard Login
                            </label>
                        </div>
                    </div>

                    <div class="form-group" id="ongc_fields">
                        <input type="text" id="cpf_no" name="cpf_no" class="form-control" placeholder="Your CPF number">
                    </div>

                    <div class="form-group" id="guard_fields" style="display: none;">
                        <input type="text" id="phone_no" name="phone_no" class="form-control" placeholder="Your Phone number">
                    </div>

                    <div class="form-group">
                        <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Sign in</button>
                    </div>
                    <?php if (!empty($errorMessage)): ?>
                        <div class="error-message"><?php echo $errorMessage; ?></div>
                    <?php endif; ?>
                </form>
                <p>Signup for Guards <a href="newsignup.php">Sign Up</a></p>
            </div>
        </div>
    </div>

    <script>
        // Show/hide input fields based on login type selection
        var ongcLogin = document.getElementById("ongc_login");
        var guardLogin = document.getElementById("guard_login");
        var ongcFields = document.getElementById("ongc_fields");
        var guardFields = document.getElementById("guard_fields");

        ongcLogin.addEventListener("change", function() {
            ongcFields.style.display = "block";
            guardFields.style.display = "none";
        });

        guardLogin.addEventListener("change", function() {
            guardFields.style.display = "block";
            ongcFields.style.display = "none";
        });
    </script>
</body>

</html>
