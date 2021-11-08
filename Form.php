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
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    Title: <input type="text" name="title" value="<?php echo $title;?>">
    <span class="error">* <?php echo $titleErr;?></span>
    <br><br>
    Event Date: <input type="date" name="date" value="<?php echo $date=$_POST['date'];?>">
    <span class="error">* <?php echo $dateErr;?></span>
    <br><br>
    Comment: <textarea name="comment" rows="5" cols="40"><?php echo $comment;?></textarea>
    <span class="error">* <?php echo $commentErr;?></span>
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
    $_POST=array();
    //$sql = "INSERT INTO Reports (ReportID, Report_title, Submission_Date, Report_Description) VALUES (?, ?, ?, ?)";
    $sql = "INSERT INTO Reports (Report_title, Submission_Date, Report_Description) VALUES (?, ?, ?)";

    if($result = mysqli_prepare($link, $sql)){
        //Bind param to sql statement
         //mysqli_stmt_bind_param($result, "isss", $t,$ReportTitle, $ReportDate, $ReportComment);
        mysqli_stmt_bind_param($result,"sss", $ReportTitle, $ReportDate, $ReportComment);
        //$t = time();
        $ReportTitle = $title;
        $ReportDate = $date;
        $ReportComment = $comment;

        if(mysqli_stmt_execute($result)){
            // when values are entered, a report ID will be generated into the database
            $t = time();
            $sqlID = "UPDATE Reports SET ReportID = '$t' WHERE Report_title = '$title' AND Submission_Date = '$date' AND Report_Description = '$comment'";
            $ReportID = mysqli_query($link, $sqlID);
            echo '<script>alert("Submission Success! Your ReportID is '.$t.'")</script>';
            ///f_alert('Submission Success!');
        }
        mysqli_stmt_close($result);

    }
}
mysqli_close($link);

// -------------- ANOTHER ELSE STATEMENT FOR WHEN CONNECTION CANNOT BE ESTABLISHED -------------------------
//else {
//    f_alert('Continue');

//}
?>

</body>
</html>


