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
	<title>Art Submission</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" type="text/css" href="css/comp.css">
	<link rel="stylesheet" type="text/css" href="css/fonts.css">
	
	<script src="js/jquery-1.8.3.js"></script>
	<script src="js/jquery-ui.js"></script>
	<script src="js/art.js"></script>
</head>
<body class="form">
	<?php include 'compheader.php'; ?>
	
	<div id="title-row" class="form-row" style="margin-top: 48px; display: none;">
		<span class="form-label form-placeholder">Title:</span><input class="form-input" type="text" name="ftitle"><span class="form-placeholder"></span>
	</div>
	<h2 id="header-holder">Click below to submit an art piece.</h2>
	<button class="orange-button form-button" id="upload-content">Upload</button></div>
	<div id="img-container">
		<span id="img-loader"></span>
		<img id="img-preview" style="visibility: hidden;"></img>
	</div>
	<input type="file" id="file-upload" name="file" style="visibility:hidden;" onchange="uploadFile(this);">
</body>
</html>
<?php ob_end_flush(); ?>