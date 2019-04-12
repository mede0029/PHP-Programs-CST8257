<?php
	  $book = simplexml_load_file('Book.xml');
      $para = $book->chapter->para;
	
      print "<h1>".$book->chapter->title."</h1>";
	  
	  print "<b>Paragraph num: $para[num]</b>";
	  print "<p>".$para."</p>"; 
	  
	  print "<b>Paragraph num: {$para[1][num]}</b>";
      print "<p>".$para[1]."</p>";
	  
	  print "<b>Paragraph num: {$para[2][num]}</b>";
	  print "<p>".$para[2]."</p>";
?>