<?php
	  $book = simplexml_load_file('Book.xml');
      $para = $book->chapter->para;
	
      print "<h1>".$book->chapter->title."</h1>";
	  
      foreach($para AS $paragraph) 
      {
		   print "<b>Paragraph num: $paragraph[num]</b>";
           print "<p>$paragraph</p>";
      }
?>