<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$msg = "";
$success=false;
$interval = 45;//seconds
require_once 'db_functions.php';
$event = "";
$conn = connect_db();
if(gettype($conn) == 'string') {
	$$msg = "DB Error: ".$conn;
} else {
	$cur_time = time();
	$query = "SELECT `time_started` from `event_details` WHERE `id`=1";
	if(!$result = $conn->query($query)) {
		$msg = "DB Error: " . $conn->error;
	} else {
		while($row = $result->fetch_assoc()) {
			$time_started = $row['time_started']+(60);
		}
		$event_id = ceil(($cur_time - $time_started)/$interval);
		$query = "SELECT `event` from `news` WHERE `id`=$event_id LIMIT 1";
		if(!$result = $conn->query($query)) {
			$msg = "DB Error: (Error fetching news)" . $conn->error;
		} else {
			while($row = $result->fetch_assoc()) {
				$event = $row['event'];
			}
			$msg = "News Event fetched successfully";
			$success = true;
		}
	}
	close_db($conn);
}
if($success)
	echo json_encode(Array('success'=>1,'event_id'=>$event_id,'event'=>$event),JSON_PRETTY_PRINT,JSON_UNESCAPED_SLASHES);
else
	echo json_encode(Array('success'=>0,'message'=>$msg),JSON_PRETTY_PRINT,JSON_UNESCAPED_SLASHES);
?>