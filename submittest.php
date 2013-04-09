<?php
	error_reporting(E_ERROR | E_PARSE);
	
	$FILES_DIR = "img/usr/";
	$path = $FILES_DIR . '2.txt';
	echo 'Id: ' . getmyuid() . '<br>';
	echo 'Gid: ' . getmygid() . '<br>';
	echo '<br>';
	echo nl2br(print_r(stat('submittest.php'), true)) . '<br>';
	echo $path . '<br>';
	echo file_put_contents($path, "dog");
?>