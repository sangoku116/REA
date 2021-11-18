<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
   header("location: Admin_login.php");
    exit;
}

include_once "database_connect.php";

$sql = 'SELECT * FROM Reports';
$result = mysqli_query($link,$sql);

// selects all file info from db and sets them to array called $files
$files = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Test</title>
    <link rel="stylesheet" href="style1.css">
</head>
<body>
<h1 class="center-title">Hello, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to our site.</h1>
<p>
    <a href="logout.php" class="button">Sign Out of Your Account</a>
</p>
<table>
    <tr>
        <th>ID</th>
        <th>Filename</th>
        <th>Event Date</th>
        <th>Report Description</th>
        <th>Action</th>
    </tr>
    <tbody>
    <?php foreach ($files as $file): ?>
        <tr>
            <td><?php echo $file['ReportID']; ?></td>
            <td><?php echo $file['Report_title']; ?></td>
            <td><?php echo $file['Submission_Date']; ?></td>
            <td><?php echo $file['Report_Description']; ?></td>
            <td><a href="upload/<?php echo $file['File']?>">View</a></td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>
</body>
</html>

