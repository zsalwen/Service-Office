<?
if (!$_COOKIE[psdata][user_id]){
error_log(date('h:iA j/n/y')." SECURITY PREVENTED ACCESS to ".$_SERVER['SCRIPT_NAME']." by ".$_SERVER["REMOTE_ADDR"]."\n", 3, '/logs/user.log');
header ('Location: http://staff.mdwestserve.com');
}
?>
<html>
<head>
<?
mysql_connect();
mysql_select_db('core');
?>
</head>
<body>
<fieldset>
<legend>Attachments</legend>
<table border="1">
<tr>
<td>Date Received</td>
<td></td>
<td></td>
<td>Description</td>
<td>Status</td>
</tr>
<?
$r=@mysql_query("select * from attachment where packet_id = '$_GET[packet]'");
while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
?>
 <tr>
<td><?=$d[processed];?></td>
<td>
<img onClick="parent.frames['pane1'].location.href = 'attachment.php?id=<?=$d[id];?>'; " src="http://connect.stern.nyu.edu/zimbra/img/startup/ImgLeftArrow.gif" border="0"> 
Edit 
<img onClick="parent.frames['pane2'].location.href = 'attachment.php?id=<?=$d[id];?>'; " src="http://connect.stern.nyu.edu/zimbra/img/startup/ImgRightArrow.gif" border="0">
</td>
<td>
<img onClick="parent.frames['pane1'].location.href = '<?=$d[absolute_url];?>'; " src="http://connect.stern.nyu.edu/zimbra/img/startup/ImgLeftArrow.gif" border="0"> 
Open 
<img onClick="parent.frames['pane2'].location.href = '<?=$d[absolute_url];?>'; " src="http://connect.stern.nyu.edu/zimbra/img/startup/ImgRightArrow.gif" border="0">
</td>
<td><?=$d[description];?></td>
<td><?=$d[status];?></td>
</tr>
<? }?>
</table>
</fieldset>
</body>
</html>