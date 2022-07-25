<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: Admin_login.php");
    exit;
} elseif ($_SESSION["username"] !== 'user1'){ // if the signed in user is not the User Admin
    header("location: Admin_login.php");
    exit;
}

require_once "database_connect.php";
?>

<!DOCTYPE HTML>
<html>
<head>
    <title>Del records</title>
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
            <a href="adduser.php" class="w3-button w3-bar-item w3-black">ADD USER</a>
        </div>
    </div>
</div>


<div class="w3-centered">
    <br><br>
    <br><br>
    <h5 class="w3-center w3-padding-48"><span class="w3-tag w3-wide">DELETE EXISTING USERS</span></h5>
    <?php
    include_once 'database_connect.php';
    $show = "SELECT * from Admins";
    $result = mysqli_query($link, $show);
    echo "<table>";
    echo "<tr>";
    echo "<th>UserID</th>";
    echo "<th>Username</th>";
    echo "</tr>";

    while($row = mysqli_fetch_array($result)){
        echo "<tr>";
        echo "<td>".$row["UserID"]."</td>";
        echo "<td>".$row["Username"]."</td>";
        echo "</tr>";
    }
    echo "</table>";
    ?>

    <?php
    echo '<span class="w3-tag w3-green w3-text-white">' . $message . '</span>';
    ?>
    <form method="post" style="text-align: center;" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <br><br><br>
        <label> <span style="color: white">Username:</span></label>
        <input name="username" type="text" id="username">
        <input name="delete" type="submit" id="delete" value="Delete">
    </form>
</div>
</body>
</html>

<?php
require_once "database_connect.php";


if(isset($_POST["delete"])){
    $username = $_POST["username"];
    // insert rest of code here

    $sql = "DELETE FROM Admins WHERE Username = '$username'";
    $stmt = mysqli_query($link, $sql);


    if(mysqli_affected_rows($link) > 0){
        $url1 = $_SERVER["REQUEST_URI"];
        header("Refresh: 3; URL=$url1");
        $message = "User deleted - Refreshing page";
        echo '<div style="text-align: center;">';
        echo '<br>';
        echo '<span class="w3-tag w3-green w3-text-white">' . $message . '</span>';
        echo '</div>';
    }elseif (mysqli_affected_rows($link) == 0){
        $message =  "That user does not exist.";
        echo '<div style="text-align: center;">';
        echo '<br>';
        echo '<span class="w3-tag w3-red w3-text-white">' . $message . '</span>';
        echo '</div>';
    }

    mysqli_close($link);

    // end rest of code
}
?>
