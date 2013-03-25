<?php

error_reporting(E_ERROR | E_PARSE);

try
{
	$mysqli = new mysqli("localhost", "root", "root", "lampooncomp");
	if (mysqli_connect_errno())
		exit('error');

	$email = $mysqli->real_escape_string($_POST["email"]);

	if (!$result = $mysqli->query("SELECT email FROM users WHERE email='$email'"))
		exit('error');

	if ($result->num_rows == 0)
		exit('false');
	else
		exit('true');
}
catch (Exception $e)
{
	exit('error');
}
?>