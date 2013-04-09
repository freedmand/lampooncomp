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
	<title>Account Management</title>
	<link rel="stylesheet" type="text/css" href="css/comp.css">
	<link rel="stylesheet" type="text/css" href="css/fonts.css">
	
	<script src="js/jquery-1.8.3.js"></script>
	<script src="js/jquery-ui.js"></script>
	
	<script src="js/account.js"></script>
</head>
<body class="form">
	<?php include 'compheader.php'; ?>
	<div class="vspace" style="padding-bottom: 20px;">
		<div id="msg" class="error-msg" style="display: none;"></div>
		<div class="notify-msg">
			Enter your current password to change a setting.<br>Click the right arrow for more settings.
		</div>
		<div id="pass-row" class="form-row">
			<span class="form-label form-placeholder">Password:</span><input class="form-input" type="password" name="fpass"><span class="form-placeholder"></span>
		</div>
		<div class="settings-panel vspace">
			<button id="next-button" class="account-button red-button" onclick="next();">&#x27a1;</button>
			<div id="old-pass-row" class="form-row">
				<span id="finput-label" class="sub-form-label form-placeholder">New password:</span><input class="form-input" type="password" name="finput"><span class="form-placeholder"></span>
			</div>
			<div id="old-pass-conf-row" class="form-row">
				<span class="sub-form-label form-placeholder">Confirm:</span><input class="form-input" type="password" name="fconfirm"><span class="form-placeholder"></span>
			</div>
			<button id="apply-button" type="button" class="form-button purple-button" onclick="apply();">Apply</button>
		</div>
	</div>
</body>
</html>
<?php ob_end_flush(); ?>