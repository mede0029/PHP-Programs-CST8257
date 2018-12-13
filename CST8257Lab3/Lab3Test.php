<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->

<?php
// RETRIEVING VALUES FROM FORM
$name = $_POST['name'];
$principalAmount = $_POST["principalAmount"];
$principalAmountError = "";
$interestRate = $_POST["interestRate"];
$interestRateError = "";
$yearsToDeposit = $_POST["yearsToDeposit"]; 
$yearsToDepositError = "";
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
$postalCodeExpression = '/^[ABCEGHJKLMNPRSTVXY]{1}\d{1}[A-Z]{1} *\d{1}[A-Z]{1}\d{1}$/';
$phoneNumberExpression = '/^[2-9][0-9][0-9]-[2-9][0-9][0-9]-[0-9][0-9][0-9][0-9]$/';   
       

// FUNCTIONS:
// Principal Amount must be numeric and greater than zero
function ValidatePrincipalAmount($principalAmount) 
{ 
    if (!is_numeric($principalAmount) || $principalAmount <= 0) {return 1;}    
}

function ValidateRate($interestRate) 
{
    if (!is_numeric($interestRate) || $interestRate <= 0) {return 1;}
}

function ValidateYears($yearsToDeposit) 
{ 
    if (!is_numeric($yearsToDeposit) || $yearsToDeposit < 1 &&  $yearsToDeposit > 20) {return 1;}    
}

function ValidateName($name)
{
    if ($name == ""){return 1;}
}

function ValidatePostalCode($postalCodeExpression, $postalCode)
{
    $valid = (bool)preg_match($postalCodeExpression, $postalCode);
    if ($valid == false) {return 1; }
}

function ValidatePhoneNumber ($phoneNumberExpression, $phoneNumber)
{
    $valid2 = (bool) preg_match($phoneNumberExpression, $phoneNumber);
    if ($valid2 == false) { return 1; }
}

