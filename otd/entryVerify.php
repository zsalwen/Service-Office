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
function addTotal($packet_id){
	mysql_select_db ('core');
	$q="SELECT address1, address1a, address1b, address1c, address1d, address1e FROM ps_packets WHERE packet_id='$packet_id'";
	$r=@mysql_query($q) or die("Query: defendantTotal: $q<br>".mysql_error());
	$i=0;
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	if($d[address1]){ $i++; }
	foreach(range('a','e') as $letter){
		if($d["address1$letter"]){ $i++; }
	}
	return $i;
}
 function timeline($id,$note){
  		@mysql_query("insert into explorer (date,date_time,user,packet,uri) values (NOW(),NOW(),'".$_COOKIE[psdata][name]."','OTD$id','$note')") or die(mysql_error());
	//talk('insidenothing@gmail.com',"$note for presale packet $id");

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
	$r=@mysql_query("SELECT entry_id FROM ps_packets WHERE packet_id='$_POST[packet]'");
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	if ($d[entry_id] == $id){
		echo "<table align='center' id='angry' height='100%'><tr><td align='center'>You cannot verify the data entry for a file you entered yourself, silly!</td></tr></table>";
	}else{
		timeline($_POST[packet],"$name confirmed data entry.");
		$r=@mysql_query("UPDATE ps_packets SET qualityControl='$name' WHERE packet_id='$_POST[packet]'");
		$name2=id2name($d[entry_id])."'s";
		echo "<table align='center' id='kind' height='100%'><tr><td align='center'>You have succesfully confirmed $name2 data entry for packet $_POST[packet]</td></tr></table>";
		if($_GET[matrix]){
			//update process_status to READY TO MAIL
			@mysql_query("UPDATE ps_packets SET process_status='READY TO MAIL' WHERE packet_id='$_POST[packet]'");
			//redirect to mailMatrix for "MAIL ONLY" files
			echo "<script>window.location.href='http://service.mdwestserve.com/mailMatrix.php?packet=$_POST[packet]&product=OTD';</script>";
		}
	}
	die();
}
$q="SELECT * FROM ps_packets WHERE packet_id='$_GET[packet]'";
$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
$d=mysql_fetch_array($r,MYSQL_ASSOC);
$otdStr=str_replace('portal//var/www/dataFiles/service/orders/','PS_PACKETS/',$d[otd]);
$otdStr=str_replace('data/service/orders/','PS_PACKETS/',$otdStr);
$otdStr=str_replace('portal/','',$otdStr);
$client=id2attorney($d[attorneys_id]);
if (!strpos($otdStr,'mdwestserve.com')){
	$otdStr="http://mdwestserve.com/".$otdStr;
}
if (addTotal($d[packet_id]) == 1 && $d[avoidDOT] == 'checked'){
	echo "<script>alert('FINAL LOSS MITIGATION AFFIDAVITS LISTS PROPERTY ADDRESS AS VACANT/NOT OWNER-OCCUPIED, BUT NO ALTERNATE ADDRESSES LISTED.  CONTACT $client')</script>";
}
?>
<table align='center'><tr><td colspan='2'>
<fieldset>
<legend>Case Information</legend>
<li>File #: <?=$d[client_file]?></li>
<li>Case #: <?=$d[case_no]?> - County: <?=$d[circuit_court]?></li>
<li>Client: <?=$client?></li>
<li>Addl Docs: <?=$d[addlDocs]?></li>
<li><b>LOSS MITIGATION TYPE: <span style='color:red;'><?=$d[lossMit]?></span></b></li>
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