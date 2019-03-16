<?php
$msg = "";
$success=false;
// require_once 'db_functions.php';
// $conn = connect_db();
// if(gettype($conn) == 'string') {
// 	$msg = "DB Error: ".$conn;
// } else {
// 	// $query = "SELECT `id`,'name', `price` from `stocks`";
// 	$query = "SELECT id, name, price from stocks;";
// 	if(!$result = $conn->query($query)) {
// 		$msg = "<p>DB Error: " . $conn->error . "</p>";
// 	} else {
// 		$success = true;
// 		while($row = $result->fetch_array()) {
// 			$id = $row[0];
// 			// $prices[$id] = $row[2];
// 			// $name[$id] = $row[1];

// 			// $id = $row[0];
// 			// $data[name] = $row[1];
// 			// $prices[price] = $row[2];

// 			$data[$id][0] = $row[1];
// 			$data[$id][1] = $row[2];

// 		}
// 	}
// 	close_db($conn);
// }

				require_once 'db_functions.php';
				$conn = connect_db();
				if(gettype($conn) == 'string') {
					echo "<p>DB Error: " . $conn . "</p>";
				} else {
					$query = "SELECT * from stocks";
					if(!$result = $conn->query($query)) {
						$msg = "<p>DB Error: " . $conn->error . "</p>";
					} else {
						$success = true;
						while($row = $result->fetch_array()) {
							$id = $row[0];
							$data[$id][0] = $row[1];
							// echo "<label for=". $id. ">". $name . "<input type=\"number\" name=". $id . " value=\"\" placeholder=\"\" min=\"1\"></label><br>";
						}
					}
					close_db($conn);
				}


if($success)
	echo json_encode(Array('success'=>1, 'data'=>$data),JSON_PRETTY_PRINT,JSON_UNESCAPED_SLASHES);
else
	echo json_encode(Array('success'=>0,'message'=>$msg),JSON_PRETTY_PRINT,JSON_UNESCAPED_SLASHES);
?>