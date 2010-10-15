<? 
include 'functions.php';
mysql_connect();
mysql_select_db('core');
$add = $_SERVER['REMOTE_ADDR'];
if ($_POST[staffEmail] && $_POST[staffPassword]){
	$email = $_POST[staffEmail];
	$pass = $_POST[staffPassword];
	$q1 = "SELECT * FROM ps_users WHERE email = '$email' AND password = '$pass' AND level = 'Operations'";		
	$r1 = @mysql_query ($q1) or die(mysql_error());
	if ($data = mysql_fetch_array($r1, MYSQL_ASSOC)){
			$inEightHours= 60 * 60 * 8 + time();
			setcookie ("psdata[user_id]", $data[id], $inEightHours, "/", ".mdwestserve.com");
			setcookie ("psdata[effects]", $data[effects], $inEightHours, "/", ".mdwestserve.com");
			setcookie ("psdata[name]", $data[name], $inEightHours, "/", ".mdwestserve.com");
			setcookie ("psdata[tos_date]", $data[tos_date], $inEightHours, "/", ".mdwestserve.com");
			setcookie ("psdata[email]", $data[email], $inEightHours, "/", ".mdwestserve.com");
			setcookie ("psdata[level]", $data[level], $inEightHours, "/", ".mdwestserve.com");
			setcookie ("staff[user_id]", $data[id], $inEightHours, "/", "staff.mdwestserve.com");
			setcookie ("staff[name]", $data[name], $inEightHours, "/", "staff.mdwestserve.com");
			setcookie ("staff[email]", $data[email], $inEightHours, "/", "staff.mdwestserve.com");
			error_log(date('h:iA j/n/y')." $data[name] logged in using ".$_SERVER["REMOTE_ADDR"]."\n", 3, '/logs/user.log');
			if ($data[level] != "Operations"){
				header ('Location: http://mdwestserve.com');
			}else{
				header ('Location: action.php');
			}
	}
} else{
	header ('Location: http://staff.mdwestserve.com');
}
?>