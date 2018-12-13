<?php
    session_start();
    
    $friendIdTxt = htmlspecialchars($_POST["friendIdTxt"]);
    $validateError = ""; 
    $_SESSION['friendIdTxt'] = htmlspecialchars($_POST['friendIdTxt']);
    
    //only authenticated users access this page. Other than that, back to loging +
    //creating a session to make user come back here after authentitcated
     if ($_SESSION['userIdTxt'] == null)
    { 
        $_SESSION['activePage'] = "AddFriend.php";        
        header('Location: Login.php');
        exit;
    }
    
    //validators
    if(isset($_POST['sendFriendRequest']))
    {
        //Connection to DBO            
        $dbConnection = parse_ini_file("ProjectCommon/db_connection.ini");        	
        extract($dbConnection);
        $myPdo = new PDO($dsn, $user, $password); 
        //checking if ID exists in application         
        $sqlStatement = 'SELECT * FROM User WHERE UserId = :PlaceHolderUserID ';
        $pStmt = $myPdo->prepare($sqlStatement);       
        $pStmt ->execute([':PlaceHolderUserID' => $friendIdTxt]);      
        $chkAccount = $pStmt->fetch();        
        
        //user cannot send a request to someone who is already a friend
        //a) if user is a requester and invite was accepeted:
        $sqlStatement = 'SELECT * FROM friendship '
                . 'WHERE Friend_RequesterId = :resquesterId AND Friend_RequesteeId = :requesteeId AND Status = :status';
        $pStmt = $myPdo->prepare($sqlStatement);        
        $pStmt ->execute(array(':resquesterId' => $_SESSION['userIdTxt'] , ':requesteeId' => $_SESSION['friendIdTxt'], ':status' => 'accepted' ));      
        $requester = $pStmt->fetch(); 
        
        //b) if user is a requestee and invite was accepeted
        $sqlStatement = 'SELECT * FROM friendship '
                . 'WHERE Friend_RequesterId = :resquesterId AND Friend_RequesteeId = :requesteeId AND Status = :status';
        $pStmt = $myPdo->prepare($sqlStatement);        
        $pStmt ->execute(array(':resquesterId' => $_SESSION['friendIdTxt'] , ':requesteeId' => $_SESSION['userIdTxt'] , ':status' => 'accepted' ));      
        $requestee = $pStmt->fetch(); 
        
        //if user is a requestee and invite is pending:
        $sqlStatement = 'SELECT * FROM friendship '
                . 'WHERE Friend_RequesterId = :friend AND Friend_RequesteeId = :user AND Status = :status';
        $pStmt = $myPdo->prepare($sqlStatement);        
        $pStmt ->execute(array(':user' => $_SESSION['userIdTxt'] , ':friend' => $_SESSION['friendIdTxt'], ':status' => 'request' ));      
        $pending = $pStmt->fetch();  
        
        //if user is a requester and invite is pending:
        $sqlStatement = 'SELECT * FROM friendship '
                . 'WHERE Friend_RequesterId = :user AND Friend_RequesteeId = :friend AND Status = :status';
        $pStmt = $myPdo->prepare($sqlStatement);        
        $pStmt ->execute(array(':user' => $_SESSION['userIdTxt'] , ':friend' => $_SESSION['friendIdTxt'], ':status' => 'request' ));      
        $pendingFriend = $pStmt->fetch(); 
        
        //checking if this request was already sent
        if ($pendingFriend != null){
            $validateError = "You can't send this request twice. Invitation is still pending";
        }       
        else {    
            //retrieving information on requestee
            $sqlStatement = "SELECT UserId, Name FROM user WHERE UserId = :requesteeId";
            $pStmt = $myPdo->prepare($sqlStatement);        
            $pStmt ->execute([':requesteeId' => $_SESSION['friendIdTxt']]);  
            $identity = $pStmt->fetch();
            
            //if user is not in social media yet
            if ($chkAccount == null){
                $validateError = "User is not in this social media yet!";
            }       
            //user cannot send a friend request to himself/herself
            else if ($_SESSION['userIdTxt'] == $_SESSION['friendIdTxt']) {
                $validateError = "You cannot send a friend request to yourself!";
            }
            //user cannot send a request to someone who is already a friend
            else if ($requester != null || $requestee != null){
                $validateError = "This user is already your friend!";
            }
            //If A sends a friend request to B, while A has a friend request from B 
            //waiting for A to accept, A and B become friends.
            else if ($pending != null)  {
                //update requestee status
                $sqlStatement = "UPDATE friendship SET status = 'accepted' "
                    . "WHERE Friend_RequesterId = :requesteeId AND Friend_RequesteeId = :requesterId "; 
                $pStmt = $myPdo->prepare($sqlStatement);        
                $pStmt ->execute(array(':requesterId' => $_SESSION['userIdTxt'] , ':requesteeId' => $_SESSION['friendIdTxt'] ));      
                $pStmt->commit;
                //update requester status            
                $sqlStatement = "INSERT INTO friendship (Friend_RequesterId, Friend_RequesteeId, Status) "
                        . "VALUES (:requesterId, :requesteeId, :status)";
                $pStmt = $myPdo->prepare($sqlStatement);        
                $pStmt ->execute(array(':requesterId' => $_SESSION['userIdTxt'] , ':requesteeId' => $_SESSION['friendIdTxt'], ':status' => 'accepted' ));      
                $pStmt->commit;    
                $validateError = "You and  ". $identity[1] . " (ID:" . $identity[0] . ") are now friends.";        
            }          
            //sending the invitation which will be pending, until accepted by new friend
            else { 
                //inserting friendship into table
                $sqlStatement = "INSERT INTO friendship (Friend_RequesterId, Friend_RequesteeId, Status) "
                        . "VALUES (:requesterId, :requesteeId, :status)";
                $pStmt = $myPdo->prepare($sqlStatement);        
                $pStmt ->execute(array(':requesterId' => $_SESSION['userIdTxt'] , ':requesteeId' => $_SESSION['friendIdTxt'], ':status' => 'request' ));      
                $pStmt->commit;     
                //confirmation message
                $validateError = "Your request was sent to ". $identity[1] . " (ID:" . $identity[0] . "). "
                        . "<br>" . "&nbsp &nbsp &nbsp" ."Once " . $identity[1] . " accepts your request, you and ". $identity[1] . " will be friends "
                        . "and will be able to see each others' shared albums.";
            }  
        }
    }        
    include 'ProjectCommon/Header.php';
?>
 
    <br><h1>&nbsp &nbsp Add Friend</h1>  
    <br><h4>&nbsp &nbsp Welcome <b><?php print $_SESSION['nameTxt'];?></b>! (Not you? Change your session <a href="Login.php">here</a>)</h4>
    <h4>&nbsp &nbsp Enter the ID of the user you want to be friends with:</h4>

    <form method='post' action=AddFriend.php>             
    <br><br><div class="row">
        <div class="col-lg-1" >
            <label for='friendId' class='col-form-label'><b>&nbsp &nbsp &nbsp ID:</b> </label>
        </div>
        <div class="col-lg-3" >
            <input type='text' class='form-control' id='friendIdTxt' name='friendIdTxt' value='<?php print $_SESSION['friendIdTxt']; ?>' >
        </div> 
        <div class="col-lg-5" >
            <button type='submit' name='sendFriendRequest' class='btn btn-primary'>Send Friend Request</button>
        </div>
        <br><div class='col-lg-10' style='color:red'>&nbsp &nbsp &nbsp <?php print $validateError;?></div>
    </div>
    </form>
        
<?php 
    include 'ProjectCommon/Footer.php';
?>