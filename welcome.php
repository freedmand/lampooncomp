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
	
	<script src="js/welcome.js"></script>
</head>
<body class="form">
	<?php include 'compheader.php'; ?>
	<div class="content" style="padding-top: 106px;">
		<h1>Welcome, <?php echo $first?></h1>
		<button class="form-button orange-button" onclick=<?php echo '"submitPiece(this, \'' . $board. '\');"'?>>
			Submit piece
		</button>
	</div>
	<div class="castle">
</body>
</html>
<?php ob_end_flush(); ?>