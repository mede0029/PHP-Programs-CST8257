<?php 
session_start();
include 'ProjectCommon/PageHeader.php';
$bookId = $_GET['bookId']; //getting the ID from URL

//Connection to DBO            
$dbConnection = parse_ini_file("ProjectCommon/db_connection.ini");        	
extract($dbConnection);
$myPdo = new PDO($dsn, $user, $password); 

//Retrieving user's name:
$sql = "SELECT Title, Description FROM Book WHERE BookId = :bookId";
$pStmt = $myPdo->prepare($sql);
$pStmt ->execute([':bookId' => $bookId]);   
$bookArray = $pStmt->fetch();

?>

<h3><?php print $bookArray[0] ?></h3><br>
<p><?php print $bookArray[1] ?><p>
    
<?php 
    include 'ProjectCommon/PageFooter.php';
?>
