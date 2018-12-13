<?php
    include 'ProjectCommon/Class_Lib.php';
    session_start();
    include 'ProjectCommon/Header.php';
    include 'ProjectCommon/Functions.php';
    $userIdTxt = htmlspecialchars($_POST["userIdTxt"]);
    $_SESSION['userIdTxt'] = $userIdTxt;
    $passwordTxt = htmlspecialchars($_POST["passwordTxt"]);
    $_SESSION['passwordTxt'] = $passwordTxt;
    $_SESSION['nameTxt'] = $nameTxt;
    $userIdError = "";
    $passwordError = "";
    $validateError = "";    
   
    //Submit button:
    if(isset($_POST['submit']))
    {
         //VALIDATORS:
        if (ValidateUserId($userIdTxt) == 1)
        { $userIdError = "User ID cannot be blank!"; }
        else { $studentIdError = ""; }
      
        if (ValidateBlankPassword ($passwordTxt) == 1)
        { $passwordError = "Password cannot be blank!"; } 
        else { $passwordError = ""; }  
        
        //If passing the validators:
        if ($studentIdError == "" && $passwordError == "")
        {            
            $hashed_password = sha1($passwordTxt);            
            
            $validateError = "Ready to code!";
            //Connection to DBO            
            $dbConnection = parse_ini_file("ProjectCommon/db_connection.ini");        	
            extract($dbConnection);
            $myPdo = new PDO($dsn, $user, $password);                 

            //Query database to verify StudentId and Password          
            $sqlStatement = 'SELECT * FROM User WHERE UserId = :PlaceHolderUserID AND Password = :PlaceHolderPassword';
            $pStmt = $myPdo->prepare($sqlStatement);                                           
            $pStmt ->execute(array(':PlaceHolderUserID' => $userIdTxt, ':PlaceHolderPassword' => $hashed_password));      
            $chkAccount = $pStmt->fetch(); //store first result of query to $chkAccount        

            if ($chkAccount['UserId'] != "") //user is in database and password matches
            {                
                $_SESSION['nameTxt'] = $chkAccount[1] ; //storing user's name in a session                 
                //redirects user to last active page (if any previously accessed)
                if ($_SESSION['activePage'] != ""){
                    exit(header('Location: '.$_SESSION['activePage']));
                }else{
                    exit(header('Location: MyFriends.php'));
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
    <div class="container-fluid">
        <h1>Log In</h1><br>
        <h4>You need to <a href="NewUser.php">sign up</a> if you are a new user!</h4><br/>
        
        <form method='post' action=Login.php>            
            <div class='row'>
                <div class='col-lg- col-md-4 col-sm-4' style='color:red'> <?php print $validateError;?></div>
            </div>
            <br>

            <div class='form-group row'>
                <div class="col-lg-1 col-md-1 col-sm-2">
                    <label for='userId' class='col-form-label'><b>User ID:</b> </label>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-4">
                    <input type='text' class='form-control' id='userIdTxt'  value='<?php print $_SESSION['userIdTxt'];?>' name='userIdTxt' >
                </div>
                <div class='col-lg-4 col-md-2 col-sm-4' style='color:red'> <?php print $userIdError;?></div>
            </div>
            <br/>

            <div class='form-group row'>
                <div class="col-lg-1 col-md-1 col-sm-2">
                    <label for='password' class='col-form-label'><b>Password:</b> </label>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-4">
                <input type='password' class='form-control' id='passwordTxt'  value='<?php print $_SESSION['passwordTxt'];?>' name='passwordTxt' ></div>
                <div class='col-lg-4 col-md-2 col-sm-4' style='color:red'> <?php print $passwordError;?></div>
            </div><br>
            
            <div class='row'>
                <div class="col-lg-1 col-md-1 col-sm-2 col-xs-2">&nbsp;</div>
                <div class='col-lg-1 col-md-1 col-sm-2 col-xs-2 text-left'>
                    <button type='submit' name='submit' class='btn btn-block btn-primary'>Submit</button>
                </div>
                <div class='col-lg-1 col-md-1 col-sm-2 col-xs-2 text-left'>
                    <button type='submit' name='clear' class='btn btn-block btn-primary'>Clear</button>
                </div>
            </div>
        </form>
    </div>
<?php
    include 'ProjectCommon/Footer.php';
?>