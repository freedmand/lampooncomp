<?php

error_reporting(E_ERROR | E_PARSE);

try
{
	$mysqli = new mysqli("localhost", "root", "root", "lampooncomp");
	if (mysqli_connect_errno())
		exit('error');

	$email = $mysqli->real_escape_string($_POST["email"]);
	$password = md5($mysqli->real_escape_string($_POST["password"]));

	if (!$result = $mysqli->query("SELECT passwordhash FROM users WHERE email='$email' AND registered='1'"))
		exit('error');

	if ($result->num_rows == 0)
		exit('false');
	
	$row=$result->fetch_assoc();
	$passwordhash = $row["passwordhash"];
	if ($password === $passwordhash)
		exit('true');
	else
		exit('false');
}
catch (Exception $e)
{
	exit('error');
}
?>