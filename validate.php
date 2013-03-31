<?php

error_reporting(E_ERROR | E_PARSE);

// Robert Jenkins' Newhash function
function h($k, $x, $a)
{
	$c = $x;
	$b = $k;
	$a=$a-$b; $a=$a-$c; $a=$a^($c >> 13);
	$b=$b-$c; $b=$b-$a; $b=$b^($a << 8); 
	$c=$c-$a; $c=$c-$b; $c=$c^($b >> 13);
	$a=$a-$b; $a=$a-$c; $a=$a^($c >> 12);
	$b=$b-$c; $b=$b-$a; $b=$b^($a << 16);
	$c=$c-$a; $c=$c-$b; $c=$c^($b >> 5);
	$a=$a-$b; $a=$a-$c; $a=$a^($c >> 3);
	$b=$b-$c; $b=$b-$a; $b=$b^($a << 10);
	$c=$c-$a; $c=$c-$b; $c=$c^($b >> 15);
	return $c;
}

// force integer to unsigned value
function u($i)
{
	return $i & 0xFFFFFFFF;
}
function ul($i, $m)
{
	return u($i) % $m;
}

function aura($i, $X, $Y, $k1, $k2, $k3, $a)
{
	$v = (int)($i / $X);
	$u = $i % $X;
	$v = ul(($v + h($k1, $u, $a)), $Y);
	$u = ul(($u + h($k2, $v, $a)), $X);
	$v = ul(($v + h($k3, $u, $a)), $Y);
	return u($v * $X + $u);
}

// random seed values
$k1_id = 1971686994;
$k2_id = 2890581612;
$k3_id = 3758735121;
$q_id = 1070150195;

$width = 50000;
$height = 50000;

function verificationCode($id)
{
	$padlen = strlen(dechex($width * $height));
	$prefix = str_pad(dechex($id), $padlen, "0", STR_PAD_LEFT);
	$suffix = md5($prefix . "_lampooncastle_comp_harv");
	return $prefix . $suffix;
}

function verifyCode($code)
{
	$padlen = strlen(dechex($width * $height));
	if (strlen($code) != $padlen + 32)
		return false;
	$prefix = substr($code,0,$padlen);
	$suffix = substr($code,$padlen);
	return strcmp($suffix, md5($prefix . "_lampooncastle_comp_harv")) == 0;
}

function sendEmail($from, $from_name, $to, $subject, $html, $text)
{
	$from = "$from_name <$from>";
	$mime_boundary = 'Multipart_Boundary_x'.md5(time()).'x';

	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: multipart/alternative; boundary=\"$mime_boundary\"\r\n";
	$headers .= "Content-Transfer-Encoding: 7bit\r\n";
	$replyto .= "reply-to: $from";

	$body = "This is a multi-part message in mime format.\n\n";

	# Add in plain text version
	$body.= "--$mime_boundary\n";
	$body.= "Content-Type: text/plain; charset=\"charset=us-ascii\"\n";
	$body.= "Content-Transfer-Encoding: 7bit\n\n";
	$body.= $text;
	$body.= "\n\n";

	# Add in HTML version
	$body.= "--$mime_boundary\n";
	$body.= "Content-Type: text/html; charset=\"UTF-8\"\n";
	$body.= "Content-Transfer-Encoding: 7bit\n\n";
	$body.= $html;
	$body.= "\n\n";
	
	# End email
	$body.= "--$mime_boundary--\n";

	# Finish off headers
	$headers .= "From: $from\r\n";
	$headers .= "X-Sender-IP: $_SERVER[SERVER_ADDR]\r\n";
	$headers .= 'Date: '.date('n/d/Y g:i A')."\r\n";
	$replyto .= "reply-to: $from";
	# Mail it out
	return mail($to, $subject, $body, $headers);
}

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

		if (!$result = $mysqli->query("REPLACE INTO users (id,id_incr,name,email,passwordhash,room,year,board,registered) VALUES(NULL,DEFAULT,'$name','$email',NULL,'$room','$year',NULL,'0')"))
			exit('error');
		$id_incr = $mysqli->insert_id;
		
		$id = aura($id_incr, $width, $height, $k1_id, $k2_id, $k3_id, $q_id);
		
		if (!$result = $mysqli->query("UPDATE users SET id='$id' WHERE id_incr='$id_incr'"))
			exit('error');
	
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
	else if ($type === 'resend')
	{
		$email = $mysqli->real_escape_string($_POST["email"]);
		
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
		$email = $mysqli->real_escape_string($_POST["email"]);
		$code = $mysqli->real_escape_string($_POST["validation"]);
		
		if (!$result = $mysqli->query("SELECT id FROM users WHERE email='$email' AND registered='0'"))
			exit('error');
		if ($result->num_rows != 1)
			exit('error');
		$row=$result->fetch_assoc();
		$id = $row["id"];
		
		$verification = verificationCode($id);
		if ($code === $verification)
			exit('true');
		exit('false');
	}
	else if ($type === 'finish')
	{
		$board = $mysqli->real_escape_string($_POST["board"]);
		$email = $mysqli->real_escape_string($_POST["email"]);
		$code = $mysqli->real_escape_string($_POST["validation"]);
		
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
		$mysqli = new mysqli("localhost", "root", "root", "lampooncomp");
		if (mysqli_connect_errno())
			exit('error');

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