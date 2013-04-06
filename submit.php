<?php

error_reporting(E_ERROR | E_PARSE);

include 'hash.php';

ob_start();

$FILES_DIR = "/Users/freedmand/private/";
if (!file_exists($FILES_DIR))
{
	mkdir($FILES_DIR, 0777);
}

// random seed values
$k1_id = 1750680230;
$k2_id = 2187714091;
$k3_id = 1575001739;
$q_id = 973325368;

$data = require('logincheck.php');
if ($data != 'true')
	forceLogin();

$id = $_SESSION["id"];

$mysqli = new mysqli("localhost", "root", "root", "lampooncomp");
if (mysqli_connect_errno())
	exit('error');

$title = $mysqli->real_escape_string($_POST["title"]);
$istext = $mysqli->real_escape_string($_POST["istext"]);
$data = $_POST["data"];

if (!$result = $mysqli->query("REPLACE INTO articles (id,title,istext) VALUES('$id','$title','$istext')"))
	exit('error');
$article_id_incr = $mysqli->insert_id;

$article_id = aura($article_id_incr, $width, $height, $k1_id, $k2_id, $k3_id, $q_id);

$path = $FILES_DIR . $article_id . ($istext == '1' ? '.html' : '.jpg');

if (!$result = $mysqli->query("UPDATE articles SET article_id='$article_id', path='$path' WHERE article_id_incr='$article_id_incr'"))
	exit('error');

exit(file_put_contents($path, $data));
ob_end_flush();

?>