<?php
    include 'Lab5Common/Class_Lib.php';
    session_start();
    include 'Lab5Common/Footer.php';
    include 'Lab5Common/Header.php';
    include 'Lab5Common/Functions.php';
    
    $studentIdTxt = $_POST["studentIdTxt"];
    $_SESSION['studentIdTxt'] = $studentIdTxt;
    $passwordTxt = $_POST["passwordTxt"];
    $_SESSION['passwordTxt'] = $passwordTxt;
    $_SESSION['nameTxt'] = $nameTxt;
    $studentIdError = "";
    $passwordError = "";
    $validateError = "";    
   
    //Submit button:
    if(isset($_POST['submit']))
    {
         //VALIDATORS:
        if (ValidateStudentId($studentIdTxt) == 1)
        { $studentIdError = "Student ID cannot be blank!"; }
        else { $studentIdError = ""; }
      
        if (ValidateBlankPassword ($passwordTxt) == 1)
        { $passwordError = "Password cannot be blank!"; } 
        else { $passwordError = ""; }  
        
        //If passing the validators:
        if ($studentIdError == "" && $passwordError == "")
        { 
            //Connection to DBO
            $dbConnection = parse_ini_file("Lab5Common/db_connection.ini");        	
            extract($dbConnection);
            $myPdo = new PDO($dsn, $user, $password);
            
            //Query database for requested StudentId            
            $sqlStatement = 'SELECT * FROM Student WHERE StudentId = :PlaceHolderStudentID AND Password = :PlaceHolderPassword';
            $pStmt = $myPdo->prepare($sqlStatement);       
            $pStmt ->execute(array(':PlaceHolderStudentID' => $studentIdTxt, ':PlaceHolderPassword' => $passwordTxt));      
            $chkAccount = $pStmt->fetch(); //store first result of query to $chkAccount            
            
            if ($chkAccount['StudentId'] != "") //user is in database
            {                
                $_SESSION['nameTxt'] = $chkAccount[1] ; //storing user's name in a session 
                if ($_SESSION['activePage'] == "CurrentRegistration.php")
                {
                    header('Location: CurrentRegistration.php');
                    exit;  
                }
                if ($_SESSION['activePage'] == "CourseSelection.php")
                {
                    header('Location: CourseSelection.php');
                    exit;  
                }
                else 
                {
                    header('Location: CourseSelection.php');
                    exit;  
                }
            }
            else //if student does not match the database
            { 
                $validateError = "Incorrect ID and/or password!";                 
            }       
        }
    }
    
    //Clear button:
    if(isset($_POST['clear']))
    {
        $_SESSION['studentIdTxt'] = "";       
        $_SESSION['passwordTxt'] = "";
    }
    
?>
        

<html>
    <head>
        <meta charset="UTF-8">
        <title>Login</title>
    </head>
    <body>
        <h1>&nbsp &nbsp &nbsp Log In</h1><br>
        <h4>&nbsp &nbsp You need to <a href="NewUser.php">sign up</a> if you are a new user!</h4><br> <br>
        
        <form method='post' action=Login.php>
            
        <div class='col-lg-4' style='color:red'> <?php print $validateError;?></div><br>      
        <div class='form-group row'>
            <label for='studentId' class='col-lg-2 col-form-label'><b>&nbsp &nbsp Student ID:</b> </label>
            <div class='col-lg-4'>
            <input type='text' class='form-control' id='studentIdTxt'  value='<?php print $_SESSION['studentIdTxt'];?>' name='studentIdTxt' ></div>
            <div class='col-lg-4' style='color:red'> <?php print $studentIdError;?></div>
        </div><br> 
        
        <div class='form-group row'>
            <label for='password' class='col-lg-2 col-form-label'><b>&nbsp &nbsp Password:</b> </label>
            <div class='col-lg-4'>
            <input type='text' class='form-control' id='passwordTxt'  value='<?php print $_SESSION['passwordTxt'];?>' name='passwordTxt' ></div>
            <div class='col-lg-4' style='color:red'> <?php print $passwordError;?></div>
        </div><br>
        
        <div class='form-group row'>                
            <button type='submit' name='submit' class='btn btn-primary col-lg-1'>Submit</button>
                <div class='col-lg-10'>
                <button type='submit' name='clear' class='btn btn-primary col-lg-1'>Clear</button>
                </div>
        </div>               
        </form>               

    </body>
</html>
