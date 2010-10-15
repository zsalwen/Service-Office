<?
if ($_COOKIE[psdata][level] != "Operations"){
	error_log("Die Securty Lock ('/sandbox/staff/lock.php') Triggered in ".$_SERVER['PHP_SELF']." \n", 3, '/logs/fail.log');
	die();
}else{
	//error_log("Securty Lock ('/sandbox/staff/lock.php') Passed in ".$_SERVER['PHP_SELF']." \n", 3, '/logs/fail.log');
}
?>