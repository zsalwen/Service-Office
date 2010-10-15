<?
mysql_connect();
mysql_select_db('hacking');
if($_POST[submit]){
@mysql_query("update fisherSales set caseNames = '$_POST[caseNames]', caseNumber = '$_POST[caseNumber]', server = '$_POST[server]' where id = '$_POST[id]'");
header('Location: fisherData.php');
}
$r=@mysql_query("select * from fisherSales where id like '%".$_GET['id']."%'");
$d=mysql_fetch_array($r,MYSQL_ASSOC);
?>
<form method="POST">
<table cellspacing="0" cellpadding="2" border="1" style="border-collapse:collapse;">
	<tr>
		<td bgcolor="#FF0000">County</td>
		<td bgcolor="#FF0000">Fisher #</td>
		<td bgcolor="#FF00FF">Address</td>
	</tr>
	<tr>
		<td><?=$d[county];?></td>
		<td><?=$d[id]?></td>
		<td><?=$d[address]?></td>
	</tr>
	<tr>
		<td bgcolor="#FFFF00">Case Names</td>
		<td bgcolor="#FFFF00">Case Number</td>
		<td bgcolor="#0000FF">Server</td>
	</tr>	
	<tr>
		<td><input name="caseNames" value="<?=$d[caseNames]?>"></td>		
		<td><input name="caseNumber" value="<?=$d[caseNumber]?>"></td>		
		<td><input name="server" value="<?=$d[server]?>"></td>		
	</tr>	
</table>
<input type="hidden" name="id" value="<?=$d[id]?>">
<input name="submit" type="submit">
</form><hr>
<div>First Seen Online: <?=$d[online]?></div>
<div>Auction Notes: <?=$d[notes]?></div>