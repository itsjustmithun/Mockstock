<?php
if(isset($_FILES['import-file'])){
	$errors= array();
	$file_name = $_FILES['import-file']['name'];
	$file_size =$_FILES['import-file']['size'];
	$file_tmp =$_FILES['import-file']['tmp_name'];
	$file_type=$_FILES['import-file']['type'];
	$file_ext = strtolower(explode('.', $file_name)[1]);
	$extensions= array("sql");

	if(in_array($file_ext,$extensions)=== false){
		$errors[]="extension not allowed, please choose a .SQL file.";
	}

	/*if($file_size > 2097152){
	$errors[]='File size must be less than 2 MB';
	}*/

	if(empty($errors)==true){
		$filename = $_SERVER['DOCUMENT_ROOT'] . "/temp_db_dump.sql";
		move_uploaded_file($file_tmp,$filename);
		$db_file_path = $_SERVER['DOCUMENT_ROOT'] . "/db_config.txt";
		$db_file = fopen($db_file_path,"r") or die("Unable to open db_config.txt!");
		$mysql_host = trim(fgets($db_file));
		$mysql_username = trim(fgets($db_file));
		$mysql_password = trim(fgets($db_file));
		$mysql_database = trim(fgets($db_file));
		fclose($db_file);

		// Connect to MySQL server
		mysql_connect($mysql_host, $mysql_username, $mysql_password) or die('Error connecting to MySQL server: ' . mysql_error());
		// Select database
		mysql_select_db($mysql_database) or die('Error selecting MySQL database: ' . mysql_error());
		mysql_query("SET FOREIGN_KEY_CHECKS=0");
		// Temporary variable, used to store current query
		$templine = '';
		// Read in entire file
		$lines = file($filename);
		// Loop through each line
		foreach ($lines as $line)
		{
			// Skip it if it's a comment
			if (substr($line, 0, 2) == '--' || $line == '')
			continue;

			// Add this line to the current segment
			$templine .= $line;
			// If it has a semicolon at the end, it's the end of the query
			if (substr(trim($line), -1, 1) == ';')
			{
				// Perform the query
				mysql_query($templine) or print('Error performing query \'<strong>' . $templine . '\': ' . mysql_error() . '<br /><br />');
				// Reset temp variable to empty
				$templine = '';
			}
		}
		mysql_query("SET FOREIGN_KEY_CHECKS=1");
		echo "Tables imported successfully";
		unlink($filename);
	}else{
		print_r($errors);
	}
}
?>