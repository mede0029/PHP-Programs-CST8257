<?php
    session_start();
    include_once 'ProjectCommon/ConstantsAndSettings.php';
    include 'ProjectCommon/Header.php';
    include 'ProjectCommon/ImageHandler.php';
    include 'ProjectCommon/Picture.php';

    //only authenticated users can access this page. Others are redirected to the login page
    //updates the session so the user can come back to this page after authentication
    if ($_SESSION['userIdTxt'] == null)
    { 
        $_SESSION['activePage'] = "MyPictures.php";        
        exit(header('Location: Login.php'));
    }
    $userIdTxt = $_SESSION['userIdTxt'];

    $dbConnection = parse_ini_file("ProjectCommon/db_connection.ini");        	
    extract($dbConnection);
    $myPdo = new PDO($dsn, $user, $password);
    //to throw error messages to the user
    $myPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //Retrieves available album options from database
    $sql = "SELECT album_id, title FROM album WHERE album.Owner_Id = :userID ";
    $pStmt = $myPdo->prepare($sql);
    $pStmt->execute(array(userID => $userIdTxt));
    $albums = $pStmt->fetchAll();
    
    if(count($albums) > 0){

        $selectAlbum = $albums[0][0]; //initial selection
        if(isset($_POST['selectAlbum'])){
            $selectAlbum = $_POST['selectAlbum']; //change selection (from dropdown change)
        } 

        //when users navigate from My Albums or to refresh after a comment
        if(isset($_GET['action'])){
            if($_GET['action'] == 'album'){
                $selectAlbum = $_GET['id'];
            } else if($_GET['action'] == 'picture'){
                $selectAlbum = $_GET['id'];
                $selected_img_id = $_GET['pic'];
            }
        } 

        $imgs = Picture::getPictures($myPdo, $selectAlbum);
        $idx = 0; //initial selection
        
        if(!empty($imgs)){

            if(isset($_POST['selectedImage'])){
                $selected_img_id = intval($_POST['selectedImage']);
            }

            if($selected_img_id != ""){
                $size = count($imgs);
                //get the array id based on the picture Id
                for ($i=0; $i < $size; $i++) {
                    if($imgs[$i]->getId() == $selected_img_id){
                        $idx = $i;
                        break;
                    }
                }
            }
            
            if(isset($_POST['addComment'])){
                if($_POST['commentTxt']!= ""){
                    //inserts picture comment in DB
                    try{
                        $sql = "INSERT INTO comment(Author_Id, Picture_Id, Comment_Text, Date) "
                            ."VALUES (:userId, :pictureId, :commentTxt, NOW())";
                        $pStmt = $myPdo->prepare($sql);
                        $pStmt->execute(array(
                            ':userId' => $userIdTxt,
                            ':pictureId' => $selected_img_id,
                            ':commentTxt' => $_POST['commentTxt']));
                        $pStmt->commit;
                        exit(header('Location: MyPictures.php?action=picture&id='.$selectAlbum.'&pic='.$selected_img_id));
                    } catch(PDOException $e) {
                        $commentError = $e->getMessage();
                    }
                }else{
                    $commentError = "Comment cannot be blank!";
                }
            } else if(isset($_POST['action'])){
                //Rotate, downloads or deletes the selected Image, according to the informed action
                
                switch ($_POST['action']) {
                    case 'rotateLeft':
                        $imgs[$idx]->rotatePicture(90);
                        break;
                    case 'rotateRight':
                        $imgs[$idx]->rotatePicture(-90);
                        break;
                    case 'download':
                        $file = $imgs[$idx]->downloadFile();
                        break;
                    case 'delete':
                        $commentError = $imgs[$idx]->deleteFile($myPdo);
                        if($commentError == ""){ //successfully deleted the file
                            exit(header('Location: MyPictures.php?action=album&id='.$selectAlbum));
                        }
                        break;
                }
            }
            //gets the file path to display as main picture
            $imageFilePath = $imgs[$idx]->getAlbumFilePath();
            $selected_img_id = $imgs[$idx]->getId();
        }

?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h1>My Pictures</h1>
                <br/>
            </div>
        </div>
        <form action=MyPictures.php method="post">
            <div class="row">
                <div class="col-lg-1 col-md-2"></div>
                <div class='col-lg-5 col-md-5'>
                    <select name='selectAlbum' class='form-control' onchange="this.form.submit()">
                        <?php
                            foreach($albums as $row){
                                echo "<option value='$row[0]' ";
                                //if ($row[0] == $_POST['selectAlbum']){
                                if ($row[0] == $selectAlbum){
                                    echo "selected='selected'";
                                }
                                echo ">" . $row[1] . "</option>";
                            }
                        ?>
                    </select>
                </div>
            </div>
            <?php
                if(!empty($imgs)){
            ?>
                <div class="row">
                    <div class="col-lg-3 col-md-3"></div>
                    <div class='col-lg-4 col-md-4'>
                        <h2><?php echo $imgs[$idx]->getTitle();?></h2>
                    </div>
                </div>
            <br/>
            <div class="container-fluid">
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1"></div>
                    <div class="img-container col-lg-10 col-md-10 col-sm-10 col-xs-10">
                        <img src="<?php echo $imageFilePath;?>" />
                        <div class="menu">
                            <button type="submit" name="action" class="btn-glyph" value="rotateLeft">
                                <i class="glyphicon glyphicon-repeat gly-flip-horizontal"></i>
                            </button>
                            <button type="submit" name="action" class="btn-glyph" value="rotateRight">
                                <span class="glyphicon glyphicon-repeat"></span>
                            </button>
                            <button type="submit" name="action" class="btn-glyph" value="download">
                                <span class="glyphicon glyphicon-download-alt"></span>
                            </button>
                            <button type="submit" name="action" class="btn-glyph" value="delete">
                                <span class="glyphicon glyphicon-trash"></span>
                            </button>
                        </div>
                    </div>
                    <div class="thumbnails" >
                        <div class="col-lg-10 col-md-12 col-sm-12 col-xs-12" style="overflow-x: auto; white-space: nowrap;">
                            <?php
                                foreach ($imgs as $img) {
                            ?>
                                    <img src=<?php echo $img->getThumbnailFilePath();?>
                                    name="imgThumbnail"
                                    id=<?php echo $img->getId();
                                    if($img->getId() == $selected_img_id){ //highlight selected image
                                        echo' style="border: 3px solid blue;"';
                                    }
                                    ?> style="padding: 5px; white-space: nowrap;">
                            <?php
                                }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 side-comments">
                    <div class="comments-list">
                    <?php
                        if($imgs[$idx]->getDescription()){
                            echo"<b>Description:</b>";
                            echo "<p>".$imgs[$idx]->getDescription()."</p>";
                        }
                        $comments = $imgs[$idx]->getComments($myPdo);
                        if(count($comments)>0){
                            echo"<b>Comments:</b>";
                            foreach($comments as $comment){
                                echo'<p><i style="color: blue">'.$comment[1].' ('
                                    .$comment[2].'):</i> '.$comment[0].'</p>';
                            }
                        }
                    ?>
                    </div>
                    <br/>
                    <div class='form-group row'>
                        <div class='col-lg-11 col-md-11 col-sm-11 col-xs-11'>
                        <textarea  class='form-control' id='commentTxt'
                            name='commentTxt' placeholder="Leave Comment..."
                            style='height:150px'><?php
                            if (isset($_POST['descriptionTxt'])){
                                echo $_POST['descriptionTxt'];
                            }
                        ?></textarea></div>
                    </div>
                    <div class='row'>
                        <div class='col-lg-6 col-md-8 col-sm-12 col-xs-12 text-left'>
                            <button type='submit' name='addComment' class='btn btn-block btn-primary'>Add Comment</button>
                        </div>
                        <div class='col-lg-6 col-md-12 col-sm-12 col-xs-12 text-left' style="color: red;"><?php echo $commentError;?></div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="selectedImage" 
                value="<?php echo $imgs[$idx]->getId(); ?>" /> 
        </form>
    </div>
<?php
        } else{ //If there is no pictures associated with this album
?>
        </form>
        <div class="row">
            <div class="col-lg-12 text-center">
                <h4>This album does not have any pictures yet. Click <a href="UploadPictures.php">here</a> to Upload Pictures.</h4>

            </div>
        </div>
    </div>
<?php       
        }
    } else{ //If there is no album yet
?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h1>My Pictures</h1>
                <br/>
            </div>
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h4>You don't have an album yet. Click <a href="AddAlbum.php">here</a> to Create a new Album</h4>
                </div>
            </div>
        </div>
<?php
    }
    include 'ProjectCommon/Footer.php';
?>