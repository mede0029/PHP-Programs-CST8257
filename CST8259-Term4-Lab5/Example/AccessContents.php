<?php
	$book = simplexml_load_file('Book.xml');
	$bookinfo = $book->bookinfo;
	$title = $bookinfo->title;

	// Object examined with var_dump
	echo "<br/>";
	echo "Book Var Dump";
	echo "<br/>";
	var_dump($book);
	
	echo "<br/>";
	echo "<br/>";
	echo "Book Info Var Dump";
	echo "<br/>";
	var_dump($bookinfo);
	
	echo "<br/>";
	echo "<br/>";
	echo "Title Var Dump";
	echo "<br/>";
	var_dump($title);
	
	// Output with element containing text-only content */
	print "<p>Title: $title </p>";

	$author = $bookinfo->author;
	
	// Object examined with var_dump */
	var_dump($author);
	
	// Output element containing child elements */
	print "<p>Author: $author->firstname </p>";
?>