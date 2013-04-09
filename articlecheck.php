<?php

error_reporting(E_ERROR | E_PARSE);

try
{
	if (!isset($articleid))
		return 'false';
	
	$mysqli = new mysqli("localhost", "root", "root", "lampooncomp");
	if (mysqli_connect_errno())
		return 'error';
	if (!$result = $mysqli->query("SELECT title,path FROM articles WHERE id='$id' AND article_id='$articleid' AND istext='$istext'"))
		return 'error';
	
	if ($result->num_rows != 0)
	{
		$row = $result->fetch_assoc();
		$title = $row['title'];
		$path = $row['path'];
		return array(
			"title" => $title,
			"path" => $path
		);
	}
	else
		return 'false';
}
catch (Exception $e)
{
	return 'error';
}
?>