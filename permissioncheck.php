<?php
	// checks if user has succificient privileges to view and comment on pieces by owner
	function hasPermissions($mysqli, $user, $owner)
	{
		if ($user == $owner)
			return 'true';
		if (!$result = $mysqli->query("SELECT board, director FROM users WHERE id='$user'"))
			return 'error';
		
		if ($result->num_rows != 0)
		{
			$row = $result->fetch_assoc();
			$user_board = $row['board'];
			$user_director = $row['director'];
		}
		else
			return 'false';
		
		if (!$result = $mysqli->query("SELECT board, director FROM users WHERE id='$owner'"))
			return 'error';
		
		if ($result->num_rows != 0)
		{
			$row = $result->fetch_assoc();
			$owner_board = $row['board'];
			$owner_director = $row['director'];
		}
		else
			return 'false';
		
		if ($owner_director == '1' || $user_director == '0')
			return 'false';
		
		if ($owner_board == 'lit' || $owner_board == 'both')
			if ($user_board != 'lit' && $user_board != 'both')
				return 'false';
		
		if ($owner_board == 'art' || $owner_board == 'both')
			if ($user_board != 'art' && $user_board != 'both')
				return 'false';
		
		return 'true';
	}
?>