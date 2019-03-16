<?php 
$msg = "";
$success = 0;
require_once('../db_functions.php');
$db_handler = connect_db();
$id = null ;
$event = null ;


if(gettype($db_handler) == 'string') {
	$msg = "DB Error: ".$db_handler;
} else {
	if(isset($_POST['id']) && $_POST['id'] != "") {
		$id = $db_handler->real_escape_string(isset($_POST['id'])?$_POST['id']:"");
		$event = $db_handler->real_escape_string(isset($_POST['event'])?$_POST['event']:"");
	}
	if(!strlen($event)){
		$msg = "Error: Invalid Event!";
	} else {
		$query = "UPDATE `news` SET `event`='$event' WHERE `id`=$id";
		if(!$result = $db_handler->query($query)) {
			$msg = "DB Error: ".$db_handler->error;
		}
		else{
			$msg = "News data updated successfully";
			$success = 1;
		}
	}
}
close_db($db_handler);
echo json_encode(Array('success'=>$success,'message'=>$msg));
?>