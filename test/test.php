<?php
	setcookie("phpcook", 'dog', time()+3600, "/");
	exit($_COOKIE['testcook']);
?>