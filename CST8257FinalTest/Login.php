<?php 
session_start();
include 'ProjectCommon/PageHeader.php';
$validateError = "";

//Login button:
if(isset($_POST['login']))
{
    $userIdTxt = $_POST["userIdTxt"];
    $passwordTxt = $_POST["passwordTxt"];
    $_SESSION['userIdTxt'] = $userIdTxt;
    $_SESSION['passwordTxt'] = $passwordTxt;
    
    //encrypting password 
    $hashed_password = sha1($passwordTxt);

    //Connection to DBO            
    $dbConnection = parse_ini_file("ProjectCommon/db_connection.ini");        	
    extract($dbConnection);
    $myPdo = new PDO($dsn, $user, $password); 

    //Query database to verify StudentId and Password          
    $sqlStatement = 'SELECT * FROM customer where customer.UserId = :userID AND customer.Password = :password';                   
    $pStmt = $myPdo->prepare($sqlStatement);       
    $pStmt ->execute(array(':userID' => $userIdTxt, ':password' => $hashed_password));      
    $chkAccount = $pStmt->fetch(); //store first result of query to $chkAccount            

    if ($chkAccount['UserId'] != "") //user is in database and password matches
    {                
        $_SESSION['userIdTxt'] = $userIdTxt;
        header('Location: BookCatalog.php');
        exit; 
    }
    else 
    {
        $validateError = "Incorrect ID and/or password!";    
    }       
}

//Clear button:
if(isset($_POST['clear']))
{
    $_SESSION['userIdTxt'] = "";       
    $_SESSION['passwordTxt'] = "";
}   
    
?>
<br><h3>&nbsp &nbsp Please enter your User ID and password:</h3><br> <br>
<form method='post' action=Login.php>    
                 
    <div class='form-group row'>
        <label for='userId' class='col-lg-1 col-form-label'><b>User ID:</b> </label>
        <div class='col-lg-4'>
        <input type='text' class='form-control' id='userIdTxt'  value='<?php print $_SESSION['userIdTxt'];?>' name='userIdTxt' ></div>
    </div><br>     
    <div class='form-group row'>
        <label for='password' class='col-lg-1 col-form-label'><b>Password:</b> </label>
        <div class='col-lg-4'>
            <input type='text' class='form-control' id='userIdTxt'  value='<?php print $_SESSION['passwordTxt'];?>' name='passwordTxt' >
            <div class='col-lg-6' style='color:red' background='#b0c4de' > &nbsp &nbsp <?php print $validateError;?></div>
        </div>
    </div><br>        
    <div class='form-group row'>                
        <button type='submit' name='login' class='btn btn-primary col-lg-1'>Login</button>
        <div class='col-lg-10'>
            <button type='submit' name='clear' class='btn btn-primary col-lg-1'>Clear</button>
        </div>
    </div>        
</form>  
    
<?php 
    include 'ProjectCommon/PageFooter.php';
?>
