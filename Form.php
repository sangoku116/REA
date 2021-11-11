<!DOCTYPE HTML>
<html>
<head>
    <style>
        .error {color: #FF0000;}
    </style>
</head>
<body>

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

<h2>Anonymous Reporting Form</h2>
<p><span class="error">* required field</span></p>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
    Title: <input type="text" name="title" value="<?php echo $title;?>">
    <span class="error">* <?php echo $titleErr;?></span>
    <br><br>
    Event Date: <input type="date" name="date" value="<?php echo $date=$_POST['date'];?>">
    <span class="error">* <?php echo $dateErr;?></span>
    <br><br>
    Comment: <textarea name="comment" rows="5" cols="40"><?php echo $comment;?></textarea>
    <span class="error">* <?php echo $commentErr;?></span>
    <br><br>
    <label for="file_name"> Files:</label>
    <input type="file" name="anyfile" id="anyfile">
    <br><br>
    <input type="submit" name="submit" value="Submit">
    <a href="Admin_login.php" class="btn btn-info" role="button">Admin mode</a>
</form>
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
                    $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png", "heif" => "image/heif");
                    $filename = $_FILES["anyfile"]["name"];
                    $filetype = $_FILES["anyfile"]["type"];
                    $filesize = $_FILES["anyfile"]["size"];

                    //Validate file extension
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                    if(!array_key_exists($ext, $allowed)) die("Error: Please select a valid file format.");

                    //Validate File size - Max 10MB
                    $maxsize = 10*1024*1024;
                    if($filesize > $maxsize) die("Error: File size is larger than 10MB");

                    //Validate File type
                    if(in_array($filetype, $allowed)){

                        //Folder where file will be stored
                        $upload_folder = "upload/";

                        //Check whether file exists before uploading it
                        if(move_uploaded_file($_FILES["anyfile"]["tmp_name"], $upload_folder.$filename)){
                            $stmt = "UPDATE Reports SET File = '$filename' WHERE ReportID = '$t'";
                            mysqli_query($link, $stmt);
                            echo "File uploaded!";
                        } else{
                            echo "File not uploaded.";
                        }
                    } else{
                        echo "Error: There was a problem uploading your file";
                    }
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


