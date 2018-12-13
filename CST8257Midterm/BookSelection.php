<?php
    session_start();
    include "BookList.php";   
?>

<?php
$copies = $_POST["copies"]; 
$error = "";
$count = 0;

if(isset($_POST['buy']))
{        
    //checking if values are "":
    for($i = 0; $i <= 30; $i++ ) 
    {
        if ($copies[$i] != "")
        $count = $count + 1;
    }  
    if ($count == 0000000000000000000000000000000)
    {
        $error = "At least one book's number of copies should be greater than 0! <br/>";
    } 
    
    //if there is a value, going to second page:
    else 
    {
        header("Location: Confirmation.php"); 
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
            <tr><th>Title</th><th>Price</th><th>Copies</th></tr>
            <?php            
            foreach ($bookList as $title => $price)
            {
                echo "<tr>";
                echo "<td style='background-color:lightgrey' align='left'>";
                echo $title;
                echo "</td>";

                echo "<td style='background-color:lightgrey' align='left'>";
                echo $price;
                echo "</td>";  

                echo "<td style='background-color:lightgrey' align='left'>";
                echo "<input type='text' name='copies[]' id='copies'>";          
         
                echo "</td>"; 
            }                
            ?>
        </table>
        <br/>
        <input type='submit'  class='button' name='buy' value='buy'/>
    </form>
</body>
</html>