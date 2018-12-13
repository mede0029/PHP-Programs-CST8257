<!DOCTYPE html>

<html>
    <head>

    </head>
    <body>
        <?php
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
            ?>
    </body>
</html>
