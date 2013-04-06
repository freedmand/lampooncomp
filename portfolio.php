<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php
		$data = require('logincheck.php');
		if ($data != 'true')
			forceLogin();
		$name = $_SESSION['name'];
		$id = $_SESSION['id'];
	?>
	<title>Portfolio</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" type="text/css" href="css/comp.css">
	<link rel="stylesheet" type="text/css" href="css/fonts.css">
	
	<script src="js/jquery-1.8.3.js"></script>
	<script src="js/jquery-ui.js"></script>
	
	<script src="js/methods.js"></script>
</head>
<body class="form">
	<div class="body-header">
		<div class="header-options">
			<a class="header-link" href="login.html">account</a> <span class="divider">|</span> <a class="header-link" href="signout.php">sign out</a>
		</div>
		<a class="logo-holder">
			<img src="img/logo_fut.png" id="logo_img" alt="The Harvard Lampoon">
		</a>
	</div>
	<div class="comp-header">
		<a class="comp-link" href="about:blank">PORTFOLIO</a>
		<span class="comp-sep">&middot;</span>
		<a class="comp-link" href="about:blank">FEEDBACK</a>
		<span class="comp-sep">&middot;</span>
		<a class="comp-link" href="about:blank">DIRECTORS</a>
		<span class="comp-sep">&middot;</span>
		<a class="comp-link" href="about:blank">ACCOUNT</a>
	</div>
	<div class="content">
<?php
error_reporting(E_ERROR | E_PARSE);

$FILES_DIR = "/Users/freedmand/private/";

$mysqli = new mysqli("localhost", "root", "root", "lampooncomp");
if (mysqli_connect_errno())
	exit('error');

if (!$result = $mysqli->query("SELECT article_id, title, istext, path FROM articles WHERE id='$id' AND path IS NOT NULL ORDER BY article_id_incr DESC LIMIT 10"))
	exit('error');

$num_rows = $result->num_rows;


for ($i = 0; $i < $num_rows; $i++)
{
	$row = $result->fetch_assoc();
	$article_id = $row['article_id'];
	$title = $row['title'];
	$path = $row['path'];
	
	$html = file_get_contents($path);
	
	echo '<div class="entry">';
	echo '	<span class="author-list">' . $name . '</span><div class="separator"></div><span class="title-list">' . $title . '</span>';
	echo '	<div class="sample-text">';
	echo $html;
	echo '	</div>';
	echo '</div>';
}
?>
	</div>
</body>
</html>
<?php ob_end_flush(); ?>