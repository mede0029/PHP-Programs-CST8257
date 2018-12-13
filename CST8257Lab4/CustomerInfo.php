<!DOCTYPE html>
<?php
//SESSIONS
session_start(); 
if ($_SESSION['terms'] == "") 
{
    header("Location: Disclaimer.php");
}    

// RETRIEVING VALUES FROM FORM
$name = $_POST["name"];
$nameError = "";
$postalCode = $_POST["postalCode"];
$postalCodeError = "";
$phoneNumber = $_POST["phoneNumber"];
$phoneNumberError = "";
$email = $_POST["email"];
$emailError = "";
$preferredContact = $_POST["preferredContact"];
$preferredContactError = "";
$contact = $_POST["contact"];
$contactError = "";
$errorMessage ="";
$total = $principalAmount;
$interestYear;
$counter = count($contact);
$_SESSION['counter'] = $counter;
$postalCodeExpression = '/^[ABCEGHJKLMNPRSTVXY]{1}\d{1}[A-Z]{1} *\d{1}[A-Z]{1}\d{1}$/';
$phoneNumberExpression = '/^[2-9][0-9][0-9]-[2-9][0-9][0-9]-[0-9][0-9][0-9][0-9]$/';  

// FUNCTIONS:
include_once 'Lab4Common/Functions.php';

// VALIDATIONS:
if(isset($_POST['submit']))
{ 
   if (ValidateName($name) == 1)
   {
       $nameError = "Name cannot be blank!";
   }
   
   if (ValidatePostalCode($postalCodeExpression, $postalCode) == 1)
   {
       $postalCodeError = "You must enter a format of XnX nXn for the postal code!";
   }
   
   if (ValidatePhoneNumber($phoneNumberExpression, $phoneNumber) == 1)
   {
       $phoneNumberError = "You must enter a format of nnn-nnn-nnnn for the phone number where the first digit in the first and the second 3-digit groups cannot be 0 or 1!";
   }
   
   if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
   {
       $emailError  = "Email is not in the correct format (aaa@xxx.yyy)!";
   }
   
   if ($preferredContact == "")
   {
       $preferredContactError = "You must choose a method to be contacted!";
   }
   
   if ($preferredContact == "phone" && $contact == "") 
   {
        $contactError = "When the preferred method of contact is phone, please select the best time to contact you! <br/>";
   }  
    $_SESSION['name'] = $_POST['name'];    
    $_SESSION['postalCode'] = $_POST['postalCode'];    
    $_SESSION['phoneNumber'] = $_POST['phoneNumber'];   
    $_SESSION['email'] = $_POST['email']; 
    $_SESSION['preferredContact'] = $preferredContact;
    $_SESSION['contact'] = $contact;
      
   if ( $nameError == "" && $postalCodeError == "" && $phoneNumberError == "" && $emailError == "" && $contactError == "")        
   {
        header("Location: DepositCalculator.php");
   }  
   
   if ($preferredContact == "phone") 
   {
        $phoneCheck = "checked";
        $emailCheck = "";
   }
   elseif ($preferredContact == "email")
   {
        $phoneCheck = "";
        $emailCheck = "checked";
   }
     if(!isset( $_POST['preferredContact'] ) )
   {
        $preferredContactError = "Please choose the preferred way of contact! <br/>";
   }
} 

if(isset($_POST['clear']))
{
    $_SESSION['name'] = "";
    $_SESSION['postalCode'] = "";
    $_SESSION['phoneNumber'] = "";
    $_SESSION['email'] = "";
    $_SESSION['preferredContact'] = "";
    $_SESSION['contact'] = "";
}
?>

