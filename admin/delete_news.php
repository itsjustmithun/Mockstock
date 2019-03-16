<?php 
$msg = "";
$success = 0;
require_once('../db_functions.php');
$db_handler = connect_db();
if(gettype($db_handler) == 'string') {
	$msg = "DB Error: ".$db_handler;
} else {
	if(isset($_POST['id']) && $_POST['id'] != "") {
		$id = $db_handler->real_escape_string(isset($_POST['id'])?$_POST['id']:"");
		$query = "DELETE FROM `news` WHERE `id`=$id";
		if(!$result = $db_handler->query($query)) {
			$msg = "DB Error: ".$db_handler->error;
		}
		else{
			$msg = "News data deleted successfully";
			$success = 1;
		}
	} else {
		$msg = "Error: Invalid Details!";
	}
}
close_db($db_handler);
echo json_encode(Array('success'=>$success,'message'=>$msg));
?>