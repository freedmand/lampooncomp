<?php
	function getrand()
	{
		return md5(mt_rand()) . md5(mt_rand());
	}
	function getCookieVal($id, $rand)
	{
		return dechex($id) . '-' . $rand;
	}
	function parseCookieVal($val)
	{
		$split = explode("-", $val);
		$split[0] = hexdec($split[0]);
		return $split;
	}
	function issueNewCookie($mysqli,$id)
	{
		$rand = getrand();
		if (!$result = $mysqli->query("INSERT INTO cookiestore (id,rand,expiry) VALUES('$id','$rand',NOW() + INTERVAL 2 WEEK)"))
			exit('error');
		
		setcookie('lcomp_user', getCookieVal($id, $rand), time()+60*60*24*14, "/");
		return $rand;
	}
	
	error_reporting(E_ERROR | E_PARSE);
	
	try
	{
		$mysqli = new mysqli("localhost", "root", "root", "lampooncomp");
		if (mysqli_connect_errno())
			exit('error');
	
		if (isset($_COOKIE['lcomp_user']))
		{
			$split = parseCookieVal($_COOKIE['lcomp_user']);
			$id = $split[0];
			$rand = $split[1];
		
			// check if cookie is valid in database
			if (!$result = $mysqli->query("SELECT expiry FROM cookiestore WHERE id='$id' AND rand='$rand'"))
				exit('error');
			if ($result->num_rows > 0)
			{
				$row=$result->fetch_assoc();
				$expiry = $row['expiry'];
			
				$success = 0;
				if ($result = $mysqli->query("SELECT NOW()<'$expiry'"))
				{
					if ($result->num_rows > 0)
					{
						$mysqli->query("DELETE FROM cookiestore WHERE id='$id' AND rand='$rand'");
					
						$row = $result->fetch_assoc();
						$arrayval = array_values($row);
						$bool = $arrayval[0];
						if ($bool == '0')
						{
							exit('login');
						}
						else
						{
							$rand = issueNewCookie($mysqli, $id);
							exit($rand);
						}
					}
					else
					{
						exit('error');
					}
				}
				else
					exit('error');
			
				$mysqli->query("DELETE FROM cookiestore WHERE id='$id' AND rand='$rand'");
				$rand = issueNewCookie($mysqli, $id);
			
				exit($rand);
			}
			else
				exit('login');
		}
		else
			exit('login');
	}
	catch (Exception $e)
	{
		exit('error');
	}
?>