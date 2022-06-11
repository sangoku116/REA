
<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: Admin_login.php");
    exit;
}

include_once "database_connect.php";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> -->
    <link rel="stylesheet" href="w3css.css">
    <style>
        body{font: 14px sans-serif; text-align: center;
            background: linear-gradient(#6c88a0, #145288) no-repeat fixed;
            background-size: cover;
        }
    </style>
</head>
<body>
<div class="w3-top">
    <div class="w3-row w3-padding w3-black">
        <div class="w3-col w3-left w3-hide-small s1">
            <span class="w3-button-welcome w3-bar-item w3-black"><b><?php echo htmlspecialchars($_SESSION["username"]); ?></b></span>
        </div>
        <div class="w3-col w3-right w3-hide-small s1">
            <a href="logout.php" class="w3-button w3-bar-item w3-black">SIGN OUT</a>
        </div>
        <?php
        $check = $_SESSION["username"];

        if($check === "user1"){
            echo '<div class="w3-col w3-right w3-hide-small s1">';
            echo '<a href="deluser1.php" class="w3-button w3-bar-item w3-black">DELETE USER</a>';
            echo '</div>';
            echo '<div class="w3-col w3-right w3-hide-small s1">';
            echo '<a href="adduser.php" class="w3-button w3-bar-item w3-black">ADD USER</a>';
            echo '</div>';
        }
        ?>
    </div>
</div>
<br><br><br>
<h3 class="w3-center w3-padding-48"><span class="w3-tag w3-wide">REPORTS</span></h3>
<?php
include_once 'database_connect.php';
$display = "SELECT * FROM Report ORDER BY ReportID DESC";
$result = mysqli_query($link, $display);
echo "<table>";
echo "<tr>";
echo "<th>Report ID</th>";
echo "<th>Report Title</th>";
echo "<th>Submission Date</th>";
echo "<th>Report Description</th>";
echo "<th>Action</th>";
echo "<th>Checked On</th>";
echo "</tr>";

while ($row = mysqli_fetch_array($result)){
    if(empty($row['File'])){
        echo "<tr>";
        echo "<td>".$row["ReportID"]."</td>";
        echo "<td>".$row["Report_title"]."</td>";
        echo "<td>".$row["Submission_Date"]."</td>";
        echo "<td>".$row["Report_Description"]."</td>";
        echo "<td> No file added</td>";
        echo "<td> N/A</td>";
        echo "</tr>";
    } else{
        echo "<tr>";
        echo "<td>".$row["ReportID"]."</td>";
        echo "<td>".$row["Report_title"]."</td>";
        echo "<td>".$row["Submission_Date"]."</td>";
        echo "<td>".$row["Report_Description"]."</td>"; ?>
        <td><a href="view.php?file_id=<?php echo $row['ReportID'] ?>">View</a></td>
        <?php
        echo "<td>".$row["Checked"]."</td>"; ?>
        <?php
        echo "</tr>";
    }
}
echo "</table>";
?>
</body>
</html>