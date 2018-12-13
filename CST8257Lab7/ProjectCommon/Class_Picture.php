<?php

class Picture {
    private $fileName;
    private $id;
    
    public static function getPictures(){
        $pictures = array();
        $files = scandir (ALBUM-THUMBNAILS_DIR);
        $numFiles = count ($files);
        if ($numFiles > 2 ){
            for ($i = 2; $i < $numFiles; $i++){
                $ind = strrpos(($files[$i]), "/");
                $fileName = substr ($files[$i], $ind);
                $picture = new Picture($fileName, $i);
                $pictures[$i] = $picture;
            }
        }
        return $pictures;
    }
    
    public function __construct($fileName, $id){
        $this->fileName = $fileName;
        $this->id = $id;
    }
    
    public function getId(){
        return $this->id;
    }
    
    public function getName(){
        $ind = strrpos($this->fileName, ".");
        $name = substr($this->fileName, 0, $ind);
        return $name;
    }
    
    public function getAlbumFilePath(){
        return ALBUM_PICTURES_DIR."/".$this->fileName;
    }
    
    public function getThumbNailFilePath(){
        return ALBUM_THUMBNAILS_DIR."/".$this->fileName;
    }
    
    public function getOriginalFilePath(){
        return ORIGINAL_PICTURES_DIR."/".$this->fileName;
    }
       
}
?>
