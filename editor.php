<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php
		$data = require('logincheck.php');
		if ($data != 'true')
			forceLogin();
		$board = $_SESSION['board'];
		
		if ($board != "lit" && $board != "both")
			forceLogin();
	?>
	<title>Literature Submission</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" type="text/css" href="css/comp.css">
	<link rel="stylesheet" type="text/css" href="css/fonts.css">
	
	<script src="js/jquery-1.8.3.js"></script>
	<script src="js/jquery-ui.js"></script>
	<script src="js/rangy-core.js"></script>
	<script src="js/rangy-cssclassapplier.js"></script>
	<script src="js/jquery.hotkeys.js"></script>
	
	<script src="js/textbox.js"></script>
</head>
<body class="main">
	<div class="notify-msg" style="margin-top: 1em;">
		Type your piece below, or <a href="#">upload</a> it.<br>
		Focus on the content, not the formatting.
	</div>
	<table style="margin-left: auto; margin-right: auto; table-layout: fixed;">
		<tr>
			<td class="panel">
				<span style="float-left;">
					<button id="bold-button" class="formatting-button inactive-button" style="background-image: url('img/bold.png');" onclick="bold();"></button>
					<button id="italic-button" class="formatting-button inactive-button" style="background-image: url('img/italic.png');" onclick="italic();"></button>
					<button id="underline-button" class="formatting-button inactive-button" style="background-image: url('img/underline.png');" onclick="underline();"></button>
					<button id="comment-button" class="formatting-button inactive-button" style="background-image: url('img/comment.png');" onclick="comment();"></button>
					<button id="word-button" class="formatting-button inactive-button" style="background-image: url('img/word.png');" onclick="$('#file-upload').click(); return false;"></button>
				</span>
				<span style="float: right;"><button id="submit-button" class="compact-button orange-button" onclick="submit();">Submit</button></span>
				<span style="float: right;"><button id="back-button" class="compact-button purple-button" style="margin-right: 0.5em;" onclick="back();">Back</button></span>
			</td>
			<td></td>
		</tr>
		<tr>
			<td>
				<div class="paper-holder paper-text">
					<div class="top-bar"></div>
					<input type="text" class="paper-title" placeholder="Click to edit title...">
					<div class="paper" contenteditable="true" onpaste="pasteHandle(event); return false;" onblur="disableButtons();" onchange="updateButtons();" onfocus="updateButtons();" onselect="updateButtons();" onkeydown="updateButtons();" onkeyup="updateButtons();" onmousedown="updateButtons();" onmouseup="updateButtons();"></div>
				</div>
			</td>
			<td>
				<div class="comment-pane" style="display: none;">
					<div class='pane-header'>Feedback</div>
				</div>
			</td>
		</tr>
	</table>
	<input type="file" id="file-upload" name="file" style="visibility:hidden;" onchange="uploadFile(this);">
</body>
</html>
<?php ob_end_flush(); ?>