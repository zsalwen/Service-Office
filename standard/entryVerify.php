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
function talk($to,$message){
	include_once '/thirdParty/xmpphp/XMPPHP/XMPP.php';
	$conn = new XMPPHP_XMPP('talk.google.com', 5222, 'talkabout.files@gmail.com', '', 'xmpphp', 'gmail.com', $printlog=false, $loglevel=XMPPHP_Log::LEVEL_INFO);
	try {
		$conn->useEncryption(true);
		$conn->connect();
		$conn->processUntil('session_start');
		//$conn->presence("Ya, I'm online","available","talk.google.com");
		$conn->message($to, $message);
		$conn->disconnect();
	} catch(XMPPHP_Exception $e) {
		die($e->getMessage());
	}
}

 function timeline($id,$note){
  		@mysql_query("insert into explorer (date,date_time,user,packet,uri) values (NOW(),NOW(),'".$_COOKIE[psdata][name]."','S$id','$note')") or die(mysql_error());
	//talk('insidenothing@gmail.com',"$note for standard packet $id");

	error_log("[".date('h:iA n/j/y')."] [".$_COOKIE[psdata][name]."] [".trim($id)."] [".trim($note)."] \n", 3, '/logs/timeline.log');
	mysql_select_db ('core');
	//hardLog("$note for packet $id",'user');

	$q1 = "SELECT timeline FROM ps_packets WHERE packet_id = '$id'";		
	$r1 = @mysql_query ($q1) or die(mysql_error());
	$d1 = mysql_fetch_array($r1, MYSQL_ASSOC);
	$access=date('m/d/y g:i A');
	if ($d1[timeline] != ''){
		$notes = $d1[timeline]."<br>$access: ".$note;
	}else{
		$notes = $access.': '.$note;
	}
	$notes = addslashes($notes);
	$q1 = "UPDATE ps_packets set timeline='$notes' WHERE packet_id = '$id'";		
	$r1 = @mysql_query ($q1) or die(mysql_error());
	//@mysql_query("insert into syslog (logTime, event) values (NOW(), 'Packet $id: $note')");
}

mysql_connect();
mysql_select_db('service');
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
	$r=@mysql_query("SELECT entry_id FROM standard_packets WHERE packet_id='$_POST[packet]'");
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	if ($d[entry_id] == $id){
		echo "<table align='center' id='angry' height='100%'><tr><td align='center'>You cannot verify the data entry for a file you entered yourself, silly!</td></tr></table>";
	}else{
		$next = $_POST[packet] + 1;
		timeline($_POST[packet],"$name confirmed data entry.");
		$r=@mysql_query("UPDATE standard_packets SET qualityControl='$name' WHERE packet_id='$_POST[packet]'");
		$name2=id2name($d[entry_id])."'s";
		echo "<table align='center' id='kind' height='100%'><tr><td align='center'>You have succesfully confirmed $name2 data entry for packet $_POST[packet]<br><br><br><a href='?packet=$next&frame=no'>NEXT</a></td></tr></table>";
		//echo "<script>parent.location.reload()</script>";
	}
	die();
}
$q="SELECT packet_id, name1, name2, name3, name4, name5, name6, address1, address1a, address1b, address1c, address1d, address1e, city1, city1a, city1b, city1c, city1d, city1e, state1, state1a, state1b, state1c, state1d, state1e, zip1, zip1a, zip1b, zip1c, zip1d, zip1e, address2, address2a, address2b, address2c, address2d, address2e, city2, city2a, city2b, city2c, city2d, city2e, state2, state2a, state2b, state2c, state2d, state2e, zip2, zip2a, zip2b, zip2c, zip2d, zip2e, address3, address3a, address3b, address3c, address3d, address3e, city3, city3a, city3b, city3c, city3d, city3e, state3, state3a, state3b, state3c, state3d, state3e, zip3, zip3a, zip3b, zip3c, zip3d, zip3e, address4, address4a, address4b, address4c, address4d, address4e, city4, city4a, city4b, city4c, city4d, city4e, state4, state4a, state4b, state4c, state4d, state4e, zip4, zip4a, zip4b, zip4c, zip4d, zip4e, address5, address5a, address5b, address5c, address5d, address5e, city5, city5a, city5b, city5c, city5d, city5e, state5, state5a, state5b, state5c, state5d, state5e, zip5, zip5a, zip5b, zip5c, zip5d, zip5e, address6, address6a, address6b, address6c, address6d, address6e, city6, city6a, city6b, city6c, city6d, city6e, state6, state6a, state6b, state6c, state6d, state6e, zip6, zip6a, zip6b, zip6c, zip6d, zip6e, pobox, pobox2, pocity, pocity2, postate, postate2, pozip, pozip2, case_no, circuit_court, client_file, date_received, attorneys_id, otd, onAffidavit1, onAffidavit2, onAffidavit3, onAffidavit4, onAffidavit5, onAffidavit6 FROM ps_packets WHERE packet_id='$_GET[packet]'";
$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
$d=mysql_fetch_array($r,MYSQL_ASSOC);
$otdStr=str_replace('portal//var/www/dataFiles/service/orders/','PS_PACKETS/',$d[otd]);
$otdStr=str_replace('data/service/orders/','PS_PACKETS/',$otdStr);
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
<tr><td valign='top'>
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
<legend>Addresses</legend>
<table>
<?
if ($d[address1]){
	echo "<tr><td valign='top'><fieldset><legend>Deed of Trust</legend>$d[address1]<br>$d[city1], $d[state1] $d[zip1]</fieldset></td></tr>";
}
if ($d[address1a]){
	echo "<tr><td valign='top'><fieldset><legend>Possible Place of Abode 1</legend>$d[address1a]<br>$d[city1a], $d[state1a] $d[zip1a]</fieldset></td></tr>";
}
if ($d[address1b]){
	echo "<tr><td valign='top'><fieldset><legend>Possible Place of Abode 2</legend>$d[address1b]<br>$d[city1b], $d[state1b] $d[zip1b]</fieldset></td></tr>";
}
if ($d[address1c]){
	echo "<tr><td valign='top'><fieldset><legend>Possible Place of Abode 3</legend>$d[address1c]<br>$d[city1c], $d[state1c] $d[zip1c]</fieldset></td></tr>";
}
if ($d[address1d]){
	echo "<tr><td valign='top'><fieldset><legend>Possible Place of Abode 4</legend>$d[address1d]<br>$d[city1d], $d[state1d] $d[zip1d]</fieldset></td></tr>";
}
if ($d[address1e]){
	echo "<tr><td valign='top'><fieldset><legend>Possible Place of Abode 5</legend>$d[address1e]<br>$d[city1e], $d[state1e] $d[zip1e]</fieldset></td></tr>";
}
if ($d[pobox]){
	echo "<tr><td valign='top'><fieldset><legend>Mail Only 1</legend>$d[pobox]<br>$d[pocity], $d[postate] $d[pozip]</fieldset></td></tr>";
}
if ($d[pobox2]){
	echo "<tr><td valign='top'><fieldset><legend>Mail Only 2</legend>$d[pobox2]<br>$d[pocity2], $d[postate2] $d[pozip2]</fieldset></td></tr>";
}
?>
</table>
</fieldset>
</td></tr>
<tr><td colspan='2' align='center'>
<? if ($_GET[frame] != 'no'){ ?>
<a href="<?=$otdStr?>" target="preview">OTD</a><br>
<? } ?>
<form method='post'>
<input type='hidden' name='packet' value='<?=$_GET[packet]?>'>
<input type='submit' name='submit' value='Confirm Data'>
</form>
</td></tr></table>