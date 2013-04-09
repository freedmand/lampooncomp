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
	<title>Review</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" type="text/css" href="css/comp.css">
	<link rel="stylesheet" type="text/css" href="css/fonts.css">
	
	<script src="js/jquery-1.8.3.js"></script>
	<script src="js/jquery-ui.js"></script>
	<script src="js/portfolio.js"></script>
</head>
<body class="portfolio">
	<?php include 'compheader.php'; ?>
	<div class="content" style="padding-top: 40px;">
<?php
error_reporting(E_ERROR | E_PARSE);

$FILES_DIR = "/Users/freedmand/private/";

$mysqli = new mysqli("localhost", "root", "root", "lampooncomp");
if (mysqli_connect_errno())
	exit('error');

if (!$result = $mysqli->query("SELECT id, article_id, title, istext, path FROM articles WHERE path IS NOT NULL AND title IS NOT NULL ORDER BY article_id_incr DESC LIMIT 10"))
	exit('error');

$num_rows = $result->num_rows;

for ($i = 0; $i < $num_rows; $i++)
{
	$row = $result->fetch_assoc();
	
	$id = $row['id'];
	
	if (!$result2 = $mysqli->query("SELECT name FROM users WHERE id='$id'"))
		exit('error');
	if ($result2->num_rows == 0)
		exit('error');
	$row2 = $result2->fetch_assoc();			
	$author_name = $row2['name'];
	$article_id = $row['article_id'];
	$title = $row['title'];
	$path = $row['path'];
	$istext = $row["istext"];
	
	if ($istext == '1')
	{
		$html = stripcslashes(file_get_contents($path));
		echo '<div class="entry">';
		echo '	<a class="title-list" href="editor.php?articleid=' . $article_id . '">' . $title . '</a><br><span class="author-list">By ' . $author_name . '</span>';
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