<html>
    <head>
        <?php include 'Lab4Common/Header.php'; ?>
        <link rel="stylesheet" type="text/css" href="Site.css">
    </head>
    
    <body>   
        <h1 align="center">Customer Information</h1>
        <br><hr><br>        
        
        <form method='post' action=CustomerInfo.php>
        <div class='form-group row'>
            <label for='name' class='col-lg-2 col-form-label'><b>Name:</b> </label>
            <div class='col-lg-4'>
            <input type='text' class='form-control' id='name' value='<?php print $_SESSION['name'];?>' name='name' ></div>
            <div class='col-lg-4' style='color:red'> <?php print $nameError;?></div>
        </div><br>    

        <div class='form-group row'>
            <label for='postalCode' class='col-lg-2 col-form-label'><b>Postal Code: </b><br> (Format: XnX nXn)</b></label>
            <div class='col-lg-4'>
            <input type='text' class='form-control' id='postalCode' value='<?php print $_SESSION['postalCode'];?>' name='postalCode' ></div>
            <div class='col-lg-4' style='color:red' ><?php print $postalCodeError;?></div>                
        </div>    

        <div class='form-group row'>
            <label for='phoneNumber' class='col-lg-2 col-form-label'><b>Phone Number: <br></b>(nnn-nnn-nnnn) </label>
            <div class='col-lg-4'>
            <input type='text' class='form-control' id='phoneNumber' value='<?php print $_SESSION['phoneNumber'];?>' name='phoneNumber' ></div>
            <div class='col-lg-4' style='color:red' ><?php print $phoneNumberError;?></div>                
        </div>  
        
        <div class='form-group row'>
            <label for='email' class='col-lg-2 col-form-label'><b>eMail Address: </b></label>
            <div class='col-lg-4'>
            <input type='email' class='form-control' id='email' value='<?php print $_SESSION['email'];?>' name='email'></div>
            <div class='col-lg-4' style='color:red' ><?php print $emailError;?></div>
        </div><br><hr><br> 
        
        <div class='form-group row'>   
            <label for='preferredMethod' class='col-lg-2 col-form-label'><b>Preferred contact method: </b></label>
            <div class='col-lg-3'>
                <label class='radio-inline col-lg-3 col-form-label'>
                <input type='radio' name='preferredContact' value='phone'<?=$phoneCheck?>>Phone</label>
                <label class='radio-inline col-lg-2 col-form-label'>
                    <input type='radio' name='preferredContact' value='email'<?=$emailCheck?>>eMail</label> </div>
        <div class='col-lg-3' style='color:red' ><?php print $preferredContactError; ?></div>
        </div>
        
        <div class='form-group row'> 
            <div class='col-lg-6 col-form-label'><b>If phone was selected, when can we contact you? </br>(check all applicable):</b> </div>
        </div>   
        <div class='form-group row'> 
            <div class='col-lg-4'>
                <div class='form-check form-check-inline'>
                    <input class='form-check-input' type='checkbox' name='contact[]' value='morning' 
                        <?php if(isset($_POST['submit']) && $_POST["contact"]){if(in_array("morning", $_POST["contact"])){
                            echo "checked='checked'"; }} ?> >
                    <label class='form-check-label' for='inlineCheckbox'>Morning</label>
                </div>
                <div class='form-check form-check-inline'>
                    <input class='form-check-input' type='checkbox' name='contact[]' value='afternoon' 
                        <?php if(isset($_POST['submit']) && $_POST["contact"]){if(in_array("afternoon", $_POST["contact"])){
                            echo "checked='checked'"; }} ?> >                           
                    <label class='form-check-label' for='inlineCheckbox'>Afternoon</label>
                </div>
                <div class='form-check form-check-inline'>
                    <input class='form-check-input' type='checkbox' name='contact[]' value='evening' 
                        <?php if(isset($_POST['submit']) && $_POST["contact"]){if(in_array("evening", $_POST["contact"])){
                            echo "checked='checked'"; }} ?> >                           
                    <label class='form-check-label' for='inlineCheckbox'>Evening</label>                        
                </div> 
            </div>
            <div class='col-lg-6' style='color:red' ><?php print $contactError; ?></div>   
        </div>

        <div class='form-group row'>                
        <button type='submit' name='submit' class='btn btn-primary col-lg-1'>Submit</button>
            <div class='col-lg-10'>
            <button type='submit' name='clear' class='btn btn-primary col-lg-1'>Clear</button>
            </div>
        </div>                      
        </form><br><br>          

    </body>
    <?php include 'Lab4Common/Footer.php'; ?>
</html>
