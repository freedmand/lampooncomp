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
		$board = $_SESSION['board'];
		
		function html_cut($text, $max_length)
		{
			$tags   = array();
			$result = "";

			$is_open   = false;
			$grab_open = false;
			$is_close  = false;
			$in_double_quotes = false;
			$in_single_quotes = false;
			$tag = "";

			$i = 0;
			$stripped = 0;

			$stripped_text = strip_tags($text);

			while ($i < strlen($text) && $stripped < strlen($stripped_text) && $stripped < $max_length)
			{
				$symbol  = $text{$i};
				$result .= $symbol;

				switch ($symbol)
				{
				   case '<':
						$is_open   = true;
						$grab_open = true;
						break;

				   case '"':
					   if ($in_double_quotes)
						   $in_double_quotes = false;
					   else
						   $in_double_quotes = true;

					break;

					case "'":
					  if ($in_single_quotes)
						  $in_single_quotes = false;
					  else
						  $in_single_quotes = true;

					break;

					case '/':
						if ($is_open && !$in_double_quotes && !$in_single_quotes)
						{
							$is_close  = true;
							$is_open   = false;
							$grab_open = false;
						}

						break;

					case ' ':
						if ($is_open)
							$grab_open = false;
						else
							$stripped++;

						break;

					case '>':
						if ($is_open)
						{
							$is_open   = false;
							$grab_open = false;
							array_push($tags, $tag);
							$tag = "";
						}
						else if ($is_close)
						{
							$is_close = false;
							array_pop($tags);
							$tag = "";
						}

						break;

					default:
						if ($grab_open || $is_close)
							$tag .= $symbol;

						if (!$is_open && !$is_close)
							$stripped++;
				}

				$i++;
			}

			while ($tags)
				$result .= "</".array_pop($tags).">";

			return $result;
		}
	?>
	<title>Feedback</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" type="text/css" href="css/comp.css">
	<link rel="stylesheet" type="text/css" href="css/fonts.css">
	
	<script src="js/jquery-1.8.3.js"></script>
	<script src="js/jquery-ui.js"></script>
</head>
<body class="portfolio">
	<?php include 'compheader.php'; ?>
	<div class="content" style="padding-top: 40px;">
		<h2 style="margin-top:0; margin-bottom:20px;">The following articles have been reviewed:</h2>
<?php
error_reporting(E_ERROR | E_PARSE);

$FILES_DIR = "/Users/freedmand/private/";

$mysqli = new mysqli("localhost", "root", "root", "lampooncomp");
if (mysqli_connect_errno())
	exit('error');

if ($_SESSION['director'] == '1')
{
	if (!$result = $mysqli->query("SELECT article_id FROM feedback"))
		exit('error');
}
else
{
	if (!$result = $mysqli->query("SELECT article_id FROM feedback WHERE id='$id'"))
		exit('error');
}

$article_ids = array();
for ($i = 0; $i < $result->num_rows; $i++)
{
	$row = $result->fetch_assoc();
	
	$article_id = $row['article_id'];
	$article_ids[] = $article_id;
}

$unique_articles = array_unique($article_ids);
for ($i = 0; $i < count($unique_articles); $i++)
{
	$article_id = $unique_articles[$i];
	if (!$result = $mysqli->query("SELECT title, istext, path FROM articles WHERE article_id='$article_id' AND path IS NOT NULL AND title IS NOT NULL"))
		exit('error');
	if ($result->num_rows == 0)
		continue;
	
	$row = $result->fetch_assoc();
	
	$title = $row['title'];
	$path = $row['path'];
	$istext = $row["istext"];
	
	if ($istext == '1')
	{
		$html = stripcslashes(file_get_contents($path));
		echo '<div class="entry">';
		echo '	<a class="title-list" href="editor.php?articleid=' . $article_id . '">' . $title . '</a><br><span class="author-list">By ' . $name . '</span>';
		echo '	<div class="sample-text">';
		echo html_cut($html, 300) . '...';
		echo ' <a class="title-list" style="font-size: 1em;" href="editor.php?articleid=' . $article_id . '">Continue</a>';
		echo '	</div>';
		echo '</div>';
	}
	else
	{
		echo '<div class="entry">';
		// echo '	<div><span class="author-list">' . $name . '</span><div class="separator"></div><span class="title-list">' . $title . '</span></div>';
		echo '	<a class="title-list" href="preview.php?articleid=' . $article_id . '">' . $title . '</a><br><span class="author-list">By ' . $name . '</span>';
		// echo '	<div id="img-container"';
		echo "		<img class='img-preview' src='$path'>";
		// echo '	</div>';
		echo '</div>';
	}
}
?>
	</div>
</body>
</html>
<?php ob_end_flush(); ?>