<?
include 'common.php';
include 'lock.php';
if ($_POST['attempt']){
	$action="<li>$_POST[attempt_no] Effort: $_POST[address_source]</li>
$_POST[date] $_POST[time]<br>
$_POST[address], $_POST[city], $_POST[state] $_POST[zip]<br>
$_POST[defendant_detail]<br>
$_POST[property_detail]";
	$action = strtoupper($action);
	$q=@mysql_query("UPDATE ps_history SET action_str='$action' where history_id='$_POST[history_id]'");
}
if ($_POST['posted']){
	$action="<li>Posting the Property:</li>
$_POST[date] $_POST[time]<br>
$_POST[address], $_POST[city], $_POST[state] $_POST[zip]<br>
$_POST[property_detail]";
	$action = strtoupper($action);
	$q=@mysql_query("UPDATE ps_history SET action_str='$action' where history_id='$_POST[history_id]'");
}
if ($_POST['resident']){
	$action="<u>Name of person served</u>:<br>
<b>Not Mortgagor / Grantor<br>
$_POST[name]</b><br>
<u>Date</u>:<br>
<b>$_POST[date] $_POST[time]</b><br>
<u>Particular place of service</u>:<br>
<b>$_POST[address_source]<br>";
if ($_POST[delivery_detail]){
$action .= "$_POST[delivery_detail]<br>";
}
$action .="$_POST[address]<br>
$_POST[city], $_POST[state] $_POST[zip]</b><br>
<u>Description of the individual served and the facts upon which the individual making service concluded that the individual served is of suitable age and discretion</u>:<br>
<b>$_POST[resident_detail]</b>";
	$action = strtoupper($action);
	$q=@mysql_query("UPDATE ps_history SET action_str='$action' where history_id='$_POST[history_id]'");

}
if ($_POST['defendant']){
	$action="<u>Name of person served</u>:<br>
<b>Mortgagor / Grantor<br>
$_POST[name]</b><br>
<u>Date</u>:<br>
<b>$_POST[date] $_POST[time]</b><br>
<u>Particular place of service</u>:<br>
<b>$_POST[address_source]<br>";
if ($_POST[delivery_detail]){
	$action .= "$_POST[delivery_detail]<br>";
}
$action .= "$_POST[address]<br>
$_POST[city], $_POST[state] $_POST[zip]</b>";
	$action = strtoupper($action);
	$q=@mysql_query("UPDATE ps_history SET action_str='$action' where history_id='$_POST[history_id]'");
}
if ($_POST['first']){
	$action="<li>Mailed Papers to $_POST[defendant] at $_POST[address], $_POST[city], $_POST[state] $_POST[zip] \'Residential Property Subject to Mortgage or Deed of Trust\' by first class mail.</li>
$_POST[date]";
	$action = strtoupper($action);
	$q=@mysql_query("UPDATE ps_history SET action_str='$action' where history_id='$_POST[history_id]'");	
}
if ($_POST['crr']){
	$action="<li>Mailed Papers to $_POST[defendant] at $_POST[address], $_POST[city], $_POST[state] $_POST[zip] \'Residential Property Subject to Mortgage or Deed of Trust\' by certified mail, return receipt requested, and by first class mail.</li>
$_POST[date]";
	$action = strtoupper($action);
	$q=@mysql_query("UPDATE ps_history SET action_str='$action' where history_id='$_POST[history_id]'");
}
$packet=$_GET['packet'];
$q="SELECT * from ps_packets where packet_id = '$packet'";
$r=@mysql_query($q) or die(mysql_error());
$d=mysql_fetch_array($r, MYSQL_ASSOC);
?>
<style>
table { padding:0px;}
body { margin:0px; padding:0px; background-color:#999999}
input, select { background-color:#CCFFFF; font-variant:small-caps; font-size:12px }
textarea { background-color:#CCFFFF; font-variant:small-caps; }
td { font-variant:small-caps;}
legend {border:solid 1px #FF0000; background-color:#FFFFFF; padding:0px; font-size:13px}
</style>
<table width="100%" align="left">
	<tr align="center">
    	<td align="center">
	        <FIELDSET style="background-color:#CCCCCC; padding:0px">
			<legend accesskey="C" align="center" style="font-size:12px; font-weight:bold;">History Items for <a href="order.php?packet=<?=$d[packet_id]?>">Packet <?=$d[packet_id]?>:</a> <?=$d[address1]?> <?=$d[address1a]?> (Servers: <?=id2name($d[server_id]);?><? if ($d[server_ida]){echo ", ".id2name($d[server_ida]);}?>)</legend>
<form target="_blank" action="http://service.mdwestserve.com/wizard.php">
<div align="left">
<small>Jump: </small><input name="jump" size="2" /> <small>Server: </small><input name="server" size="2" /><BR>
<small>mailDate: </small><input name="mailDate" size="10" value="<?=date('Y-m-d')?>" /> <input type="submit" value="Wizard" />
</div>
</form><form target="_self" action="http://service.mdwestserve.com/miniwizard.php">
<div align="left">
<small>Jump: </small><input name="jump" size="2" /> <small>Server: </small><input name="server" size="2" /> <input type="submit" value="MiniWizard" />
</div>
</form>
<form target="_blank" action="http://service.mdwestserve.com/obAffidavit.php">
<div align="left">
<small>Packet: </small><input name="packet" size="2" value="<?=$packet?>" /> <small>Def: </small><input name="def" size="2" value="1" /> <input type="submit" value="Single Affidavit" />
</div>
</form>
<form target="_blank" action="http://service.mdwestserve.com/obAffidavit.php">
<div align="left">
<input type="hidden" name="mail" value="1">
<small>Packet: </small><input name="packet" size="2" value="<?=$packet?>" /> <input type="submit" value="Mailing Affidavits" />
</div>
</form>
<form target="_blank" action="http://service.mdwestserve.com/obAffidavit.php">
<div align="left">
<small>Packet: </small><input name="packet" size="2" value="<?=$packet?>" /> <input type="submit" value="Case Affidavits" />
</div>
</form>
<?
$q1="SELECT * from ps_history where packet_id = '$packet' order by history_id ASC";
$r1=@mysql_query($q1) or die("Query: $q1<br>".mysql_error());
while($d1=mysql_fetch_array($r1, MYSQL_ASSOC)){
?>
<FIELDSET style="padding:0px">
<LEGEND ACCESSKEY=C <? if ($d1[action_type] == 'UNLINKED'){?> style="background-color:#FFCCFF" <? } ?>>History Item <?=$d1[history_id]?>, Defendant <?=$d1[defendant_id]?>, by <?=id2name($d1[serverID]);?>: <?=$d1[action_type]?></LEGEND>
<table width="100%" align="left">
	<tr>
		<td valign="top" align="left" width="40%"><small><?=$d1[action_str]?><? if ($d1[residentDesc]){ echo"<br /><b>D:</b> ".$d1[residentDesc];}?></small></td>
    </tr>
</table>
</FIELDSET>
<? } ?>
</FIELDSET>
</td></tr></table>