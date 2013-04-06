<!DOCTYPE html>
<html lang="en">
<head>
	<title>Validate email</title>
	<link rel="stylesheet" type="text/css" href="css/comp.css">
	<link rel="stylesheet" type="text/css" href="css/fonts.css">
	
	
	<script src="js/jquery-1.8.3.js"></script>
	<script src="js/jquery-ui.js"></script>
	<script src="js/register.js"></script>
</head>
<body class="form">
	<div class="form-header">
		Verify your email
	</div>
	<div id="msg" class="error-msg">
<?php

error_reporting(E_ERROR | E_PARSE);

$width = 50000;
$height = 50000;

function returnError($e)
{
	echo($e);
}

try
{
	$mysqli = new mysqli("localhost", "root", "root", "lampooncomp");
	if (mysqli_connect_errno())
	{
		returnError('An error occurred. Please try again.');
	}
	else
	{
		$code = $_GET["v"];
	
		$padlen = strlen(dechex($width * $height));
		if (strlen($code) != $padlen + 32)
		{
			returnError('Incorrect validation code. Please try again.');
		}
		else
		{
			$prefix = substr($code,0,$padlen);
			$suffix = substr($code,$padlen);

			$id = hexdec($prefix);

			if (!$result = $mysqli->query("SELECT email FROM users WHERE id='$id' AND registered='0'"))
			{
				returnError('An error occurred. Please try again.');
			}
			else
			{
				if ($result->num_rows != 1)
				{
					returnError('An error occurred. Please try again.');
				}
				else
				{
					$row=$result->fetch_assoc();
					$email = $row["email"];

					if (strcmp($suffix, md5($prefix . "_lampooncastle_comp_harv")) != 0)
					{
						returnError('Incorrect validation code. You may have already registered. <a href="login.html">Log in?</a>');
					}
					else
					{
						setcookie('reg_email', $email, time()+60*60*24*14, "/");
						setcookie('reg_val', $code, time()+60*60*24*14, "/");
						header('Location: createaccount.html');
					}
				}
			}

		}
	}
}
catch (Exception $e)
{
	returnError('An error occurred. Please try again.');
}
?>
</div>
</body>
</html>