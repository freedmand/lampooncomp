<?php

include 'loginfunc.php';

error_reporting(E_ERROR | E_PARSE);
session_start();

try
{
	session_destroy();

	if (isset($_COOKIE['lcomp_user']))
	{
		$split = parseCookieVal($_COOKIE['lcomp_user']);
		$id = $split[0];
		$rand = $split[1];
		
		unsetCookie();
		
		$mysqli = new mysqli("localhost", "root", "root", "lampooncomp");
		if (mysqli_connect_errno())
			error_log('error');
		
		// check if cookie is valid in database
		if (!$result = $mysqli->query("SELECT expiry FROM cookiestore WHERE id='$id' AND rand='$rand'"))
			error_log('error');
		if ($result->num_rows > 0)
			$mysqli->query("DELETE FROM cookiestore WHERE id='$id' AND rand='$rand'");
	}
	
	forceHome();
}
catch (Exception $e)
{
	forceHome();
}
?>