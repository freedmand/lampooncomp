<?php
	$type = pathinfo($_SERVER['HTTP_X_FILE_NAME'], PATHINFO_EXTENSION);
	
	$tmp_file = tempnam(sys_get_temp_dir(), 'doc');
	$tmp_in = $tmp_file . '.' . $type;
	$tmp_out = $tmp_file . '.html';
	file_put_contents($tmp_in, file_get_contents("php://input"));
	
	$handle = popen("abiword --plugin AbiCommand 2>&1", "w");
	fwrite($handle, "convert $tmp_in $tmp_out html \n");
	pclose($handle);
	
	system("python convert.py $tmp_out 2>&1");
	
	// exit(file_get_contents($tmp_out));
	//   //include("ome.html");
	// 
	// exit($tmp_file);
	// exit();
	// exit('dog');
	// exit($_SERVER['HTTP_X_FILE_NAME']);

?>