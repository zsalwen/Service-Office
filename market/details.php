<?
// connect
mysql_connect();
mysql_select_db('core');

if ($_POST[id]){
	$id=$_POST[id];
}else{
	$id=$_GET[id];
}

function dbCleaner($str){
$str = trim($str);
$str = addslashes($str);
//$str = strtoupper($str);
//$str = ucwords($str);
return $str;
}

function marketAssoc($id,$type){
	$q="SELECT * FROM market_assoc WHERE childID='$id'";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		$r1=@mysql_query("SELECT * FROM market WHERE marketID='$d[parentID]'");
		$d1=mysql_fetch_array($r1,MYSQL_ASSOC);
		$list .= "<li>Uses $d1[name] for $d1[type]</li>";
	}
	$q="SELECT * FROM market_assoc WHERE parentID='$id'";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		$r1=@mysql_query("SELECT * FROM market WHERE marketID='$d[childID]'");
		$d1=mysql_fetch_array($r1,MYSQL_ASSOC);
		$list .= "<li>Is used by $d1[name] for $type</li>";
	}
	return $list;
}

function phaseList($phase,$date){
	$phase=strtoupper($phase);
	$list="<select name='select' onchange='this.form.submit()'>";
	if ($phase == 'GOOD LEAD'){
		$list .= "<option value='$phase'>GOOD LEAD-RECEIVED BUSINESS</option>";
	}elseif ($phase == 'CALL BACK'){
		$list .= "<option value='$phase'>CALL BACK ON $date</option>";
	}else{
		$list .= "<option value='$phase'>$phase</option>";
	}
	if ($phase != 'COLD CALL'){
		$list .= "<option>COLD CALL</option>";
	}
	if ($phase != 'SEND INFO'){
		$list .= "<option>SEND INFO</option>";
	}
	if ($phase != 'CALL BACK'){
		$list .= "<option value='CALL BACK'>SENT INFO, SET CALL BACK DATE</option>";
	}
	if ($phase != 'GOOD LEAD'){
		$list .= "<option value='GOOD LEAD'>GOOD LEAD-RECEIVED BUSINESS</option>";
	}
	$list .= '</select>';
	return $list;
}
function getName($id){
	$r = @mysql_query("select * from market where marketID = '$id' ") or die(mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	return strtoupper($d[name]);
}
$today=date('Y-m-d');
if ($_POST[delete]){
	echo "<table align='center' style='background-color:FFFFFF;'>
			<tr><td align='center'>Are you <i>SURE</i> you want to delete this entry?</td><tr><td align='center'><form method='post' name='form2' style='display:inline;'><input type='hidden' name='id' value='$id'><input style='background-color:green;' type='submit' name='confirm' value='YES'></form> | <form action='http://staff.mdwestserve.com/market/details.php' name='form3' style='display:inline;'><input type='hidden' name='id' value='$id'><input style='background-color:red;' type='submit' name='restart' value='NO'></form></td>
				</tr>
			</table>";
}
if ($_POST[confirm]){
	$q2="DELETE from market where marketID = '".$_POST["id"]."'";
	$r2=@mysql_query($q2) or die("Query: $q2<br>".mysql_error());
	$msg="Entry Deleted.";
	echo "<script>window.location='http://staff.mdwestserve.com/market/index.php?msg=$msg'</script>";
	$logMsg=$_COOKIE[psdata][name]." Deleting Marketing Entry For ".getName($_POST[id])." (ID $_POST[id])";
}
if ($_POST[submit]){
	if ($_POST[phase] == 'CALL BACK'){
		$q="UPDATE market SET contact='$_POST[contact]', name='$_POST[clientName]', phone='$_POST[phone]', address='$_POST[address]', phase='$_POST[phase]', coldCall=NOW(), doNotCall='$_POST[doNotCall]', callBack='$_POST[callBack]', sendInfo='$_POST[sendDate]' WHERE marketID='$id'";
	}else{
		$q="UPDATE market SET contact='$_POST[contact]', name='$_POST[clientName]', phone='$_POST[phone]', address='$_POST[address]', phase='$_POST[phase]', coldCall=NOW(), doNotCall='$_POST[doNotCall]' WHERE marketID='$id'";
	}
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	echo "<center><h1>Entry Updated.</h1></center>";
}
// build resources
$r = @mysql_query("select * from market where marketID = '$id' ");
$d=mysql_fetch_array($r,MYSQL_ASSOC);

if ($logMsg){
	error_log("[".date('h:iA n/j/y')."] ".$logMsg,3,"/logs/user.log");
}else{
	error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Updating Marketing Entry For ".getName($id)." (ID $id)";,3,"/logs/user.log");
}
?>
<style>
.y{background-color:FFFFCC;}
input,textarea,.select{background-color:CCFFFF;}
</style>
<table border="1" align="center">
	<tr>
		<td colspan="2" align="center" class="y"><?=$d[name]?>-ID [<?=$id?>]</td>
	</tr>
	<tr>
		<td>Type</td>
		<td><?=strtoupper($d[type]);?></td>
	</tr>
	<tr>
		<td>Phase</td>
		<td><form method="post" name="select" style='display:inline;'><input type="hidden" name="id" value="<?=$d[marketID]?>"><?if ($_POST[select] != ''){ echo phaseList($_POST[select],$d[callBack]);}else{ echo phaseList($d[phase],$d[callBack]);}?></form></td>
	</tr>
	<? if($_POST[select] == 'CALL BACK'){	?>
	<tr>
		<td colspan='2'><form method="post" style='display:inline;'>
		<table><tr><td>Date Info Sent</td><td><input name="sendDate" value='<?=$today?>' />(YYYY-MM-DD)</td><td>Call Back Date</td><td><input name="callBack" />(YYYY-MM-DD)</td></tr></table>
		</td>
	</tr>
	<? } ?>
	<tr>
		<td>Contact</td>
		<td><? if($_POST[select] != 'CALL BACK'){?><form method="post" style='display:inline;'><? } ?><input type='hidden' name='phase' value='<? if ($_POST[select] != ''){ echo $_POST[select];}else{ echo $d[phase];}?>'><input name="contact" value="<?=$d[contact]?>" size="50"> <input type='checkbox' name='doNotCall' value='checked' <? if ($d[doNotCall] == 'checked'){ echo 'checked';}?>> Do Not Call (remove from list)</td>
	</tr>
	<tr>
		<td>Name</td>
		<td><input name='clientName' value='<?=$d[name]?>' size="50"></td>
	</tr>
	<tr>
		<td>Phone</td>
		<td><input name="phone" value="<?=$d[phone]?>" size="50"></td>
	</tr>
	<tr>
		<td>Address</td>
		<td><textarea name='address'><?=$d[address]?></textarea></td>
	</tr>
	<tr>
		<td>Notes</td>
		<td><iframe height="200px" width="700px"  frameborder="0" src="http://staff.mdwestserve.com/market/notes.php?id=<?=$d[marketID]?>"></iframe></td>
	</tr>
	<tr>
		<td colspan="2" align="center" class="y"><input type="submit" name="submit" "value="Submit"> <input type="submit" value="Delete Contact" name="delete"> <a href="index.php">Return to Index</a></td>
	</tr>
</table>
</form>