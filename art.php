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
	
	<h2>Click below to submit an art piece.</h2>
	<div id="content-holder"><button class="orange-button form-button" onclick="$('#file-upload').click(); return false;">Upload</button></div>
	<input type="text" class="paper-title" placeholder="Click to edit title..." style="visibility: hidden;">
	<div id="img-container">
		<span id="img-loader"></span>
		<img id="img-preview"></img>
	</div>
	<input type="file" id="file-upload" name="file" style="visibility:hidden;" onchange="uploadFile(this);">
</body>
</html>
<?php ob_end_flush(); ?>