<?php
    session_start();
    $validatorError = "";
    
    //only authenticated users access this page. Other than that, back to loging +
    //creating a session to make user come back here after authentitcated
     if ($_SESSION['userIdTxt'] == null)
    { 
        $_SESSION['activePage'] = "MyFriends.php";        
        exit(header('Location: Login.php'));
    }
    
    //Connection to DBO            
    $dbConnection = parse_ini_file("ProjectCommon/db_connection.ini");        	
    extract($dbConnection);
    $myPdo = new PDO($dsn, $user, $password);  
    
    //Checking friends per user
    //getting a list of userId's where friendshipstatus = accepted
    $sql = "SELECT friendship.Friend_RequesterId, friendship.Friend_RequesteeId FROM friendship "
            . "WHERE (Friend_RequesterId = :userId OR Friend_RequesteeId = :userId) AND Status = 'accepted' ";
    $pStmt = $myPdo->prepare($sql);
    $pStmt->execute ( [':userId' => $_SESSION['userIdTxt'] ]);
    $friendsByUser = $pStmt->fetchAll();
    
    //sending userId's to $friendIdArray
    $friendIdArray = array();
    foreach ($friendsByUser as $row){
        if ($row[0] != $_SESSION['userIdTxt'] && (!in_array($row[0], $friendIdArray))){
            array_push($friendIdArray, $row[0]);
        }
        if ($row[1] != $_SESSION['userIdTxt'] && (!in_array($row[1], $friendIdArray))){
            array_push($friendIdArray, $row[1]);
        }
    }
    
    //Defriend button:    
    if(isset($_POST['defriendBtn'])){
        if (isset($_POST['defriend'])){
            foreach ($_POST['defriend'] as $row) //iterate and look for what was selected
            {
                //for each selected line, delete the corresponding friend from friends' list
                $sql = "DELETE FROM friendship "
                        . "WHERE (friendship.Friend_RequesterId = :userId AND friendship.Friend_RequesteeId = :friendId) "
                        . "OR (friendship.Friend_RequesterId = :friendId AND friendship.Friend_RequesteeId = :userId)"; 
                $pStmt = $myPdo->prepare($sql);
                $pStmt->execute(array(':userId' => $_SESSION['userIdTxt'], ':friendId' => $row)); 
                $pStmt->commit;                 
            }
            header('Location: MyFriends.php'); //redirect to update table view
            exit;             
        }
        else 
        {
            $validatorError = "You must select at least one checkbox!"; //at least one checkbox must be selected
        }          
    }
    
    //Accept Selected Button
    if (isset($_POST['acceptBtn'])){
        if (isset($_POST['acceptDeny'])){
            foreach ($_POST['acceptDeny'] as $row){
            //update requestee status to accepted
            $sqlStatement = "UPDATE friendship SET status = 'accepted' "
                . "WHERE Friend_RequesterId = :requesteeId AND Friend_RequesteeId = :requesterId "; 
            $pStmt = $myPdo->prepare($sqlStatement);        
            $pStmt ->execute(array(':requesterId' => $_SESSION['userIdTxt'] , ':requesteeId' => $row ));      
            $pStmt->commit;
            
            //insert accepted status for main user         
            $sqlStatement = "INSERT INTO friendship (Friend_RequesterId, Friend_RequesteeId, Status) "
                    . "VALUES (:requesterId, :requesteeId, :status)";
            $pStmt = $myPdo->prepare($sqlStatement);        
            $pStmt ->execute(array(':requesterId' => $_SESSION['userIdTxt'] , ':requesteeId' => $row, ':status' => 'accepted' ));      
            $pStmt->commit;                                
            }
            header('Location: MyFriends.php'); //redirect to update table view
            exit; 
        }
        else 
        {
            $validatorError = "You must select at least one checkbox!"; //at least one checkbox must be selected
        }   
    }
    
    //Deny Selected Button
    if (isset($_POST['denyBtn'])){
        if (isset($_POST['acceptDeny'])){
            foreach ($_POST['acceptDeny'] as $row){
                //delete request(pending) statement from database
                $sqlStatement = "DELETE FROM friendship "
                        . "WHERE friendship.Friend_RequesterId = :requesterId "
                        . "AND friendship.Friend_RequesteeId = :requesteeId ";
                $pStmt = $myPdo->prepare($sqlStatement);        
                $pStmt ->execute(array(':requesteeId' => $_SESSION['userIdTxt'] , ':requesterId' => $row ));      
                $pStmt->commit;  
            }
            header('Location: MyFriends.php'); //redirect to update table view
            exit;            
        }
        else 
        {
            $validatorError = "You must select at least one checkbox!"; //at least one checkbox must be selected
        }   
    }
     
    include 'ProjectCommon/Header.php';
