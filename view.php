<?php

require_once "database_connect.php";
if (isset($_GET['file_id'])) {
    $id = $_GET['file_id'];

    // fetch file to download from database
    $sql = "SELECT * FROM Report WHERE ReportID='$id'";
    $result = mysqli_query($link, $sql);

    $file = mysqli_fetch_assoc($result);
    $filepath = 'upload/'.$file['File'];

    header("location: http://localhost:63342/REA/".$filepath);

    // Now update downloads count
    $check = date('Y-m-d H:i:s');
    $updateQuery = "UPDATE Report SET checked='$check' WHERE ReportID='$id'";
    mysqli_query($link, $updateQuery);
    exit;


}
?>