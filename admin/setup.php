<?php
	//error_reporting(E_ALL & ~E_NOTICE& ~E_WARNING);
	error_reporting(E_ALL);
	//Global variables
	//$db_file_path = $_SERVER['DOCUMENT_ROOT'] ."/db_config.txt";
	$tables = Array('stocks','news','teams','log','stocks_owned','event_details');
	$event_duration = 60*60;
	//also change $tables in export_db.php and reset_tables.php
	$todo_html = "";
	$create_table_sql = Array();
	$create_table_sql['stocks'] = "create table stocks(
		id INT(2) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		name VARCHAR(50) NOT NULL,
		description TEXT,
		price FLOAT(10,2),
		minval FLOAT(10,2),
		maxval FLOAT(10,2),
		remaining INT(10))";
	$create_table_sql['news'] = "create table news(
		id INT(5) NOT NULL PRIMARY KEY AUTO_INCREMENT,
		event TEXT)";
	$create_table_sql['teams'] = "create table teams(
		ticket INT(5) NOT NULL PRIMARY KEY,
		team_name VARCHAR(30),
		player1 VARCHAR(30),
		player2 VARCHAR(30),
		money FLOAT(10,2))";
	$create_table_sql['log'] = "create table log(
		id INT(5) NOT NULL PRIMARY KEY AUTO_INCREMENT,
		ticket INT(5) NOT NULL,
		description VARCHAR(255),
		time TIMESTAMP,
		FOREIGN KEY (ticket) REFERENCES teams(ticket)
		ON DELETE CASCADE
		ON UPDATE CASCADE)";
	$create_table_sql['stocks_owned'] = "create table stocks_owned(
		ticket_no INT(5) NOT NULL,
		stock_id INT(2) NOT NULL,
		amount INT(10),
		PRIMARY KEY (ticket_no,stock_id),
		FOREIGN KEY (ticket_no) REFERENCES teams(ticket)
		ON DELETE CASCADE
		ON UPDATE CASCADE,
		FOREIGN KEY (stock_id) REFERENCES stocks(id)
		ON DELETE CASCADE
		ON UPDATE CASCADE)";
	$create_table_sql['event_details'] = "create table event_details(
		id INT(4) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		time_started VARCHAR(30))";
	function create_tables($db_handler) {
		global $create_table_sql;
		foreach($create_table_sql as $query){
			if(!$result = $db_handler->query($query)){
				if($db_handler->errno != 1050)
				echo "DB Error: ".$db_handler->error;
			}
		}
	}
	function get_event_status() {
		global $event_duration;
		require_once('../db_functions.php');
		$conn = connect_db();
		if(gettype($conn) == 'string')
			return "";
		$cur_time = time();
		$time_started = "";
		$query = "SELECT `time_started` from `event_details` WHERE `id`=1";
		if(!$result = $conn->query($query)) {
			$msg = "DB Error: " . $conn->error;
			return $msg;
		} else {
			while($row = $result->fetch_assoc()) {
				$time_started = $row['time_started'];
				$time_ending = $time_started + $event_duration;
			}
		}
		if(strlen($time_started) == 0)
			return "not started yet";
		else if($cur_time > $time_started && $cur_time < $time_ending){
			return "started";
		}
		else if($cur_time > $time_started && $cur_time > $time_ending)
			return "finished";
		close_db($conn);
	}
	//Read database credentials
	//$db_file = fopen($db_file_path, "r") or die("Unable to open db_config.txt!");
	//$server = trim(fgets($db_file));
	$server = "localhost";
	//$db_user = trim(fgets($db_file));
	$db_user = "root";
	//$db_pass = trim(fgets($db_file));
	$db_pass = "";
	//$db_name = trim(fgets($db_file));
	$db_name = "mockstock";
	//fclose($db_file);
	//Process database credentials update and write to db_config.txt
	if(isset($_POST['db_form']) && $_POST['db_form'] == 'update') {
		$db_file = fopen($db_file_path, "w+") or die("Unable to open db_config.txt!");
		if(strlen($_POST['server']))
			$server = $_POST['server'];
		if(strlen($_POST['db_user']))
			$db_user = $_POST['db_user'];
		if(strlen($_POST['db_pass']))
			$db_pass = $_POST['db_pass'];
		if(strlen($_POST['db_name']))
			$db_name = $_POST['db_name'];
		fwrite($db_file, $server."\n");
		fwrite($db_file, $db_user."\n");
		fwrite($db_file, $db_pass."\n");
		fwrite($db_file, $db_name);
		fclose($db_file);
	} else if(isset($_POST['create_db']) && $_POST['create_db'] == 'Create Database and tables') {
		require_once('../db_functions.php');
		$db_handler = connect_db(true);
		create_tables($db_handler);
		close_db($db_handler);
	} else if(isset($_POST['create_tables']) && $_POST['create_tables'] == 'Create tables') {
		require_once('../db_functions.php');
		$db_handler = connect_db();
		create_tables($db_handler);
		close_db($db_handler);
	}

	//Try connecting to database
	require_once('../db_functions.php');
	$db_handler = connect_db();
	if($db_handler == "Missing Database credentials") {
		$todo_html = "<p>Update Database credentials below.</p>";
	} else if($db_handler == "Database does not exist") {
		$todo_html = '<p>Database does not exist.</p>
		<form action="setup.php" method="post">
			<input type="submit" name="create_db" value="Create Database and tables">
		</form>';
	} else if(gettype($db_handler) == 'string') {
		$todo_html = "<p>Error:".$db_handler;
	} else {
		$check_tables = $tables;
		if(!$result = $db_handler->query('SHOW TABLES'))
				echo "Error".$db_handler->error;
		while($row = $result->fetch_row()){
			$key = array_search($row[0],$check_tables);
			unset($check_tables[$key]);
		}
		if(count($check_tables) !== 0){
			$todo_html = '<p>Tables do not exist</p>
			<form action="setup.php" method="post">
				<input type="submit" name="create_tables" value="Create tables">
			</form>';
		} else {
			$todo_html = '<p>All Done. Populate the tables</p>';
		}
	}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>MockStock - Setup</title>
	<link rel="stylesheet" href="css/main.css">
