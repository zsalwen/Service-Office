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
		<td><?=$_GET[packet]?></td>
	</tr>
	<tr>
		<td>Server</td>
		<td><select name="server_id"><option><?=$_GET[packet]?></option></select></td>
	</tr>
	<tr>
		<td>Address</td>
		<td><select name="address_id"><option><?=$_GET[packet]?></option></select></td>
	</tr>
	<tr>
		<td>Name</td>
		<td><select name="name_id"><option><?=$_GET[packet]?></option></select></td>
	</tr>
	<tr>
		<td>Allow Sub-Service</td>
		<td><select name="allowSubService"><option>Yes</option><option>No</option></select></td>
	</tr>
</form>
<? } ?>

<? mysql_close(); ?>