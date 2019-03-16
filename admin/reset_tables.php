<?php 
$tables = Array('stocks','news','teams','log','stocks_owned','event_details');
require_once '../db_functions.php';
$conn = connect_db();
if (gettype($conn) == 'string') {
	echo json_encode(Array('success'=>0,'msg'=>$conn));
} else {
	$query = 'SET FOREIGN_KEY_CHECKS=0';
	if(!$result = $conn->query($query)){
		echo json_encode(Array('success'=>0,'msg'=>$conn->error));
		exit;
	}
	if(isset($_GET['tables'])&&$_GET['tables'] == 'restrict_few') {
		$query = 'TRUNCATE TABLE teams';
		if(!$result = $conn->query($query))
			echo json_encode(Array('success'=>0,'msg'=>$conn->error));
		else
			echo json_encode(Array('success'=>1,'msg'=>'success'));
	} else {
		$msg_ar = Array();
		foreach ($tables as $value) {
			$query = 'TRUNCATE TABLE '.$value;
			if(!$result = $conn->query($query)){
				array_push($msg_ar,	$conn->error);
			}
		}
		if(count($msg_ar) === 0)
			echo json_encode(Array('success'=>1,'msg'=>'success'));
		else
			echo json_encode(Array('success'=>0,'msg'=>serialize($msg_ar)));
	}
	$query = 'SET FOREIGN_KEY_CHECKS=1';
	if(!$result = $conn->query($query)){
		echo json_encode(Array('success'=>0,'msg'=>$conn->error));
		exit;
	}
	close_db($conn);
}
?>