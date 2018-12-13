<!DOCTYPE html>

<?php
session_start(); 
$name = $_SESSION['name'];
$email = $_SESSION['email'];
$phoneNumber = $_SESSION['phoneNumber'];
$preferredContact = $_SESSION['preferredContact'];
$contact = $_SESSION['contact'];
$counter = $_SESSION['counter'];

?>
<html>
    <head>
        <?php include 'Lab4Common/Header.php'; ?>
        <link rel="stylesheet" type="text/css" href="Site.css">
    </head>
    <body>
        <?php
        if ( $_SESSION['name'] != "" || $_SESSION['postalCode'] != "" || $_SESSION['phoneNumber'] != "" || $_SESSION['email'] != "") 
        {
            echo "<h1><br>Thank you, ".$name.", for using our deposit calculation tool!</h1><Br>"; 
            if ($preferredContact == "phone")
            {
                if ($counter == 1){
                    echo "<br><p>Our customer service department will call you tomorrow ".$contact[0]. " , at ".$phoneNumber." </p>";
                }
                if ($counter == 2){
                    echo "<br><p>Our customer service department will call you tomorrow ".$contact[0]. " or " .$contact[1]. ", at ".$phoneNumber." </p>";
                }
                if ($counter == 3)
                {
                    echo "<br><p>Our customer service department will call you tomorrow ".$contact[0]. ", " .$contact[1]. " or " .$contact[2]. " at ".$phoneNumber." </p>";
                }
            }
            if ($preferredContact == "email")
            {
            echo "<p><br>An email about the details of your GIC has been sent to " .$email. ".<p>";
            }
            
        }   
        else 
        {
             echo "<h1><br>Thank you for using our deposit calculator tool.</h1>";
             
        }
        session_destroy(); 
        ?>        
    </body>
    <?php include 'Lab4Common/Footer.php'; ?>
</html>
