<?php 
$html = "";
$value = 0;
if(isset($_COOKIE['ticket_no']) && isset($_COOKIE['event']) && $_COOKIE['event'] === 'mockstock') {
	require_once 'db_functions.php';
	$conn = connect_db();
	if(gettype($conn) == 'string') {
		$html = "DB Error: ".$conn;
	} else {
		$ticket = $_COOKIE['ticket_no'];
		$query = "SELECT s1.`name`,s1.`price`,s2.`amount` from `stocks`s1,`stocks_owned`s2 WHERE `ticket_no`=$ticket AND s1.`id`=s2.`stock_id`";
		if(!$result = $conn->query($query)) {
			$html = "<p>DB Error: " . $conn->error . "</p>";
		} else {
			while($row = $result->fetch_assoc()) {
				$value += $row['amount']*$row['price'];
				$html .= "<p><span class=\"name\">". $row['name'] . "</span><span class=\"price\">" . $row['amount'] ."</span></p>";
			}
		}
		$query = "SELECT `money` from `teams` WHERE `ticket`=$ticket LIMIT 1";
		if(!$result = $conn->query($query)) {
			$html = "<p>DB Error: " . $conn->error . "</p>";
		} else {
			while($row = $result->fetch_assoc()) {
				$html .= "<p><span class=\"name\">". "Cash in hand" . "</span><span class=\"price\">" . $row['money'] ."</span></p>";
				$value += $row['money'];
				$html .= "<p><span class=\"name\">". "Total Value" . "</span><span class=\"price\">" . $value ."</span></p>";
			}
		}
		close_db($conn);
	}
}
else {
	$html .= "<p>Ticket Num not found. Refresh page.</p>";
}
echo $html;
?>