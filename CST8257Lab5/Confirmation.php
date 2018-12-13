<?php
    include 'Lab5Common/Class_Lib.php';
    session_start();
    include 'Lab5Common/Footer.php';
    include 'Lab5Common/Header.php';
    include 'Lab5Common/Functions.php';    
?>

<?php
// back bottun:
if(isset($_POST['back'])) { header("Location: BookSelection.php"); }
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

        $counter = 0; // Counts the position in the array
        $total = 0; // the total cost of all selected books                       
        $newArray = $_SESSION['newArray']; //retrieving array value    
        
        foreach ($newArray as $item) 
            {   
                // Only display books which have greater than 0 in the 'copies' field
                if((int)$_SESSION["copies"][$counter] != 0) 
                {
                    echo "<tr>";
                    echo "<td style='background-color:lightgrey' align='left'>";
                    echo $item->title;
                    echo "</td>";

                    echo "<td style='background-color:lightgrey' align='left'>";
                    printf("$"."%.2f", $item->price);
                    echo "</td>";  

                    echo "<td style='background-color:lightgrey' align='left'>";
                    // Print the number of copies selected
                    echo $_SESSION["copies"][$counter];
                    echo "</td>"; 

                    echo "<td style='background-color:lightgrey' align='left'>";
                    // Multiply the number of copies selected by the price
                    $multiply = (int)$_SESSION["copies"][$counter] * $item->price;
                    printf("$"."%.2f", $multiply);
                    
                    // Add the price to the total price
                    $total = $total + (int)$_SESSION["copies"][$counter] * $item->price;
                    echo "</td>";   
                }                    
                $counter = $counter + 1;                
            }             
                  
            // Display the total cost
            echo "<tr><th>Total</th><th></th><th></th><th>$total</th>";
            
            ?>    
    </table>
    </br></br>
    <form action="BookSelection.php" method="post">
        <input type='submit'  class='button' name='back' value='Back'/>
    </form>
</body>
</html>