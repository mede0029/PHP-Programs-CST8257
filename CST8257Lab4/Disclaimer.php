<!DOCTYPE html>
<?php
//SESSIONS
session_start(); 
$terms = $_POST["terms"];
if(isset($_POST['start']))
{
    if ($terms == "") 
    {
         $termsError = "You must agree to terms and conditions!";
    }        
    if ( $termsError == "")
    {
        header("Location: CustomerInfo.php");
    }      
    $_SESSION['terms'] = $_POST['terms'];
}
?>

<html>
    <head>
        <?php include 'Lab4Common/Header.php'; ?>
        <link rel="stylesheet" type="text/css" href="Site.css">
    </head>
    <body>        
        <h1 align='center'>Terms and Conditions</h1><br>
        <p>I agree to abide by the Bank's Terms and Conditions and rules in force and the changes thereto in Terms and Conditions 
            from time to time relating to my account as communicated and made available on the Bank's Website.</p>        
        <p>I agree that the bank before opening any deposit account will carry out a due diligence as required under Know
        Your Customer guidelines of the bank. I would be required to submit necessary documents of proofs, such as identity,
        address, photograph and any such information. I agree to submit the above documents again at periodic intervals, 
        as may be required by the Bank.</p>        
        <p>I agree that the Bank can at its sole discretion, amend any of the services/facilities given in my account
        either wholly or partially at any time by giving me at least 30 days notice and/or provide
        an option to me to swith to other services/facilities.</p><br>        
        <form method='post' action=Disclaimer.php >                
        <div style='color:red' ><?php print $termsError;?></div>            
        <div class='form-check'>
        <input type='checkbox' class='form-check-input' id='exampleCheck1' name='terms'>
        <label class='form-check-label' for='exampleCheck1'>I have read and agree with the terms and conditions.</label>
        </div> <br><br>     
        <button type='submit' name='start' class='btn btn-primary col-lg-1'>Start</button>
        </form>    
    </body>    
    <?php include 'Lab4Common/Footer.php'; ?>
</html>
