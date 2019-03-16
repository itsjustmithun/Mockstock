<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title></title>
	<link rel="stylesheet" href="css/main.css">
</head>
<body>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$money = 250000;
$event_duration = 60*60;
function get_event_status() {
	global $event_duration;
	require_once('../db_functions.php');
	$conn = connect_db();
	if($conn == "Database does not exist"){
		echo "<div id='content'><div class='card'><h1 class='heading'>Database Error</h1><p>Database does not exist. Go back to admin page, create database and try again.</p><a href='setup.php'>Go back to admin page</a></div></div>";
		exit();
	}
	else{
		$cur_time = time();
		$query = "SELECT `time_started` from `event_details` WHERE `id`=1 LIMIT 1";
		if(!$result = $conn->query($query)) {
			echo "<div class='content'><div class='card' id='event'><h1 class='heading'>Error</h1><p> ".$conn->error."</p></div></div>";
		} else {																							
			while($row = $result->fetch_assoc()) {
				$time_started = $row['time_started'];
				$time_ending = $time_started + $event_duration;
			}
		}
		//echo var_dump($time_started);
		if(!isset($time_started))
			return "not started yet";
		if(strlen($time_started) == 0 || (isset($_GET['force'])&&$_GET['force']==true))
			return "not started yet";
		else if($cur_time > $time_started && $cur_time < $time_ending)
			return "started";
		else if($cur_time > $time_started && $cur_time > $time_ending)
			return "finished";
		close_db($conn);
	}
}
$event_status = get_event_status();
if($event_status == 'started') {
	echo "<div id='content'><div class='card'><h1 class='heading'>Event ongoing</h1><a href='event_stats.php'>View Event Stats</a><a href='start_event.php?force=true'>Delete ongoing event and Start new event?</a><a href='setup.php'>Admin Control Page</a></div></div>";
}
else if($event_status == 'finished') {
	echo "<div id='content'><div class='card'><h1 class='heading'>Event Finished</h1><a href='event_stats.php'>View Event Stats</a><a href='start_event.php?force=true'>Delete ongoing event and Start new event?</a><a href='setup.php'>Admin Control Page</a></div></div>";
}
else {
	require_once '../db_functions.php';
	$conn = connect_db();
	$time = time();
	$query = "INSERT INTO `event_details` VALUES('1','$time') ON DUPLICATE KEY UPDATE `time_started`=$time";
	if(!$result = $conn->query($query)){
		echo "<div class='content'><div class='card' id='event'><h1 class='heading'>Error</h1><p> ".$conn->error."</p></div></div>";
		close_db($conn);
	}else {
		if(isset($_GET['force'])&&$_GET['force']==true) {
			$query1 = "UPDATE `teams` SET `money`='$money' WHERE 1";
			$query2 = "UPDATE `stocks_owned` SET `amount`='0' WHERE 1";
			if(!$result1 = $conn->query($query1)) {
				echo "<div class='content'><div class='card' id='event'><h1 class='heading'>Error</h1><p> ".$conn->error."</p></div></div>";
				close_db($conn);
			} else {
				if(!$result2 = $conn->query($query2)){
					echo "<div class='content'><div class='card' id='event'><h1 class='heading'>Error</h1><p> ".$conn->error."</p></div></div>";
					close_db($conn);
				} else {
					echo "<div id='content'><div class='card'><h1 class='heading'>Event Started.</h1> <a href=\"event_stats.php\">View Event Stats</a><a href='start_event.php?force=true'>Delete ongoing event and Start new event?</a><a href='setup.php'>Admin Control Page</a></div></div>";

				}
			}
		} else {
			if(isset($_GET['force'])&&$_GET['force']==false) {
				echo "<div id='content'><div class='card'><p>Event Started.</h1> <a href=\"event_stats.php\">View Event Stats</a><a href='start_event.php?force=true'>Delete ongoing event and Start new event?</a><a href='setup.php'>Admin Control Page</a></div></div>";

			}
		}
		close_db($conn);
		
	}
}
?>	
</body>
</html>