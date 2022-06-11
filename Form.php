

<?php
include "database_connect.php";

// define variables and set to empty values
$title = $date = $comment = "";
// $submit_err = $submit_conf = "";

// When the report is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $title = $_POST["title"];
    $comment = $_POST["comment"];
    $date = $_POST["date"];

    $sql = "INSERT INTO Report (ReportID, Report_title, Submission_Date, Report_Description) VALUES (?, ?, ?, ?)";

    //Prepares statement $sql to be executed.
    if($result = mysqli_prepare($link, $sql)){
        mysqli_stmt_bind_param($result, "isss", $ReportId,$ReportTitle, $ReportDate, $ReportComment);
        //Bind the parameters for execution
        $ReportId = time();
        $ReportTitle = $title;
        $ReportDate = $date;
        $ReportComment = $comment;

        // if there is an attachment with the sent report
        if(!empty($_FILES["attachment"]["name"])){
            //Check for errors with the report upload
            if(isset($_FILES["attachment"]) && $_FILES["attachment"]["error"] == 0){
                $filename = $_FILES["attachment"]["name"];
                $filesize = $_FILES["attachment"]["size"];
                $fileplace = $_FILES["attachment"]["tmp_name"];
                $maxsize = 1024*1024; // max size of file is 1MB.
                //$upload_folder = 'upload/'.$filename; // upload folder
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                //list of allowed filetypes
                $allowed = array("jpg", "jpeg", "gif", "png", "doc", "docx", "zip", "txt", "pdf");

                if(in_array($ext, $allowed) === FALSE){
                    $submit_err = "Error: Please select a valid file format.";
                }
                if($filesize > $maxsize){
                    $submit_err = "Error: File cannot be larger than 1MB.";
                }

                $newFileName = md5(time().$filename).'.'.$ext;
                $upload_folder = 'upload/'.$newFileName; // upload folder

                if(empty($submit_err)){
                    mysqli_stmt_execute($result);
                    move_uploaded_file($fileplace, $upload_folder);

                    chmod($upload_folder, 0755); // change permission of the file uploaded to not be executable
                    $stmt = "UPDATE Report SET File = '$newFileName' WHERE ReportID = '$ReportId'";
                    mysqli_query($link, $stmt);
                    echo '<script>alert("Submission Success! Your ReportID is '.$ReportId.'")</script>';
                }
            } else {
                $submit_err = "File upload encountered an error. The file may be missing, or corrupted.";
            }
        } elseif(empty($_FILES["attachment"]["name"])){
            mysqli_stmt_execute($result);
            echo '<script>alert("Submission Success! Your ReportID is '.$ReportId.'")</script>';
        }
        mysqli_stmt_close($result);
    }
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html>
<title>Anonymous Reporting Website</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="w3css.css">
<style>
    body, html {
        height: 100%;
        font-family: "Inconsolata", sans-serif;
    }

    .bgimg {
        background-position: center;
        background-size: cover;
        background-image: url("adjust.jpg");
        min-height: 75%;
    }

    .menu {
        display: none;
    }
</style>
<body>

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

<!-- Header with image -->
<header class="bgimg w3-display-container w3-grayscale-min" id="home">
    <div class="w3-display-bottomleft w3-center w3-padding-large w3-hide-small">
        <span class="w3-tag">REA Group 12</span>
    </div>
    <div class="w3-display-middle w3-center">
        <span class="w3-text-white" style="font-size:90px">Tanpa<br>Nama</span>
    </div>
    <div class="w3-display-bottomright w3-center w3-padding-large">
        <span class="w3-text-white">Last Updated on Dec 2021</span>
    </div>
</header>

<!-- Add a background color and large text to the whole page -->
<div class="w3-blue w3-grayscale w3-large">

    <!-- About Container -->
    <div class="w3-container" id="about">
        <div class="w3-content" style="max-width:700px">
            <h5 class="w3-center w3-padding-64"><span class="w3-tag w3-wide"><a href="#where">Fill out a Form</a></span></h5>
            <h5 class="w3-center w3-padding-64"><span class="w3-tag w3-wide">ABOUT TANPA NAMA</span></h5>
            <p>Tanpa Nama is a service founded by students who believe in creating a space for where injustices can be reported. By allowing submissions without the need of sharing personal information about the sender, reports can be sent by users without having to fear negative repercussions. </p>
            <!-- <p>In addition to our full espresso and brew bar menu, we serve fresh made-to-order breakfast and lunch sandwiches, as well as a selection of sides and salads and other good stuff.</p> -->
            <img src="2.gif" style="width:100%;max-width:1000px" class="w3-margin-top">
        </div>
    </div>


    <!-- Contact/Area Container -->
    <div class="w3-container" id="where" style="padding-bottom:32px;">
        <div class="w3-content" style="max-width:700px">
            <h5 class="w3-center w3-padding-48"><span class="w3-tag w3-wide">ANONYMOUS REPORTING FORM</span></h5>
            <p><span class="w3-tag">FYI!</span> Please be direct but informative when filling in the Title, Event Date, and Description fields. The administrators of this service will use the information you send for their investigation, and concise information is appreciated. The submission form will take an optional one attachment file if the file can be used to supplement the report.</p>
            <p>When the report is successfully submitted, a confirmation ID will be given. Please take note of this number as it will be used to keep track of the progress on your reports. </p>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data" class="form-submit">
                <!--<form action="/action_page.php" target="_blank"> -->
                <p><input class="w3-input w3-padding-16 w3-border" type="text" placeholder="Report Title" required name="title"></p>
                <p><input class="w3-input w3-padding-16 w3-border" type="date" placeholder="Date of Event" required name="date"></p>
                <p><textarea name="comment" rows="5" cols="40" maxlength="1000" class="w3-input w3-padding-16 w3-border" placeholder="Description of the Event - Character limit of 1000" required name="Comment"></textarea></p>
                <label for="file_name"> Files:</label>
                <input type="file" name="attachment" id="attachment">
                <br><br>
                <span class="w3-tag">Note:</span> Only .zip, .pdf, .docx, .doc, .jpg, .png, .txt file formats are accepted.
                <br><br>
                <?php
                if(!empty($submit_err)){
                    echo '<span class="w3-tag w3-red w3-text-white">' . $submit_err . '</span>';
                }
                ?>
                <p><button class="w3-button w3-black" type="submit" value="Submit">SUBMIT REPORT</button></p>
            </form>
        </div>
    </div>

    <!-- End page content -->
</div>

<!-- Footer
<footer class="w3-center w3-light-grey w3-padding-48 w3-large">
    <p>Powered by <a href="https://www.w3schools.com/w3css/default.asp" title="W3.CSS" target="_blank" class="w3-hover-text-green">w3.css</a></p>
</footer>
-->

<script>
    // Tabbed Menu
    function openMenu(evt, menuName) {
        var i, x, tablinks;
        x = document.getElementsByClassName("menu");
        for (i = 0; i < x.length; i++) {
            x[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablink");
        for (i = 0; i < x.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" w3-dark-grey", "");
        }
        document.getElementById(menuName).style.display = "block";
        evt.currentTarget.firstElementChild.className += " w3-dark-grey";
    }
    document.getElementById("myLink").click();
</script>
</body>
</html>


