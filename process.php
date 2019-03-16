<?php
$msg = "";
$flag = false;
if(isset($_GET['_'])) unset($_GET['_']);
if(isset($_COOKIE['ticket_no']) && isset($_COOKIE['event']) && $_COOKIE['event'] === 'mockstock') {
	require_once 'db_functions.php';
	$conn = connect_db();
	if(gettype($conn) == 'string') {
		echo json_encode(Array('success'=>0,'msg'=>$conn),JSON_PRETTY_PRINT,JSON_UNESCAPED_SLASHES);
	}
	$ticket = $_COOKIE['ticket_no'];
	$prices = Array();
	$minval = Array();
	$maxval = Array();
	$remaining = Array();
	$stocks_owned = Array();
	$co_names = Array();
	$team = Array();
	if(!$result = $conn->query("SELECT `id`,`name`,`price`,`remaining`,`minval`,`maxval` FROM `stocks` ORDER BY `id` ASC")) {
		$msg = "DB Error(Failed to fetch prices) ". $conn->error;
	} else {
		while($row = $result->fetch_assoc()) {
			$id = $row['id'];
			$co_names[$id] = $row['name'];
			$prices[$id] = $row['price'];
			$remaining[$id] = $row['remaining'];
			$minval[$id] = $row['minval'];
			$maxval[$id] = $row['maxval'];
		}
	}
	if(!$result = $conn->query("SELECT `stock_id`,`amount` FROM `stocks_owned` WHERE `ticket_no`='$ticket' ORDER BY `stock_id` ASC")) {
		$msg = "DB Error(Failed to fetch stocks owned) ". $conn->error;
	} else {
		while($row = $result->fetch_assoc()) {
			$id = $row['stock_id'];
			$stocks_owned[$id] = $row['amount'];
		}
	}
	if(!$result = $conn->query("SELECT `team_name`,`money` FROM `teams` WHERE `ticket`='$ticket'")) {
		$msg = "DB Error(Failed to fetch team data) ". $conn->error;
	} else {
		while($row = $result->fetch_assoc()) {
			$team['name'] = $row['team_name'];
			$team['amount'] = $row['money'];
		}
	}
	if(!$result = $conn->query('LOCK TABLES stocks WRITE, teams WRITE, log WRITE, stocks_owned WRITE')){
		$msg = "DB Error(Failed to LOCK tables) ". $conn->error;
	}
	if($_GET['trade_action'] == 'Buy'){
		unset($_GET['trade_action']);
		foreach($_GET as $id=>$quant){
			if(strlen($quant) == 0)
				continue;
			if($quant < 0)
				$quant = 0;
			$amount = $prices[$id] * $quant;
			if($team['amount'] >= $amount && $remaining[$id] >= $quant) {
				$msg = "BUY successful";
				$stocks_owned[$id] += $quant;
				$team['amount'] -= $amount;
				$prices[$id] += ($quant/$remaining[$id])*1*$prices[$id];
				if($prices[$id]<$minval[$id]) $prices[$id] = $minval[$id];
				if($prices[$id]>$maxval[$id]) $prices[$id] = $maxval[$id];
				$remaining[$id] -= $quant;
				$co_name = $co_names[$id];
				$t_name = $team['name'];
				$query= "INSERT into `log` values(DEFAULT,'$ticket','Team $t_name bought $quant stocks in $co_name. <br />','".date('Y-m-d H:i:s')."')";
				if(!$result = $conn->query($query)) {
					$msg = "DB Error(Failed to write to log1) ".$conn->error . " " . $query . " " . date('Y-m-d H:i:s');
				}				
				$query = "UPDATE `stocks` SET `price`={$prices[$id]}, `remaining`={$remaining[$id]} WHERE `id`=$id";
				if(!$result = $conn->query($query)) {
					$msg = "DB Error(Failed to update stocks table) ".$conn->error;
				}
				$query = "UPDATE `teams` SET `money`={$team['amount']} WHERE `ticket`=$ticket";
				if(!$result = $conn->query($query)) {
					$msg = "DB Error(Failed to update teams table) ".$conn->error;
				}
				$query = "UPDATE `stocks_owned` SET `amount`={$stocks_owned[$id]} WHERE `stock_id`=$id AND `ticket_no`=$ticket";
				if(!$result = $conn->query($query)) {
					$msg = "DB Error(Failed to update stocks_owned table) ".$conn->error;
				}			
			} else {
				$stockavailable = $remaining[$id];
				if($remaining[$id] <= $quant)
					$msg = "Last  BUY failed. There are only $stockavailable stocks of {$co_names[$id]} available.";
				else if($team['amount'] <= $amount)
					$msg = "Last BUY failed. You don't have enough money to purchase $quant stocks in {$co_names[$id]}";
				$msg2 = $conn->real_escape_string($msg) . "<br />";
				$query= "insert into log values(DEFAULT,'$ticket','$msg2',date('Y-m-d H:i:s'))";
				if(!$result = $conn->query($query)) {
					$msg = "DB Error(Failed to write to log2) ".$conn->error . " " . $query . " " . date('Y-m-d H:i:s');
				}				
				$flag = true;
			}
		}
	} else if($_GET['trade_action'] == 'Sell') {
		unset($_GET['trade_action']);
		foreach($_GET as $id=>$quant){
			if(strlen($quant) == 0)
				continue;
			if($quant < 0)
				$quant = 0;
			if($quant <= $stocks_owned[$id]) {				
				$msg = "Sell successful";
				$amount = $prices[$id] * $quant;
				$stocks_owned[$id] -= $quant;
				$team['amount'] += $amount;
				$prices[$id] -= ($quant/$remaining[$id])*$prices[$id];
				if($prices[$id]<$minval[$id]) $prices[$id] = $minval[$id];
				if($prices[$id]>$maxval[$id]) $prices[$id] = $maxval[$id];
				$remaining[$id] += $quant;
				$co_name = $co_names[$id];
				$t_name = $team['name'];
				$query= "INSERT into `log` values(DEFAULT,'$ticket','Team $t_name sold $quant of it\'s stocks in $co_name. <br />','".date('Y-m-d H:i:s')."')";
				if(!$result = $conn->query($query)) {
					$msg = date('Y-m-d H:i:s')."\n".$query."\nDB Error(Failed to write to log3) ".$conn->error;
				}
				$query = "UPDATE `stocks` SET `price`={$prices[$id]}, `remaining`={$remaining[$id]} WHERE `id`=$id";
				if(!$result = $conn->query($query)) {
					$msg = "DB Error(Failed to update stocks table) ".$conn->error;
				}
				$query = "UPDATE `teams` SET `money`={$team['amount']} WHERE `ticket`=$ticket";
				if(!$result = $conn->query($query)) {
					$msg = "DB Error(Failed to update teams table) ".$conn->error;
				}
				$query = "UPDATE `stocks_owned` SET `amount`={$stocks_owned[$id]} WHERE `stock_id`=$id AND `ticket_no`=$ticket";
				if(!$result = $conn->query($query)) {
					$msg = "DB Error(Failed to update stocks_owned table) ".$conn->error;
				}
			} else {
				$co_name = $co_names[$id];
				$t_name = $team['name'];
				$msg = "Last SELL failed. Check log for details.";
				$msg2 = $conn->real_escape_string("Last SELL failed. You don't own $quant stocks in $co_name.") . "<br />";
				$query= "insert into log values(DEFAULT,'$ticket','$msg2',date('Y-m-d H:i:s'))";
				if(!$result = $conn->query($query)) {
					$msg = "DB Error(Failed to write to log4) ".$conn->error;
				}				
				$flag = true;
			}
		}
	}
	if(!$result = $conn->query("UNLOCK TABLES")) {
		$msg = "DB Error(Failed to UNLOCK table) ".$conn->error;
	}
	close_db($conn);
	if(!$flag)
	{
		echo json_encode(Array('success'=>1,'msg'=>$msg),JSON_PRETTY_PRINT,JSON_UNESCAPED_SLASHES);
	}
	else
	{
		echo json_encode(Array('success'=>0,'msg'=>$msg),JSON_PRETTY_PRINT,JSON_UNESCAPED_SLASHES);
	}
}
else {
	$msg = "Ticket Num not found. Refresh page.";
	echo json_encode(Array('success'=>0,'msg'=>$msg),JSON_PRETTY_PRINT,JSON_UNESCAPED_SLASHES);
}
?>