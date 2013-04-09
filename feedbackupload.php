<?php
	error_reporting(E_ERROR | E_PARSE);

	include 'hash.php';
	include 'permissioncheck.php';

	ob_start();
	
	// random seed values
	$k1_id = 1750680230;
	$k2_id = 2187714091;
	$k3_id = 1575001739;
	$q_id = 973325368;

	$data = require('logincheck.php');
	if ($data != 'true')
		forceLogin();

	$id = $_SESSION["id"];

	$mysqli = new mysqli("localhost", "root", "root", "lampooncomp");
	if (mysqli_connect_errno())
		exit('error');
	
	$article_id = $mysqli->real_escape_string($_POST["articleid"]);
	$data = $mysqli->real_escape_string($_POST["data"]);
	$image = true;
	if (isset($_POST["start"]))
	{
		$image = false;
		$start = $mysqli->real_escape_string($_POST["start"]);
		$end = $mysqli->real_escape_string($_POST["end"]);
	}
	else
	{
		$start_x = $mysqli->real_escape_string($_POST["start_x"]);
		$start_y = $mysqli->real_escape_string($_POST["start_y"]);
		$end_x = $mysqli->real_escape_string($_POST["end_x"]);
		$end_y = $mysqli->real_escape_string($_POST["end_y"]);
	}
	
	if (!$image)
	{
		if (!$result = $mysqli->query("SELECT start,end FROM feedback WHERE article_id='$article_id'"))
			exit('error');
		for ($i = 0; $i < $result->num_rows; $i++)
		{
			$row = $result->fetch_assoc();
			$start2 = $row['start'];
			$end2 = $row['end'];

			# check boundary point
			if (($start >= $start2 && $start < $end2) || ($end > $start2 && $end <= $end2) ||
				($start2 >= $start && $start2 < $end) || ($end2 > $start && $end2 <= $end))
			{
				exit('false');
			}
		}
	}
	
	if (!$result = $mysqli->query("SELECT id FROM articles WHERE article_id='$article_id'"))
		exit('error');
	
	if ($result->num_rows != 0)
	{
		$row = $result->fetch_assoc();
		$owner = $row['id'];
		$perm = hasPermissions($mysqli, $id, $owner);
		if ($perm != 'true')
			exit($perm);
	}
	else
		exit('false');
	
	if ($image)
	{
		if (!$result = $mysqli->query("SELECT * FROM feedback WHERE id='$owner' AND article_id='$article_id' AND author_id='$id' AND data='$data' AND start='$start_x' AND end='$end_x' AND start_y='$start_y' AND end_y='$end_y'"))
			exit('error');
		if ($result->num_rows != 0)
			exit('false');
		if (!$result = $mysqli->query("INSERT INTO feedback (id,article_id,author_id,data,start,end,start_y,end_y) VALUES('$owner','$article_id','$id','$data','$start_x','$end_x','$start_y','$end_y')"))
			exit('error');
	}
	else
	{
		if (!$result = $mysqli->query("INSERT INTO feedback (id,article_id,author_id,data,start,end) VALUES('$owner','$article_id','$id','$data','$start','$end')"))
			exit('error');
	}
	
	exit('true');
	
	ob_end_flush();
?>