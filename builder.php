<?
mysql_connect();
mysql_select_db('core');

if($_GET[delete]){
@mysql_query("update affidavits set status='deleted' where id = '$_GET[delete]'");
mail('service@mdwestserve.com','standardBuilder Deleted '.$_GET[delete],$_GET[delete].'::'.mysql_error());
header('Location: http://staff.mdwestserve.com/builder.php?message=affidavit '.$_GET[delete].' deleted');
}

if($_GET[approve]){
@mysql_query("update affidavits set qc='".$_COOKIE[psdata][name]."' where id = '$_GET[approve]'");
mail('service@mdwestserve.com','standardBuilder Approved '.$_GET[approve],$_GET[approve].'::'.mysql_error());
header('Location: http://staff.mdwestserve.com/builder.php?message=affidavit '.$_GET[approve].' approved');
}

if($_GET[message]){
echo "<div>$_GET[message]</div>";
}


if ($_POST[packet]){
$q="insert into affidavits ( buildDate, defendantID, serverName, serverAge, saleDate, saleTime, saleLocation, product, affidavit, packet, serverX, whenX, whereX, howX, attempt1, attempt2, attempt3 ,ifMAil, processor, cb1, cb2, cb3, cb4, cb5, whoX, resident, officer, agent, personal ) values ( NOW(), '".addslashes($_POST[defendantID])."', '".addslashes($_POST[serverName])."', '".addslashes($_POST[serverAge])."', '".addslashes($_POST[saleDate])."', '".addslashes($_POST[saleTime])."', '".addslashes($_POST[saleLocation])."', '$_POST[product]','".addslashes($_POST[affidavit])."','".addslashes($_POST[packet])."','".addslashes($_POST[server])."','".addslashes($_POST[when])."','".addslashes($_POST[where])."','".addslashes($_POST[how])."','".addslashes($_POST[attempt1])."','".addslashes($_POST[attempt2])."','".addslashes($_POST[attempt3])."','".addslashes($_POST[ifMail])."', '".addslashes($_COOKIE[psdata][name])."','".addslashes($_POST[cb1])."','".addslashes($_POST[cb2])."','".addslashes($_POST[cb3])."','".addslashes($_POST[cb4])."','".addslashes($_POST[cb5])."', '".addslashes($_POST[whoX])."', '".addslashes($_POST[resident])."', '".addslashes($_POST[officer])."', '".addslashes($_POST[agent])."', '".addslashes($_POST[personal])."')";
@mysql_query($q);
if (mysql_error()){
echo "<li>".mysql_error()."</li>";
echo "<li>".$q."</li>";
}else{
echo "<li>".$q."</li>";
echo "<li><a href='wizard.php?id=".mysql_insert_id()."' target='_Blank'>New Affidavit #".mysql_insert_id()."</a></li>";
}
}
function getSample($field){
$r=@mysql_query("select $field from affidavits where $field <> ''");
$d=mysql_fetch_array($r,MYSQL_ASSOC);
return $d[$field];
}
if($_GET[edit]){
$r=@mysql_query("select * from affidavits where id = '$_GET[edit]'");
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
		<td>affidavit</td>
		<td>n/a</td>
		<td><select name="affidavit"><? if($d[affidavit]){ ?><option><?=stripslashes($d[affidavit])?></option><? }?><?
$directory = '/sandbox/staff/templates';
    $results = array();
    $handler = opendir($directory);
    while ($file = readdir($handler)) {
        if ($file != '.' && $file != '..' && $file != 'CVS')
            echo "<option>$file</option>";
    }
    closedir($handler);
?></select>
</td>
		<td>Template Used</td>
	</tr>
	<tr>
		<td>product</td>
		<td>n/a</td>
		<td><select name="product"><? if($d[product]){ ?><option><?=stripslashes($d[product])?></option><? }?><option>S</option><option>OTD</option><option>EV</option></td>
		<td>Product Core</td>
	</tr>
	<tr>
		<td>packet</td>
		<td>n/a</td>
		<td><input name="packet" value="<?=stripslashes($d[packet]);?>"></td>
		<td>Standard Packet ID</td>
	</tr>
	<tr>
		<td>defendantID</td>
		<td>n/a</td>
		<td><input name="defendantID" value="<?=stripslashes($d[defendantID]);?>"></td>
		<td>Standard Defendant ID [1-6]</td>
	</tr>
	<tr>
		<td>serverX</td>
		<td>n/a</td>
		<td><input name="server" value="<?=stripslashes($d[serverX]);?>"></td>
		<td>Process Server ID</td>
	</tr>
	<tr>
		<td>whenX</td>
		<td>[when]</td>
		<td><textarea rows="2" cols="40" name="when"><?=stripslashes($d[whenX]);?></textarea></td>
		<td>When service was completed.</td>
	</tr>
	<tr>
		<td>whereX</td>
		<td>[where]</td>
		<td><textarea rows="2" cols="40" name="where"><?=stripslashes($d[whereX]);?></textarea></td>
		<td>Where service was completed.</td>
	</tr>
	<tr>
		<td>howX</td>
		<td>[how]</td>
		<td><textarea rows="2" cols="40" name="how"><?=stripslashes($d[howX]);?></textarea></td>
		<td>How service was completed.</td>
	</tr>
	<tr>
		<td>attempt1</td>
		<td>[attempt1]</td>
		<td><textarea rows="2" cols="40" name="attempt1"><?=stripslashes($d[attempt1]);?></textarea></td>
		<td>Attempt #1, service date and time.</td>
	</tr>
	<tr>
		<td>attempt2</td>
		<td>[attempt2]</td>
		<td><textarea rows="2" cols="40" name="attempt2"><?=stripslashes($d[attempt2]);?></textarea></td>
		<td>Attempt #2, service date and time.</td>
	</tr>
	<tr>
		<td>attempt3</td>
		<td>[attempt3]</td>
		<td><textarea rows="2" cols="40" name="attempt3"><?=stripslashes($d[attempt3]);?></textarea></td>
		<td>Attempt #3, service date and time.</td>
	</tr>
	<tr>
		<td>ifMail</td>
		<td>[ifMail]</td>
		<td><textarea rows="2" cols="40" name="ifMail"><?=stripslashes($d[ifMail]);?></textarea></td>
		<td>If server performed mailing.</td>
	</tr>
	<tr>
		<td>cb1</td>
		<td>[cb1]</td>
		<td><input name="cb1" value="<?=stripslashes($d[cb1]);?>"></td>
		<td>Checkbox Checked</td>
	</tr>
	<tr>
		<td>cb2</td>
		<td>[cb2]</td>
		<td><input name="cb2" value="<?=stripslashes($d[cb2]);?>"></td>
		<td>Checkbox Checked</td>
	</tr>
	<tr>
		<td>cb3</td>
		<td>[cb3]</td>
		<td><input name="cb3" value="<?=stripslashes($d[cb3]);?>"></td>
		<td>Checkbox Checked</td>
	</tr>
	<tr>
		<td>cb4</td>
		<td>[cb4]</td>
		<td><input name="cb4" value="<?=stripslashes($d[cb4]);?>"></td>
		<td>Checkbox Checked</td>
	</tr>
	<tr>
		<td>cb5</td>
		<td>[cb5]</td>
		<td><input name="cb5" value="<?=stripslashes($d[cb5]);?>"></td>
		<td>Checkbox Checked</td>
	</tr>
	<tr>
		<td>whoX</td>
		<td>[who]</td>
		<td><input name="whoX" value="<?=stripslashes($d[whoX]);?>"></td>
		<td>Defendant</td>
	</tr>
	<tr>
		<td>officer</td>
		<td>[who4]</td>
		<td><input name="officer" value="<?=stripslashes($d[officer]);?>"></td>
		<td>Company Officer To</td>
	</tr>
	<tr>
		<td>agent</td>
		<td>[who4a]</td>
		<td><input name="officer" value="<?=stripslashes($d[agent]);?>"></td>
		<td>Resident Agent To</td>
	</tr>
	<tr>
		<td>personal</td>
		<td>[who1]</td>
		<td><input name="personal" value="<?=stripslashes($d[personal]);?>"></td>
		<td>Personal Delivery To</td>
	</tr>
	<tr>
		<td>resident</td>
		<td>[who2]</td>
		<td><input name="resident" value="<?=stripslashes($d[resident]);?>"></td>
		<td>SubService To</td>
	</tr>
	<tr>
		<td>saleDate</td>
		<td>[saleDate]</td>
		<td><input name="saleDate" value="<?=stripslashes($d[saleDate]);?>"></td>
		<td>Auction Sale Date</td>
	</tr>
	<tr>
		<td>saleTime</td>
		<td>[saleTime]</td>
		<td><input name="saleTime" value="<?=stripslashes($d[saleTime]);?>"></td>
		<td>Auction Sale Time</td>
	</tr>
	<tr>
		<td>saleLocation</td>
		<td>[saleLocation]</td>
		<td><input name="saleLocation" value="<?=stripslashes($d[saleLocation]);?>"></td>
		<td>Auction Sale Location</td>
	</tr>
	<tr>
		<td>serverName</td>
		<td>[serverName]</td>
		<td><input name="serverName" value="<?=stripslashes($d[serverName]);?>"></td>
		<td>Process Server's Name</td>
	</tr>
	<tr>
		<td>serverAge</td>
		<td>[serverAge]</td>
		<td><input name="serverAge" value="<?=stripslashes($d[serverAge]);?>"></td>
		<td>Process Server's Age</td>
	</tr>
</table>
<center><input type="submit" value="Save Affidavit Details"><center>
</form>
</td><td valign="top">
<iframe src="preview.php" height="1000" width="1000">
</td></tr></table>