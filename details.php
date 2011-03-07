<?
if (!$_COOKIE[psdata][user_id]){
error_log(date('h:iA j/n/y')." SECURITY PREVENTED ACCESS to ".$_SERVER['SCRIPT_NAME']." by ".$_SERVER["REMOTE_ADDR"]."\n", 3, '/logs/user.log');
header ('Location: http://staff.mdwestserve.com');
}
date_default_timezone_set('America/New_York');
include 'edit.functions.php';
mysql_connect();
mysql_select_db('core');
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="details.css" />
</head>
<body>
<table style="width:100%; height:100%;">
<tr>
<td colspan="3" style="height:100px;" align="center"><? include 'details.bar.php';?></td>
</tr>
<tr>
<td><iframe name="pane1" id="pane1" style="width:100%; height:100%;"></iframe></td>
<td valign="center" style="width:100px;"><? include 'details.menu.php';?></td>
<td><iframe name="pane2" id="pane2" style="width:100%; height:100%;"></iframe></td>
</tr>
</table>
</body>
</html>