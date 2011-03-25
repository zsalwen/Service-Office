<?
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
setcookie ("admin[user_id]", $data[id], $inEightHours, "/", ".mdwestserve.com");
setcookie ("admin[effects]", $data[effects], $inEightHours, "/", ".mdwestserve.com");
setcookie ("admin[name]", $data[name], $inEightHours, "/", ".mdwestserve.com");
setcookie ("admin[tos_date]", $data[tos_date], $inEightHours, "/", ".mdwestserve.com");
setcookie ("admin[email]", $data[email], $inEightHours, "/", ".mdwestserve.com");
setcookie ("admin[level]", $data[level], $inEightHours, "/", ".mdwestserve.com");
//if ($data[level] != "Operations"){
//header ('Location: http://mdwestserve.com');
//}else{
//header ('Location: index.php');
//}
}


} else {
?>


			<form action="login.php" method="post" id="search" name="router">

                <li>E-Mail Address</td><td><input size="40" style="background-color:#ffccff;" name="staffEmail" />

<li>Password</li><input input size="40" style="background-color:#ffccff;"  name="staffPassword" type="password" />

<input type="submit" value="GO">
							
            </form>
			


<? } ?>