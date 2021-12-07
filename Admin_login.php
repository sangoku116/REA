<?php
// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: welcome.php");
    exit;
}

// Check for reset password instance part 2 - works with reset page
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
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    $sql = "SELECT UserID, Username, Password, LoginTime FROM Admins WHERE Username = ?";

    if($result = mysqli_prepare($link, $sql)){
        mysqli_stmt_bind_param($result, "s", $admin_user);
        $admin_user = $username;

        if(mysqli_stmt_execute($result)){
            mysqli_stmt_store_result($result);

            if(mysqli_stmt_num_rows($result) == 1){
                mysqli_stmt_bind_result($result, $id, $username, $hashed_pass, $time);
                if(mysqli_stmt_fetch($result)){
                    //if($passowrd === $hashed_pass){
                    if(password_verify($password, $hashed_pass)){
                        if(is_null($time)){
                            session_start();
                            $_SESSION["loggedin"] = TRUE;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            header("Location: reset_pass.php");
                        }else{ // possible if/else statement for the User role admin?
                            session_start();
                            $_SESSION["loggedin"] = TRUE;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            header("Location: welcome.php");
                        } // here will be for any other user.
                    }else{
                        $login_err = "Bad Username or Password.";
                    }
                }
            } else{
                $login_err = "Bad Username or Password.";
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }

    }
    mysqli_stmt_close($result);
}
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
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

    <?php
    if(!empty($login_err)){
        echo '<span class="loginError">' . $login_err . '</span>';
    }
    ?>

    <div class="w3-container" id="where" style="padding-bottom:32px;">
        <div class="w3-content" style="max-width:700px">
            <br><br><br><br><br><br>
            <h5 class="w3-center w3-padding-48"><span class="w3-tag w3-wide">ADMINISTRATOR LOGIN</span></h5>
            <p><span style="color: white">Please enter your credentials to login. </span></p>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <!--<form action="/action_page.php" target="_blank"> -->
                <p><input class="w3-input w3-padding-16 w3-border" type="text" placeholder="Username" required name="username" value="<?php echo $username; ?>"></p>
                <p><input class="w3-input w3-padding-16 w3-border" type="password" placeholder="Password" required name="password"></p>
                <?php
                if(!empty($login_err)){
                    echo '<span class="w3-tag w3-red w3-text-white">' . $login_err . '</span>';
                }
                ?>
                <p><button class="w3-button w3-black" type="submit" value="Submit">LOGIN</button></p>
            </form>
        </div>
    </div>
</div>
</body>
</html>

