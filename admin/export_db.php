<?php
$db_file_path = $_SERVER['DOCUMENT_ROOT'] . "/db_config.txt";
//$db_file = fopen($db_file_path,"r") or die("Unable to open db_config.txt!");
$server = "localhost";
$db_user = "root" ;
$db_pass = "" ;
$db_name = "mockstock";

$tables = Array('news','log','stocks_owned','event_details');
$tables2 = Array('stocks','teams');
include ('dumper.php');
try {
	$dumper = Shuttle_Dumper::create(array(
		'host' => $server,
		'username' => $db_user,
		'password' => $db_pass,
		'db_name' => $db_name,
		'exclude_tables' => $tables2,
	));
	$tmp_file = $_SERVER['DOCUMENT_ROOT'] . "/Old_Projects/mockstock/temp_db_dump.sql";
	$dumper->dump($tmp_file);
	$dumper = Shuttle_Dumper::create(array(
		'host' => $server,
		'username' => $db_user,
		'password' => $db_pass,
		'db_name' => $db_name,
		'include_tables' => $tables2,
	));
	$tmp_file2 = $_SERVER['DOCUMENT_ROOT'] . "/Old_Projects/mockstock/temp2_db_dump.sql";
	$dumper->dump($tmp_file2);
	$content = file_get_contents($tmp_file);
	file_put_contents($tmp_file2, $content, FILE_APPEND);
	header('Content-type:  application/octet-stream');
	header('Content-Length: ' . filesize($tmp_file));
	header('Content-Disposition: attachment; filename='. $db_name . '.sql');
	readfile($tmp_file2);
	unlink($tmp_file);
	unlink($tmp_file2);
} catch(Shuttle_Exception $e) {
	echo "Couldn't dump database: " . $e->getMessage();
}