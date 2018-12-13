<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
$principalAmount = $_POST["principalAmount"];
$interestRate = $_POST["interestRate"];
$principleAmount = $_POST["principalAmount"];
$yearsToDeposit = $_POST["yearsToDeposit"]; 
$name = $_POST["name"];
$postalCode = $_POST["postalCode"];
$phoneNumber = $_POST["phoneNumber"];
$email = $_POST["email"];
$preferredContact = $_POST["preferredContact"];
$contact = $_POST["contact"];
$errorMessage ="";
$total = $principalAmount;
$interestYear;
$counter = count($contact);

// VALIDATIONS:
// Principal Amount must be numeric and greater than zero.
if (!is_numeric($principalAmount) || $principalAmount <= 0)
{
    $errorMessage = "• The principal amount must be numeric and greater than 0. <br/>";
}
// Interest rate must be numeric and not negative.
if (!is_numeric($interestRate) || $interestRate < 0)
{
    $errorMessage = $errorMessage."• The interest rate must be numeric and not negative. <br/>";
}
// Number of years to deposit must be a numeric between 1 and 20.
if (!is_numeric($yearsToDeposit) || ($yearsToDeposit < 1 &&  $yearsToDeposit > 20))
{
    $errorMessage = $errorMessage."• The number of years to deposit must be numeric and between 1 and 20. <br/>";
}
// When a user select phone as his/her preferred contact method, he/she must select one or more 
//contact times (morning, afternoon and/or evening).
if ($preferredContact == "phone" && $contact == "") 
{
    $errorMessage = $errorMessage."• Please select the best time to contact you by phone. <br/>";
}
//The user must enter data in all fields, no field can be blank.
if ($principalAmount == "" || $interestRate == "" || $principleAmount == "" || $yearsToDeposit == "" || $name == "" ||   
$postalCode == "" || $phoneNumber == "" || $email == "" || $preferredContact == "" || $contact == "")
{
    $errorMessage = $errorMessage."• You must type a value for all fields. <br/>";
}

?>


<html>
    <head>        
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <title>Deposit Calculator</title>
        <link rel="stylesheet" type="text/css" href="DepositCalculator.css">
        <meta charset="UTF-8">        
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>    
    <body>    
        <?php        
        echo "<h1>Thank you, ".$name.", for using our deposit calculator!</h1>"; 
        
        // Error php page:
        if ($errorMessage != "")
        {
            echo "<h3>However, we can not process your request because of the following input errors:</h3>";
            print("<p>$errorMessage</p>");
        }
        else 
        {
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
                echo "<br><h3>Our customer service department will email you tomorrow, at $email.</h3>";
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
        }
        
        
        
        ?>  
        
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>        
    </body>
</html>
