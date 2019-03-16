<?php 
$msg = "";
$success = 0;
$id=-1;
require_once('../db_functions.php');
$db_handler = connect_db();
if(gettype($db_handler) == 'string') {
	$msg = "DB Error: ".$db_handler;
} else {
	$event = $db_handler->real_escape_string(isset($_POST['event'])?$_POST['event']:"");
	if(!strlen($event)){
		$msg = "Error: Invalid Event!";
	} else {
		$query = "INSERT INTO `news` Values (DEFAULT, '$event')";
		if(!$result = $db_handler->query($query)) {
			$msg = "DB Error: ".$db_handler->error;
		}
		else{
			$id=$db_handler->insert_id;
			$msg = "News data inserted successfully";
			$success = 1;
		}
	}
}
close_db($db_handler);
echo json_encode(Array('success'=>$success,'message'=>$msg,'id'=>$id));
?>