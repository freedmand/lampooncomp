<?php
function getrand()
{
	return md5(mt_rand()) . md5(mt_rand());
}
function getCookieVal($id, $rand)
{
	return dechex($id) . '-' . $rand;
}
function parseCookieVal($val)
{
	$split = explode("-", $val);
	$split[0] = hexdec($split[0]);
	return $split;
}
function issueNewCookie($mysqli,$id)
{
	$rand = getrand();
	if (!$result = $mysqli->query("INSERT INTO cookiestore (id,rand,expiry) VALUES('$id','$rand',NOW() + INTERVAL 2 WEEK)"))
		return false;
	
	setcookie('lcomp_user', getCookieVal($id, $rand), time()+60*60*24*14, "/");
	return true;
}

function unsetCookie()
{
	setcookie('lcomp_user', "", time() - 3600, "/");
}

function forceLogin()
{
	ob_end_clean();
	$relocation = $_SERVER['PHP_SELF'];
	header("Location: login.html?redirect=$relocation");
	exit;
}
function forceHome()
{
	ob_end_clean();
	header('Location: index.php');
	exit;
}

function forceWelcome()
{
	ob_end_clean();
	header('Location: welcome.php');
	exit;
}

function setSession($mysqli, $id)
{
	if (!$result = $mysqli->query("SELECT name, email, board FROM users WHERE id='$id'"))
		return false;
	
	if ($result->num_rows == 0)
		forceLogin();
	
	$row = $result->fetch_assoc();
	$name = $row["name"];
	$email = $row["email"];
	$board = $row["board"];
	
	$_SESSION['id'] = $id;
	$_SESSION['name'] = $name;
	$_SESSION['email'] = $email;
	$_SESSION['board'] = $board;
	
	return true;
}
?>