<?php
    include 'Lab5Common/Class_Lib.php';
    session_start();
    include 'Lab5Common/Footer.php';
    include 'Lab5Common/Header.php';
    include 'Lab5Common/Functions.php';
    $validatorError = "";
    
    if ($_SESSION['studentIdTxt'] == null)
    { 
        $_SESSION['activePage'] = "CourseSelection.php";        
        header('Location: Login.php');
        exit;
    }
    
    //Getting list of semesters
    //Connection to DBO
    $dbConnection = parse_ini_file("Lab5Common/db_connection.ini");        	
    extract($dbConnection);
    $myPdo = new PDO($dsn, $user, $password);
    
    //Retrieving all semesters from database 
    $sql = "SELECT * FROM Semester ";
    $pStmt = $myPdo->prepare($sql); 
    $pStmt->execute(); 
    
    //Put each record into an array
    foreach ($pStmt as $row)
    {
         $term = array( $row['SemesterCode'], $row['Year'], $row['Term']  );
         $termsArray[] = $term;
    }
    $_SESSION['termsArraySession'] = $termsArray; //session with all semesters from database        
    
    //Checking existing hours for user per semester    
    $sql = "SELECT Course.CourseCode CourseCode, Title, WeeklyHours "
            . " FROM Course INNER JOIN Registration "
            . " ON Course.CourseCode = Registration.CourseCode "
            . " INNER JOIN semester ON registration.SemesterCode = semester.SemesterCode "
            . " WHERE Registration.StudentID = :studendId AND semester.SemesterCode = :semesterCode ";
    $pStmt = $myPdo->prepare($sql);
    $pStmt->execute (array(':studendId' => $_SESSION['studentIdTxt'], ':semesterCode' => $_POST['selectTerm']));
    $courseById = $pStmt->fetchAll();
    $totalRegisteredHours = 0;            
    foreach ($courseById as $row)
    {
        $totalRegisteredHours = $totalRegisteredHours + $row[2];
    }
    $_SESSION['totalRegisteredHours'] = $totalRegisteredHours; //sessions with total number of hours already registered for users
       
    //submit1 button
    if(isset($_POST['submit1']))
    {
        if (isset($_POST['selectedCourse']))
        { 
            //Counting the number of hours student is trying to register for
            foreach ($_POST['selectedCourse'] as $row)
            {
                $sql = "SELECT WeeklyHours FROM Course WHERE CourseCode = :courseCode";
                $pStmt = $myPdo->prepare($sql);
                $pStmt->execute([':courseCode' => $row]);
                $courseHours = $pStmt->fetch();
                $totalRegisteredHours = $totalRegisteredHours + $courseHours[0]; //total of hours user is trying to register for
            }
            
            if ($totalRegisteredHours <= 16) //register for courses
            {
                foreach ($_POST['selectedCourse'] as $row)
                {
                    $sql = "INSERT INTO registration VALUES (:StudentID, :CourseCode, :SemesterCode)";
                    $pStmt = $myPdo->prepare($sql); 
                    $pStmt->execute(array( ':StudentID'=> $_SESSION['studentIdTxt'] , ':CourseCode' => $row, ':SemesterCode' => $_POST['selectTerm']));
                    $pStmt->commit;
                }
                $_SESSION['totalRegisteredHours'] = $totalRegisteredHours; 

            }
            else //display error
            {
                $validatorError = "Your selection exceeds the maximum weekly hours!";
            }
        }
        else 
        {
            $validatorError = "You need to select at least one course!";
        }   
    }

?>
    
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        
    <form method='post' action=CourseSelection.php>   
        <br><br><h4>&nbsp &nbsp Welcome <b><?php print $_SESSION['nameTxt'];?></b>! (Not you? Change your session <a href="Login.php">here</a>).</h4>
        <h4>&nbsp &nbsp Your have registered <b><?php print $_SESSION['totalRegisteredHours']; ?></b> hours for the selected semester. </h4>
        <h4>&nbsp &nbsp Your can register <b><?php print (16 - $_SESSION['totalRegisteredHours']); ?></b> more hour(s) of courses for the semester. </h4>
        <h4>&nbsp &nbsp Please note that the courses you have registered will not be displayed in the list. </h4>        
        
        <!--        displaying dropdown list:-->
        <br><br><div class="row">
            <div class="dropdown col-md-12 school-options-dropdown text-center">
                <div class="dropdown btn-group"> 
                    Term:                     
                    <select name='selectTerm'  onchange="this.form.submit()">   
                    <option value=''></option>;
                    <?php            
                    $termsArray = $_SESSION['termsArraySession']; //session with all terms from database
                    foreach ($termsArray as $terms)
                    {                        
                        echo "<option value='$terms[0]' "; //atributing the value Ex: 18F
                        if ($terms[0] == $_POST['selectTerm']) //if tern coming from db is equal the one selected from user, set it as 'selected'
                        { 
                            echo "selected='selected'";
                        }
                        echo ">" . $terms[1] . " " . $terms[2] . "</option>"; //printing year  + term 
                    } 
                    ?>
                </select>                
                </div>
            </div>
        </div>    

        <!--        displaying table-->    
        <div class='col-lg-4' style='color:red'> <?php print $validatorError;?></div><br>        
        <br><br><table class="table">
            <thead>
                <tr>
                    <th scope="col">Code</th>
                    <th scope="col">Course Title</th>
                    <th scope="col">Hours</th>
                    <th scope="col">Select</th>
                </tr>
            </thead>              
            <tbody>   
                
            <!--   printing the table-->
            <?php            
            if (isset($_POST['selectTerm']) && $_POST['selectTerm'] != "") //if someone has choosen something from dropdown list
            {
                $semester = $_POST['selectTerm'];//variable $semester will be the value selected on dropdown list
                $sem = getCourseBySemeter($semester);//method in functions to get course according to term, returns array with 3 positions
                
                foreach ($sem as $var) //from the variable, print
                {      
                    //querying database to retrive the code of course selected
                    $sql = "SELECT CourseCode "
                            . "FROM Registration "
                            . "WHERE StudentId = :studentId AND CourseCode = :courseCode";
                    $pStmt = $myPdo->prepare($sql);
                    $pStmt -> execute([':studentId' => $_SESSION['studentIdTxt'], ':courseCode' => $var[0]]);
                    $courseHasBeenSelected = $pStmt->fetch();
                    
                    if ($courseHasBeenSelected[0] != $var[0])
                    {                        
                        echo "<tr>";
                        echo "<td scope='col'>".$var[0]."</td>"; // Course Code
                        echo "<td scope='col'>".$var[1]."</td>"; // Course Title
                        echo "<td scope='col'>".$var[2]."</td>"; // Hours

                        // Checkbox, value = Course Code
                        echo "<td scope='col'><input type='checkbox' name='selectedCourse[]' value='$var[0]' /></td>"; 
                        echo "</tr>";
                    }                        
               }                
            }             
            ?>
            </tbody>  
          </table> 
                 
        <!--buttons-->
        <br><br><div class='form-group row pull-right col-lg-5'>                
            <button type='submit' name='submit1' class='btn btn-primary col-lg-2'>Submit</button>
                <div class='col-lg-5'>
                <button type='submit' name='clear' class='btn btn-primary col-lg-5'>Clear</button>
                </div>
        </div>  
        <br><br><br><br><br>
    </form>             
    </body>
</html>
