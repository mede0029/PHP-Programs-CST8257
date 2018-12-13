<?php 
    include 'ProjectCommon/Class_Lib.php';
    session_start();
    include 'ProjectCommon/Footer.php';
    include 'ProjectCommon/Header.php';
    include 'ProjectCommon/Functions.php';
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        session_destroy();
        header('Location: Index.php');
        exit;        
        ?>
    </body>
</html>
