<?php
    session_start();
    include 'ProjectCommon/Functions.php';
    
    $userIdTxt = htmlspecialchars($_POST["userIdTxt"]);
    $_SESSION['userIdTxt'] = $userIdTxt;
    $nameTxt = htmlspecialchars($_POST["nameTxt"]);
    $_SESSION['nameTxt'] = $nameTxt;
    $phoneNumberTxt = htmlspecialchars($_POST["phoneNumberTxt"]);
    $_SESSION['phoneNumberTxt'] = $phoneNumberTxt;
    $passwordTxt = htmlspecialchars($_POST["passwordTxt"]);
    $_SESSION['passwordTxt'] = $passwordTxt;
    $passwordAgainTxt = htmlspecialchars($_POST["passwordAgainTxt"]);
    $_SESSION['passwordAgainTxt'] = $passwordAgainTxt;
    $phoneNumberExpression = '/^[2-9][0-9][0-9]-[2-9][0-9][0-9]-[0-9][0-9][0-9][0-9]$/'; 
    $passwordExpression = '/(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{6,}/';
    $studentIdError = "";
    $nameError = "";
    $phoneNumberError = "";
    $passwordError = "";    
    $validateError = "";
    
    if(isset($_POST['submit']))
    {
        //VALIDATORS:
        if (ValidateUserId($userIdTxt) == 1)
        { $userIdError = "User ID cannot be blank!"; }
        else { $userIdError = ""; }
        if (ValidateName($nameTxt) == 1) { $nameError = "Name cannot be blank!"; }
        else{ $nameError = ""; }
        if (ValidatePhoneNumber($phoneNumberExpression, $phoneNumberTxt) == 1)
        {
            $phoneNumberError = "You must enter a format of nnn-nnn-nnnn for the phone number where the first digit in the "
                    . "first and the second 3-digit groups cannot be 0 or 1!";
        } else { $phoneNumberError = ""; }
        
        if (ValidatePassword($passwordExpression, $passwordTxt) == 1)
        {
            $passwordError = "Your password must be at least 6 characters long, contain at least one upper case, "
                    . "one lowercase and one digit.";
        } else { $passwordError = ""; }
        
        if (ValidateEqualPassword($passwordTxt, $passwordAgainTxt)){
            $passwordAgainError = "Passwords do not match!";
        } else { $passwordAgainError = ""; }          
        
        
        //IF PASSING ALL THE VALIDATIONS:
        if ($userIdError == "" && $nameError == "" && $phoneNumberError == "" && $passwordError == "" && $passwordAgainError == "")
        {    
            //encrypting password
            $hashed_password = sha1($passwordTxt);
            
            //Connection to DBO            
            $dbConnection = parse_ini_file("ProjectCommon/db_connection.ini");        	
            extract($dbConnection);
            $myPdo = new PDO($dsn, $user, $password);       
                       
            //Query database to see if userId already exists      
            $sqlStatement = 'SELECT * FROM User WHERE User.UserId = :PlaceHolderUserID';
            $pStmt = $myPdo->prepare($sqlStatement);  
            $pStmt ->execute(array(':PlaceHolderUserID' => $userIdTxt));      
            $chkAccount = $pStmt->fetch(); //store first result of query to $chkAccount        
            
            if ($chkAccount['UserId'] == "") //user does not exist
            {
                $sql = "INSERT INTO User VALUES( :idTxt, :nameTxt, :phoneNumberTxt, :passwordTxt)";
                $pStmt = $myPdo->prepare($sql);
                $pStmt->execute(array(':idTxt' => $userIdTxt, ':nameTxt' => $nameTxt, ':phoneNumberTxt' => $phoneNumberTxt, ':passwordTxt' => $hashed_password));
                $pStmt->commit;      
                header('Location: MyFriends.php'); //ARE WE SUPPOSED TO GO TO MY FRIENDS PAGE?
                exit;  
            }
            else //if student already exists
            { 
                $validateError = "A user with this ID has already signed up!";                 
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
        $_SESSION['userIdTxt'] = "";
    } 
    
    include 'ProjectCommon/Header.php';
?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6">
                <h1 class="text-center">Sign Up</h1>
            </div>
        </div>
        <h4>All fields are required:</h4>
    
        <form method='post' action=NewUser.php>    
            <div class='col-lg-2 col-md-2 col-sm-4' style='color:red'> <?php print $validateError;?></div><br>
            
            <div class='form-group row'>
                <div class='col-lg-1 col-md-2 col-sm-2'>
                    <label for='userId' class='col-form-label'><b>User ID:</b> </label>
                </div>
                <div class='col-lg-4 col-md-4 col-sm-4'>
                    <input type='text' class='form-control' id='userIdTxt' value='<?php print $_SESSION['userIdTxt'];?>' name='userIdTxt' >
                </div>
                <div class='col-lg-4 col-md-4 col-sm-4' style='color:red'> <?php print $userIdError;?></div>
            </div>
            <br/>

            <div class='form-group row'>
                <div class='col-lg-1 col-md-2 col-sm-2'>
                    <label for='name' class='col-form-label'><b>Name:</b></label>
                </div>
                <div class='col-lg-4 col-md-4 col-sm-4'>
                    <input type='text' class='form-control' id='nameTxt' value='<?php print $_SESSION['nameTxt'];?>' name='nameTxt' ></div>
                <div class='col-lg-4 col-md-4 col-sm-4' style='color:red'> <?php print $nameError;?></div>
            </div>
            <br/>

            <div class='form-group row'>
                <div class='col-lg-1 col-md-2 col-sm-2'>
                    <label for='phoneNumber' class='col-form-label'><b>Phone Number:</b><br/>
                        <small id="phoneHelp" class="text-muted">
                            (nnn-nnn-nnnn)
                        </small>
                    </label>
                </div>
                <div class='col-lg-4 col-md-4 col-sm-4'>
                    <input type='text' class='form-control' id='phoneNumberTxt' value='<?php print $_SESSION['phoneNumberTxt'];?>' name='phoneNumberTxt'>
                </div>
                <div class='col-lg-4 col-md-4 col-sm-4' style='color:red' ><?php print $phoneNumberError;?></div>
            </div>

            <div class='form-group row'>
                <div class='col-lg-1 col-md-2 col-sm-2'>
                    <label for='password' class='col-form-label'><b>Password:</b> </label>
                </div>
                <div class='col-lg-4 col-md-4 col-sm-4'>
                    <input type='password' class='form-control' id='passwordTxt'  value='<?php print $_SESSION['passwordTxt'];?>' name='passwordTxt' >
                </div>
                <div class='col-lg-4 col-md-4 col-sm-4' style='color:red'> <?php print $passwordError;?></div>
            </div><br>

            <div class='form-group row'>
                <div class='col-lg-1 col-md-2 col-sm-2'>
                    <label for='passwordAgain' class='col-form-label'><b>Password Again:</b></label>
                </div>
                <div class='col-lg-4 col-md-4 col-sm-4'>
                    <input type='password' class='form-control' id='passwordAgainTxt' value='<?php print $_SESSION['passwordAgainTxt'];?>' name='passwordAgainTxt' >
                </div>
                <div class='col-lg-4 col-md-4 col-sm-4' style='color:red'> <?php print $passwordAgainError;?></div>
            </div><br> 

            <div class='row'>
                <div class="col-lg-1 col-md-2 col-sm-2"></div>
                <div class='col-lg-2 col-md-2 col-sm-2 text-left'>
                    <button type='submit' name='submit' class='btn btn-block btn-primary'>Submit</button>
                </div>
                <div class='col-lg-2 col-md-2 col-sm-2 text-left'>
                    <button type='submit' name='clear' class='btn btn-block btn-primary'>Clear</button>
                </div>
            </div>  
        </form>
    </div>
<?php
    include 'ProjectCommon/Footer.php';
?>