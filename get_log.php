<?php 
$html = "";
if(isset($_COOKIE['ticket_no']) && isset($_COOKIE['event']) && $_COOKIE['event'] === 'mockstock') {
	require_once 'db_functions.php';
	$conn = connect_db();
	if(gettype($conn) == 'string') {
		$html = "DB Error: ".$conn;
	} else {
		$ticket = $_COOKIE['ticket_no'];
		$query = "SELECT * from `log` WHERE `ticket`=$ticket ORDER BY `id` DESC";
		if(!$result = $conn->query($query)) {
			$html = "<p>DB Error: " . $conn->error . "</p>";
		} else {
			while($row = $result->fetch_assoc()) {
				$html .= "<p>". $row['description'] . "</p>";
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