<?php
	function connect_db($create_db=false) {
		//$db_file_path = $_SERVER['DOCUMENT_ROOT'] . "/db_config.txt";
		//$db_file = fopen($db_file_path,"r") or die("Unable to open db_config.txt!");
		$server = "localhost"; // trim(fgets($db_file));
		$db_user ="root";// trim(fgets($db_file));
		$db_pass = "" ;//trim(fgets($db_file));
		$db_name = "mockstock";// trim(fgets($db_file));
		//fclose($db_file);
		if(!$server || !$db_user || !$db_name)
			return "Missing Database credentials";
		$mysqli;
		$mysqli = new mysqli($server, $db_user, $db_pass, $db_name);
		if($mysqli->connect_error) {
			if($mysqli->connect_errno == '1049'){
				if($create_db){
					$mysqli = new mysqli($server, $db_user, $db_pass);
					if(!$result=$mysqli->query("CREATE DATABASE $db_name"))
						return "Database Error: " . $mysqli->error;
					if(!$mysqli->select_db($db_name))
						return "Database Error: " . $mysqli->error;
					return $mysqli;
				}
				return "Database does not exist";
			}
			else
				return 'Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error;
		}
		/*
		 * Use this instead of $connect_error if you need to ensure
		 * compatibility with PHP versions prior to 5.2.9 and 5.3.0.
		 *
		if (mysqli_connect_error()) {
		    die('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
		}*/
		$mysqli->set_charset("utf8");
		return $mysqli;

	}
	function close_db($mysqli) {
		$mysqli->close();
	}

?>
