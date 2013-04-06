<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php
		$data = require('logincheck.php');
		if ($data == 'true')
			forceWelcome();
	?>
	<title>Lampoon Comp</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" type="text/css" href="css/comp.css">
	<link rel="stylesheet" type="text/css" href="css/fonts.css">
	
	<script src="js/jquery-1.8.3.js"></script>
	<script src="js/jquery-ui.js"></script>
</head>
<body class="main">
	<div class="body-header">
		<div class="header-options">
			<a class="header-link" href="login.html">login</a> <span class="divider">|</span> <a class="header-link" href="register.html">register</a>
		</div>
		<a class="logo-holder">
			<img src="img/logo_fut.png" id="logo_img" alt="The Harvard Lampoon">
		</a>
	</div>
	<div class="comp-header">
		<a class="comp-link" href="about:blank">LITERATURE</a>
		<span class="comp-sep">&middot;</span>
		<a class="comp-link" href="about:blank">ART</a>
		<span class="comp-sep">&middot;</span>
		<a class="comp-link" href="about:blank">BUSINESS</a>
		<span class="comp-sep">&middot;</span>
		<a class="comp-link" href="about:blank">TECH</a>
	</div>
	<div class="content">
		<p class="curl"><img src="img/curly.png" style="height: 40px;"></p>
		<h1>Comp the Harvard Lampoon</h1>
		<p class="intro-text">
			The Lampoon is one of the premier comps at Harvard University. Click on a board position above to learn more.
		</p>
	</div>
</body>
</html>
