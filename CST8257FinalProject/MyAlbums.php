<?php
    include 'ProjectCommon/Class_Lib.php';
    session_start();
    include 'ProjectCommon/Functions.php';
    
    $userIdTxt = $_SESSION['userIdTxt'];
    $accessibilityArray = $_SESSION['accessibilityArray'];
    
    //only authenticated users access this page. Other than that, back to loging +
    //creating a session to make user come back here after authentitcated
     if ($_SESSION['userIdTxt'] == null)
    { 
        $_SESSION['activePage'] = "MyAlbums.php";        
        exit(header('Location: Login.php'));
    }
    $dbConnection = parse_ini_file("ProjectCommon/db_connection.ini");        	
    extract($dbConnection);
    $myPdo = new PDO($dsn, $user, $password);
    
    if ($_GET['action']== 'delete' && isset($_GET['id'])){
        $albumID = $_GET['id'];
        $dbConnection = parse_ini_file("ProjectCommon/db_connection.ini");
        extract($dbConnection);  

        $deletePictures = "DELETE FROM picture WHERE Album_Id = :albumID";            
        $stmt = $myPdo->prepare($deletePictures);

        $delalbum = "DELETE FROM album WHERE album.Album_Id = :albumID";
        $stmt1 = $myPdo->prepare($delalbum);
        $stmt->execute([albumID => $albumID]);
        $stmt1->execute([':albumID' => $albumID]);
        //$stmt1->commit;
    }            
    //Checking albums per user and number of pictures per album
    $sql = "SELECT a.Title, a.Date_Updated, ac.Description, a.Album_Id, COALESCE(pictures, 0) number_pictures "
            . "FROM album a "
            . "LEFT JOIN (SELECT count(*) as pictures, Album_Id FROM picture GROUP BY Album_Id) p ON a.Album_Id = p.Album_Id "
            . "INNER JOIN accessibility ac ON ac.Accessibility_Code = a.Accessibility_Code "
            . "WHERE a.Owner_Id = :userId ORDER BY a.Title";

    $pStmt = $myPdo->prepare($sql);
    $pStmt->execute ( [':userId' => $userIdTxt] );
    $albumByUser = $pStmt->fetchAll();
    
    //Retrieving all acessibility options coming from database 
    $sql = "SELECT * FROM accessibility ";    
    $pStmt = $myPdo->prepare($sql); 
    $pStmt->execute();
    
    //Put each record into an array
    $accessibilityArray = null;     //setting array to empty at first
    foreach ($pStmt as $row)
    {
        $accessibility = array( $row['Accessibility_Code'], $row['Description'] ); 
        $accessibilityArray[] = $accessibility;
    }
    $_SESSION['accessibilityArray'] = $accessibilityArray;      //session with all semesters from database       
    
    if(isset($_POST['submit'])){
        if(isset($_POST['selectAcessibility'])){
            $sql = "UPDATE album SET Accessibility_Code = :access_code WHERE Album_Id = :album_id";
            $options = $_POST['selectAcessibility'];
            // for each item in selectAcessibility array (key = album id, value = accessibility code)
            for ($i=0; $i < count($options); $i++) {
                $albumByUser[$i][2] = $options[$i];
                $pStmt = $myPdo->prepare($sql);
                $pStmt->execute(array(':access_code' => $albumByUser[$i][2], ':album_id' => $albumByUser[$i][3]));
            }
            $pStmt->commit;
            exit(header('Location: MyAlbums.php')); //refreshes page to get the current value
        }
    }

    include 'ProjectCommon/Header.php';         
?>
    <div class="container-fluid">
        <br>
        <h1>My Albums</h1>
        <br>
        <h4>Welcome <b><?php print $_SESSION['nameTxt'];?></b>! (Not you? Change your session <a href="Login.php">here</a>)</h4>

        <form method='post' action='MyAlbums.php'>
            <div class='col-lg-4' style='color:red'> <?php print $validatorError;?></div>
            <br><br><br>
            <div class='row'>               
                <div class='col-lg-10 col-md-9 col-sm-9 col-xs-7'></div>
                <div class='col-lg-2 col-md-3 col-sm-3 col-xs-5'>
                    <b><a href="AddAlbum.php">Create a New Album</a></b>
                </div>
            </div>  
            <table class="table">
                <!-- display table header -->
                <thead>
                    <tr>
                        <th scope="col">Title</th>
                        <th scope="col">Date Updated</th>
                        <th scope="col">Number of Pictures</th>
                        <th scope="col">Accessibility</th>
                        <th scope="col"></th>                                                                              
                    </tr>
                </thead>   

                <tbody>
                    <?php
                    foreach ($albumByUser as $var)
                    {
                        echo "<tr>";
                        echo '<td scope="col"><a href="MyPictures.php?action=album&id='.$var[3].'">'.$var[0].'</a></td>'; // Title
                        echo "<td scope='col'>".$var[1]."</td>"; // Date Updated
                        echo "<td scope='col'>". $var[4] . "</td>"; // Number of pictures
                        //displaying accessibility dropdown menu for each album
                        echo "<td scope='col'><select name='selectAcessibility[]' class='form-control' >  ";
                        foreach ($accessibilityArray as $row)
                        {   
                            echo "<option value='$row[0]' "; //accessibility description 
                            if ($row[1] == $var[2]) //if description from dropdown equals to description from database
                                { 
                                    echo "selected='selected'"; //select this description
                                }
                            echo ">" . $row[1] . "</option>"; //display description text
                        }          
                        echo "</select>";
                        echo "<td scope='col'><a href='MyAlbums.php?action=delete&id=$var[3]' onclick='return myFunctionDelete()'/a>Delete</td>"; // delete button
                        echo "</tr>";
                    }                              
                    ?>
                </tbody>
            </table>

            <br>
            <div class='row'>               
                <div class='col-lg-9 col-md-9 col-sm-8 col-xs-6'></div>
                <div class='col-lg-2 col-md-2 col-sm-3 col-xs-6'>
                    <button type='submit' name='submit' class='btn btn-block btn-primary'>Save Changes</button>  
                </div> 
            </div>  
    </form>   
    </div>
<?php
    include 'ProjectCommon/Footer.php';
?>
 <script>
    function myFunctionDelete() 
    {
        if(confirm("The selected album and its pictures will be deleted!"))
        {
            return true;
        }
        else
        {
            return false; 
        }      
    }
</script>