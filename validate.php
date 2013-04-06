<?php

error_reporting(E_ERROR | E_PARSE);

include 'hash.php';
include 'email.php';

// random seed values
$k1_id = 1971686994;
$k2_id = 2890581612;
$k3_id = 3758735121;
$q_id = 1070150195;

try
{
	$type = $_POST["type"];
	
	$mysqli = new mysqli("localhost", "root", "root", "lampooncomp");
	if (mysqli_connect_errno())
		exit('error');
	
	if ($type === 'register')
	{
		$name = $mysqli->real_escape_string($_POST["name"]);
		$email = $mysqli->real_escape_string($_POST["email"]);
		$room = $mysqli->real_escape_string($_POST["room"]);
		$year = $mysqli->real_escape_string($_POST["year"]);

		if (!$result = $mysqli->query("REPLACE INTO users (id,id_incr,name,email,passwordhash,room,year,board,registered,director) VALUES(NULL,DEFAULT,'$name','$email',NULL,'$room','$year',NULL,'0','0')"))
			exit('error');
		$id_incr = $mysqli->insert_id;
		
		$id = aura($id_incr, $width, $height, $k1_id, $k2_id, $k3_id, $q_id);
		
		if (!$result = $mysqli->query("UPDATE users SET id='$id' WHERE id_incr='$id_incr'"))
			exit('error');
	
		$verification = verificationCode($id);
		
		setcookie('reg_email', $email, time()+60*60*24*14, "/");
		
		// send verification email
	
		$link = "http://freedmand.com/lampooncomp/verify.php?v=" . $verification;
		$html_msg = "<h1>Welcome to the Harvard Lampoon comp.</h1>Please click on the following link to verify your email address:<br><a href=\"$link\">$link</a><br><br>Alternatively, copy and paste the following verification code: $verification";
		$text_msg = "Welcome to the Harvard Lampoon comp. Please visit the following link in your browser to verify your email address:\r\n$link\r\n\r\nAlternatively, enter in the following verification code: $verification";
		
		if (sendEmail("comp@freedmand.com", "LampoonComp", "freedmand@gmail.com", "[LampoonComp] Please verify your email", $html_msg, $text_msg))
			exit("Mail Sent.");
		else
			exit('error');
	}
	else if ($type === 'resend')
	{
		$email = $mysqli->real_escape_string($_COOKIE['reg_email']);//$mysqli->real_escape_string($_POST["email"]);
		
		if (!$result = $mysqli->query("SELECT id FROM users WHERE email='$email' AND registered='0'"))
			exit('error');
		if ($result->num_rows != 1)
			exit('error');
		$row=$result->fetch_assoc();
		$id = $row["id"];
		
		$verification = verificationCode($id);
		
		// send verification email
	
		$link = "http://freedmand.com/lampooncomp/verify.php?v=" . $verification;
		$html_msg = "<h1>Welcome to the Harvard Lampoon comp.</h1>Please click on the following link to verify your email address:<br><a href=\"$link\">$link</a><br><br>Alternatively, copy and paste the following verification code: $verification";
		$text_msg = "Welcome to the Harvard Lampoon comp. Please visit the following link in your browser to verify your email address:\r\n$link\r\n\r\nAlternatively, enter in the following verification code: $verification";
		
		if (sendEmail("comp@freedmand.com", "LampoonComp", "freedmand@gmail.com", "[LampoonComp] Please verify your email", $html_msg, $text_msg))
			exit("Mail Sent.");
		else
			exit('error');
	}
	else if ($type === 'validate')
	{
		$email = $mysqli->real_escape_string($_COOKIE['reg_email']);//$mysqli->real_escape_string($_POST["email"]);
		$code = $mysqli->real_escape_string($_POST["validation"]);
		
		if (!$result = $mysqli->query("SELECT id FROM users WHERE email='$email' AND registered='0'"))
			exit('error');
		if ($result->num_rows != 1)
			exit('error');
		$row=$result->fetch_assoc();
		$id = $row["id"];
		
		$verification = verificationCode($id);
		if ($code === $verification)
		{
			setcookie('reg_val', $code, time()+60*60*24*14, "/");
			exit('true');
		}
		exit('false');
	}
	else if ($type === 'finish')
	{
		$board = $mysqli->real_escape_string($_POST["board"]);
		// $email = $mysqli->real_escape_string($_POST["email"]);
		$email = $mysqli->real_escape_string($_COOKIE['reg_email']);
		// $code = $mysqli->real_escape_string($_POST["validation"]);
		$code = $mysqli->real_escape_string($_COOKIE['reg_val']);
		
		if (!$result = $mysqli->query("SELECT id FROM users WHERE email='$email' AND registered='0'"))
			exit('error');
		if ($result->num_rows != 1)
			exit('error');
		$row=$result->fetch_assoc();
		$id = $row["id"];
		$verification = verificationCode($id);
		
		if ($code !== $verification)
			exit('verification error');
		
		$password = md5($mysqli->real_escape_string($_POST["password"]));
		
		if (!$result = $mysqli->query("UPDATE users SET passwordhash='$password', board='$board', registered='1'  WHERE email='$email'"))
			exit('error');
		exit('success');
	}
	else if ($type === 'reset')
	{
		$email = $mysqli->real_escape_string($_POST["email"]);

		$newpass = substr(md5(microtime()),rand(0,26),8);
		$newpasshash = md5($newpass);

		if (!$result = $mysqli->query("UPDATE users SET passwordhash='$newpasshash' WHERE email='$email' AND registered='1'"))
			exit('error');

		if ($mysqli->affected_rows == 0)
			exit('false');
		
		// send reset email
		
		$html_msg = "<h1>We have reset your password.</h1>Your password is now $newpass. Please login with your new password and then change it under the account management page.";
		$text_msg = "We have reset your password.<br><br>Your password is now $newpass. Please login with your new password and then change it under the account management page.";
		
		if (sendEmail("comp@freedmand.com", "LampoonComp", "freedmand@gmail.com", "[LampoonComp] Your password has been reset", $html_msg, $text_msg))
			exit("Mail Sent.");
		else
			exit('error');
	}
}
catch (Exception $e)
{
	exit('error');
}
?>