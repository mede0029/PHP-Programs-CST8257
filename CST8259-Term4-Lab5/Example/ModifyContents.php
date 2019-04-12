<?php
      $book = simplexml_load_file('Book.xml');

      $book->chapter->title = "New Title";
      
      $book->chapter->para[1] = "Removed";
      
      print "<h1>".$book->chapter->title."</h1>";
	  
      foreach($book->chapter->para AS $node) 
      {
           print "<p>$node</p>";
      }
	  
	  print "<br/>";
	  print $book->asXML();
	  
	  $book->asXML('c:\temp\newXMLFile.xml');
?>