<?php 
$msg = "";
$success=false;
$flag = false;
$found = false;
require_once 'db_functions.php';
$conn = connect_db();
if(gettype($conn) == 'string') {
	$msg = "DB Error: ".$conn;
} else {
	$query="SELECT t.`ticket`, t.`team_name`, t.`money`+SUM(s.`price`*o.`amount`) AS total_value FROM `stocks_owned` o INNER JOIN `stocks` s ON s.`id`=o.`stock_id` INNER JOIN `teams` t ON o.`ticket_no`=t.`ticket` GROUP BY t.`ticket` ORDER BY total_value DESC";
	if(!$result = $conn->query($query)) {
		$msg = "DB Error: " . $conn->error;
	} else {
		if(isset($_COOKIE['ticket_no']) && isset($_COOKIE['event']) && $_COOKIE['event'] === 'mockstock') {
			$ticket = $_COOKIE['ticket_no'];
			$flag = true;
		}
		$i = 0;
		$standings = Array();
		$success = true;
		while($row = $result->fetch_assoc()) {
			if($found && $i > 2)
				break;
			if($flag && $i > 2 && $ticket != $row['ticket']) {
				$i++;
			} else {
				$i++;	
				if($flag && $row['ticket']==$ticket)	
					$standings[$i] = "You (" . sprintf("%'.03d", $row['ticket']) . ")";
				else
					$standings[$i] = $row['team_name'] . " (" . sprintf("%'.03d", $row['ticket']) . ")";
			}
		}
	}
	close_db($conn);
}
if($success)
	echo json_encode(Array('success'=>1,'leaders'=>$standings),JSON_PRETTY_PRINT,JSON_UNESCAPED_SLASHES);
else
	echo json_encode(Array('success'=>0,'msg'=>$msg),JSON_PRETTY_PRINT,JSON_UNESCAPED_SLASHES);
?>