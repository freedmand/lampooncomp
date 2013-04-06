<?php

include 'loginfunc.php';

error_reporting(E_ERROR | E_PARSE);
session_start();

try
{
	$mysqli = new mysqli("localhost", "root", "root", "lampooncomp");
	if (mysqli_connect_errno())
		exit('error');
	
	$email = $mysqli->real_escape_string($_POST["email"]);
	$password = md5($mysqli->real_escape_string($_POST["password"]));

	if (!$result = $mysqli->query("SELECT id, name, board FROM users WHERE email='$email' AND passwordhash='$password' AND registered='1'"))
		exit('error');

	if ($result->num_rows == 0)
		exit('false');
	
	$row = $result->fetch_assoc();
	$id = $row["id"];
	$name = $row["name"];
	$board = $row["board"];
	
	$_SESSION['id'] = $id;
	$_SESSION['email'] = $email;
	$_SESSION['name'] = $name;
	$_SESSION['board'] = $board;
	
	issueNewCookie($mysqli,$id);
	
	exit("true");
}
catch (Exception $e)
{
	exit('error');
}
?>
<?php ob_end_flush(); ?>