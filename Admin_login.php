<?php
// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: welcome.php");
    exit;
}

// Check for reset password instance part 2
if(isset($_SESSION['confirmMsg'])){
    echo "<script>alert('". $_SESSION['confirmMsg'] . "') </script>";
}

// Include config file
//require_once "database_connect.php";
include "database_connect.php";

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }

    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        //Prepare sql statement
        $sql = "SELECT UserID, Username, Password, LoginTime FROM Admin WHERE Username = ?";

        if($result = mysqli_prepare($link, $sql)){
            //Bind parameters to sql statement
            mysqli_stmt_bind_param($result, "s", $stmt_username);
            $stmt_username = $username;

            //execute the prepared statement
            if(mysqli_stmt_execute($result)){
                //store result of sql
                mysqli_stmt_store_result($result);

                //If username exists, then check password
                if(mysqli_stmt_num_rows($result) == 1){
                    // take id, user, password results and bind them into variables
                    mysqli_stmt_bind_result($result, $id, $username, $hashed_pass, $time);
                    if(mysqli_stmt_fetch($result)) {
                        //if($password === $hashed_pass) {
                        if(password_verify($password, $hashed_pass)){
                            if(is_null($time)){
                                session_start();
                                $_SESSION["loggedin"] = true;
                                $_SESSION['username'] = $username;
                                $_SESSION['id'] = $id;
                                header("Location: reset_pass.php");
                            } else{
                                session_start();
                                //Store cookie session
                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = $id;
                                $_SESSION["username"] = $username;

                                //Direct them into Welcome page
                                header("Location: welcome.php");
                            }
                        } else{
                            //Incorrect credentials
                            $login_err = "Bad Username or Password";
                        }
                    }
                } else{
                    //Username or Password does not exist
                    $login_err = "Bad username or password";
                }

            } else {
                echo 'Oops! Something went wrong. Please try again later.';
            }
            //Close statement
            mysqli_stmt_close($result);
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
    <title>Login</title>
    <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> -->
    <link rel="stylesheet" href="style1.css">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 360px; padding: 20px; }
    </style>
</head>
<body>
<div class="form-all">
    <h2><span class="center-title">Administrator Login</span></h2>

    <?php
    if(!empty($login_err)){
        echo '<span class="loginError">' . $login_err . '</span>';
    }
    ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
            <label>Username:</label>
            <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
            <span class="error"><?php echo $username_err; ?></span>
            <br><br>
        </div>
        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
            <span class="error"><?php echo $password_err; ?></span>
        </div>
        <div class="form-group">
            <br><br>
            <input type="submit" class="btn btn-primary" value="Login">

        </div>
    </form>
    <a href="Form.php" class="button" role="button">Back to Forms</a>
</div>
</body>
</html>
