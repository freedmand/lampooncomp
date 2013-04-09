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
	<title>Directors</title>
	<link rel="stylesheet" type="text/css" href="css/comp.css">
	<link rel="stylesheet" type="text/css" href="css/fonts.css">
	
	<script src="js/jquery-1.8.3.js"></script>
	<script src="js/jquery-ui.js"></script>
</head>
<body class="form">
	<?php include 'compheader.php'; ?>
	<div style="margin-top: 100px; padding-bottom: 20px; text-align: center; line-spacing: 2em;">
		<?php
			$mysqli = new mysqli("localhost", "root", "root", "lampooncomp");
			if (mysqli_connect_errno())
				exit('error');
			
			if (!$result = $mysqli->query("SELECT name, email FROM users WHERE director='1' AND registered='1'"))
				exit('error');
			
			for ($i = 0; $i < $result->num_rows; $i++)
			{
				$row = $result->fetch_assoc();
				echo '<div style="margin-bottom: 1em;"><h2 style="display: inline;">' . $row['name'] . '</h2>&nbsp;&nbsp;&nbsp;';
				echo '<h3 style="display: inline;"><a href="mailto:' . $row['email'] . '">' . $row['email'] . '</a></h3></div>';
			}
		?>
	</div>
</body>
</html>
<?php ob_end_flush(); ?>