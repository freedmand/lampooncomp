<?php
	error_reporting(E_ERROR | E_PARSE);

	ob_start();
	
	$data = require('logincheck.php');
	if ($data != 'true')
		forceLogin();
	
	$id = $_SESSION["id"];
	$state = $_POST['state'];
	
	$mysqli = new mysqli("localhost", "root", "root", "lampooncomp");
	if (mysqli_connect_errno())
		exit('error');
	
	$value = $mysqli->real_escape_string($_POST['value']);
	$pass = md5($mysqli->real_escape_string($_POST['pass']));
	
	switch ($state)
	{
		case 'New password':
			$newpass = md5($value);
			$query = "UPDATE users SET passwordhash='$newpass' WHERE id='$id' AND passwordhash='$pass'";
			break;
		case 'Name':
			$query = "UPDATE users SET name='$value' WHERE id='$id' AND passwordhash='$pass'";
			break;
		case 'Room':
			$query = "UPDATE users SET room='$value' WHERE id='$id' AND passwordhash='$pass'";
			break;
		case 'Year':
			$query = "UPDATE users SET year='$value' WHERE id='$id' AND passwordhash='$pass'";
			break;
		case 'Grant privileges':
			if ($_SESSION['director'] != '1')
				exit('error');
			if (!$result = $mysqli->query("SELECT * FROM users WHERE id='$id' AND passwordhash='$pass'"))
				exit('error');
			if ($result->num_rows == 0)
				exit('false');
			$query = "UPDATE users SET director='1' WHERE email='$value'";
			break;
		default:
			exit('error');
			break;
	}
	if (!$result = $mysqli->query($query))
		exit('error');
	
	if ($mysqli->affected_rows == 0)
		exit('false');
	
	setSession($mysqli, $id);
	exit('true');
	ob_end_flush();
?>