<?php
    include 'ProjectCommon/Class_Picture.php';
    session_start();
    include 'ProjectCommon/Functions.php';    
    $image = $_POST['image'];
    
    //define constants for convenience
    define(ORIGINAL_IMAGE_DESTINATION, "Pictures/OriginalPicture"); 
    define(IMAGE_DESTINATION, "Pictures/AlbumPictures"); 
    define(IMAGE_MAX_WIDTH, 800);
    define(IMAGE_MAX_HEIGHT, 600);
    define(THUMB_DESTINATION, "Pictures/AlbumThumbnails");  
    define(THUMB_MAX_WIDTH, 100);
    define(THUMB_MAX_HEIGHT, 100);
    date_default_timezone_set("America/Toronto"); 
    
    //reading pictures from thumbnails folder
    $pictures = array();
    $files = scandir (THUMB_DESTINATION);
    $numFiles = count ($files);
    if ($numFiles > 2 ){
        for ($i = 2; $i < $numFiles; $i++){
            $ind = strrpos(($files[$i]), "/");
            $fileName = substr ($files[$i], $ind);
            $picture = new Picture($fileName, $i);
            $pictures[$i] = $picture;
        }
    }  
      
    //image url:
    $imageFilePath = $_GET["imagesrc"];
    
    //image absolute address in localhost:
    $pictureName = 'C:/Program Files (x86)/Ampps/www/CST8257Lab7/'.substr($imageFilePath, 34);  
    
    //rotating right:
    if (isset($_GET['btnRight_x'])) 
    {     
        rotateImage($pictureName, -90);
        header("Location: MyPictures.php");
    }
    
    //rotating left:
    else if (isset($_GET['btnLeft_x']))
    {        
        rotateImage($pictureName, 90);
        header("Location: MyPictures.php");
    }
    
    //downloading:
    else if (isset($_GET['btnDownload_x']))
    {
        //name of picure insite Pictures Folder
        $nameDownload = substr($imageFilePath, 59); 
        
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.$nameDownload);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');      
        header('Pragma: public');
        header('Content-Length: '.filesize($pictureName));
        ob_clean();
        flush();
        readfile($pictureName);
        exit;        
    }
    
    //deleting:
    else if (isset($_GET['btnDelete_x']))
    {
        unlink($pictureName);
        header("Location: MyPictures.php");
        exit;
    }         
    include 'ProjectCommon/Header.php';    
?>

<br><div>
    <h1>&nbsp &nbsp &nbsp My Pictures</h1>
</div><br><br>  

<!--Original picture:-->
<div class="container" >
    <span onclick="this.parentElement.style.display='none'" class="closebtn">&times;</span>
    <img id="expandedImg" style="width:100%"  >     
    <div id="imgtext" class="wrapper"  ></div>    
    
    <!--Action buttons-->
    <div class="centered">
    <form action="MyPictures.php" method="get" >
        <input type="hidden" id="imagesrc" name="imagesrc" />
        <input type="image" name="btnLeft" src="Contents/img/btnLeft.png" width="40" height="40" value="left"/>
        &nbsp; &nbsp; <input type="image" name="btnRight" src="Contents/img/btnRight.png" width="40" height="40" value="right"/>
        &nbsp; &nbsp; <input type="image" name="btnDownload" src="Contents/img/downloadBtn.png" width="40" height="40" value="download"/>
        &nbsp; &nbsp; <input type="image" name="btnDelete" src="Contents/img/trashBtn.png" width="40" height="40" value="delete"/>
    </form> 
    </div>

</div>

<!-- Thumbnails -->
<div class='scrollmenu' style='margin:90px; width:1300px; padding:5px; '    display='inline-block' > 
 <?php
        //Glob function: returns an array of filenames or directories matching a specified pattern.         
        $filesOrigin = glob("Pictures/OriginalPicture/*.*");           
        for ($i = 0; $i < count($filesOrigin); $i++) //counting number of elements in $FilesOrigin
        {            
            $imageOrigin = $filesOrigin[$i]; //image reference for Original pictures  
            echo "<a>";
            echo "<img src='$imageOrigin' width='150' height='100' onclick='myFunction(this);'>"; 
            echo "</a>"; 
        }
    ?>
</div>

<script>
function myFunction(imgs) {
    var expandImg = document.getElementById("expandedImg");
    var imgText = document.getElementById("imgtext");
    expandImg.src = imgs.src +"?rnd="+Math.random();
    
    var imagesrc = document.getElementById("imagesrc");
    imagesrc.value = imgs.src;
    
    imgText.innerHTML = imgs.alt;
    expandImg.parentElement.style.display = "block";
}
</script>
