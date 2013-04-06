<?php

include 'loginfunc.php';

error_reporting(E_ERROR | E_PARSE);

session_start();

try
{
	if (isset($_SESSION['id']))
		return 'true';
	
	if (isset($_COOKIE['lcomp_user']))
	{
		$split = parseCookieVal($_COOKIE['lcomp_user']);
		$id = $split[0];
		$rand = $split[1];
		
		$mysqli = new mysqli("localhost", "root", "root", "lampooncomp");
		if (mysqli_connect_errno())
			return 'error';
		
		// check if cookie is valid in database
		if (!$result = $mysqli->query("SELECT expiry FROM cookiestore WHERE id='$id' AND rand='$rand'"))
			return 'error';
		if ($result->num_rows > 0)
		{
			$row=$result->fetch_assoc();
			$expiry = $row['expiry'];

			$mysqli->query("DELETE FROM cookiestore WHERE id='$id' AND rand='$rand'");

			$success = 0;
			if ($result = $mysqli->query("SELECT NOW()<'$expiry'"))
			{
				if ($result->num_rows > 0)
				{
					$row = $result->fetch_assoc();
					$arrayval = array_values($row);
					$bool = $arrayval[0];
					if ($bool == '1')
					{
						setSession($mysqli, $id);
						issueNewCookie($mysqli, $id);
						return 'true';
					}
					else
						return 'false';
				}
				else
				{
					return 'error';
				}
			}
			else
				return 'error';
		}
		else
			return 'false';
	}
	else
		return 'false';
}
catch (Exception $e)
{
	return 'error';
}
?>