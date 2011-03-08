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
$nList .= "<option value='$d2[id]'>$d2[full]</option>";
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
		<td colspan="2"><b>Server</b></td>
		<td colspan="2"><b>Address</b></td>
	</tr>
	<tr>
		<td colspan="2"><select name="server_id" size="10"><?=$sList?></select></td>
		<td colspan="2"><select name="address_id" size="10"><?=$aList?></select></td>
	</tr>
	<tr>
		<td colspan="4"><b>Name</b></td>
	</tr>
	<tr>
		<td colspan="4"><select name="name_id" size="10"><?=$nList?></select></td>
	</tr>
	<tr>
		<td><b># Attempts</b></td>
		<td><b>Allow Posting</b></td>
		<td><b>Allow Sub-Service</b></td>		
                <td><b>Show name on case header</b></td>
	</tr>
	<tr>
		<td valign="top"><input name="attempts"><option>Yes</option><option>No</option></select></td>
		<td valign="top"><select name="allowPosting" size="2"><option>Yes</option><option>No</option></select></td>
		<td valign="top"><select name="allowSubService" size="2"><option>Yes</option><option>No</option></select></td>
		<td valign="top"><select name="onAffidavit" size="2"><option>Yes</option><option>No</option></select></td>
	</tr>
</table>
<fieldset>
<legend>Document to serve</legend>
<?
$rOFS=@mysql_query("select * from attachment where packet_id = '$_GET[packet]' ");
while($dOFS=mysql_fetch_array($rOFS,MYSQL_ASSOC)){
 echo "<li><input type='checkbox' name='doc[$dOFS[id]]'>$dOFS[instruction_id]  $dOFS[user_id] $dOFS[server_id] $dOFS[processed] $dOFS[url] <small  onClick=\"parent.frames['pane2'].location.href = '$dOFS[absolute_url]' \">preview</small></li>";
}
?>
</fieldset>
<input type="submit" value="Add Manual Instruction Set">
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

<?

$r=@mysql_query("select client_file from packet where id = '$_GET[packet]' ");
$d=mysql_fetch_array($r,MYSQL_ASSOC);

$select_query = "Select create_id, create_date, update_id, update_date, filenumber,clientidentifier,defendantnumber, defendantfullname,defendantaddress1,defendantaddress2,defendantcity,defendantstate,defendantstateid, defendantzip, defendantrelationship,other,status,statusdate From defendants Where filenumber = '$d[client_file]'";
$result = mysql_query($select_query);

// Bail out if the query or connection fails
if ($result == false) {
echo $system_message[104];
exit;
}
else {

echo '<table border="1">';
echo '<tr>';
echo '<td>Service contacts for '.$d[client_file].'</td> <td>Address</td> <td>City</td> <td>State</td> <td>Zip</td> <td>Status</td> <td>Status Date</td> ';
echo '</tr>';

while ($row = @mysql_fetch_array($result,MYSQL_ASSOC)) {
// check and add to name and address tables
$newData=0;

$rTest=@mysql_query("select id from name where full = '".$row['defendantfullname']."' ");
$dTest=mysql_fetch_array($rTest,MYSQL_ASSOC);
if(!$dTest[id]){
$newData=1;
@mysql_query("insert into name (full) values ('".$row['defendantfullname']."') ");
}

$address = $row['defendantaddress1'].' '.$row['defendantaddress2'];   


$rTest=@mysql_query("select id from address where mailingAddress = '$address' and city = '".$row['defendantcity']."' and zip = '".$row['defendantzip']."' and state = '".$row['defendantstate']."' ");
$dTest=mysql_fetch_array($rTest,MYSQL_ASSOC);
if(!$dTest[id]){
$newData=1;
@mysql_query("insert into address (mailingAddress, city, state, zip) values ('$address', '".$row['defendantcity']."', '".$row['defendantstate']."',  '".$row['defendantzip']."') ");
}

echo '<tr>';
echo '<td valign="top">
<table>
<tr>

<td>'.$row['defendantfullname'].'</td></tr></table></td> <td valign="top">
<table>
<tr>
<td>'.$row['defendantaddress1'].' '.$row['defendantaddress2'].'</td></tr></table></td>';
echo '<td valign="top">'.$row['defendantcity'].'</td> <td valign="top">'.$row['defendantstate'].'</td> <td valign="top">'.$row['defendantzip'].'</td>';
echo '<td valign="top">'.$row['status'].'</td> <td valign="top">'.$row['statusdate'].'</td> ';


echo '</tr>';

}

    }
echo "</table>";
?>



<? 
if($newData==1){ ?>
<meta http-equiv="refresh" content="0"> 
<? } 
mysql_close(); ?>