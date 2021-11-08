<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: Admin_login.php");
    exit;
}

require_once "database_connect.php";

$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    //validatepassword
    if(empty(trim($_POST["new_password"]))){
        $new_password_err = "Please enter a new Password";
    } elseif(strlen(trim($_POST["new_password"])) < 3) {
        $new_password_err = "Password must have at least 8 characters";
    } else {
        $new_password = trim($_POST["new_password"]);
    }

    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm the password.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }


    //Check input error for database
    if(empty($new_password_err) && empty($confirm_password_err)) {

        echo '<script>alert("Password changed successfully! The page will be redirected back to Administator Login.")</script>';

        // sql statement
        $sql = 'UPDATE Admin SET Password = ? WHERE UserId = ?';

        if($result = mysqli_prepare($link, $sql)) {
            //Bind variables;
            mysqli_stmt_bind_param($result, "si", $param_password, $param_id);

            // define variables for binding
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            $param_id = $_SESSION["id"];

            //Execute statement
            if (mysqli_stmt_execute($result)) {
                $created_date = date('Y-m-d H:i:s');
                $sqltime = "UPDATE Admin SET LoginTime = '$created_date' WHERE UserID = '$param_id'";
                $changetime = mysqli_query($link, $sqltime);
                //Password has updated, and can redirect back to login
                session_destroy();
                // start a session for creating password reset alert box part 1
                session_start();
                $_SESSION['confirmMsg'] = "Password changed successfully! The page will be redirected back to Administrator Login.";
                header("Location: Admin_login.php");
                exit();
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            //Close statement
            mysqli_stmt_close($result);
        }
    }
    //Close link
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 360px; padding: 20px; }
        .error {color: #FF0000;}
    </style>
</head>
<body>
<div class="wrapper">
    <h2> Password Reset</h2>
    <p>Change your Temporary Password.</p>
    <!--<h1 class="my-5">Hi, <b><?php echo $_SESSION["id"]; ?></b>. Welcome to our site.</h1>--!>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
            <label>New Password</label>
            <input type="password" name="new_password" class="form-control <?php echo (!empty($new_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $new_password; ?>">
            <span class="invalid-feedback"><?php echo $new_password_err; ?></span>
        </div>
        <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
            <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
        </div>
        <p><span class="error">WARNING: Remember your password. <b>You will NOT be able to retrieve it after this step!</b></span></p>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Submit">
        </div>
    </form>
</div>
</body>
</html>