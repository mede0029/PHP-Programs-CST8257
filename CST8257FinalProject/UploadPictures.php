<?php
    session_start();
    include 'ProjectCommon/Functions.php';
    include 'ProjectCommon/ImageHandler.php';
    include 'ProjectCommon/Header.php';
    include_once 'ProjectCommon/ConstantsAndSettings.php';

    //only authenticated users can access this page. Others are redirected to the login page
    //updates the session so the user can come back to this page after authentication
    if ($_SESSION['userIdTxt'] == null)
    { 
        $_SESSION['activePage'] = "UploadPictures.php";        
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

    //Clear button:
    if(isset($_POST['clear'])){
        $_POST['uploadAlbum'] = $_POST['descriptionTxt'] = $_POST['albumTitleTxt'] = "";
    } else if(isset($_POST['submit'])){
        //check if there is an album
        $uploadAlbumError = ValidateBlankAlbum($_POST['uploadAlbum']);
        
        if(isset($_POST['uploadAlbum'])){
            extract($_POST);
            $date = date("Y/m/d");
            $fileError = ValidateFileUpload($_FILES, 'fileUpload');

            if ($fileError == ""){ //files are valid to upload
                $total = count($_FILES['fileUpload']['name']);
                //inserts picture reference in DB
                $iSql = "INSERT INTO picture(album_id, fileName, title, description, date_added) "
                       ."VALUES(:albumId, :fileName, :title, :description, :date_added)";
                
                $uSql = "UPDATE album set date_updated = :dateUpdated WHERE album_id = :albumId";

                try{ 
                    for ($i=0; $i < $total; $i++) {
                        //gets file extension
                        $ext = pathinfo($_FILES['fileUpload']['name'][$i], PATHINFO_EXTENSION);
                        $pStmt = $myPdo->prepare($iSql);
                        $pStmt->execute(array(
                            ':albumId' => $uploadAlbum,
                            ':fileName' => $ext,
                            ':title' => $albumTitleTxt,
                            ':description' => $descriptionTxt,
                            ':date_added' => $date));

                        //get picture_id that was saved in DB to use as file name (to avoid overwriting files)
                        $pic_id = $myPdo->lastInsertId(); 
    
                        $filePath = save_uploaded_file(ORIGINAL_PICTURES_DIR, $_FILES['fileUpload'], $i, $pic_id);
        
                        $imageDetails = getimagesize($filePath);
                        
                        if ($imageDetails && in_array($imageDetails[2], $supportedImageTypes)){
                            resamplePicture($filePath, ALBUM_PICTURES_DIR, IMAGE_MAX_WIDTH, IMAGE_MAX_HEIGHT);
                            
                            resamplePicture($filePath, ALBUM_THUMBNAILS_DIR, THUMB_MAX_WIDTH, THUMB_MAX_HEIGHT);

                            $pStmt = $myPdo->prepare($uSql);
                            $pStmt->execute(array(
                                ':albumId' => $uploadAlbum,
                                ':dateUpdated' => $date));
                        } else {
                            $error = "Uploaded file is not a supported type"; 
                            unlink($filePath);
                            $pStmt->rollback;
                        }
                        $pStmt->commit;
                    }
                } catch(PDOException $e) {
                    $fileError = $e->getMessage();
                }
                exit(header('Location: UploadPictures.php'));
            }
        }
    }

?>
    <div class="container-fluid">
        <h1>Upload Pictures</h1>
        <br><h4>Accepted picture types: JPG(JPEG), GIF and PNG.</h4>
        <h4>You can upload multiple pictures at a time by pressing the SHIFT key while selecting pictures.</h4>
        <h4>When uploading multiple pictures, the title and description fields will be applied to all pictures.</h4>
        <br><br>
        <form action=UploadPictures.php method="post" enctype="multipart/form-data">
            <div class='form-group row'>
                <div class='col-lg-1 col-md-2 col-sm-3 col-xs-3'>
                    <label for='uploadAlbum' class='col-form-label'><b>Upload To Album:</b> </label>
                </div>
                <div class='col-lg-4 col-md-6 col-sm-8 col-xs-8'>
                    <select name='uploadAlbum' class='form-control'>
                        <?php
                            foreach($albums as $row){
                                echo "<option value='$row[0]' ";
                                if ($row[0] == $_POST['uploadAlbum']){                         
                                    echo "selected='selected'";
                                }
                                echo ">" . $row[1] . "</option>";
                            }
                        ?>
                    </select>
                </div>
                <div class='col-lg-4 col-md-2 col-sm-4' style='color:red'>
                    <?php echo $uploadAlbumError; ?>
                </div>
            </div> 
        
            <div class='form-group row'>
                <div class='col-lg-1 col-md-2 col-sm-3 col-xs-3'>
                    <label for='fileUpload' class='col-form-label'><b>File to Upload:</b> </label>
                </div>
                <div class='col-lg-4 col-md-6 col-sm-8 col-xs-8'>
                    <input type='file' class='form-control' id='fileUpload' name='fileUpload[]' accept="image/gif, image/jpeg, image/png" multiple="multiple">
                </div>
                <div class='col-lg-4 col-md-2 col-sm-4' style='color:red'>
                    <?php echo $fileError; ?>
                </div>
            </div> 
            
            <div class='form-group row'>
                <div class='col-lg-1 col-md-2 col-sm-3 col-xs-3'>
                    <label for='albumTitle' class='col-form-label'><b>Title:</b> </label>
                </div>
                <div class='col-lg-4 col-md-6 col-sm-8 col-xs-8'>
                    <input type='text' class='form-control' id='albumTitleTxt' name='albumTitleTxt' 
                    value=<?php if (isset($_POST['albumTitleTxt'])){
                            echo $_POST['albumTitleTxt'];
                        }?>>
                </div>            
            </div> 
            
            <div class='form-group row'>
                <div class='col-lg-1 col-md-2 col-sm-3 col-xs-3'>
                    <label for='description' class='col-form-label'><b>Description:</b> </label>
                </div>
                <div class='col-lg-4 col-md-6 col-sm-8 col-xs-8'>
                    <textarea  class='form-control' id='descriptionTxt'  name='descriptionTxt' style='height:150px'><?php
                        if (isset($_POST['descriptionTxt'])){
                            echo $_POST['descriptionTxt'];
                        }
                    ?></textarea></div>
            </div>
            <br> 
        
            <div class='row'>
                <div class="col-lg-1 col-md-2 col-sm-3 col-xs-3"></div>
                <div class='col-lg-2 col-md-2 col-sm-2 col-xs-4 text-left'>
                    <button type='submit' name='submit' class='btn btn-block btn-primary'>Submit</button>
                </div>
                <div class='col-lg-2 col-md-2 col-sm-2 col-xs-4 text-left'>
                    <button type='submit' name='clear' class='btn btn-block btn-primary'>Clear</button>
                </div>
            </div>
        </form>
    </div>
<?php
    include 'ProjectCommon/Footer.php';
?>
