<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php
		$data = require('logincheck.php');
		if ($data != 'true')
			forceLogin();
		$name = $_SESSION['name'];
		$board = $_SESSION['board'];
		$split = explode(" ", $name);
		$first = $split[0];
	?>
	<title>Welcome, <?php echo $first?></title>
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
		<h1>Welcome, <?php echo $first?></h1>
		<!-- <p class="intro-text">
			Click on a button above, or
		</p> -->
		<button class="form-button orange-button" onclick=<?php echo '"submitPiece(this, \'' . $board. '\');"'?>>
			Submit piece
		</button>
	</div>
	<div class="castle">
</body>
</html>
<?php ob_end_flush(); ?>