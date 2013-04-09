<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php
		$data = require('logincheck.php');
		if ($data != 'true')
			forceLogin();
		$board = $_SESSION['board'];
		$id = $_SESSION['id'];
		$name = $_SESSION['name'];
		
		if ($board != "lit" && $board != "both")
			forceLogin();
		
		$preload = false;
		if (isset($_GET["articleid"]))
		{
			$preload = true;
			$articleid = $_GET["articleid"];
			$istext = '1';
			$data = (include 'articlecheck.php');
			if ($data == 'error')
			{
				$title = "error";
				$preload = false;
				// show unexpected error occurred
			}
			else if ($data == 'false')
			{
				$title = "false";
				$preload = false;
				// show invalid articleid error
			}
			else
			{
				$title = $data["title"];
				$path = $data["path"];
			}
		}
	?>
	<title>Literature <?php if ($preload) echo 'Review'; else echo 'Submission'; ?></title>
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
	<script src="js/textbox.js"></script>
	
	<script>
		<?php
			echo "var name='$name';";
			echo "var articleid='$articleid';";
		?>
	</script>
</head>
<body class="main" onload=<?php
		$mysqli = new mysqli("localhost", "root", "root", "lampooncomp");
		if (mysqli_connect_errno())
			exit('error');
		
		if (!$result = $mysqli->query("SELECT start,end,data,author_id FROM feedback WHERE article_id='$articleid'"))
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
			
			$start = $row['start'];
			$end = $row['end'];
			$data = $row['data'];
			
			echo "createComment($start, " . $end . ", '" . $author_name . "', '" . $data . "');";
		} echo '"'; ?>>
	<div id="msg" class="error-msg" style="display: none; margin-top: 20px;"></div>
	<div class="notify-msg" style="margin-top: 1em;">
<?php
	if (!$preload)
	{
		echo 'Type your piece below, or <a href="#" onclick="$(\'#file-upload\').click(); return false;">upload</a> it.<br>';
		echo 'Focus on the content, not the formatting.';
	}
	else
	{
		echo 'Add comments by selecting text below<br>and clicking on the pencil tool.';
		if ($_SESSION['director'] == '0')
			echo '<br><i>(let your comp director add feedback first)</i>';
	}
?>
	</div>
	<div style="text-align: center;">
		<span class="panel">
			<span style="float: left;">
				<?php
if (!$preload)
{
	echo '<button id="bold-button" alt="Bold" class="formatting-button inactive-button" style="background-image: url(\'img/bold.png\');" onclick="bold();"></button>';
	echo '<button id="italic-button" alt="Italic" class="formatting-button inactive-button" style="background-image: url(\'img/italic.png\');" onclick="italic();"></button>';
	echo '<button id="underline-button" alt="Underline" class="formatting-button inactive-button" style="background-image: url(\'img/underline.png\');" onclick="underline();"></button>';
	echo '<button id="word-button" alt="Document upload" class="formatting-button inactive-button" style="background-image: url(\'img/word.png\');" onclick="$(\'#file-upload\').click(); return false;"></button>';
}
else
{
	echo '<button id="comment-button" alt="Comment" class="formatting-button inactive-button wide-formatting-button" style="background-image: url(\'img/comment.png\');" onclick="comment();"></button>';
}
				?>
			</span>
			<span style="float: right;"><button id="submit-button" class="compact-button orange-button" onclick=<?php echo $preload ? '"feedbackSubmit();"' :  '"submit();"';?>>Submit</button></span>
			<span style="float: right;"><button id="back-button" class="compact-button purple-button" style="margin-right: 0.5em;" onclick="window.location='portfolio.php';">Back</button></span>
			</span>
		<span class="comment-placeholder" style="display: none; width:300px;"></span>
	</div>
	<div style="text-align: center;">
			<span class="paper-holder paper-text">
				<div class="top-bar"></div>
				<input type="text" class="paper-title"<?php if ($preload) echo ' readonly="true" value="' . $title . '"'; ?> placeholder="Click to edit title...">
				<div class="paper"<?php if (!$preload) echo ' contenteditable="true"' ?> onpaste="pasteHandle(event); return false;" onblur="disableButtons();" onchange="updateButtons();" onfocus="updateButtons();" onselect="updateButtons();" onkeydown="updateButtons();" onkeyup="updateButtons();" onmousedown="updateButtons();" onmouseup="updateButtons();"><?php if ($preload) echo(file_get_contents($path)); ?></div>
			</span>
			<span class="comment-placeholder" style="display: none; width:300px;"></span>
			<span class="comment-pane" style="display: none;">
				<div class='pane-header'>Feedback</div>
				<div class='comment-content'></div>
			</span>
	</div>
	<input type="file" id="file-upload" name="file" style="visibility:hidden;" onchange="uploadFile(this);">
</body>
</html>
<?php ob_end_flush(); ?>