<?php 
session_start();
include 'ProjectCommon/PageHeader.php';
$validateError = "";
$userId = $_SESSION['userIdTxt'];

//if user not logged yet
if ($userId == null){
    header("Location: Login.php");//move to second page 
    exit;
}

//Connection to DBO            
$dbConnection = parse_ini_file("ProjectCommon/db_connection.ini");        	
extract($dbConnection);
$myPdo = new PDO($dsn, $user, $password); 

//Query database to retrieve book catalog        
$sql = 'SELECT BookId, Title, Price FROM Book WHERE BookId NOT IN (SELECT BookId FROM ShoppingCart WHERE CustomerId = :userId)';                   
$pStmt = $myPdo->prepare($sql);
$pStmt ->execute([':userId' => $userId]);   
$bookCatalog = $pStmt->fetchAll();

//add Buttom
if(isset($_POST['add']))
{
    // Assign copies array values to copies session:
    $_SESSION['copies'] = $_POST['copies'];        
    $isNegative = 0;
    $isEmpty = 0;    
    foreach($_POST['copies'] as $n) //looping through each array row
    {
        if ((int)$n != "")                      
        {  
            $isEmpty = $isEmpty + 1; //isEmpty will be more than 0 if there is at least one value
        }          
        if ((int)$n < 0){
            $isNegative = $isNegative + 1; //isNegative will be more than one if there is any negative number
        }
    }    
  
    if (($isEmpty != 0) && ($isNegative == 0))//if there's at least one number and no one is negative
    {     
        $intCounter=0;
        $intCopies = $_POST['copies'];
        $strTest="";
        
        //insert books into ShoppingCart:
        foreach ($bookCatalog as $row)            
        {
            if ($intCopies[$intCounter] > 0)
            {
                $sqlStatement = "INSERT INTO ShoppingCart VALUES (:userId, :bookId, :copies)";
                $pStmt = $myPdo->prepare($sqlStatement);        
                $pStmt ->execute(array(':userId' => $userId, ':bookId' => $row[0], ':copies' => $intCopies[$intCounter] ));      
                $pStmt->commit; 
            }
            $intCounter++;
        } 
        header("Location: ShoppingCart.php");//move to second page  
        exit;
    }
    else //in case everything is empty or there's a negative value
    {        
        $validateError = "At least one book's number of copies should be greater than 0! <br/>"; 
    } 
}
?>

<br><p>&nbsp &nbsp Enter the number of copies for books you want to buy and click <b>'Add to Shopping Cart'</b> button:<p>
<form action="BookCatalog.php" method='post'>
    <table border="1" align="left" padding-left="20px;">
        <!--table header:-->
        <div class='col-lg-6' style='color:red'> <?php print $validateError;?></div><br>       
        <tr><th>Title</th><th>Price</th><th>Copies</th></tr>        
        <!--table body-->
        <?php
        $counter = 0; // Counts the position in the array
            foreach ($bookCatalog as $row)
            {
                echo "<tr>";
                //creating link with Id parameter (hidden)
                echo "<td style='background-color:lightgrey' align='left'> <a href='BookDetail.php?bookId=".$row[0]."'>";                
                echo $row[1];
                echo "</a></td>";

                echo "<td style='background-color:lightgrey' align='left'>";
                echo "$".$row[2];
                echo "</td>";  

                echo "<td style='background-color:lightgrey' align='left'>";
                echo "<input type='text' name='copies[]' value=''>";              
                echo "</td>";
                $counter = $counter + 1;
            }                  
        ?>   
    </table><br/><br><br>    

    <div class='form-group row'>                
        <button type='submit' name='add' class='btn btn-primary col-lg-2'>Add to Shopping Cart</button>
    </div>    
</form>


<?php 
    include 'ProjectCommon/PageFooter.php';
?>