<?php
	error_reporting(E_ERROR | E_PARSE);

	include 'hash.php';

	ob_start();

	$FILES_DIR = "img/usr/";
	if (!file_exists($FILES_DIR))
	{
		mkdir($FILES_DIR, 0777);
	}
	
	// random seed values
	$k1_id = 331719786;
	$k2_id = 334803793;
	$k3_id = 908798562;
	$q_id = 1698931722;

	$data = require('logincheck.php');
	if ($data != 'true')
		forceLogin();

	$id = $_SESSION["id"];

	$mysqli = new mysqli("localhost", "root", "root", "lampooncomp");
	if (mysqli_connect_errno())
		exit('error');
	
	if (!$result = $mysqli->query("INSERT INTO articles (id,istext) VALUES('$id','0')"))
		exit('error');
	$article_id_incr = $mysqli->insert_id;

	$article_id = aura($article_id_incr, $width, $height, $k1_id, $k2_id, $k3_id, $q_id);
	
	$type = pathinfo($_SERVER['HTTP_X_FILE_NAME'], PATHINFO_EXTENSION);
	$path = $FILES_DIR . $article_id . '.' . $type;

	if (!$result = $mysqli->query("UPDATE articles SET article_id='$article_id', path='$path' WHERE article_id_incr='$article_id_incr'"))
		exit('error');

	file_put_contents($path, file_get_contents("php://input"));
	echo $article_id;
	ob_end_flush();
?>