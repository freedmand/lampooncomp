<?php

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

?>