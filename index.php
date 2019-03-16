<?php
	error_reporting(E_ALL & ~E_NOTICE& ~E_WARNING);
	$event_duration = 60*60;//55 minutes
	//also change time in get_remaining,admin/setup.php and admin/start_event.php time
	function get_event_status() {
		global $event_duration;
		require_once('db_functions.php');
		$conn = connect_db();
		$cur_time = time();
		$time_started = "";
		$query = "SELECT `time_started` from `event_details` WHERE `id`=1";
		if(!$result = $conn->query($query)) {
			$msg = "DB Error: " . $conn->error;
		} else {
			while($row = $result->fetch_assoc()) {
				$time_started = $row['time_started'];
				$time_ending = $time_started + $event_duration;
			}
		}
		if(strlen($time_started) == 0)
			return "not started yet";
		else if($cur_time > $time_started && $cur_time < $time_ending)
			return "started";
		else if($cur_time > $time_started && $cur_time > $time_ending)
			return "finished";
		close_db($conn);
	}
	if(isset($_COOKIE['ticket_no']) && isset($_COOKIE['event']) && $_COOKIE['event'] === 'mockstock') {
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<!-- <meta http-equiv="refresh" content="5"> -->
	<title>Mockstock</title>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

	<!-- jQuery library -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

	<!-- Latest compiled JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> 
	<link rel="stylesheet" href="css/TimeCircles.css">
	<link rel="stylesheet" href="css/main.css">
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600' rel='stylesheet' type='text/css'>
	<!-- <script type="text/javascript" src="js/vendor/jquery.js"></script> -->
	<script type="text/javascript" src="js/vendor/jquery.timers.js"></script>
	<script type="text/javascript" src="js/vendor/TimeCircles.js"></script>
	<script>
		var status = "<?php echo get_event_status();?>";
		// alert("the status is "+status);
		console.log(status);
		if(status != "started" && status != "finshed") {
			setTimeout(function(){
				location.reload();
			},5000);
		}
	</script>
	<script src="js/main.js"></script>
</head>
<body>
	<?php if(get_event_status() == 'started') { ?>
		<div id="error_overlay" class="hidden"></div>
		<div id="error_wrapper" class="card hidden">
			<h1 class="heading">Error<a href="" class="close">&nbsp;&nbsp;</a></h1>
			<p></p>
		</div>
		<div id="desc_overlay" class="hidden"></div>
		<div id="desc_wrapper" class="card hidden">
			<?php
				$html = "";
				require_once 'db_functions.php';
				$conn = connect_db();
				if(gettype($conn) == 'string') {
					$html = "DB Error: ".$conn;
				} else {
					$query = "SELECT `id`,`name`,`description` from `stocks`";
					if(!$result = $conn->query($query)) {
						$html = "<p>DB Error: " . $conn->error . "</p>";
					} else {
						while($row = $result->fetch_assoc()) {
							$html .= "<div class=\"content hidden\" id=". $row['id'] ."><h1 class=\"heading\">". $row['name'] ."<a href=\"\" class=\"close\">&nbsp;&nbsp;</a></h1><p>". $row['description'] ."</p></div>";
						}
					}
					close_db($conn);
				}
				echo $html;
			?>
		</div>
		
		<div id="news_wrapper" class="card">
			<h1 class="heading">News</h1>
			<ul id="news_ticker" data-eventId="0">
				<li></li>
				<li></li>
				<li></li>
			</ul>
		</div>
		<div id="misc_wrapper">
			<div class="card" id="time_wrapper">
				<h1 class="heading">Time Remaining </h1>
				<p  id="timer" ></p>
			</div>
			<div class="card" id="leader_wrapper">
				<h1 class="heading">Leader board </h1>
			</div>
		</div>
		<div id="price_wrapper" class="card">
			<h1 class="heading" style="margin-bottom: 10px;">Current Prices</h1>
			<div class="content">


				 <?php
				// $html = "";
				// require_once 'db_functions.php';
				// $conn = connect_db();
				// if(gettype($conn) == 'string') {
				// 	$html = "DB Error: ".$conn;
				// } else {
				// 	$query = "SELECT `id`,`name`,`price` from `stocks`";
				// 	if(!$result = $conn->query($query)) {
				// 		$html = "<p>DB Error: " . $conn->error . "</p>";
				// 	} else {
				// 		while($row = $result->fetch_assoc()) {
				// 			$html .= "<p id=".$row['id']."><span class=\"indicator\">&nbsp;</span><span class=\"name\">". $row['name'] . "</span><span class=\"price\">" . $row['price'] ."</span></p>";
				// 		}
				// 	}
				// 	close_db($conn);
				// }
				// echo $html;
				?>


			</div>
		</div>
		<div id="trade_wrapper" class="card" style="height: 400px;">
			<h1 class="heading" style="margin-bottom: 10px;">Trading</h1>
			<form action="" id="trade_form">
			<?php
				require_once 'db_functions.php';
				$conn = connect_db();
				if(gettype($conn) == 'string') {
					echo "<p>DB Error: " . $conn . "</p>";
				} else {
					$query = "SELECT * from `stocks`";
					if(!$result = $conn->query($query)) {
						echo "<p>DB Error: " . $conn->error . "</p>";
					} else {
						while($row = $result->fetch_assoc()) {
							$id = $row['id'];
							$name = $row['name'];
							echo 
								"<div class=\"row\">
									<div class=\"col-sm-6\" style=\"text-align: left; text-transform: capitalize; padding: 5px; padding-left: 25px;\">
										<label for=\"".$id."\">".$name."</label>
									</div>
									<div class=\"col-sm-6\" style=\"padding: 5px; padding-right: 25px;\">
										<input type=\"number\" name=\"".$id."\" value=\"\" placeholder=\"\" min=\"1\" style=\"width: 100%;\">
									</div>
								</div>";
							// echo "<label for=". $id. ">". $name . "<input type=\"number\" name=". $id . " value=\"\" placeholder=\"\" min=\"1\" style=\"width: 65%;\"></label><br>";
						}
					}
					close_db($conn);
				}
				?>
				<input type="submit" name="trade_action" value="Buy">
				<input type="submit" name="trade_action" value="Sell">
				<img id="trade-spinner" src="img/spinner.gif" class="spinner hidden">
			</form>
		</div>
		<div id="holdings_wrapper" class="card">
			<h1 class="heading" style="margin-bottom: 10px;">Current Holdings</h1>
			<div class="content" style="padding-left: 10px; padding-right: 10px;">
				<?php require_once 'get_holdings.php'; ?>
			</div>
		</div>
		<div id="log_wrapper" class="card">
			<h1 class="heading">Log</h1>
			<div class="content">
				<?php require_once 'get_log.php'; ?>
			</div>
		</div>
	<?php } else if(get_event_status() == 'finished') {?>
		<p class='status_msg'>Event has finished!</p>
		<p class="hidden"><?php require_once 'leaderboard.php'; ?></p>
		<div class="card" id="finished-leader">
		<h1 class="heading">Leader Board</h1>
			<?php
				foreach ($standings as $key => $value) {
					echo "<p class='$value'><span>$key</span>$value</p>";
				}
			?>
		</div>
	<?php } else { echo "<p class='status_msg'>Successfully Registered. Event will start soon.</p>";} ?>
</body>
</html>
<?php } else {?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Mockstock</title>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

	<!-- jQuery library -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

	<!-- Latest compiled JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> 
	<link rel="stylesheet" href="css/main.css">
	<link rel="stylesheet" href="css/TimeCircles.css">
	<script type="text/javascript" src="js/vendor/jquery.js"></script>
	<script type="text/javascript" src="js/register.js"></script>	
</head>
<body id="register">

<div class="container-fluid">
  <div class="row">
    <div class="col-sm-4">
    	<img src="img/Tatva-Colors.jpg" class="img-responsive" style="height: 262px; margin-left:150px;">" >
    </div>
    <div class="col-sm-4" style="text-align: center;">
    	<form action="" id="register_form">
			<input type="text" name="ticket" value="" placeholder="Ticket Number"><br>
			<input type="text" name="team_name" value="" placeholder="Team name"><br>
			<input type="text" name="player1" value="" placeholder="Teammate1"><br>
			<input type="text" name="player2" value="" placeholder="Teamname2"><br>
			<input type="submit" name="reg_team" value="Register">
		</form>
    </div>
    <div class="col-sm-4">
    	<img src="img/Tatva-Colors.jpg" class="img-responsive" style="height:262px; margin-right:150px;">
    </div>
  </div>
</div>

<div id="instructions">
	<div class="item card">
		<img src="img/co_desc.png" alt="">
		<p class="desc">Click on any company to read a detailed description</p>
	</div>
	<div class="item card">
		<img src="img/news.png" alt="">
		<p class="desc">Periodic news events affect the stock prices of the companies</p>
	</div><div class="item card">
		<img src="img/trade.png" alt="">
		<p class="desc">Enter the number of stocks and click one of the two buttons below</p>
	</div><div class="item card">
		<img src="img/holdings.png" alt="">
		<p class="desc">View your current holdings and cash in hand here.</p>
	</div>
	<div class="item card">
		<img src="img/leaderboard.png" alt="">
		<p class="desc">See where you stand among all players</p>
	</div>
	<div class="item card">
		<img src="img/log.png" alt="">
		<p class="desc">View your transaction history here.</p>
	</div>
</div>
</body>
<?php } ?>

















<!-- <div class="row">
	<div class="col-sm-4">
		<label for="1">name</label>
	</div>
	<div class="col-sm-8">
		<input type="number" name="" value="" placeholder="" min="1">
	</div>
</div> -->