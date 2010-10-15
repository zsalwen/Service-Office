<?
mysql_connect();
mysql_select_db('core');
if ($_POST[packet]){
$q="insert into standard_affidavits ( affidavit, packet, serverX, whenX, whereX, howX, attempt1, attempt2, attempt3 ,ifMAil, processor, cb1, cb2, cb3, cb4, cb5, whoX, resident, officer, agent, personal ) values ('$_POST[affidavit]','$_POST[packet]','$_POST[server]','$_POST[when]','$_POST[where]','$_POST[how]','$_POST[attempt1]','$_POST[attempt2]','$_POST[attempt3]','$_POST[ifMAil]', '".$_COOKIE[psdata][name]."','$_POST[cb1]','$_POST[cb2]','$_POST[cb3]','$_POST[cb4]','$_POST[cb5]', '$_POST[whoX]', '$_POST[resident]', '$_POST[officer]', '$_POST[agent]', '$_POST[personal]')";
@mysql_query($q);
if (mysql_error()){
echo "<li>".mysql_error()."</li>";
echo "<li>".$q."</li>";
}else{
echo "<a href='wizard.php?id=".mysql_insert_id()."' target='_Blank'>New Affidavit #".mysql_insert_id()."</a>";
}
}
function getSample($field){
$r=@mysql_query("select $field from standard_affidavits where $field <> ''");
$d=mysql_fetch_array($r,MYSQL_ASSOC);
return $d[$field];
}
if($_GET[edit]){
$r=@mysql_query("select * from standard_affidavits where id = '$_GET[edit]'");
$d=mysql_fetch_array($r,MYSQL_ASSOC);
}
?><table>
<tr>
<td valign="top">
<form id="affidavitBuilder" action="builder.php" method="POST">
<table border="1">
	<tr>
		<td>Field Name</td>
		<td>Template Replace</td>
		<td>Data to Record</td>
		<td>Instructions</td>
	</tr>
	<tr>
		<td>packet</td>
		<td>n/a</td>
		<td><input name="packet" value="<?=$d[packet]?>"></td>
		<td>Standard Packet ID</td>
	</tr>
	<tr>
		<td>serverX</td>
		<td>n/a</td>
		<td><input name="server" value="<?=$d[serverX]?>"></td>
		<td>Process Server ID</td>
	</tr>
	<tr>
		<td>whenX</td>
		<td>[when]</td>
		<td><textarea rows="3" cols="30" name="when"><?=$d[whenX]?></textarea></td>
		<td>When service was completed.</td>
	</tr>
	<tr>
		<td>whereX</td>
		<td>[where]</td>
		<td><textarea rows="3" cols="30" name="where"><?=$d[whereX]?></textarea></td>
		<td>Where service was completed.</td>
	</tr>
	<tr>
		<td>howX</td>
		<td>[how]</td>
		<td><textarea rows="3" cols="30" name="how"><?=$d[howX]?></textarea></td>
		<td>How service was completed.</td>
	</tr>
	<tr>
		<td>attempt1</td>
		<td>[attempt1]</td>
		<td><textarea rows="3" cols="30" name="attempt1"><?=$d[attempt1]?></textarea></td>
		<td>Attempt #1, service date and time.</td>
	</tr>
	<tr>
		<td>attempt2</td>
		<td>[attempt2]</td>
		<td><textarea rows="3" cols="30" name="attempt2"><?=$d[attempt2]?></textarea></td>
		<td>Attempt #2, service date and time.</td>
	</tr>
	<tr>
		<td>attempt3</td>
		<td>[attempt3]</td>
		<td><textarea rows="3" cols="30" name="attempt3"><?=$d[attempt3]?></textarea></td>
		<td>Attempt #3, service date and time.</td>
	</tr>
	<tr>
		<td>ifMail</td>
		<td>[ifMail]</td>
		<td><textarea rows="3" cols="30" name="ifMail"><?=$d[ifMail]?></textarea></td>
		<td>If server performed mailing.</td>
	</tr>
	<tr>
		<td>cb1</td>
		<td>[cb1]</td>
		<td><input name="cb1" value="<?=$d[cb1]?>"></td>
		<td>Checkbox Checked</td>
	</tr>
	<tr>
		<td>cb2</td>
		<td>[cb2]</td>
		<td><input name="cb2" value="<?=$d[cb2]?>"></td>
		<td>Checkbox Checked</td>
	</tr>
	<tr>
		<td>cb3</td>
		<td>[cb3]</td>
		<td><input name="cb3" value="<?=$d[cb3]?>"></td>
		<td>Checkbox Checked</td>
	</tr>
	<tr>
		<td>cb4</td>
		<td>[cb4]</td>
		<td><input name="cb4" value="<?=$d[cb4]?>"></td>
		<td>Checkbox Checked</td>
	</tr>
	<tr>
		<td>cb5</td>
		<td>[cb5]</td>
		<td><input name="cb5" value="<?=$d[cb5]?>"></td>
		<td>Checkbox Checked</td>
	</tr>
	<tr>
		<td>whoX</td>
		<td>[who]</td>
		<td><input name="whoX" value="<?=$d[whoX]?>"></td>
		<td>Defendant</td>
	</tr>
	<tr>
		<td>officer</td>
		<td>[who4]</td>
		<td><input name="officer" value="<?=$d[officer]?>"></td>
		<td>Company Officer To</td>
	</tr>
	<tr>
		<td>agent</td>
		<td>[who4a]</td>
		<td><input name="officer" value="<?=$d[agent]?>"></td>
		<td>Resident Agent To</td>
	</tr>
	<tr>
		<td>personal</td>
		<td>[who1]</td>
		<td><input name="personal" value="<?=$d[personal]?>"></td>
		<td>Personal Delivery To</td>
	</tr>
	<tr>
		<td>resident</td>
		<td>[who2]</td>
		<td><input name="resident" value="<?=$d[resident]?>"></td>
		<td>SubService To</td>
	</tr>
	
</table>
<center><input type="submit" value="Save Affidavit Details"><center>
</form>
</td><td valign="top">
<iframe src="preview.php" height="1000" width="1000">
</td></tr></table>