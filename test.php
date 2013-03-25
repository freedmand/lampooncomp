<?php

$mysqli = new mysqli("localhost", "root", "root", "lampooncomp");

$name = $mysqli->real_escape_string($_POST["fname"]);
$email = $mysqli->real_escape_string($_POST["femail"]);
$room = $mysqli->real_escape_string($_POST["froom"]);
$year = $mysqli->real_escape_string($_POST["fyear"]);

// random seed values
$k1_id = 1971686994;
$k2_id = 2890581612;
$k3_id = 3758735121;
$q_id = 1070150195;

$k1_v = 3563299367;
$k2_v = 3459360053;
$k3_v = 2317494888;
$q_v = 2893072605;

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

$width = 50000;
$height = 50000;

$result = $mysqli->query("INSERT INTO users (id,id_incr,verify_id,name,email,passwordhash,room,year,boards) VALUES(NULL,DEFAULT,NULL,'$name','$email',NULL,'$room','$year',NULL)");
$id_incr = $mysqli->insert_id;

$id = aura($id_incr, $width, $height, $k1_id, $k2_id, $k3_id, $q_id);
$verify_id = aura($id_incr, $width, $height, $k1_v, $k2_v, $k3_v, $q_v);

$result = $mysqli->query("UPDATE users SET id='$id', verify_id='$verify_id' WHERE id_incr='$id_incr'");

// $result = $mysqli->query("SELECT COUNT(*) FROM users");
// $row = $result->fetch_assoc();
// echo intval($row["COUNT(*)"]);

$subject = "Lampoon Comp Verification";
$message = "Hello! Your verification code is " . $verify_id . ".";
$from = "comp@freedmand.com";
$headers = "From:" . $from;
mail($email,$subject,$message,$headers);
echo "Mail Sent.";
?>