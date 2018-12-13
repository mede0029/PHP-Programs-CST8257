<?php
        session_start(); 
        include 'BookList.php';        
?>

<?php
// back bottun:
if(isset($_POST['back']))
{
    header("Location: BookSelection.php");
}
?>

<html>
<head>
	<title>Confirmation</title>
	<link rel="stylesheet" type="text/css" href="Contents/BookStore.css" />
</head>
<body>
    <h2>Thank you, please review your selection</h2>
    <table border="1">
        <tr><th>Title</th><th>Price</th><th>Copies</th><th>Total</th></tr>
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
                echo $copies;
                echo "</td>"; 
                
                echo "<td style='background-color:lightgrey' align='left'>";
                echo $copies * $price;
                echo "</td>";
            }   
            ?>    
        <tr><th>Total</th><th></th><th></th><th></th>       
    </table>
    </br></br>
    <form action="BookSelection.php" method="post">
        <input type='submit'  class='button' name='back' value='Back'/>
    </form>
</body>
</html>