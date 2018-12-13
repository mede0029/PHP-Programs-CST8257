<?php        

    function ValidateUserId($userIdTxt){
            if ($userIdTxt == ""){return 1;}
    }

    function ValidateName($nameTxt){
        if ($nameTxt == ""){return 1;}
    }

    function ValidatePhoneNumber ($phoneNumberExpression, $phoneNumberTxt){
        $valid2 = (bool) preg_match($phoneNumberExpression, $phoneNumberTxt);
        if ($valid2 == false) { return 1; }
    }  

    function ValidatePassword ($passwordExpression, $passwordTxt){
        $valid3 = (bool) preg_match($passwordExpression, $passwordTxt);
        if ($valid3 == false) { return 1; }
    }         
    function ValidateEqualPassword ($passwordTxt, $passwordAgainTxt){
        if ($passwordTxt != $passwordAgainTxt) {return 1; }
    }
    function ValidateBlankPassword($passwordTxt){
        if ($passwordTxt == ""){return 1;}
    }
    function ValidateBlankAlbum($albumTxt){
        if ($albumTxt == ""){
            return "Album is required";
        }
    }
    function ValidateFileUpload($files, $name){
        $allowed =  array('gif','png' ,'jpg', 'jpeg');
        $total = count($_FILES[$name]['name']);

        if (in_array(1, $files[$name]['error'], false))
        {
            return "Upload file is too large"; 
        }
        if (in_array(4, $files[$name]['error'], false))
        {
            return "No upload file specified"; 
        }

        //validates extensions and sizes for all files
        for ($i=0; $i < $total ; $i++) {
            $filename = $files[$name]['name'][$i];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if(!in_array($ext, $allowed)){
                return 'Accepted picture types: JPG(JPEG), GIF and PNG!';
            }
        }
    }
?>