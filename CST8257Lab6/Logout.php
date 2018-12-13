<?php 
    include 'Lab5Common/Class_Lib.php';
    session_start();
    include 'Lab5Common/Footer.php';
    include 'Lab5Common/Header.php';
    include 'Lab5Common/Functions.php';
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
