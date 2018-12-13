<?php
    session_start();
    include 'ProjectCommon/Functions.php';
    $titleTxt = htmlspecialchars($_POST['titleTxt']);
    $_SESSION['titleTxt'] = $titleTxt;
    $validatorError = "";
    $selectAcessibility = $_POST['selectAcessibility'];
    $titleError = "";
    $descriptionTxt = htmlspecialchars($_POST['descriptionTxt']);
    $_SESSION['descriptionTxt'] = $descriptionTxt;
    $userIdTxt = $_SESSION['userIdTxt'];
    
    //only authenticated users access this page. Other than that, back to loging +
    //creating a session to make user come back here after authentitcated
     if ($_SESSION['userIdTxt'] == null){ 
        $_SESSION['activePage'] = "AddAlbum.php";        
        exit(header('Location: Login.php'));
    }
    
    //Connection to DBO            
    $dbConnection = parse_ini_file("ProjectCommon/db_connection.ini");        	
    extract($dbConnection);
    $myPdo = new PDO($dsn, $user, $password);  
    
    //Retrieving all acessibility options coming from database 
    $sql = "SELECT * FROM accessibility ";    
    $pStmt = $myPdo->prepare($sql); 
    $pStmt->execute(); 
    
    //Put each record into an array
    foreach ($pStmt as $row)
    {
        $accessibility = array( $row['Accessibility_Code'], $row['Description'] ); 
        $accessibilityArray[] = $accessibility;
    }
    $_SESSION['accessibilityArray'] = $accessibilityArray; //session with all semesters from database       
    
    //Submit button:
    if(isset($_POST['submit']))
    {
        //VALIDATORS:
        if (ValidateName($titleTxt) == 1) //name
        {
            $titleError = "Please type in an album title!";
        }  
        if ($selectAcessibility  == '0'){ //acessibility
            $validatorError = "Please select one type of accessibility!";
        }
                //checking to see if the album title already exists   
        $titlesql = 'SELECT * FROM album WHERE album.Title = :albumTitle and '
                . 'album.Owner_Id = :userID';
        $titlestmt = $myPdo->prepare($titlesql);
        $titlestmt->execute([albumTitle => $titleTxt, userID => $userIdTxt]);
        $checkTitle = $titlestmt->fetchAll();

        if ($checkTitle != null){
            $titleError = "Album title already exists!";
        }          
        //If passing the validation:
        if ($titleError == "" && $validatorError == "")
        {               
            $albumId = null;
            $date = date("Y/m/d");
            $access = $_POST['selectAcessibility'];        
            
            //creating new album
            $sql = "INSERT INTO Album (Album_Id, Title, Description, Date_Updated, Owner_Id, Accessibility_Code) VALUES (:albumId, :albumTitle, :albumDescription, :albumDate, :userID, :accessibility) ";
            $pStmt = $myPdo->prepare($sql); 
            $pStmt->execute(array(':albumId' => $albumId , ':albumTitle' => $titleTxt, ':albumDescription' => $descriptionTxt, ':albumDate' => $date, ':userID' => $userIdTxt, ':accessibility' => $access));            
            $pStmt->commit;
            
            //view MyAlbums page(?)
            header('Location: MyAlbums.php');
            exit;
        }
    }
     
    //Clear button:
    if(isset($_POST['clear']))
    {
        $_SESSION['titleTxt'] = "";
        $_POST['descriptionTxt'] = "";
        $_POST['selectAcessibility'] = "";
    }
    
    include 'ProjectCommon/Header.php';
?>

    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 text-center">
                <h1>Create New Album</h1>
            </div>
        </div>        
        <br/>

        <h4>Welcome <b><?php print $_SESSION['nameTxt'];?></b>! (Not you? Change your session <a href="Login.php">here</a>).</h4>
        <br/>

        <form method='post' action=AddAlbum.php> 
            <div class='form-group row'>
                <div class='col-lg-2 col-md-2 col-sm-2'>
                    <label for='title' class='col-form-label'><b>Title:</b> </label>
                </div>
                <div class='col-lg-4'>
                    <input type='text' class='form-control' value='<?php print $_SESSION['titleTxt'];?>' id='titleTxt' name='titleTxt' >
                </div>
                <div class='col-lg-4' style='color:red'> <?php print $titleError;?></div>
            </div>        
        
            <div class='form-group row'>
                <div class='col-lg-2 col-md-2 col-sm-2'>
                    <label for='acessibility' class='col-form-label'><b>Acessibility:</b></label>
                </div>
                <div class='col-lg-4'>                
                <select name='selectAcessibility' class='form-control' >       
                        <option value='0'></option>;  
                        <!--printing the accessibility options coming from database -->
                        <?php   
                        $accessibilityArray = $_SESSION['accessibilityArray'];
                        foreach ($accessibilityArray as $row)
                        {   
                            echo "<option value='$row[0]' "; //atributing the value Ex: 18F
                            if ($row[0] == $_POST['selectAcessibility']) //if term coming from db is equal the one selected from user, set it as 'selected'
                            { 
                                echo "selected='selected'";
                            }
                            echo ">" . $row[1] . "</option>"; 
                        }
                        ?>         
                </select>  
                </div>  
                <div class='col-lg-3' style='color:red'> <?php print $validatorError;?></div>
            </div>
            
            <div class='form-group row'>
                <div class='col-lg-2 col-md-2 col-sm-2'>
                    <label for='description' class='col-form-label'><b>Description:</b> </label>
                </div>
                <div class='col-lg-4'> 
                    <textarea  class='form-control' id='descriptionTxt'  name='descriptionTxt' style='height:150px'><?php 
                        if(isset($_POST['descriptionTxt']) ){
                            echo $_POST['descriptionTxt'];
                        }
                        ?></textarea>
                </div>
            </div>
            <br/>
            
            <div class='row'>
                <div class="col-lg-2 col-md-0 col-sm-0"></div>
                <div class='col-lg-2 col-md-2 col-sm-2 text-left'>
                    <button type='submit' name='submit' class='btn btn-block btn-primary col-lg-2'>Submit</button>
                </div>
                <div class='col-lg-2 col-md-2 col-sm-2 text-left'>
                    <button type='submit' name='clear' class='btn btn-block btn-primary col-lg-3'>Clear</button>
                </div>
            </div> 
        </form>
        </div>
<?php
    include 'ProjectCommon/Footer.php';
?>