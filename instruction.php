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
@mysql_query("insert into name (full,last) values ('$_POST[full]','$_POST[last]') ");
header('Location: instruction.php?packet='.$_GET[packet]);
}

if ($_POST[addAddress]){
@mysql_query("insert into address (mailingAddress,city,state,zip) values ('$_POST[mailingAddress]','$_POST[city]','$_POST[state]','$_POST[zip]') ");
header('Location: instruction.php?packet='.$_GET[packet]);
}


// build server list
$q= "select * from ps_users where contract = 'YES' order by name ASC";
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
$q2= "select * from name where last = '$d[last]' order by full";
$r2=@mysql_query($q2) or die("Query: $q2<br>".mysql_error());
while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)) {
if ($d2[on_affidavit]== 'Yes'){ $str="On Affidavit"; }else{ $str = "Not On Affidavit";  }
$nList .= "<option value='$d2[id]'>$d2[full] $str </option>";
}
$nList .= "</OPTGROUP>" ;
}

// build address list
$q= "select distinct state from address order by state";
$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
while ($d=mysql_fetch_array($r, MYSQL_ASSOC)) {
$aList .= "<OPTGROUP LABEL='$d[state]'>";
$q2= "select * from address where state = '$d[state]' order by id DESC";
$r2=@mysql_query($q2) or die("Query: $q2<br>".mysql_error());
while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)) {
$aList .= "<option value='$d2[id]'>$d2[mailingAddress] $d2[city], $d2[state] $d2[zip]</option>";
}
$aList .= "</OPTGROUP>" ;
}

?>
<li>Add <a href="?packet=<?=$_GET[packet];?>&add=name">name</a> to database</li>
<li>Add <a href="?packet=<?=$_GET[packet];?>&add=address">address</a> to database</li>
<hr>
<? if (!$_GET[add]){ ?>
<h3>Adding Instruction Set</h3>
<form method="POST">
<input type="hidden" name="mainInstruction" value="1">
<input type="hidden" name="packet_id" value="<?=$_GET[packet]?>">
<table>
	<tr>
		<td>Server</td>
		<td>Address</td>
		<td>Name</td>
		<td>Allow Sub-Service</td>
	</tr>
	<tr>
		<td><select name="server_id" size="10"><?=$sList?></select></td>
		<td><select name="address_id" size="10"><?=$aList?></select></td>
		<td><select name="name_id" size="10"><?=$nList?></select></td>
		<td valign="top"><select name="allowSubService" size="2"><option>Yes</option><option>No</option></select></td>
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
		<td>Full Name</td>
		<td>Last Name (for sorting)</td>
		<td>On affidavit headers</td>
	</tr>
	<tr>
		<td><input name="full"></td>
		<td><input name="last"></td>
		<td valign="top"><select name="on_affidavit" size="2"><option>Yes</option><option>No</option></select></td>
	</tr>
</table>
<input type="submit">
</form>
<? } ?>


<? if ($_GET[add] == 'address'){ ?>
<h3>Adding Address</h3>
<form method="POST">
<input type="hidden" name="addAddress" value="1">
<table>
	<tr>
		<td>First Line of Address</td>
		<td>City</td>
		<td>State</td>
		<td>Zip</td>
	</tr>
	<tr>
		<td><input name="mailingAddress"></td>
		<td><input name="city"></td>
		<td><input name="state"></td>
		<td><input name="zip"></td>
	</tr>
</table>
<input type="submit">
</form>
<? } ?>


<? mysql_close(); ?>