</head>
<body>
<header>
	<p class="logo">MockStock Setup</p>
	<?php if(get_event_status() == 'started') { ?>
		<a href="event_stats.php" class="start">Event Stats</a>
		<a class="start" href="start_event.php">Restart Event</a>
		<p id="event-status">Event Ongoing</p>
	<?php } else if(get_event_status() == 'finished') { ?>
		<a href="event_stats.php" class="start">Event Stats</a>
		<a class="start" href="start_event.php">Restart Event</a>
		<p id="event-status">Event Finished</p>
	<?php } else {?>		
		<a class="start" href="start_event.php?force=false">Start Event</a>
	<?php } ?>
	<p id="teams-registered"><span>0</span> teams registered</p>
</header>
<div id="content">
	<div id="todo" class="card">
		<h1 class="heading">Todo</h1>
		<?php echo $todo_html; ?>
	</div>
	<div id="db-actions" class="card">
		<h1 class="heading">Database Functions</h1>
		<form action="import_db.php" method="POST" enctype="multipart/form-data">
         	<input type="file" accept=".sql" name="import-file" />
        	<input type="submit" value="Import DB" />
      	</form>
		<button id="export-db">Export DB</button>
		<button id="reset">Reset Teams</button>
		<button id="reset-all">Reset all tables</button>
	</div>
	<div id="db-cred" class="card">
		<h1 class="heading">Database Credentials</h1>
		<p>Server: <?php echo $server; ?></p>
		<p>Username: <?php echo $db_user; ?></p>
		<p>Password: <?php echo $db_pass; ?></p>
		<p>Database: <?php echo $db_name; ?></p>
		<button id="edit_db_cred">Edit</button>
		<form action="setup.php" id="db_update" method="post" class="change-this">
			<input type="text" name="server" value="" placeholder="Server">
			<input type="text" name="db_user" value="" placeholder="Username">
			<input type="text" name="db_pass" value="" placeholder="Password">
			<input type="text" name="db_name" value="" placeholder="Database">
			<input type="submit" name="db_form" value="update">
		</form>
	</div>
	<?php 
		require_once '../db_functions.php';
		$conn = connect_db();
		if($conn !== "Database does not exist") {
	?>
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
							<p class=\"description\">description</p>
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
							<p class=\"description\">{$row['description']}</p>
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
				close_db($conn);
			}
			echo $html;
		?>
		<form id="add_company_form">
			<input type="text" name="stock" value="" placeholder="Company Name">
			<textarea name="description" id="description" cols="30" rows="10">Description</textarea>
			<input type="text" name="price" value="" placeholder="Price">
			<input type="text" name="minval" value="" placeholder="Min Value">
			<input type="text" name="maxval" value="" placeholder="Max Value">
			<input type="text" name="remaining" value="" placeholder="Stock Remaining">
			<input type="submit" name="add_stock" value="Add">
		</form>
	</div>
	<div id="news" class="card">
		<h1 class="heading">News</h1>
		<?php 
			require_once '../db_functions.php';
			$conn = connect_db();
			if(gettype($conn) == 'string')
				$html = "<p>" . $conn . "</p>";
			else {
				$query = "SELECT * from `news`";
				if(!$result = $conn->query($query)) {
					$html = "<p>DB Error: " . $conn->error . "</p>";
				} else {
					$html = "
					<ul id=\"news-list\">
						<li id=\"heading\">
							<p class=\"id\">Id</p>
							<p class=\"event\">News</p>
						</li>";
					while($row = $result->fetch_assoc()) {
						$id = $row['id'];
						$html .= "
						<li id=\"$id\">
							<p class=\"id\">$id</p>
							<p class=\"event\">{$row['event']}</p>
							<button class=\"edit\">Edit</button>
							<button class=\"update hidden\">update</button>
							<button class=\"delete\">Delete</button>
						</li>";
					}
				}
				$html .= "</ul>";
				close_db($conn);
			}
			echo $html;
		?>
		<form id="add_news_form">
			<textarea name="event" id="event" cols="30" rows="10">News Event</textarea>
			<input type="submit" name="add_news" value="Add">
		</form>
	</div>
	<?php } ?>
</div>
<script src = "js/vendor/jquery.js"></script>
<script src = "js/main.js"></script>
<script>
	
</script>
</body>
</html>
