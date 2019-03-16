<?php
require_once '../db_functions.php';
$conn = connect_db();
if(gettype($conn) == 'string') {
	echo json_encode(Array('success'=>0,'msg'=>$conn));
} else {
	$query = "SELECT COUNT(*) from `teams`";
	if(!$result = $conn->query($query)) {
		echo json_encode(Array('success'=>0,'msg'=>$conn->error));
	} else {
		while($row = $result->fetch_row()) {
			$teams = $row[0];
		}
		echo json_encode(Array('success'=>1,'teams'=>$teams));
	}
	close_db($conn);
}
?>