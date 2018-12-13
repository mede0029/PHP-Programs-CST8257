<?php
    include 'Lab5Common/Class_Lib.php';
    session_start();   
    include 'Lab5Common/Functions.php';  
    $validatorError = "";
    $selectedCourse = array();
    
    if ($_SESSION['studentIdTxt'] == null)
    {
        $_SESSION['activePage'] = "CurrentRegistration.php";
        header('Location: Login.php');
        exit;
    }
    
    //Connection to DBO
    $dbConnection = parse_ini_file("Lab5Common/db_connection.ini");        	
    extract($dbConnection);
    $myPdo = new PDO($dsn, $user, $password);
    
    //submit button
    if(isset($_POST['submit']))
    {  
        if (isset($_POST['selectedCourse']))
        { 
            foreach ($_POST['selectedCourse'] as $row) //iterate and look for what was selected
            {
                //for each selected line, delete the corresponding course code from db
                $sql = "DELETE FROM registration WHERE registration.CourseCode = :courseCode ";  
                $pStmt = $myPdo->prepare($sql);
                $pStmt->execute(array(':courseCode' => $row));
                $pStmt->commit;      
            }
        }
        else 
        {
            $validatorError = "You must select at least one checkbox!";
        }                
    }
    include 'Lab5Common/Header.php';   
?>

    <form method='post' action=CurrentRegistration.php>   
    <br><br><h1>&nbsp &nbsp Current Registrations</h1>    
    <br><br><h4>&nbsp &nbsp Welcome <b><?php print $_SESSION['nameTxt'];?></b>! (Not you? Change your session <a href="Login.php">here</a>). The following are your current registrations:</h4>

    <!--        displaying table-->  
    <div class='col-lg-4' style='color:red'> <?php print $validatorError;?></div><br>
    <br><br><table class="table">
        <thead>
            <tr>
                <th scope="col">Year</th>
                <th scope="col">Term</th>
                <th scope="col">Course Code</th>
                <th scope="col">Course Title</th>
                <th scope="col"></th>
                <th scope="col">Hours</th>
                <th scope="col">Select</th>                                                                                
            </tr>
        </thead> 
        <tbody>

            <?php
            //Getting array with information, sorted by semester and term    
            $sql = "SELECT semester.Year, semester.Term, Course.CourseCode, course.Title, course.WeeklyHours "
                . "FROM Course INNER JOIN Registration ON Course.CourseCode = Registration.CourseCode " 
                . "INNER JOIN courseoffer ON courseoffer.CourseCode = registration.CourseCode "
                . "INNER JOIN semester ON (courseoffer.SemesterCode = semester.SemesterCode AND semester.SemesterCode = registration.SemesterCode) "
                . "WHERE Registration.StudentID = :studendId "
                . "ORDER BY semester.Year ASC, semester.Term" ;  
            $pStmt = $myPdo->prepare($sql);
            $pStmt->execute ([':studendId' => $_SESSION['studentIdTxt']]);
            $coursesRegistered = $pStmt->fetchAll();
            $currentTerm = "";
            $currentYear = "";

            foreach ($coursesRegistered as $row)
            {
                //if ($currentTerm == "")
                if ($currentYear == "")
                {
                    $currentYear = $row[0];
                    $totalHours = 0;
                }  

                if ($currentTerm == "")
                {
                    $currentTerm = $row[1];                       
                    $totalHours = 0;
                }  

                if ( $currentYear != $row[0] ||  $currentTerm != $row[1]) //when something chages for either Year or Term, print an empty line
                { 
                        echo "<tr>";
                        echo "<td scope='col'></td>"; // Year
                        echo "<td scope='col'></td>"; // Term
                        echo "<td scope='col'></td>"; // CourseCode
                        echo "<td scope='col'></td>"; // CourseTitle
                        echo "<th scope='col'>Total Weekly Hours</th>"; // Blank
                        echo "<td scope='col'><b>".$totalHours."</b></td>"; // Hours
                        echo "<td></td>"; 
                        echo "</tr>";
                        //set $currentTerm to next value of Term:
                        $totalHours = 0;
                        $currentYear = $row[0]; //set year to the next year record                            
                        $currentTerm = $row[1]; //set term to next term record                        

                }     
                //print following term
                echo "<tr>";
                echo "<td scope='col'>".$row[0]."</td>"; // Year
                echo "<td scope='col'>".$row[1]."</td>"; // Term
                echo "<td scope='col'>".$row[2]."</td>"; // CourseCode
                echo "<td scope='col'>".$row[3]."</td>"; // CourseTitle
                echo "<td scope='col'></td>"; // Blank
                echo "<td scope='col'>".$row[4]."</td>"; // Hours
                // Checkbox, value = Course Code
                echo "<td scope='col'><input type='checkbox' name='selectedCourse[]' value='$row[2]' /></td>"; 
                echo "</tr>";  
                $totalHours = $totalHours + $row[4];
            }
            //printing last line
            echo "<tr>";
            echo "<td scope='col'></td>"; // Year
            echo "<td scope='col'></td>"; // Term
            echo "<td scope='col'></td>"; // CourseCode
            echo "<td scope='col'></td>"; // CourseTitle
            echo "<th scope='col'>Total Weekly Hours</th>"; // Blank
            echo "<td scope='col'><b>".$totalHours."</b></td>"; // Hours
            echo "<td></td>"; 
            echo "</tr>";
            ?> 
        </tbody>
    </table> 

    <br><br><div class='form-group row'>  
            <div class="col-md-6">
                <button type='submit' name='submit' class='btn btn-primary' onclick='return confirm("The selected registration will be deleted!")'>Delete Selected</button>
                &nbsp; &nbsp;
                <button type='submit' name='clear' class='btn btn-primary'>Clear</button>
            </div>
    </div><br><br>            
    </form>   

<?php     
    include 'Lab5Common/Footer.php';
?>