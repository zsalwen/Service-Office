<?
if (!$_COOKIE[psdata][user_id]){
error_log(date('h:iA j/n/y')." SECURITY PREVENTED ACCESS to ".$_SERVER['SCRIPT_NAME']." by ".$_SERVER["REMOTE_ADDR"]."\n", 3, '/logs/user.log');
header ('Location: http://staff.mdwestserve.com');
}
date_default_timezone_set('America/New_York');
include 'edit.functions.php';
mysql_connect();
mysql_select_db('core');
if ($_GET[packet] && $_GET[packet] < '20000'){
die('This details page is for packet 20000 and above, please use the ev/otd/standard versions for legacy packets.');
}

if($_GET[packet]){
$query = "SELECT *, CONCAT(TIMEDIFF( NOW(), date_received)) as hours FROM packet where id='$_GET[packet]'";
$r=@mysql_query($query) or die($query.'<br>'.mysql_error());
$d=mysql_fetch_array($r, MYSQL_ASSOC);
hardLog('Loaded Details for '.$_GET[packet],'user');
}else{
die('Missing Packet Number');
}
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="details.css" />
</head>
<body>
<table style="width:100%; height:100%;">
<tr>
<td colspan="3" style="height:50px;" align="center"><? include 'details.bar.php';?></td>
</tr>
<tr>
<td><iframe name="pane1" id="pane1" style="width:100%; height:100%;"></iframe></td>
<td valign="top" style="width:100px;" align="center"><font size="+2">Packet <?=$_GET[packet]?></font><br><? include 'details.menu.php';?></td>
<td><iframe name="pane2" id="pane2" style="width:100%; height:100%;"></iframe></td>
</tr>
</table>
</body>
</html>