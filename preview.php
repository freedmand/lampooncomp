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
		$articleid = $_GET["articleid"];
	?>
	<title>Art Review</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" type="text/css" href="css/comp.css">
	<link rel="stylesheet" type="text/css" href="css/fonts.css">
	
	<script src="js/jquery-1.8.3.js"></script>
	<script src="js/jquery-ui.js"></script>
	<script src="js/rangy-core.js"></script>
	<script src="js/rangy-cssclassapplier.js"></script>
	<script src="js/rangy-textrange.js"></script>
	<script src="js/jquery.hotkeys.js"></script>
	<script src="js/jquery.color.js"></script>
	<script src="js/preview.js"></script>
	
	<script>
		<?php
			echo "var name='$name';";
			echo "var articleid='$articleid';";
		?>
	</script>
</head>
<body class="form" onload=<?php
		$mysqli = new mysqli("localhost", "root", "root", "lampooncomp");
		if (mysqli_connect_errno())
			exit('error');
		
		if (!$result = $mysqli->query("SELECT title FROM articles WHERE article_id='$articleid'"))
			exit('error');
		if ($result->num_rows == 0)
			exit('error');
		$row = $result->fetch_assoc();
		$title = $row['title'];
		
		if (!$result = $mysqli->query("SELECT start,end,start_y,end_y,data,author_id FROM feedback WHERE article_id='$articleid'"))
			exit('error');
		
		echo '"';
		for ($i = 0; $i < $result->num_rows; $i++)
		{
			$row = $result->fetch_assoc();
			$author_id = $row['author_id'];
			if (!$result2 = $mysqli->query("SELECT name FROM users WHERE id='$author_id'"))
				exit('error');
			if ($result2->num_rows == 0)
				exit('error');
			$row2 = $result2->fetch_assoc();
			
			$author_name = $row2['name'];
			$start_x = $row['start'];
			$start_y = $row['start_y'];
			$end_x = $row['end'];
			$end_y = $row['end_y'];
			$data = $row['data'];
			
			echo "createComment('$author_name', " . $start_x . ", " . $start_y . ", " . $end_x . ", " . $end_y . ", '$data');";
		} echo '"'; ?>>
	<div class="notify-msg" style="margin-top: 1em;">
		Add comments by dragging<br>rectangles in the image below.</div>
	<div style="text-align: center;">
		<span class="art-panel">
			<span class='art-span'>
			</span>
			<span class="art-title art-span" style="float: left;"><?php echo $title; ?></span>
			<span class='art-span' style="float: right; margin-right: 205px;">
				<button id="back-button" class="compact-button purple-button" style="margin-right: 0.5em;" onclick="back();">Back</button>
				<button id="submit-button" class="compact-button orange-button" onclick="feedbackSubmit();">Submit</button>
			</span>
		<span class="comment-placeholder art-span" style="display: none; width:300px;"></span>
	</div>
	<div id="content-div" style="text-align: center;">
		<span id="img-container">
			<span id="img-loader"></span>
			<img class="img-full" src="img/usr/476144404.JPG"></img>
		</span>
		<span class="comment-placeholder" style="display: none; width:300px;"></span>
		<span class="comment-pane" style="display: none;">
			<div class='pane-header'>Feedback</div>
			<div class='comment-content'></div>
		</span>
	</div>
</body>
</html>
<?php ob_end_flush(); ?>