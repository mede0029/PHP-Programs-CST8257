<!DOCTYPE html>

<html>
    <head>

    </head>
    <body>
        <?php
        function ValidateStudentId($studentIdTxt)
        {
            if ($studentIdTxt == ""){return 1;}
        }

        function ValidateName($nameTxt)
        {
            if ($nameTxt == ""){return 1;}
        }

        function ValidatePhoneNumber ($phoneNumberExpression, $phoneNumberTxt)
        {
            $valid2 = (bool) preg_match($phoneNumberExpression, $phoneNumberTxt);
            if ($valid2 == false) { return 1; }
        }  

        function ValidatePassword ($passwordExpression, $passwordTxt)
        {
            $valid3 = (bool) preg_match($passwordExpression, $passwordTxt);
            if ($valid3 == false) { return 1; }
        }         

        function ValidadeEqualPassword ($passwordTxt, $passwordAgainTxt)
        {
            if ($passwordTxt != $passwordAgainTxt) {return 1; }
        }
        
        function ValidateBlankPassword($passwordTxt)
        {
            if ($passwordTxt == ""){return 1;}
        }
        
        function getCourseBySemeter($semeter)
        {
            // Database connection
            $dbConnection = parse_ini_file("Lab5Common/db_connection.ini");        	
            extract($dbConnection);
            $myPdo = new PDO($dsn, $user, $password);

            // Query database to get all course codes
            $sql = "SELECT Course.CourseCode Code, Title,  WeeklyHours "
                       ."FROM Course INNER JOIN CourseOffer ON Course.CourseCode = CourseOffer.CourseCode "
                       ."WHERE CourseOffer.SemesterCode = :semesterCode";
            $pStmt = $myPdo->prepare($sql);
            $pStmt->execute( [ 'semesterCode' => $semeter ] );

            foreach ($pStmt as $row)
            {
                // Add each record to an array
                $course = array( $row['Code'], $row['Title'], $row['WeeklyHours'] );
                $courses[] = $course;
            }

            // Return the array of course codes
            return $courses;
        }
               
        ?>     
        
    </body>
</html>
