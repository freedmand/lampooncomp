<?php

error_reporting(E_ERROR | E_PARSE);

try
{
	$mysqli = new mysqli("localhost", "root", "root", "lampooncomp");
	if (mysqli_connect_errno())
		exit('error');
	
	$email = $mysqli->real_escape_string($_POST["email"]);
	
	$newpass = substr(md5(microtime()),rand(0,26),8);
	$newpasshash = md5($newpass);
	
	if (!$result = $mysqli->query("UPDATE users SET passwordhash='$newpasshash' WHERE email='$email' AND registered='1'"))
		exit('error');
	
	if ($result->num_rows == 0)
		exit('false');
	
	
}
catch (Exception $e)
{
	exit('error');
}
?>