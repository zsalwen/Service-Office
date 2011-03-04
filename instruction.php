<?
if (!$_COOKIE[psdata][user_id]){
error_log(date('h:iA j/n/y')." SECURITY PREVENTED ACCESS to ".$_SERVER['SCRIPT_NAME']." by ".$_SERVER["REMOTE_ADDR"]."\n", 3, '/logs/user.log');
header ('Location: http://staff.mdwestserve.com');
}
mysql_connect();
mysql_select_db('core');
if ($_POST[mainInstruction]){
@mysql_query("INSERT INTO instruction (packet_id, server_id, address_id, name_id, allowSubService) VALUES
('$_POST[packet_id]', '$_POST[server_id]', '$_POST[address_id]', '$_POST[name_id]', '$_POST[allowSubService]')");
echo "<script>window.parent.location.href='edit.php?packet=$_GET[packet]';</script>";
}

if ($_POST[addName]){

}

if ($_POST[addAddress]){

}

if ($_POST[addServer]){

}

// build server list
$q= "select * from ps_users where contract = 'YES' order by id ASC";
$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
while ($d=mysql_fetch_array($r, MYSQL_ASSOC)) {
$sList .= "<option value='$d[id]'>";
if ($d[company]){ $sList .= "$d[company], $d[name]" ;}else{ $sList .= "$d[name]" ;}
$sList .= "</option>";
} 



?>
<li>Add <a href="?packet=<?=$_GET[packet];?>&add=name">name</a> to database</li>
<li>Add <a href="?packet=<?=$_GET[packet];?>&add=address">address</a> to database</li>
<li>Add <a href="?packet=<?=$_GET[packet];?>&add=server">server</a> to database</li>
<hr>
<? if (!$_GET[add]){ ?>
<h3>Adding Instruction Set</h3>
<form method="POST">
<input type="hidden" name="mainInstruction" value="1">
<input type="hidden" name="packet_id" value="<?=$_GET[packet_id]?>">
<table>
	<tr>
		<td>Packet ID</td>
		<td>Server</td>
		<td>Address</td>
		<td>Name</td>
		<td>Allow Sub-Service</td>
	</tr>
	<tr>
		<td><?=$_GET[packet]?></td>
		<td><select name="server_id" size="10"><?=$sList?></select></td>
		<td><select name="address_id" size="10"><option><?=$_GET[packet]?></option></select></td>
		<td><select name="name_id" size="10"><option><?=$_GET[packet]?></option></select></td>
		<td valign='top"><select name="allowSubService" size="2"><option>Yes</option><option>No</option></select></td>
	</tr>
</table>
<input type="submit">
</form>
<? } ?>

<? mysql_close(); ?>