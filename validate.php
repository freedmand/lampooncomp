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

try
{
	$mysqli = new mysqli("localhost", "root", "root", "lampooncomp");
	if (mysqli_connect_errno())
		exit('error');
	
	$name = $mysqli->real_escape_string($_POST["fname"]);
	$email = $mysqli->real_escape_string($_POST["femail"]);
	$room = $mysqli->real_escape_string($_POST["froom"]);
	$year = $mysqli->real_escape_string($_POST["fyear"]);

	if (!$result = $mysqli->query("INSERT INTO users (id,id_incr,verify_id,name,email,passwordhash,room,year,boards) VALUES(NULL,DEFAULT,NULL,'$name','$email',NULL,'$room','$year',NULL)"))
		exit('error');
	$id_incr = $mysqli->insert_id;

	$id = aura($id_incr, $width, $height, $k1_id, $k2_id, $k3_id, $q_id);
	
	if (!$result = $mysqli->query("UPDATE users SET id='$id' WHERE id_incr='$id_incr'"))
		exit('error');
	
	$verification = verificationCode($id);
	
	// send verification email
	$link = "http://freedmand.com/lampooncomp/verify.php?v=" . $verification;
	$message = "Welcome to the Harvard Lampoon comp. Please click on the following link to verify your email address:<br><a href="$link">$link</a><br>Alternatively, enter in the following verification code: $verification";
	$from = "comp@freedmand.com";
	$headers = "From:" . $from;
	mail($email,"[LampoonComp] Please verify your email",$message,$headers);
	echo "Mail Sent.";
	
	strlen(dechex(2500000000))
	str_pad(dechex(2500000000), 10, "0", STR_PAD_LEFT)
	hexdec('009502f900')
}
catch (Exception $e)
{
	exit('error');
}
?>