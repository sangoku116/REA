<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: Admin_login.php");
    exit;
} elseif ($_SESSION["username"] !== 'user2'){ // if the signed in user is not the User Admin
    header("location: Admin_login.php");
    exit;
}

// Include config file
require_once "database_connect.php";

// Define variables and initialize with empty values
$username = $password = $confirm_password = "";
//$username_err = $password_err = $confirm_password_err = "";
$error = $created_user = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){


    // Validate username
    if(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
        $username_err = "Username can only contain letters, numbers, and underscores.";
        $error = $username_err;
    } else{
        // Prepare a select statement to check for any duplicate with users
        $sql = "SELECT UserID FROM Admins WHERE Username = ?";

        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Set parameters
            $param_username = trim($_POST["username"]);

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);

                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
                    $error = $username_err;
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Validate password
    if(strlen(trim($_POST["password"])) < 8){
        $password_err = "Password must have at least 8 characters.";
        $error = $password_err;
    } else{
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    $confirm_password = trim($_POST["confirm_password"]);
    if(empty($password_err) && ($password != $confirm_password)){
        $confirm_password_err = "Passwords did not match.";
        $error = $confirm_password_err;
    }

    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){

        // Prepare an insert statement
        $sql = "INSERT INTO Admins (username, password) VALUES (?, ?)";

        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);

            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                $created_user = "User created.";
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close connection
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create a New User</title>
    <link rel="stylesheet" href="w3css.css">
</head>
<style>
    body, html {
        height: 100%;
        font-family: "Inconsolata", sans-serif;
        background: linear-gradient(#6c88a0, #145288) no-repeat fixed;
        background-size: cover;
    }
</style>
<body>
<div class="form-all">
    <!-- Links (sit on top) -->
    <div class="w3-top">
        <div class="w3-row w3-padding w3-black">
            <div class="w3-col w3-left w3-hide-small s1">
                <span class="w3-button-welcome w3-bar-item w3-black"><b><?php echo htmlspecialchars($_SESSION["username"]); ?></b></span>
            </div>
            <div class="w3-col w3-right w3-hide-small s1">
                <a href="logout.php" class="w3-button w3-bar-item w3-black">SIGN OUT</a>
            </div>
            <div class="w3-col w3-right w3-hide-small s1">
                <a href="welcome.php" class="w3-button w3-bar-item w3-black">REPORTS</a>
            </div>
            <div class="w3-col w3-right w3-hide-small s1">
                <a href="deluser.php" class="w3-button w3-bar-item w3-black">DELETE USER</a>
            </div>
        </div>
    </div>

    <div class="w3-container" id="where" style="padding-bottom:32px;">
        <div class="w3-content" style="max-width:700px">
            <br><br><br><br><br><br>
            <h5 class="w3-center w3-padding-48"><span class="w3-tag w3-wide">CREATE A NEW USER</span></h5>
            <p><span style="color: white">Enter the credentials for the new user. </span></p>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <!--<form action="/action_page.php" target="_blank"> -->
                <p><input class="w3-input w3-padding-16 w3-border" type="text" placeholder="Username" required name="username" value="<?php echo $username; ?>"></p>
                <p><input class="w3-input w3-padding-16 w3-border" type="password" placeholder="Password" required name="password"></p>
                <p><input class="w3-input w3-padding-16 w3-border" type="password" placeholder="Confirm Password" required name="confirm_password"></p>
                <?php
                if(!empty($error)){
                    echo '<span class="w3-tag w3-red w3-text-white">' . $error . '</span>';
                } else
                    echo '<span class="w3-tag w3-green w3-text-white">' . $created_user . '</span>';
                ?>
                <p><button class="w3-button w3-black" type="submit" value="Submit">CREATE USER</button></p>
            </form>
        </div>
    </div>
</div>
</body>
</html>