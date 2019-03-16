<?php 
if(isset($_POST['login_form'])&&$_POST['login_form'] == 'login') {
	if(isset($_POST['ticket'])&&isset($_POST['passwd'])&&$_POST['passwd'] == 'bnmit_tatva') {
		$loc = $_SERVER['DOCUMENT_ROOT'] . "/";
		header("Location: $loc");
		setcookie('event','mockstock',$expire, '/', null, null, true);
		setcookie('ticket_no',$ticket,$expire, '/', null, null, true);
		$_COOKIE['ticket_no']=$ticket;
		$_COOKIE['event']="mockstock";
		exit;
	} else {
		echo "Error logging in";
	}
}
?>
<link rel="stylesheet" href="css/main.css">
<body id="register">
<form action="" id="register_form">
	<input type="text" name="ticket" value="" placeholder="Ticket Number"><br>
	<input type="password" name="passwd" value="" placeholder="Password"><br>
	<input type="submit" name="login_form" value="login">
</form>
</body>