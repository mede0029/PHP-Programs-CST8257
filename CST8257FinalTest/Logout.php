<?php 
    session_start();
    include 'ProjectCommon/PageHeader.php';
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        session_destroy();
        header('Location: Login.php');
        exit;        
        ?>
<?php 
    include 'ProjectCommon/PageFooter.php';
?>