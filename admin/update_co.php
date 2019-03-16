<?php
$msg = "";
$success = 0;
require_once('../db_functions.php');
$db_handler = connect_db();

$name = null ;

if(gettype($db_handler) == 'string') {
	$msg = "DB Error: ".$db_handler;
} else {
	if(isset($_POST['id']) && $_POST['id'] != "") {
		$id = $db_handler->real_escape_string(isset($_POST['id'])?$_POST['id']:"");
		$name = $db_handler->real_escape_string(isset($_POST['name'])?$_POST['name']:"");
		$description = $db_handler->real_escape_string(isset($_POST['description'])?$_POST['description']:"");
		$price = $db_handler->real_escape_string(isset($_POST['price'])?$_POST['price']:"");
		$minval = $db_handler->real_escape_string(isset($_POST['minval'])?$_POST['minval']:"");
		$maxval = $db_handler->real_escape_string(isset($_POST['maxval'])?$_POST['maxval']:"");
		$remaining = $db_handler->real_escape_string(isset($_POST['remaining'])?$_POST['remaining']:"");
	}		
	if(!strlen($name) || !strlen($price) || !strlen($remaining)){
		$msg = "Error: Invalid Details!" ;
	} else {
		if(strlen($description)) {
			$query = "UPDATE `stocks` SET `name`='$name',`description`='$description',`price`='$price',`minval`='$minval',`maxval`='$maxval',`remaining`='$remaining' WHERE `id`=$id";
		} else {
			$query = "UPDATE `stocks` SET `name`='$name',`price`='$price',`minval`='$minval',`maxval`='$maxval',`remaining`='$remaining' WHERE `id`=$id";
		}
		if(!$result = $db_handler->query($query)) {
			$msg = "DB Error: ".$db_handler->error;
		}
		else{
			$msg = "Company data updated successfully";
			$success = 1;
			
		}
	}
}
close_db($db_handler);
echo json_encode(Array('success'=>$success,'message'=>$msg));
?>