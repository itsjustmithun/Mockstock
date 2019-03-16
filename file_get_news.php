<?php
	require_once 'db_functions.php';
	$db_handler = connect_db();
	if(gettype($db_handler) == 'string') {
		$html = "DB Error: ".$conn;
	} else {
		echo "db connected successfully"."<br />";

		$db_file_path = $_SERVER['DOCUMENT_ROOT'] . "/get_news.txt";
		$db_file = fopen($db_file_path,"r") or die("Unable to open db_config.txt!");
		while(!feof($db_file))
		{
			// echo trim(fgets($db_file))."<br />";
			$news = trim(fgets($db_file));
			$query = "INSERT INTO `news` Values (DEFAULT, '$news')";
			if(!$result = $db_handler->query($query)) {
				$msg = "DB Error: ".$db_handler->error;
			}
			else{
				$id=$db_handler->insert_id;
				// echo "inserted successfully".$id."<br />";
				echo "Inserted successfully $id <br/>";
			}
		}
		close_db($db_handler);
	}
?>
