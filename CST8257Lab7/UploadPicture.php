<?php
    include 'ProjectCommon/Class_Picture.php';
    session_start();
    include "ProjectCommon/Functions.php";
    $error = "";

    //define constants for convenience
    define(ORIGINAL_IMAGE_DESTINATION, "Pictures/OriginalPicture"); 

    define(IMAGE_DESTINATION, "Pictures/AlbumPictures"); 
    define(IMAGE_MAX_WIDTH, 800);
    define(IMAGE_MAX_HEIGHT, 600);

    define(THUMB_DESTINATION, "Pictures/AlbumThumbnails");  
    define(THUMB_MAX_WIDTH, 100);
    define(THUMB_MAX_HEIGHT, 100);

    date_default_timezone_set("America/Toronto");

    //Use an array to hold supported image types for convenience
    $supportedImageTypes = array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG);

    if (isset($_POST['btnUpload'])) 
    {
        for($i=0; $i< sizeof($_FILES['txtUpload']['error']); $i++)//checking each picture to see if there's an error
        {
            if ($_FILES['txtUpload']['error'][$i] == 0) //if there is no error
            { 	
                $filePath = save_uploaded_file($i, ORIGINAL_IMAGE_DESTINATION); //calling function sending index and destination

                $imageDetails = getimagesize($filePath); //retrieve info from files                    
                //                    Image details:
                //                    0 - width
                //                    1 - height
                //                    2 - type of image
                //                    3 - string height and width for an img tag      

                if ($imageDetails && in_array($imageDetails[2], $supportedImageTypes))//get type of image and check if is supported
                {
                        resamplePicture($filePath, IMAGE_DESTINATION, IMAGE_MAX_WIDTH, IMAGE_MAX_HEIGHT);
                        resamplePicture($filePath, THUMB_DESTINATION, THUMB_MAX_WIDTH, THUMB_MAX_HEIGHT);
                        $error  = "The picture(s) was(were) successfully uploaded!";
                }
                else
                {
                        $error = "Uploaded file is not a supported type"; 
                        unlink($filePath);
                }
            }
            elseif ($_FILES['txtUpload']['error'][$i] == 1) //if file is too large
            {
                $error = "Upload file is too large!"; 
            }
            elseif ($_FILES['txtUpload']['error'][$i] == 4) //if there is no extension specified
            {
                $error = "No upload file specified!"; 
            }
            else
            {
                $error  = "Error happened while uploading the file. Try again later!"; //something else went wrong
            }
        }
    }    
    include 'ProjectCommon/Header.php';
?>

    <br><h1>&nbsp &nbsp &nbsp Upload Pictures</h1>
    <br><h4>&nbsp &nbsp Accepted picture types: JPG(JPEG), GIF and PNG.</h4>
    <h4>&nbsp &nbsp You can upload multiple pictures at a time by pressing the SHIFT key while selecting pictures.</h4><br>    

    <form method='post' action=UploadPicture.php enctype="multipart/form-data">  
    <span class='error'>&nbsp &nbsp &nbsp <?php echo $error;?></span>
    <br><div class='form-group row'>        
        <label for='fileUpload' class='col-lg-2 col-form-label'><b>&nbsp &nbsp &nbsp File to Upload:</b> </label>
        <div class='col-lg-4'>  
            <input type='file' class='form-control' id='fileUploadTxt' name='txtUpload[]' accept="image/*" multiple>
        </div>  
    </div> 

    <br>&nbsp &nbsp &nbsp<div class='form-group row'>                
    <button type='submit' name='btnUpload' class='btn btn-primary col-lg-1' value="upload" accept="image/*" multiple >Submit</button>
        <div class='col-lg-10 '>
        <button type='submit' name='clear' class='btn btn-primary col-lg-1 '>Clear</button>
        </div>
    </div>           
    </form>        

<?php
    include 'ProjectCommon/Footer.php';
?> 
