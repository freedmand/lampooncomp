<?php
	error_reporting(E_ERROR | E_PARSE);

	ob_start();

	$FILES_DIR = "img/usr/";

	$data = require('logincheck.php');
	if ($data != 'true')
		forceLogin();

	$id = $_SESSION["id"];
	
	$mysqli = new mysqli("localhost", "root", "root", "lampooncomp");
	if (mysqli_connect_errno())
		exit('error');
	
	$article_id = $mysqli->real_escape_string($_POST["article_id"]);
	$title = $mysqli->real_escape_string($_POST["title"]);
	
	if (!$result = $mysqli->query("UPDATE articles SET title='$title' WHERE article_id='$article_id' AND title is NULL"))
		exit('error');
	
	exit('true');
	
	ob_end_flush();
?>