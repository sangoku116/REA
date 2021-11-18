<!DOCTYPE HTML>
<html>
<head>
    <link rel="stylesheet" href="style1.css">
</head>
<body>
<span class="welcome-text">
<h1 style="color: white">Welcome to Tampa Nama.</h1>
<h4 style="color: white"> Anonymous Reporting</h4>
<p style="color: white">some text here </p>
</span>



<?php

// define variables and set to empty values
$titleErr = $commentErr = $dateErr = "";
$title = $comment = $date = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["title"])) {
        $titleErr = "Title is required";
    } else {
        $title = test_input($_POST["title"]);
    }

// should we leave comment to be optional
    if (empty($_POST["comment"])) {
        $commentErr = "Please write a few words of description";
        //$comment = null;
    } else {
        $comment = test_input($_POST["comment"]);
    }

    if (empty($_POST["date"])) {
        $dateErr = "Date is required";
    }

    if($_FILES["anyfile"]["error"] != 0){
        $fileErr =  "No file uploaded";
    }

}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>
<div class="all"> <!-- this div is not used lol -->
<h2><span class="center-title">Anonymous Reporting Form</span></h2>
<p><span class="error">* required field</span></p>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data" class="form-submit">
    <p>Title: <input type="text" name="title" value="<?php echo $title;?>"></p>
    <span class="error">* <?php echo $titleErr;?></span>
    <br><br>
    <p>Event Date: <input type="date" name="date" value="<?php echo $date=$_POST['date'];?>"></p>
    <span class="error">* <?php echo $dateErr;?></span>
    <br><br>
    <p>Comment:</p><textarea name="comment" rows="5" cols="40"><?php echo $comment;?></textarea>
    <span class="error">* <?php echo $commentErr;?></span>
    <br><br>
    <label for="file_name"> Files:</label>
    <input type="file" name="anyfile" id="anyfile">
    <br><br>
    Note: Only .zip, .pdf, .docx, .doc, .jpg, .png, .txt file formats are accepted.
    <br><br>
    <input type="submit" name="submit" value="Submit">
</form>
    <a href="Admin_login.php" class="button" role="button">Admin mode</a>
</div>
<?php

include "database_connect.php";

function f_alert($message) {
    echo "<script>alert('$message');</script>";
}

if (!empty($titleErr) OR (!empty($dateErr)) OR (!empty($commentErr))){
    f_alert('The submission was not successful. Please check for errors and try again.');
} elseif (!empty($title) OR (!empty($comment)) OR (!empty($date))) {
    // $_POST=array();
    //$sql = "INSERT INTO Reports (ReportID, Report_title, Submission_Date, Report_Description) VALUES (?, ?, ?, ?)";
    $sql = "INSERT INTO Reports (Report_title, Submission_Date, Report_Description) VALUES (?, ?, ?)";

    if($result = mysqli_prepare($link, $sql)){
        //Bind param to sql statement
        //mysqli_stmt_bind_param($result, "isss", $t,$ReportTitle, $ReportDate, $ReportComment);
        mysqli_stmt_bind_param($result,"sss", $ReportTitle, $ReportDate, $ReportComment);
        $ReportTitle = $title;
        $ReportDate = $date;
        $ReportComment = $comment;

        if(mysqli_stmt_execute($result)){
            // when values are entered, a report ID will be generated into the database
            $t = time();
            $sqlID = "UPDATE Reports SET ReportID = '$t' WHERE Report_title = '$title' AND Submission_Date = '$date' AND Report_Description = '$comment'";
            $ReportID = mysqli_query($link, $sqlID);

            // if there is a file submitted with the Rest of the reports
            if($_FILES["anyfile"]["error"] == 0){

                //Check if file was uploaded with no error
                if(isset($_FILES["anyfile"]) && $_FILES["anyfile"]["error"] == 0){
                    //name of the uploaded file
                    // $allowed = array("jpg", "jpeg", "gif", "png", "heif", "doc", "docx", "zip", "txt", "pdf");
                    $filename = $_FILES["anyfile"]["name"]; //name of the uploaded file
                    $filesize = $_FILES["anyfile"]["size"]; //size of the uploaded file
                    $fileplace = $_FILES["anyfile"]["tmp_name"]; //physical file on a temp uploads directory on server

                    $maxsize = 10*1024*1024; // set max size of 10MB

                    $upload_folder = 'upload/'.$filename;

                    //Validate file extension
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                    if(!in_array($ext, ['zip','pdf','docx','doc','jpg','png','txt'])){
                        echo "Error: Please select a valid file format.";
                    } elseif ($filesize > $maxsize){
                        echo "Error: File is larger than 10MB";
                    } else {
                        //move the uploaded tmp file into destination
                        if(move_uploaded_file($fileplace, $upload_folder)){
                            $stmt = "UPDATE Reports SET File = '$filename' WHERE ReportID = '$t'";
                            if(mysqli_query($link, $stmt)){
                                echo "File Uploaded!";
                            } else {
                                echo "File failed to upload";
                            }
                        }
                    }

                } else {
                    echo "File upload encountered an error. The file may be missing, or corrupted.";
                }
            }
            echo '<script>alert("Submission Success! Your ReportID is '.$t.'")</script>';
            ///f_alert('Submission Success!');
        }
        mysqli_stmt_close($result);

    }
}
mysqli_close($link);
?>
</body>
</html>
