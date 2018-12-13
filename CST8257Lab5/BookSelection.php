<?php
    include 'Lab5Common/Class_Lib.php';
    session_start();
    include 'Lab5Common/Footer.php';
    include 'Lab5Common/Header.php';
    include 'Lab5Common/Functions.php';
?>

<?php
$copies = $_POST["copies"]; 
$error = "";
$action = "";

if(isset($_POST['buy']))
{       
    $_SESSION['copies'] = $_POST['copies'];//get number of copies  

    $isNegative = 0;
    $isEmpty = 0;    
    
    //Validators for values =< 0
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
  
    //If passing validator
    if (($isEmpty != 0) && ($isNegative == 0))//if there's at least one number and no one is negative
    {                    
        header("Location: Confirmation.php");//move to second page 
        exit();
    }
    else //in case everything is empty or there's a negative value
    {        
        $error = "Book's number of copies should be greater than 0! <br/>"; 
    } 
}
?>

<html>
<head>
    <title>Algonquin College Bookstore</title>
    <link rel="stylesheet" type="text/css" href="Contents/BookStore.css" />
</head>
<body>
    <h3>Select the number of copies for books you want to buy and click Buy button</h3>
    <form action="BookSelection.php" method='post'> <br/>
        <table border="1">  
            <?php print $error;?>                        
            <tr>
                <th><a href="BookSelection.php?id=<?php echo 'title';?>&action=<?php echo 'action';?>">Title</a></th>
                <th><a href="BookSelection.php?id=<?php echo 'price';?>&action=<?php echo 'action';?>">Price</a></th>
                <th>Copies</th>
            </tr>       
            <?php              
            
            $bookFile = new BookFile("BookList.txt"); //opening file txt 
            
            //checking if coming back from second page:
            if ($_SESSION['newArray'] == null)
            {
                $newArray = $bookFile->getBooks(); //calling getBooks from Class_Lib.php                
                $_SESSION['newArray'] = $newArray; //update Array session
            }
            else 
            {
                $newArray = $_SESSION['newArray'];
            }
                       
            //Sorting:
            //1. Title
            if ($_SESSION['actionTitle'] == null) { $actionTitle = 'asc';}
            else { $actionTitle = $_SESSION['actionTitle'];}
            if(($_GET['id']=='title' && $actionTitle == 'asc'))
            {
                usort($newArray, "title_asc"); //sorts Title ascending                   
                $_SESSION['actionTitle'] = 'desc';
            }                
            if(($_GET['id']=='title' && $actionTitle == 'desc'))
            {
                usort($newArray, "title_desc"); //sorts Title descending                
                $_SESSION['actionTitle'] = 'asc';
            }
            $_SESSION['newArray'] = $newArray; //update Array session 
            
            //2. Price
            if ($_SESSION['actionPrice'] == null) { $actionPrice = 'asc';}
            else { $actionPrice = $_SESSION['actionPrice']; }
            if(($_GET['id']=='price' && $actionPrice == 'asc'))
            {
                usort($newArray, "price_asc"); //sorts price ascending                   
                $_SESSION['actionPrice'] = 'desc';
            }
            if(($_GET['id']=='price' && $actionPrice == 'desc'))
            {
                usort($newArray, "price_desc"); //sorts price descending                
                $_SESSION['actionPrice'] = 'asc';
            }
            $_SESSION['newArray'] = $newArray; //update Array session
          
            //displaying table from $NewArray 
            $arrayCounter = 0;
            foreach ($newArray as $item) 
            {    
                echo "<tr>";
                echo "<td style='background-color:lightgrey' align='left'>";
                echo $item->title;
                echo "</td>";

                echo "<td style='background-color:lightgrey' align='left'>";
                printf("$"."%.2f", $item->price);
                echo "</td>";  

                echo "<td style='background-color:lightgrey' align='left'>";
                echo "<input type='text' name='copies[]' value='";                 
                // Print the number of copies selected if the value exists
                echo $_SESSION['copies'][$arrayCounter];
                echo "'></td>"; 
                $arrayCounter = $arrayCounter + 1;
            }    
            $_SESSION['newArray'] = $newArray; //update Array session
            
            ?>
        </table>
        <br/>   
        
        <input type='submit'  class='button' name='buy' value='buy'/>
    </form>
</body>
</html>