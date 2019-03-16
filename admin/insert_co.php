<?php
$msg = "";
$success = 0;
$id=-1;
$flag="";
require_once('../db_functions.php');
$db_handler = connect_db();
if(gettype($db_handler) == 'string') {
	$msg = "DB Error: ".$db_handler;
} else {
	$name = $db_handler->real_escape_string(isset($_POST['name'])?$_POST['name']:"");
	$description = $db_handler->real_escape_string(isset($_POST['description'])?$_POST['description']:"");
	$price = $db_handler->real_escape_string(isset($_POST['price'])?$_POST['price']:"");
	$minval = $db_handler->real_escape_string(isset($_POST['minval'])?$_POST['minval']:"");
	$maxval = $db_handler->real_escape_string(isset($_POST['maxval'])?$_POST['maxval']:"");
	$remaining = $db_handler->real_escape_string(isset($_POST['remaining'])?$_POST['remaining']:"");
	if(!strlen($name) || !strlen($description) || !strlen($price) || !strlen($remaining)){
		$msg = "Error: Invalid Details!" ;
	} else {
		$query = "INSERT INTO `stocks` Values (DEFAULT, '$name', '$description', '$price', '$minval', '$maxval', '$remaining')";
		if(!$result = $db_handler->query($query)) {
			$msg = "DB Error: ".$db_handler->error;
		}
		else{
			$id=$db_handler->insert_id;
			$query1 = "SELECT `ticket` FROM `teams`";
			if(!$result1 = $db_handler->query($query1)) {
				$msg = "DB Error: ".$db_handler->error;
			} else {
				$flag = true;
				while($row1 = $result1->fetch_assoc()){
					$flag = false;
					$ticket_no = $row1['ticket'];
					$query2 = "INSERT INTO `stocks_owned` VALUES('$ticket_no','$id',0)";
					if(!$result2 = $db_handler->query($query2)) {
						$flag = false;
						$msg = "DB Error: ".$db_handler->error;
					} else {
						$flag = true;
					}
				}
			}
			if($flag) {
				$msg = "Company data inserted successfully";
				$success = 1;
			}
			
		}
	}
}
close_db($db_handler);
echo json_encode(Array('success'=>$success,'message'=>$msg,'id'=>$id));
?>