// VALIDATIONS:
if(isset($_POST['submit']))
{
   if ( ValidatePrincipalAmount($principalAmount) == 1)
   {
       $principalAmountError = "The principal amount must be a number and not negative!";
   }
   
   if (ValidateRate($interestRate) == 1)
   {
       $interestRateError = "The interest rate must be numeric and not negative!";
   }
   
   if (ValidateYears($yearsToDeposit) == 1)
   { 
       $yearsToDepositError = "The number of years to deposit must be numeric and between 1 and 20!";
   }
   
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
      
   if ($preferredContact == "phone" && $contact == "") 
   {
        $contactError = "When the preferred method of contact is phone, please select the best time to contact you! <br/>";
   }  
      
   if ( $principalAmountError == "" && $interestRateError == "" && $yearsToDepositError == "" && $nameError == "" &&           
           $postalCodeError == "" && $phoneNumberError == "" && $emailError == "" && $contactError == "")
   {
       $valid = true;
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

if(isset($_POST['reset']))
{
    $principalAmount = "";
    $interestRate = "";    
    $yearsToDeposit = "";
    $name = "";
    $postalCode = "";
    $phoneNumber = "";
    $email = "";
    $preferredContact = "";
    $contact = "";
}

?>


<!--HTML DOCUMENT-->
<html>
    <head>        
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <title>Deposit Calculator</title>
        <link rel="stylesheet" type="text/css" href="Lab3TestStyleSheet.css">
        <meta charset="UTF-8">        
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>  
    <body>
        
<!-- FIRST PAGE / FORM -->

        <div style="line-height:1.8">       
            <?php
            if ($valid == false) 
            { ?> 
                <h1 align='center'>Deposit Calculator</h1><br>
                <form method='post' action=Lab3Test.php >

                    <div class='form-group row'>
                        <label for='principalAmount' class='col-lg-2 col-form-label'><b>Principal Amount: </b></label>
                        <div class='col-lg-4'>
                        <input type='text' class='form-control' id='principalAmount' value= '<?php print $name; ?>' name='principalAmount' ></div>
                        <div class='col-lg-4' style='color:red' ><?php print $principalAmountError; ?></div> 
                    </div>
                    <div class='form-group row'>
                        <label for='interestRate' class='col-lg-2 col-form-label'><b>Interest Rate (%):</b> </label>
                        <div class='col-lg-4'>
                        <input type='text' class='form-control' id='interestRate' value='<?php print $interestRate; ?>' name='interestRate' ></div>
                        <div class='col-lg-4' style='color:red' ><?php print $interestRateError;?></div>
                    </div> 
                    <div class='form-group row'>
                        <label for='yearsToDeposit' class='col-lg-2 col-form-label'><b>Years to deposit:</b></label>
                        <div class='col-lg-4'>
                        <select class='form-control' id='yearsToDeposit' name='yearsToDeposit'>
                            <option>Please select the number of years</option>
                            <?php            
                            for($i = 1; $i <= 20; $i++ ) 
                            {
                                $a = isset($yearsToDeposit) && $yearsToDeposit == $i ? "selected" : "";
                                echo "<option value='$i' $a>$i</option>";
                            }
                            ?>         
                        </select></div>
                        <div class='col-lg-4'style='color:red' ><?php print $yearsToDepositError;?></div>
                    </div><br><hr><br>

                    <div class='form-group row'>
                        <label for='name' class='col-lg-2 col-form-label'><b>Name:</b> </label>
                        <div class='col-lg-4'>
                        <input type='text' class='form-control' id='name' value='<?php print $name;?>' name='name' ></div>
                        <div class='col-lg-4' style='color:red'> <?php print $nameError;?></div>
                    </div><br>    

                    <div class='form-group row'>
                        <label for='postalCode' class='col-lg-2 col-form-label'><b>Postal Code: </b><br> (Format: XnX nXn)</b></label>
                        <div class='col-lg-4'>
                        <input type='text' class='form-control' id='postalCode' value='<?php print $postalCode;?>' name='postalCode' ></div>
                        <div class='col-lg-4' style='color:red' ><?php print $postalCodeError;?></div>                
                    </div>    

                    <div class='form-group row'>
                        <label for='phoneNumber' class='col-lg-2 col-form-label'><b>Phone Number: <br></b>(nnn-nnn-nnnn) </label>
                        <div class='col-lg-4'>
                        <input type='text' class='form-control' id='phoneNumber' value='<?php print $phoneNumber;?>' name='phoneNumber' ></div>
                        <div class='col-lg-4' style='color:red' ><?php print $phoneNumberError;?></div>                
                    </div>  

                    <div class='form-group row'>
                        <label for='email' class='col-lg-2 col-form-label'><b>eMail Address: </b></label>
                        <div class='col-lg-4'>
                        <input type='email' class='form-control' id='email' value='<?php print $email;?>' name='email'></div>
                        <div class='col-lg-4' style='color:red' ><?php print $emailError;?></div>
                    </div><br><hr><br> 
                    
                    <div class='form-group row'>   
                        <p class='col-lg-2'> <b>Preferred contact method: </b></p>                
                        <label class='radio-inline col-lg-1 col-form-label'>
                        <input type='radio' name='preferredContact' value='phone'<?=$phoneCheck?>>Phone</label>
                        <label class='radio-inline col-lg-1 col-form-label'>
                        <input type='radio' name='preferredContact' value='email'<?=$emailCheck?>>eMail</label>                     
                    <div class='col-lg-4' style='color:red' ><?php print $preferredContactError; ?></div>  
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
                        <div class='col-lg-6' style='color:red' >
                            <?php print $contactError; ?></div>                             
                    </div><br>          

                    <div class='form-group row'>                
                    <button type='submit' name='submit' class='btn btn-primary col-lg-1'>Calculate</button>
                        <div class='col-lg-10'>
                        <button type='submit' name='reset' class='btn btn-primary col-lg-1'>Clear</button>
                        </div>
                    </div>                      
                </form><br>     
            <?php
            } ?>
                
         
            
<!-- VALIDATION PAGE / TABLE    -->
            <?php
            if ($valid == true)
            {          
                echo "<h1><br>Thank you, $name, for using our deposit calculator!</h1>";                
                    if ($preferredContact == "phone")
                    {
                        if ($counter == 1)
                        {
                             echo "<br><h3>Our customer service department will call you tomorrow $contact[0] , at $phoneNumber.</h3>";
                        }
                        if ($counter == 2)
                        {
                             echo "<br><h3>Our customer service department will call you tomorrow $contact[0] or $contact[1], at $phoneNumber.</h3>";
                        }
                        if ($counter == 3)
                        {
                             echo "<br><h3>Our customer service department will call you tomorrow $contact[0] or $contact[1] or $contact[2] , at $phoneNumber.</h3>";
                        }
                    }
                    if ($preferredContact == "email")
                    {
                        echo "<br><h3>An email about the details of your GC will be sent to $email.</h3>";
                    }                 
                    echo "<h3>The following is the result of the calculation:</h3>";

                        //Printing table header       
                        echo "<br><br>";
                        echo "<table width='500' align='center' font-size='large'>";     
                            echo "<th style='background-color:lightcoral'>";             
                            echo "Year:";            
                            echo "</th>";            
                            echo "<th style='background-color:lightcoral'>";         
                            echo "Value/year:";           
                            echo "</th>";             
                            echo "<th style='background-color:lightcoral'>";
                            echo "Interest/year:";
                            echo "</th>";       

                        //Calculate and print table body
                        for($i=1; $i<=$yearsToDeposit; $i++)
                        {
                            $interestYear = ($total / 100 * $interestRate);
                            if($i % 2 == 0) //for stripped effect
                            {                             
                                echo "<tr>";
                                echo "<td style='background-color:lightcoral' align='left'>";
                                echo $i;
                                echo "</td>";

                                echo "<td style='background-color:lightcoral' align='left'>";
                                printf("$"."%.2f", $total);
                                $total = ($total / 100 * $interestRate) + $total;
                                echo "</td>"; 

                                echo "<td style='background-color:lightcoral' align='left'>";
                                printf("$"."%.2f", $interestYear);
                                echo "</td>";
                                echo "</tr>";                
                            }
                            else //for stripped effect
                            {     
                                echo "<tr>";
                                echo "<td style='background-color:white' align='left'>";
                                echo $i;
                                echo "</td>"; 

                                echo "<td style='background-color:white' align='left'>";
                                printf("$"."%.2f", $total);
                                $total = ($total / 100 * $interestRate) + $total;
                                echo "</td>";

                                echo "<td style='background-color:white' align='left'>";
                                printf("$"."%.2f", $interestYear);
                                echo "</td>";
                                echo "</tr>";
                            }
                        }
                        echo "</table>";                              
            }  ?>
                
        </div>   
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script> 
    </body>
</html>

