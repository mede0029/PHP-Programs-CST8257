<?php
    include 'Lab5Common/Class_Lib.php';
    session_start();
    include 'Lab5Common/Functions.php';
    
    $studentIdTxt = $_POST["studentIdTxt"];
    $_SESSION['studentIdTxt'] = $studentIdTxt;
    $nameTxt = $_POST["nameTxt"];
    $_SESSION['nameTxt'] = $nameTxt;
    $phoneNumberTxt = $_POST["phoneNumberTxt"];
    $_SESSION['phoneNumberTxt'] = $phoneNumberTxt;
    $passwordTxt = $_POST["passwordTxt"];
    $_SESSION['passwordTxt'] = $passwordTxt;
    $passwordAgainTxt = $_POST["passwordAgainTxt"];
    $_SESSION['passwordAgainTxt'] = $passwordAgainTxt;
    $phoneNumberExpression = '/^[2-9][0-9][0-9]-[2-9][0-9][0-9]-[0-9][0-9][0-9][0-9]$/'; 
    $passwordExpression = '/(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{6,}/';
    $studentIdError = "";
    $nameError = "";
    $phoneNumberError = "";
    $passwordError = "";    
    $validateError = "";
        
    //Submit button:
    if(isset($_POST['submit']))
    {
        //VALIDATORS:
        if (ValidateStudentId($studentIdTxt) == 1)
        { $studentIdError = "Student ID cannot be blank!"; }
        else { $studentIdError = ""; }

        if (ValidateName($nameTxt) == 1) { $nameError = "Name cannot be blank!"; }
        else{ $nameError = ""; }

        if (ValidatePhoneNumber($phoneNumberExpression, $phoneNumberTxt) == 1)
        {
            $phoneNumberError = "You must enter a format of nnn-nnn-nnnn for the phone number where the first digit in the "
                    . "first and the second 3-digit groups cannot be 0 or 1!";
        } else { $phoneNumberError = ""; }
        
        if (ValidatePassword ($passwordExpression, $passwordTxt) == 1)
        {
            $passwordError = "Your password must be at least 6 characters long, contain at least one upper case, "
                    . "one lowercase and one digit.";
        } else { $passwordError = ""; }
        
        if (ValidadeEqualPassword ($passwordTxt, $passwordAgainTxt)){
            $passwordAgainError = "Passwords do not match!";
        } else { $passwordAgainError = ""; }          
        
        
        //IF PASSING ALL THE VALIDATIONS:
        if ($studentIdError == "" && $nameError == "" && $phoneNumberError == "" && $passwordError == "" && $passwordAgainError == "")
        {            
            //Connection to DBO
            $dbConnection = parse_ini_file("Lab5Common/db_connection.ini");        	
            extract($dbConnection);
            $myPdo = new PDO($dsn, $user, $password);
            
            //Query database for requested StudentId            
            $sqlStatement = 'SELECT * FROM Student WHERE StudentId = :PlaceHolderStudentID';
            $pStmt = $myPdo->prepare($sqlStatement);       
            $pStmt ->execute(array(':PlaceHolderStudentID' => $studentIdTxt));      
            $chkAccount = $pStmt->fetch(); //store first result of query to $chkAccount            
            
            if ($chkAccount['StudentId'] == "") //user does not exist
            {
                $validateError = ""; 
                $sql = "INSERT INTO Student VALUES( :idTxt, :nameTxt, :phoneNumberTxt, :passwordTxt)";
                $pStmt = $myPdo->prepare($sql);
                $pStmt->execute(array(':idTxt' => $studentIdTxt, ':nameTxt' => $nameTxt, ':phoneNumberTxt' => $phoneNumberTxt, ':passwordTxt' => $passwordTxt));
                $pStmt->commit;      
                header('Location: CourseSelection.php');
                exit;  
            }
            else //if student already exists
            { 
                $validateError = "A student with this ID has already signed up!";                 
            }             
        }            
    }          
    
    //Clear button:
    if(isset($_POST['clear']))
    {
        $_SESSION['studentIdTxt'] = "";
        $_SESSION['nameTxt'] = "";
        $_SESSION['phoneNumberTxt'] = "";
        $_SESSION['passwordTxt'] = "";
        $_SESSION['passwordAgainTxt'] = "";
    }   
    
    include 'Lab5Common/Header.php';    
?>
        
<h1>&nbsp &nbsp &nbsp Sign Up</h1><br>
  <h4>&nbsp &nbsp All fields are required:</h4><br> <br>     

<form method='post' action=NewUser.php>        
<div class='col-lg-4' style='color:red'> <?php print $validateError;?></div><br>        
<div class='form-group row'>
    <label for='studentId' class='col-lg-2 col-form-label'><b>&nbsp &nbsp Student ID:</b> </label>
    <div class='col-lg-4'>
    <input type='text' class='form-control' id='studentIdTxt'  value='<?php print $_SESSION['studentIdTxt'];?>' name='studentIdTxt' ></div>
    <div class='col-lg-4' style='color:red'> <?php print $studentIdError;?></div>
</div><br>  

<div class='form-group row'>
    <label for='name' class='col-lg-2 col-form-label'><b>&nbsp &nbsp Name:</b> </label>
    <div class='col-lg-4'>
    <input type='text' class='form-control' id='nameTxt'  value='<?php print $_SESSION['nameTxt'];?>' name='nameTxt' ></div>
    <div class='col-lg-4' style='color:red'> <?php print $nameError;?></div>
</div><br>  

<div class='form-group row'>
    <label for='phoneNumber' class='col-lg-2 col-form-label'><b>&nbsp &nbsp Phone Number: <br></b>&nbsp &nbsp(nnn-nnn-nnnn) </label>
    <div class='col-lg-4'>
    <input type='text' class='form-control' id='phoneNumberTxt' value='<?php print $_SESSION['phoneNumberTxt'];?>' name='phoneNumberTxt' ></div>
    <div class='col-lg-4' style='color:red' ><?php print $phoneNumberError;?></div>                
</div>  

<div class='form-group row'>
    <label for='password' class='col-lg-2 col-form-label'><b>&nbsp &nbsp Password:</b> </label>
    <div class='col-lg-4'>
    <input type='text' class='form-control' id='passwordTxt'  value='<?php print $_SESSION['passwordTxt'];?>' name='passwordTxt' ></div>
    <div class='col-lg-4' style='color:red'> <?php print $passwordError;?></div>
</div><br>

<div class='form-group row'>
    <label for='passwordAgain' class='col-lg-2 col-form-label'><b>&nbsp &nbsp Password Again:</b> </label>
    <div class='col-lg-4'>
    <input type='text' class='form-control' id='passwordAgainTxt' value='<?php print $_SESSION['passwordAgainTxt'];?>' name='passwordAgainTxt' ></div>
    <div class='col-lg-4' style='color:red'> <?php print $passwordAgainError;?></div>
</div><br> 

<div class='form-group row'>                
<button type='submit' name='submit' class='btn btn-primary col-lg-1'>Submit</button>
    <div class='col-lg-10'>
    <button type='submit' name='clear' class='btn btn-primary col-lg-1'>Clear</button>
    </div>
</div>               
</form><br>   

<?php 
    include 'Lab5Common/Footer.php';
?>
