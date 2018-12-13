<?php 
session_start();
include 'ProjectCommon/PageHeader.php';
$userIdTxt = $_SESSION['userIdTxt'];
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

//Retrieving user's name:
$sql = "SELECT customer.Name FROM customer WHERE customer.UserId = :userId";
$pStmt = $myPdo->prepare($sql);
$pStmt ->execute([':userId' => $userIdTxt]);   
$userName = $pStmt->fetch();
$_SESSION['userName'] = $userName[0];

//Quering database to retrieve shopping cart       
$sql = 'SELECT book.BookId, book.Price, book.Title, shoppingcart.Copies FROM book '
        . 'INNER JOIN shoppingcart ON book.BookId = shoppingcart.BookId '
        . 'INNER JOIN customer ON customer.UserId = shoppingcart.CustomerId '
        . 'WHERE customer.UserId = :userId' ; 
$pStmt = $myPdo->prepare($sql);
$pStmt ->execute([':userId' => 'gongw']);   
$shoppingCart = $pStmt->fetchAll();

//update Button
if(isset($_POST['update']))
{
    $_SESSION['copies'] = $_POST['copies'];            
    $bookIdArray = $_POST['bookId']; //retriveing id's from books
    $intCounter = 0;    
    
    foreach($_POST['copies'] as $row) //looping through each array row
    {
        //if book copies is empty:
        if ($row == "")                      
        {  
            //delete books from ShoppingCart:
            $sqlStatement = "DELETE FROM ShoppingCart WHERE BookId = :bookId AND customerId = :userId";
            $pStmt = $myPdo->prepare($sqlStatement);        
            $pStmt ->execute(array(':userId' => $userIdTxt, ':bookId' => $bookIdArray[$intCounter] ));      
            $pStmt->commit; 
        }          
        //if book copies is 0:
        else if ($row < 1)
        {
            //delete books from ShoppingCart:
            $sqlStatement = "DELETE FROM ShoppingCart WHERE BookId = :bookId AND customerId = :userId";
            $pStmt = $myPdo->prepare($sqlStatement);        
            $pStmt ->execute(array(':userId' => $userIdTxt, ':bookId' => $bookIdArray[$intCounter] ));      
            $pStmt->commit; 
        }
        //if book copies was modified:
        else 
        {
            //update books into ShoppingCart:
            $sqlStatement = "UPDATE ShoppingCart SET copies = :newCopy "
                    . "WHERE BookId = :bookId AND customerId = :userId";
            $pStmt = $myPdo->prepare($sqlStatement);        
            $pStmt ->execute(array(':newCopy' => $row, ':userId' => $userIdTxt, ':bookId' => $bookIdArray[$intCounter] ));      
            $pStmt->commit; 
        }        
        $intCounter++;
    }    
    header("Location: ShoppingCart.php");//move to second page 
    exit;
}
?>

<br><h3>&nbsp; &nbsp;<?php print ($_SESSION['userName']); ?>'s Shopping Cart</h3><br>
<p>&nbsp; &nbsp; To change the number of copies to purchase, enter the new number and click "Update Button".</p>
<p>&nbsp; &nbsp; To remove any book from the shopping cart, change the number of copies to 0 and click "Update Button'.</p>

<form action="ShoppingCart.php" method='post'> <br/>
    <table border="1">
        <!--table header:-->
        <tr><th>Title</th><th>Price</th><th>Copies</th></tr>
        
        <!--table body-->
        <?php
        $counter = 0; // Counts the position in the array
            foreach ($shoppingCart as $row)
            {
                echo "<tr>";
                //creating a hidden ID to be used for updating query:
                echo "<input type='hidden' name='bookId[]' value='".$row[0]."' />";
                echo "<td style='background-color:lightgrey' align='left'> <a href='BookDetail.php?bookId=".$row[0]."'>";                
                echo $row[2];
                echo "</a></td>";

                echo "<td style='background-color:lightgrey' align='left'>";
                echo "$".$row[1];
                echo "</td>";  

                echo "<td style='background-color:lightgrey' align='left'>";
                echo "<input type='text' name='copies[]' value='";

                // Print the number of copies selected if the value exists
                echo $row[3];
                echo "'></td>";
                $counter = $counter + 1;
            }                  
?>   
    </table><br/><br>
    
    <div class='form-group row'>                
        <button type='submit' name='update' class='btn btn-primary col-lg-2'>Update</button>
    </div>  
</form>

<?php 
    include 'ProjectCommon/PageFooter.php';
?>