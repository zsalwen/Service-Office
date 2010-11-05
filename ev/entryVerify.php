<?
function id2attorney($id){
	$q="SELECT display_name FROM attorneys WHERE attorneys_id = '$id'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
return $d[display_name];
}
function id2name($id){
	$q="SELECT name FROM ps_users WHERE id = '$id'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
return $d[name];
}
mysql_connect();
mysql_select_db('core');
?>
<style>
fieldset, table {padding: 0px;}
legend {background-color:#FFFFCC;}
table {background-color: #FFFFFF;}
#kind {background-color: #66CCFF; font-size:48px; font-weight:bold;}
#angry {background-color: #FF0000; color: #FFFFFF; font-size:48px; font-weight:bold;}
</style>
<?
if ($_POST[submit]){
	$name=$_COOKIE[psdata][name];
	$id=$_COOKIE[psdata][user_id];
	$r=@mysql_query("SELECT entry_id FROM evictionPackets WHERE eviction_id='$_POST[packet]'");
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	if ($d[entry_id] == $id){
		echo "<table align='center' id='angry' height='100%'><tr><td align='center'>You cannot verify the data entry for a file you entered yourself, silly!</td></tr></table>";
	}else{
		$r=@mysql_query("UPDATE evictionPackets SET qualityControl='$name' WHERE eviction_id='$_POST[packet]'");
		$name2=id2name($d[entry_id])."'s";
		echo "<table align='center' id='kind' height='100%'><tr><td align='center'>You have succesfully confirmed $name2 data entry for packet $_POST[packet]</td></tr></table>";
		//echo "<script>parent.location.reload()</script>";
	}
	die();
}
$q="SELECT eviction_id, name1, name2, name3, name4, name5, name6, address1, city1, state1, zip1, address2, city2, state2, zip2, address3, city3, state3, zip3, address4, city4, state4, zip4, address5, city5, state5, zip5, address6, city6, state6, zip6, case_no, circuit_court, client_file, date_received, attorneys_id, otd, onAffidavit1, onAffidavit2, onAffidavit3, onAffidavit4, onAffidavit5, onAffidavit6, altPlaintiff FROM evictionPackets WHERE eviction_id='$_GET[packet]'";
$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
$d=mysql_fetch_array($r,MYSQL_ASSOC);
$otdStr=str_replace('portal//var/www/dataFiles/service/orders/','evictionPackets/',$d[otd]);
$otdStr=str_replace('data/service/orders/','evictionPackets/',$otdStr);
$otdStr=str_replace('portal/','',$otdStr);
if (!strpos($otdStr,'mdwestserve.com')){
	$otdStr="http://mdwestserve.com/".$otdStr;
}
?>
<table align='center'><tr><td colspan='2'>
<fieldset>
<legend>Case Information</legend>
<li>File #: <?=$d[client_file]?></li>
<li>Case #: <?=$d[case_no]?> - County: <?=$d[circuit_court]?></li>
<li>Client: <?=id2attorney($d[attorneys_id])?></li>
</fieldset>
<? if ($_GET[frame] != 'no'){ ?>
</td><td rowspan='3'>
<iframe name="preview" height="600px" width="700px"></iframe>
</td>
<? } ?>
</tr>
<tr><td valign='top' colspan='2'>
<fieldset>
<legend>Court Data</legend>
<table>
	<tr>
		<td>Alt Plaintiff:</td>
		<? if ($d[altPlaintiff]){?>
		<td><?=$d[altPlaintiff]?></td>
		<? }else{ ?>
		<td style='background-color:red; font-style:italic;'>MISSING ALT PLAINTIFF</td>
		<? } ?>
	</tr>
</table>
</fieldset>
</td></tr><tr><td valign='top'>
<fieldset>
<legend>Persons to Serve</legend>
<table>
<?
if ($d[name1]){
	$checkbox1="<input type='checkbox' $d[onAffidavit1] value='checked' name='onAffidavit1'>";
	echo "<tr><td valign='top'>$d[name1] $checkbox1</td></tr>";
}
if ($d[name2]){
	$checkbox2="<input type='checkbox' $d[onAffidavit2] value='checked' name='onAffidavit2'>";
	echo "<tr><td valign='top'>$d[name2] $checkbox2</td></tr>";
}
if ($d[name3]){
	$checkbox3="<input type='checkbox' $d[onAffidavit3] value='checked' name='onAffidavit3'>";
	echo "<tr><td valign='top'>$d[name3] $checkbox3</td></tr>";
}
if ($d[name4]){
	$checkbox4="<input type='checkbox' $d[onAffidavit4] value='checked' name='onAffidavit4'>";
	echo "<tr><td valign='top'>$d[name4] $checkbox4</td></tr>";
}
if ($d[name5]){
	$checkbox5="<input type='checkbox' $d[onAffidavit5] value='checked' name='onAffidavit5'>";
	echo "<tr><td valign='top'>$d[name5] $checkbox5</td></tr>";
}
if ($d[name6]){
	$checkbox6="<input type='checkbox' $d[onAffidavit6] value='checked' name='onAffidavit6'>";
	echo "<tr><td valign='top'>$d[name6] $checkbox6</td></tr>";
}
?>
</table>
</fieldset>
</td><td valign='top'>
<fieldset>
<legend>Address</legend>
<table>
<?
if ($d[address1]){
	echo "<tr><td valign='top'><fieldset><legend>Deed of Trust</legend>$d[address1]<br>$d[city1], $d[state1] $d[zip1]</fieldset></td></tr>";
}
?>
</table>
</fieldset>
</td></tr>
<tr><td colspan='2' align='center'>
<? if ($_GET[frame] != 'no'){ ?>
<a href="<?=$otdStr?>" target="preview">EV</a><br>
<? } ?>
<form method='post'>
<input type='hidden' name='packet' value='<?=$_GET[packet]?>'>
<input type='submit' name='submit' value='Confirm Data'>
</form>
</td></tr></table>