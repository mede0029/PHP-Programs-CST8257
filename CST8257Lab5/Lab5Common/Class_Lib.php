<?php

class Class_Lib {

}

class Book {
    public $id;
    public $title;
    public $price; 

//    function getId() {
//        return $this->id;
//    }
//
//    function getTitle() {
//        return $this->title;
//    }
//
//    function getPrice() {
//        return $this->price;
//    }
//    function __construct($id, $title, $price) {
//        $this->id = $id;
//        $this->title = $title;
//        $this->price = $price;
//    }
//    
//    function setTitle($title) {
//        $this->title = $title;
//    }
//
//    function setPrice($price) {
//        $this->price = $price;
//    }
//    
//    on the code:
//    $book->getTitle();
}

class BookFile extends SplFileObject {
   
    public function __construct($filePath)
    {
           parent::__construct($filePath);
    }
    
    
    public function getBooks()
    {  
        $newArray = array();
        foreach ($this as $book) //looping through txt file to create book objects and add to newArray
        {                    
            //REGEX:
            // \ used for special characters
            // ? 0 or 1
            // * 0 or more
            // + 1 or more
            // ^start of string
            // website: regex101.com for testing  
            
            $aBook = new Book; //creating a new Book object
            
            //getting book ID:
            $bookIdRegex = "/(^BK\d+)\s+/";
            $idRegex = preg_match($bookIdRegex, $book, $matches);
            if ($idRegex){
                $id = $matches[1]; // 1 becuase it's first submatch, it doesnt't consider the space out of the group ()                 
            }
            $aBook->id = $id;
            
            //getting book Title:
            $bookTitleRegex = "/\s+([\w+\s*\d*-?,?\&?\(?\)?\!*]+)\s?/";
            $titleRegex = preg_match($bookTitleRegex, $book, $matches);
            if ($titleRegex){
                $title = $matches[0];//0 whole match, considering the whole expression               
            }
            $aBook->title = $title;
            
            //getting book Price:
            $bookPriceRegex = "/([\$]\d+\.?\d*)/";
            $PriceRegex = preg_match($bookPriceRegex, $book, $matches);
            if ($PriceRegex){
                $price = $matches[0];//0 whole match, considering the whole expression 
                $price = ltrim ($price, "$");
            }
            else //if free (price is not found)
            {
                $price = "0.00";
            }
            $aBook->price = $price;
            
            //adding new Book object to the array
            $newArray[] = $aBook;      
        }        
        return $newArray;
    }  
}