?>
    <div class="container-fluid">
        <br>
        <h1>My Friends</h1>
        <br>
        <h4>Welcome <b><?php print $_SESSION['nameTxt'];?></b>! (Not you? Change your session <a href="Login.php">here</a>)</h4>
        <br><br>
        <form method='post' action=MyFriends.php> 
            <!--First table: FRIENDS-->
            <table class="table">
            <!-- display table header -->
            <thead>
                <tr>
                    <th scope="col">Friends:</th>
                    <th scope="col"></th>
                    <th scope="col"><a href="AddFriend.php">Add Friends</a></th>                                                                             
                </tr>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Shared Albums</th>
                    <th scope="col">Defriend</th>                                                                             
                </tr>
            </thead>   

            <!-- display table body -->             
            <div class='col-lg-4' style='color:red'> <?php print $validatorError;?></div><br>
            <tbody>
            <?php   
            foreach ($friendIdArray as $row){
                $sql="SELECT user.UserId, user.Name, album.Accessibility_Code "
                        . "FROM user LEFT JOIN album ON album.Owner_Id = user.UserId "
                        . "WHERE user.UserId = :userId "
                        . "ORDER BY user.UserId ";
                $pStmt = $myPdo->prepare($sql);
                $pStmt->execute ([ ':userId' => $row ]);
                $sharedAlbums = $pStmt->fetchAll(); 
                $albumCount = 0;
                foreach ($sharedAlbums as $albums)
                {
                    if ($albums[2] == "shared")
                    {
                        $albumCount = $albumCount + 1;   
                    }   
                }    
                    echo "<tr>";
                    echo "<td scope='col'><a href='FriendPictures.php?id=".$albums[0]."'>".$albums[1]."</a></td>"; // Name
                    echo "<td scope='col'>".$albumCount."</td>"; // Shared albums
                    echo "<td scope='col'><input type='checkbox' name='defriend[]' value='$albums[0]'/></td>"; // Defriend            
                    echo "</tr>";           
            }
            ?>              
        </tbody>
        </table>

        <!--Defriend button:-->
        <div class='form-group row'>               
            <label for='' class='col-lg-7 col-form-label'><b></b> </label>            
            <div class='col-lg-3'>                    
            <button type='submit' name='defriendBtn' class='btn btn-primary col-lg-5' onclick='return confirm("The selected friend will be defriended!")'>Defriend Selected</button>  
            </div> 
        </div>     

        <!--Second table: REQUESTS -->
            <br><br><table class="table">
            <!-- display table header -->
            <thead>
                <tr>
                    <th scope="col">Friend Requests:</th>
                    <th scope="col"></th>                                                                             
                </tr>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Accept or Deny</th>                                                                             
                </tr>
            </thead>               
            <!--example for table body - MUST BE TWEAKED TO BRING VALUES FROM DATABASE -->             
            <tbody>
            <?php
            //getting a list of userId's where friendshipstatus = requested
            $sql = "SELECT user.UserId, user.Name FROM user "
                    . "INNER JOIN friendship ON friendship.Friend_RequesterId = user.UserId "
                    . "WHERE friendship.Status = 'request' AND friendship.Friend_RequesteeId = :userId ";        
            $pStmt = $myPdo->prepare($sql);
            $pStmt->execute ( [':userId' => $_SESSION['userIdTxt'] ]);
            $requestFriend = $pStmt->fetchAll();
            foreach ($requestFriend as $friendName)
            {
                echo "<tr>";
                echo "<td scope='col'>".$friendName[1]."</td>"; // Name
                echo "<td scope='col'><input type='checkbox' name='acceptDeny[]' value='$friendName[0]' /></td>"; // Accept or deny            
                echo "</tr>";
            }            
            ?>   
            </tbody>
        </table>    

        <!--Accept/Deny buttons-->    
        <div class='form-group row'>               
            <label for='' class='col-lg-5 col-form-label'><b></b> </label>            
            <div class='col-lg-7'>                    
            <button type='submit' name='acceptBtn' class='btn btn-primary col-lg-2'>Accept Selected</button>  
                <div class='col-lg-3'>                    
                    <button type='submit' name='denyBtn' class='btn btn-primary ' onclick='return confirm("The selected request will be denied!")'>Deny Selected</button>
                </div> 
            </div> 
        </div>
        </form> 
    </div>
<?php
    include 'ProjectCommon/Footer.php';
?>