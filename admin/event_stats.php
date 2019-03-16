<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Mockstock-Event Stats</title>
	<link rel="stylesheet" href="../css/main.css">
	<link rel="stylesheet" href="../css/TimeCircles.css">
	<link rel="stylesheet" href="css/main.css">
	<link rel="stylesheet" href="css/event_stats.css">
	<script type="text/javascript" src="js/vendor/jquery.js"></script>
	<script type="text/javascript" src="js/event_stats.js"></script>
	<script type="text/javascript" src="js/main.js"></script>
	<script type="text/javascript" src="js/vendor/jquery.timers.js"></script>
	<script type="text/javascript" src="../js/vendor/TimeCircles.js"></script>	
</head>
<body>
	<div id='content'>
		<div class='card'>
			<h1 class='heading'>Navigation button</h1>
			<a href='setup.php'>Admin Control Page</a>
		</div>
	</div>
<div class="card" id="time_wrapper">
	<h1 class="heading">Time Remaining </h1>
	<p  id="timer" ></p>
</div>
<div class="card" id="leader_wrapper">
	<h1 class="heading">Leader board </h1>
</div>
<div id="news" class="card">
	<h1 class="heading">Upcoming News</h1>
	<?php 
		$time_started = null ;
		require_once '../db_functions.php';
		$conn = connect_db();
		if(gettype($conn) == 'string')
			$html1 = "<p>" . $conn . "</p>";
		else {
			$cur_time = time();
			$query = "SELECT `time_started` from `event_details` WHERE `id`=1";
			if(!$result = $conn->query($query)) {
				$html = "<p>DB Error: " . $conn->error . "</p>";
			} else {
				while($row = $result->fetch_assoc()) {
					$time_started = $row['time_started']+(2*60);
				}
				$interval = 30;
				$event_id = ceil(($cur_time - $time_started)/$interval);
				$event_id--;
				$query = "SELECT * FROM `news` WHERE `id` > $event_id ORDER BY `id` LIMIT 4";
				if(!$result = $conn->query($query)) {
					$html = "<p>DB Error: " . $conn->error . "</p>";
				} else {
					$html2 = "";
					$i = 0;
					while(($row = $result->fetch_assoc()) && $i < 4) {
						$id = $row['id'];
						$event_id = $id;
						$i++;
						$html2 .= "
						<li id=\"$id\">
							<p class=\"id\">$id</p>
							<p class=\"event\">{$row['event']}</p>
							<button class=\"edit\">Edit</button>
							<button class=\"update hidden\">update</button>
							<button class=\"delete\">Delete</button>
						</li>";
					}
					$html1 = "
					<ul id=\"news-list\" data-eventId=\"$event_id\">
						<li id=\"heading\">
							<p class=\"id\">Id</p>
							<p class=\"event\">News</p>
						</li>";
				}
			}
			$html2 .= "</ul>";
		}
		echo $html1;
		echo $html2;
		close_db($conn);
	?>
</div>
<div id="companies" class="card">
	<h1 class="heading">Companies</h1>
	<?php 
		require_once '../db_functions.php';
		$conn = connect_db();
		if(gettype($conn) == 'string')
			$html = "<p>" . $conn . "</p>";
		else {
			$query = "SELECT * from `stocks`";
			if(!$result = $conn->query($query)) {
				$html = "<p>DB Error: " . $conn->error . "</p>";
			} else {
				$html = "
				<ul id=\"co-list\">
					<li id=\"heading\">
						<p class=\"id\">Id</p>
						<p class=\"name\">name</p>
						<p class=\"price\">price</p>
						<p class=\"minval\">minval</p>
						<p class=\"maxval\">maxval</p>
						<p class=\"remaining\">remaining</p>
					</li>";
				while($row = $result->fetch_assoc()) {
					$id = $row['id'];
					$html .= "
					<li id=\"$id\">
						<p class=\"id\">$id</p>
						<p class=\"name\">{$row['name']}</p>
						<p class=\"price\">{$row['price']}</p>
						<p class=\"minval\">{$row['minval']}</p>
						<p class=\"maxval\">{$row['maxval']}</p>
						<p class=\"remaining\">{$row['remaining']}</p>
						<button class=\"edit\">Edit</button>
						<button class=\"update hidden\">update</button>
						<button class=\"delete\">Delete</button>
					</li>";
				}
			}
			$html .= "</ul>";
		}
		echo $html;
		close_db($conn);
	?>
</div>
</body>
</html>