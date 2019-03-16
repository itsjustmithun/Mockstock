<?php
$money = 250000;
//also change money in start_event.php
$success = 0;
$msg ="";
if(isset($_POST['reg_team']) && $_POST['reg_team'] == 'Register') {
	require_once('db_functions.php');
	$conn = connect_db();
	if(gettype($conn) == 'string') {
		die($conn);
	}
	$ticket = $conn->real_escape_string(isset($_POST['ticket'])?$_POST['ticket']:"");
	$team_name = $conn->real_escape_string(isset($_POST['team_name'])?$_POST['team_name']:"");
	$player1 = $conn->real_escape_string(isset($_POST['player1'])?$_POST['player1']:"");
	$player2 = $conn->real_escape_string(isset($_POST['player2'])?$_POST['player2']:"");
	if(!strlen($ticket) || !strlen($team_name) || !strlen($player1) || !strlen($player2)) {
		$msg= "Invalid details!";
	} else {
		$query = "INSERT into `teams` VALUES('$ticket','$team_name','$player1','$player2','$money')";
		if(!$result = $conn->query($query)){
			$msg= "DB Error: ".$conn->error;
		} else {
			for($i=1;$i<=8;$i++) {
				$query = "INSERT into `stocks_owned` values('$ticket','$i','0')";
				if(!$result = $conn->query($query)){
					$msg= "DB Error: ".$conn->error;
				}
			}
			$log_msg = "Team " . $team_name . " registered.";
			$query = "INSERT into log values(DEFAULT,'$ticket','$log_msg',DEFAULT)";
			if(!$result = $conn->query($query)){
				$msg= "DB Error: ".$conn->error;
			} else {
				$expire = time() + (60*60*24);
				setcookie('event','mockstock',$expire, '/', null, null, true);
				setcookie('ticket_no',$ticket,$expire, '/', null, null, true);
				$_COOKIE['ticket_no']=$ticket;
				$_COOKIE['event']="mockstock";
				$success = 1;
			}
		}
	}
	close_db($conn);
	echo json_encode(Array('success'=>$success,'msg'=>$msg),JSON_PRETTY_PRINT,JSON_UNESCAPED_SLASHES);
}
?>