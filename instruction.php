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
@mysql_query("insert into name (prefix,first,middle,last,suffix) values ('$_POST[prefix]','$_POST[first]','$_POST[middle]','$_POST[last]','$_POST[suffix]') ");
}

if ($_POST[addAddress]){

}

if ($_POST[addServer]){

}

// build server list
$q= "select * from ps_users where contract = 'YES' order by id ASC";
$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
while ($d=mysql_fetch_array($r, MYSQL_ASSOC)) {
$sList .= "";

if ($d[company]){ 
 $sList .= "<OPTGROUP LABEL='$d[company]'><option value='$d[id]'>$d[name]</option></OPTGROUP>" ;
}else{ 
 $sList .= "<option value='$d[id]'>$d[name]</option>" ;}
} 

// build name list
$q= "select distinct last from name order by last";
$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
while ($d=mysql_fetch_array($r, MYSQL_ASSOC)) {
$nList .= "<OPTGROUP LABEL='$d[last]'>";
$q2= "select * from name where last = '$d[last]' order by first";
$r2=@mysql_query($q2) or die("Query: $q2<br>".mysql_error());
while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)) {
if ($d2[on_affidavit]== 'Yes'){ $str="On Affidavit"; }else{ $str = "Not On Affidavit";  }
$nList .= "<option value='$d2[id]'>$d2[prefix] $d2[first] $d2[middle] $d2[last] $d2[suffix] $str </option>";
}
$nList .= "</OPTGROUP>" ;
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
		<td><select name="address_id" size="10"><?=$nList?></select></td>
		<td><select name="name_id" size="10"><option><?=$_GET[packet]?></option></select></td>
		<td valign='top"><select name="allowSubService" size="2"><option>Yes</option><option>No</option></select></td>
	</tr>
</table>
<input type="submit">
</form>
<? } ?>



<? if ($_GET[add] == 'name'){ ?>
<h3>Adding Name</h3>
<form method="POST">
<input type="hidden" name="addName" value="1">
<table>
	<tr>
		<td>Prefix</td>
		<td>First</td>
		<td>Middle</td>
		<td>Last</td>
		<td>Suffix</td>
		<td>On affidavit headers</td>
	</tr>
	<tr>
		<td><input name="prefix"></td>
		<td><input name="first"></td>
		<td><input name="middle"></td>
		<td><input name="last"></td>
		<td><input name="suffix"></td>
		<td valign='top"><select name="on_affidavit" size="2"><option>Yes</option><option>No</option></select></td>
	</tr>
</table>
<input type="submit">
</form>
<? } ?>




<? mysql_close(); ?>