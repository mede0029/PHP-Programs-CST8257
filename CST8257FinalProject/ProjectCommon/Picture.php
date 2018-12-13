<?php

class Picture{
    private $fileName;
    private $id;
    private $description;
    private $title;
    private $comments;

    public static function getPictures($Pdo, $albumId){
        //get list of pics that belong to an album from DB and store in an array
        $sql = "SELECT picture_id, CONCAT(picture_id, '.', fileName) AS fileName, description, title FROM picture WHERE album_id = :album_id";
        $pStmt = $Pdo->prepare($sql);
        $pStmt->execute(array(album_id => $albumId));
        $pic_info = $pStmt->fetchAll();

        $pictures = array();
        
        $numFiles = count($pic_info);
        foreach($pic_info as $pic){
            $picture = new Picture($pic[1], $pic[0], $pic[2], $pic[3]);
            array_push($pictures, $picture);
        }
        return $pictures;
    }

    public function __construct($fileName, $id, $description, $title){
        $this->fileName = $fileName;
        $this->id = $id;
        $this->description = $description;
        $this->title = $title;
    }

    public function getId(){
        return $this->id;
    }

    public function getName(){
        return $this->fileName;
    }

    public function getDescription(){
        return $this->description;
    }

    public function getTitle(){
        return $this->title;
    }

    public function getComments($Pdo){
        if(!$this->comments){
            //get list of pics that belong to an album from DB and store in an array
            $sql = "SELECT c.Comment_Text, u.Name, DATE_FORMAT(c.Date, '%Y-%m-%d') "
                   ."FROM comment c "
                   ."INNER JOIN user u ON c.Author_Id = u.UserId "
                   ."WHERE c.Picture_Id = :pictureId "
                   ."ORDER by c.Date DESC";
            $pStmt = $Pdo->prepare($sql);
            $pStmt->execute(array(pictureId => $this->getId()));
            $this->comments = $pStmt->fetchAll();
        }
        return $this->comments;
    }

    public function getAlbumFilePath(){
        return ALBUM_PICTURES_DIR."/".$this->fileName;
    }

    public function getThumbnailFilePath(){
        return ALBUM_THUMBNAILS_DIR."/".$this->fileName;
    }

    public function getOriginalFilePath(){
        return ORIGINAL_PICTURES_DIR."/".$this->fileName;
    }

    public function rotatePicture($value){
        rotateImage($this->getAlbumFilePath(), $value);
        rotateImage($this->getThumbnailFilePath(), $value);
        rotateImage($this->getOriginalFilePath(), $value);
    }

    public function downloadFile(){
        $file = $this->getOriginalFilePath();
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        ob_clean();
        flush();
        readfile($file);
        flush();
    }
    
    public function deleteFile($Pdo){
        $returnMessage = "";
        try{
            //deletes picture comments if there's any
            $sql = "DELETE FROM comment where picture_id = :pictureId ";
            $pStmt = $Pdo->prepare($sql);
            $pStmt->execute(array(pictureId => $this->getId()));
            
            //deletes picture information
            $sql = "DELETE FROM picture where picture_id = :pictureId ";
            $pStmt = $Pdo->prepare($sql);
            $pStmt->execute(array(pictureId => $this->getId()));
            // if Deleted pic info sucessfully
            //deletes files from albums
            unlink($this->getAlbumFilePath());
            unlink($this->getThumbnailFilePath());
            unlink($this->getOriginalFilePath());
        } catch(PDOException $e){
            $returnMessage = $e->getMessage();
        }
        return $returnMessage;
    }

}