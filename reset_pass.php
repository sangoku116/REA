<?php

///    THIS SESSION NOW CAN RESET WITH THE INSERT USER PAGE
/// Changes: Instead of updating with param_id, it uses the param_users.

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

    //validate password
    if(empty(trim($_POST["new_password"]))){
        $new_password_err = "Please enter a new Password";
    } elseif(strlen(trim($_POST["new_password"])) < 8) {
        $new_password_err = "Password must have at least 8 characters.";
        $error = $new_password_err;
    }elseif(!preg_match("/\d/", trim($_POST["new_password"]))){
        $new_password_err = "Password should at least have 1 digit.";
        $error = $new_password_err;
    }elseif(!preg_match("/[A-Z]/", trim($_POST["new_password"]))){
        $new_password_err = "Password should at least have one capital letter.";
        $error = $new_password_err;
    }elseif(!preg_match("/[a-z]/", trim($_POST["new_password"]))){
        $new_password_err = "Password should at least have one lowercase letter.";
        $error = $new_password_err;
    }elseif(!preg_match("/\W/", trim($_POST["new_password"]))){
        $new_password_err = "Password should at least have one special character such as !@#$%^&.";
        $error = $new_password_err;
    }else{
        $new_password = trim($_POST["new_password"]);
    }

    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm the password.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
            $error = $confirm_password_err;
        }
    }


    //Check input error for database
    if(empty($new_password_err) && empty($confirm_password_err)) {

        // echo '<script>alert("Password changed successfully! The page will be redirected back to Administator Login.")</script>';

        // sql statement
        $sql = 'UPDATE Admins SET Password = ? WHERE Username = ?';

        if($result = mysqli_prepare($link, $sql)) {
            //Bind variables;
            mysqli_stmt_bind_param($result, "ss", $param_password, $param_user);

            // define variables for binding
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            $param_user = $_SESSION["username"];

            //Execute statement
            if (mysqli_stmt_execute($result)) {
                $created_date = date('Y-m-d H:i:s');
                $sqltime = "UPDATE Admins SET LoginTime = '$created_date' WHERE Username = '$param_user'";
                $changetime = mysqli_query($link, $sqltime);

                //Password has updated, and can redirect back to login
                session_destroy();

                // start a session for creating password reset alert box part 1 - continues on with admin login page
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
    <title>Create a New User</title>
    <link rel="stylesheet" href="w3css.css">
</head>
<style>
    body, html {
        height: 100%;
        font-family: "Inconsolata", sans-serif;
        background-color: #6c88a0;
    }
</style>
<body>
<div class="form-all">

    <!-- Links (sit on top) -->
    <div class="w3-top">
        <div class="w3-row w3-padding w3-black">
            <div class="w3-col s3">
                <a href="Form.php" class="w3-button w3-block w3-black">FORM</a>
            </div>
            <div class="w3-col s3">
                <a href="Admin_login.php" class="w3-button w3-block w3-black">ADMIN</a>
            </div>
        </div>
    </div>

    <div class="w3-container" id="where" style="padding-bottom:32px;">
        <div class="w3-content" style="max-width:700px">
            <br><br><br><br><br><br>
            <h5 class="w3-center w3-padding-48"><span class="w3-tag w3-wide">RESET YOUR PASSWORD</span></h5>
            <p style="color:#ffffff"><span class="w3-tag w3-red">WARNING:</span><b>Remember your password. You will NOT be able to retrieve it after this step!</b></p>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <!--<form action="/action_page.php" target="_blank"> -->
                <p><input class="w3-input w3-padding-16 w3-border" type="password" placeholder="Password" required name="new_password"></p>
                <p><input class="w3-input w3-padding-16 w3-border" type="password" placeholder="Confirm Password" required name="confirm_password"></p>
                <?php
                if(!empty($error)){
                    echo '<span class="w3-tag w3-red w3-text-white">' . $error . '</span>';
                }
                ?>
                <p><button class="w3-button w3-black" type="submit" value="Submit">RESET PASSWORD</button></p>
            </form>
        </div>
    </div>
</div>
</body>
</html>