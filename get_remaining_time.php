<?php
require_once 'db_functions.php';
$conn = connect_db();
if(gettype($conn) == 'string') {
	$success = 0;
	$time = 0;
	$msg = "DB Error: ".$conn;
} else {
	$event_duration = 60*60;
	$cur_time = time();
	$time = 0;
	$success = 1;
	$msg = "";
	$query = "SELECT `time_started` from `event_details` WHERE `id`=1";
	if(!$result = $conn->query($query)) {
		$msg = "DB Error: " . $conn->error;
		$time = 0;
		$success = 0;
	} else {
		while($row = $result->fetch_assoc()) {
			$time_started = $row['time_started'];
			$time_ending = $time_started + $event_duration;
			$time = $time_ending - $cur_time;
			if($time<0) $time=0;
			$success = 1;
		}
	}
	close_db($conn);
}
echo json_encode(Array('success'=>$success,'time'=>$time,'msg'=>$msg));
?>