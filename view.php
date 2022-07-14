<?php

require_once "database_connect.php";


if (isset($_GET['file_id'])) {
    $id = $_GET['file_id'];

    $dwnld = "Downloading...";
    $updateQuery = "UPDATE Report SET checked='$dwnld' WHERE ReportID='$id'";
    mysqli_query($link, $updateQuery);

    // fetch file to download from database
    $sql = "SELECT * FROM Report WHERE ReportID='$id'";
    $result = mysqli_query($link, $sql);

    $file = mysqli_fetch_assoc($result);
    $filepath = 'upload/'.$file['File'];

    //set download rate limit (63 kb/s)
    $download_rate = 63;

    if(file_exists($filepath)){
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($filepath));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length:' . filesize('upload/' . $file['File']));
        ob_clean();
        flush();

        $filedownload = fopen($filepath, "r");
        while(!feof($filedownload)){
            //send the current file part to the browser
            print fread($filedownload, round($download_rate * 1024));
            //flush the content to the browser
            flush();
            //sleep for one second
            sleep(1);
        }
    }
    fclose($filedownload);

    // Now update downloads count
    $check = date('Y-m-d H:i:s');
    $updateQuery = "UPDATE Report SET checked='$check' WHERE ReportID='$id'";
    mysqli_query($link, $updateQuery);
    exit;


}
?>