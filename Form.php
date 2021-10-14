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
</form>

<?php

if ($title == null) {
    exit;
} elseif ($date == null){
    exit;
} elseif ($comment == null){
    exit;
} else {
    echo "<h2>Your Input:</h2>";
    echo $title;
    echo "<br>";
    echo $date;
    echo "<br>";
    echo $comment;
    echo "<br>";
}
?>

</body>
</html>


