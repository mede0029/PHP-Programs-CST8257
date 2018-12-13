<?php
    include 'ProjectCommon/Class_Picture.php';
    session_start();    
    include 'ProjectCommon/Functions.php';
    include 'ProjectCommon/Header.php';
?>

<br><h2>&nbsp Welcome to Algonquin Album Management Website </h2><br>
<p>&nbsp &nbsp You can upload your pictures <a href="UploadPicture.php">here</a>.</p>
<p>&nbsp &nbsp You can view your pictures <a href="MyPictures.php">here</a>.<p>

<?php           
   include 'ProjectCommon/Footer.php';
?>