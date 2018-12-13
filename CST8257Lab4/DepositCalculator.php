<!DOCTYPE html>
<?php

//INITIALIZING SESSIONS
session_start(); 
if ( $_SESSION['name'] == "" || $_SESSION['postalCode'] == "" || $_SESSION['phoneNumber'] == "" || $_SESSION['email'] == "") 
{
    header("Location: CustomerInfo.php");
}    
// RETRIEVING VALUES FROM FORM
$principalAmount = $_POST['principalAmount'];
$principalAmountError = "";
$interestRate = $_POST['interestRate'];
$interestRateError = "";
$yearsToDeposit = $_POST["yearsToDeposit"]; 
$yearsToDepositError = "";
$total = $principalAmount;
$interestYear;
$counter = count($contact);
$name = $_SESSION['name'];
$valid = "";

// FUNCTIONS:
include_once 'Lab4Common/Functions.php';

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
         
   if ( $principalAmountError == "" && $interestRateError == "" && $yearsToDepositError == "")
   {
       $_SESSION['principalAmount'] = $principalAmount; 
       $_SESSION['interestRate'] = $interestRate;
       $valid = true;
   }
} 

if(isset($_POST['clear']))
{
    $_SESSION['principalAmount'] = "";
    $_SESSION['interestRate'] = "";
    $yearsToDeposit = "";
}
?>

<html>
    <head>
        <?php include 'Lab4Common/Header.php'; ?>
        <link rel="stylesheet" type="text/css" href="Site.css">
    </head>
    <body>
        <p>Enter principal amount, interest rate and select number of years to deposit: </p><br>
        <form method='post' action=DepositCalculator.php >
            <div class='form-group row'>
                <label for='principalAmount' class='col-lg-2 col-form-label'><b>Principal Amount: </b></label>
                <div class='col-lg-4'>
                <input type='text' class='form-control' id='principalAmount' value='<?php print $_SESSION['principalAmount']; ?>' name='principalAmount' ></div>
                <div class='col-lg-4' style='color:red' ><?php print $principalAmountError; ?></div> 
            </div>
            <div class='form-group row'>
                <label for='interestRate' class='col-lg-2 col-form-label'><b>Interest Rate (%):</b> </label>
                <div class='col-lg-4'>
                <input type='text' class='form-control' id='interestRate' value='<?php print $_SESSION['interestRate']; ?>' name='interestRate' ></div>
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
                <button type='submit' name='submit' class='btn btn-primary col-lg-1'>Calculate</button>
                <div class='col-lg-10'>
                <button type='submit' name='clear' class='btn btn-primary col-lg-1'>Clear</button>
                </div>
            </div>            
        </form>       
        
       <?php
       if ($valid == true)
       {                  
                echo "<p><br>Following is the result of the calculation:</p>";

                    //Printing table header                   
                    echo "<table width='500' font-size='large'>";     
                        echo "<th style='background-color:lightgrey'>";             
                        echo "Year:";            
                        echo "</th>";            
                        echo "<th style='background-color:lightgrey'>";         
                        echo "Principal at year/start:";           
                        echo "</th>";             
                        echo "<th style='background-color:lightgrey'>";
                        echo "Interest for the year:";
                        echo "</th>";       

                    //Calculate and print table body
                    for($i=1; $i<=$yearsToDeposit; $i++)
                    {
                        $interestYear = ($total / 100 * $interestRate);
                        if($i % 2 == 0) //for stripped effect
                        {                             
                            echo "<tr>";
                            echo "<td style='background-color:lightgrey' align='left'>";
                            echo $i;
                            echo "</td>";

                            echo "<td style='background-color:lightgrey' align='left'>";
                            printf("$"."%.2f", $total);
                            $total = ($total / 100 * $interestRate) + $total;
                            echo "</td>"; 

                            echo "<td style='background-color:lightgrey' align='left'>";
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
                    echo "</table><br><br>";                   
       } ?>   
   
    </body>
    <?php include 'Lab4Common/Footer.php'; ?>
    
    
</html>
