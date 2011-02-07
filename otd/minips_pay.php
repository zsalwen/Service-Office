<?
include 'common.php';
hardLog('access payment information for '.$_GET[id],'user');

mysql_connect();
mysql_select_db('core');
function dupCheck($field,$string){
$r=@mysql_query("select * from ps_packets where $field = '$string'");
$c=mysql_num_rows($r);
if ($c == 1){
$return[0]="class='single'";
$return[1]=$c;
}else{
$return[0]="class='duplicate'";
$return[1]=$c;
}
return $return;
}
function monthConvert($month){
	if ($month == '01'){ return 'January'; }
	if ($month == '02'){ return 'February'; }
	if ($month == '03'){ return 'March'; }
	if ($month == '04'){ return 'April'; }
	if ($month == '05'){ return 'May'; }
	if ($month == '06'){ return 'June'; }
	if ($month == '07'){ return 'July'; }
	if ($month == '08'){ return 'August'; }
	if ($month == '09'){ return 'September'; }
	if ($month == '10'){ return 'October'; }
	if ($month == '11'){ return 'November'; }
	if ($month == '12'){ return 'December'; }
}
function dateExplode($date){
	$date=explode('-',$date);
	$date=monthConvert($date[1])." ".$date[2].", ".$date[0];
	return $date;
}
function getPayInstructions($attID,$prefix){
	$q = "SELECT payInstructions FROM attorneys WHERE attorneys_id = '$attID'";
	$r = @mysql_query($q) or die(mysql_error());
	$d = mysql_fetch_array($r, MYSQL_ASSOC);
	return $prefix.trim($d[payInstructions]);
}
?>
<script language="JavaScript">
<!--
function automation() {
  window.opener.location.href = window.opener.location.href;
  if (window.opener.progressWindow)
		
 {
    window.opener.progressWindow.close()
  }
  window.close();
}
function setSize(width,height) {
	if (window.outerWidth) {
		window.outerWidth = width;
		window.outerHeight = height;
	}
	else if (window.resizeTo) {
		window.resizeTo(width,height);
	}
	else {
		alert("Not supported.");
	}
}

//-->
</script>
<?
if ($_POST[submit]){
hardLog('updated payment information for '.$_GET[id],'user');

	$rxx=@mysql_query("select * from psActivity where today='".date('Y-m-d')."'") or die(mysql_error());
	$dxx=mysql_fetch_array($rxx,MYSQL_ASSOC);
	$count=$dxx[clientPayment]+1;
	@mysql_query("update psActivity set clientPayment = '$count' where today='".date('Y-m-d')."'") or die(mysql_error());
	echo "Saved! - $count for the day...";

	$q1 = "UPDATE ps_packets SET 

									bill410='$_POST[bill410]',
									bill420='$_POST[bill420]',
									bill430='$_POST[bill430]',
									bill440='$_POST[bill440]',
									bill450='$_POST[bill450]',
									code410='$_POST[code410]',
									code410a='$_POST[code410a]',
									code410b='$_POST[code410b]',
									code420='$_POST[code420]',
									code420a='$_POST[code420a]',
									code420b='$_POST[code420b]',
									code430='$_POST[code430]',
									code430a='$_POST[code430a]',
									code430b='$_POST[code430b]',
									code440='$_POST[code440]',
									code440a='$_POST[code440a]',
									code440b='$_POST[code440b]',
									code450='$_POST[code450]',
									code450a='$_POST[code450a]',
									code450b='$_POST[code450b]',
									contractor_rate='$_POST[contractor_rate]', 
									contractor_paid='$_POST[contractor_paid]',
									contractor_check='$_POST[contractor_check]', 
									contractor_ratea='$_POST[contractor_ratea]', 
									contractor_paida='$_POST[contractor_paida]',
									contractor_checka='$_POST[contractor_checka]', 
									contractor_rateb='$_POST[contractor_rateb]', 
									contractor_paidb='$_POST[contractor_paidb]',
									contractor_checkb='$_POST[contractor_checkb]', 
									contractor_ratec='$_POST[contractor_ratec]', 
									contractor_paidc='$_POST[contractor_paidc]',
									contractor_checkc='$_POST[contractor_checkc]', 
									contractor_rated='$_POST[contractor_rated]', 
									contractor_paidd='$_POST[contractor_paidd]',
									contractor_checkd='$_POST[contractor_checkd]', 
									contractor_ratee='$_POST[contractor_ratee]', 
									contractor_paide='$_POST[contractor_paide]',
									contractor_checke='$_POST[contractor_checke]', 
									client_rate='$_POST[client_rate]', 
									client_ratea='$_POST[client_ratea]', 
									client_rateb='$_POST[client_rateb]', 
									client_paid='$_POST[client_paid]',
									client_paida='$_POST[client_paida]',
									client_paidb='$_POST[client_paidb]',
									client_check='$_POST[client_check]',
									client_checka='$_POST[client_checka]',
									client_checkb='$_POST[client_checkb]',
									client_ratec='$_POST[client_ratec]', 
									client_rated='$_POST[client_rated]', 
									client_ratee='$_POST[client_ratee]', 
									client_paidc='$_POST[client_paidc]',
									client_paidd='$_POST[client_paidd]',
									client_paide='$_POST[client_paide]',
									client_checkc='$_POST[client_checkc]',
									client_checkd='$_POST[client_checkd]',
									client_checke='$_POST[client_checke]',
									accountingNotes='".addslashes($_POST[accountingNotes])."'
										WHERE packet_id='$_POST[id]'";		
	$r1 = @mysql_query ($q1) or die(mysql_error());
	
//addNote($_POST[id],$_COOKIE[userdata][name].': Entered Payment on '.date('m/d/Y'));
	

	
	
	//echo $q1;
	if ($_POST[qc]){
		echo "<script>window.location='http://service.mdwestserve.com/wizard.php?jump=$_POST[id]-1&mailDate=$_POST[qc]'</script>";
	}else{
		echo "<script>automation();</script>";
	}
}

$q1 = "SELECT * FROM ps_packets WHERE packet_id = $_GET[id]";		
$r1 = @mysql_query ($q1) or die(mysql_error());
$data = mysql_fetch_array($r1, MYSQL_ASSOC);
if ($data[lossMit] == '' || $data[lossMit] == 'N/A - OLD L'){
	$lossMit='R';
}else{
	$lossMit=$data[lossMit];
}
?>
<script>
document.title = "Accounting #<?=$data[packet_id];?>";

</script>
<body bgcolor="#99CCFF">
<style>
fieldset { background-color:#FFFFFF;  border:solid 1px #000000;}
.altset { background-color:#FFFFFF;  border:solid 1px #000000;}
.altset2 { background-color:#FFFFFF;  border:solid 1px #000000;}
legend, input, select { padding:0px; background-color:#FFFFCC; border:solid 1px #000000;}
td { font-variant:small-caps }
table {padding: 0px;}
.P {background-color:green;}
.F {background-color:green;}
.R {background-color:red;}
</style>
<form id="acc" name="acc" method="post">
<input type="hidden" name="id" value="<?=$_GET[id]?>" />
<? if ($_GET[qc]){ ?>
<input type="hidden" name="qc" value="<?=$_GET[qc]?>" />
<center style="font-weight:bold; font-size:14px;">ALL FILES MUST HAVE COST ENTERED BEFORE QUALITY CONTROL IS PROCESSED</center>
<? } ?>
<!--------
<a href="http://staff.mdwestserve.com/otd/ps_write_invoice.php?id=<?=$data[packet_id];?>" target="_Blank">PS Write Invoice</a>
----------->
<? if ($_GET[qc]){ ?>
<center><div style="border:1px solid;font-size:18px;"><?=id2attorney($data[attorneys_id])?><?=stripslashes(getPayInstructions($data[attorneys_id],'<br>'));?><? if ($data[rush] != ''){ echo "<br><b style='background-color:red;'>RUSH SERVICE--: $120 IN-STATE, $170 OUT-OF-STATE</b>"; } ?></div></center>
<? }elseif(stripslashes(getPayInstructions($data[attorneys_id],'')) != ''){ ?>
<center><div style="border:1px solid;font-size:22px; width:1000px;"><?=id2attorney($data[attorneys_id])?><?=stripslashes(getPayInstructions($data[attorneys_id],'<br>'));?><? if ($data[rush] != ''){ echo "<br><b style='background-color:red;'>RUSH SERVICE--: $120 IN-STATE, $170 OUT-OF-STATE</b>"; } ?></div></center>
<? } ?>
<table><tr><td>
<fieldset>
	<legend>Process Service Account Details</legend>
<table width="100%">
	<tr>
    	<td></td>
        <td style="font-size:12px;"><?=id2name($data[server_id])?></td>
    	<td style="font-size:12px;"><?=id2name($data[server_ida])?></td>
    	<td style="font-size:12px;"><?=id2name($data[server_idb])?></td>
    	<td style="font-size:12px;"><?=id2name($data[server_idc])?></td>
    	<td style="font-size:12px;"><?=id2name($data[server_idd])?></td>
    	<td style="font-size:12px;"><?=id2name($data[server_ide])?></td>
    </tr>
    <tr>
    	<td>Check</td>
    	<td><input name="contractor_check" size="2" maxlength="30" value="<?=$data[contractor_check]?>" /></td>
    	<td><input name="contractor_checka" size="2" maxlength="30" value="<?=$data[contractor_checka]?>" /></td>
    	<td><input name="contractor_checkb" size="2" maxlength="30" value="<?=$data[contractor_checkb]?>" /></td>
    	<td><input name="contractor_checkc" size="2" maxlength="30" value="<?=$data[contractor_checkc]?>" /></td>
    	<td><input name="contractor_checkd" size="2" maxlength="30" value="<?=$data[contractor_checkd]?>" /></td>
    	<td><input name="contractor_checke" size="2" maxlength="30" value="<?=$data[contractor_checke]?>" /></td>
	</tr>
    <tr>
    	<td>Paid</td>
    	<td><input name="contractor_paid" size="2" maxlength="7" value="<?=$data[contractor_paid]?>" /></td>
    	<td><input name="contractor_paida" size="2" maxlength="7" value="<?=$data[contractor_paida]?>" /></td>
    	<td><input name="contractor_paidb" size="2" maxlength="7" value="<?=$data[contractor_paidb]?>" /></td>
    	<td><input name="contractor_paidc" size="2" maxlength="7" value="<?=$data[contractor_paidc]?>" /></td>
    	<td><input name="contractor_paidd" size="2" maxlength="7" value="<?=$data[contractor_paidd]?>" /></td>
    	<td><input name="contractor_paide" size="2" maxlength="7" value="<?=$data[contractor_paide]?>" /></td>
	</tr>
	<tr>
    	<td>Quote</td>
    	<td><input name="contractor_rate" size="2" maxlength="7" value="<?=$data[contractor_rate]?>" /></td>
    	<td><input name="contractor_ratea" size="2" maxlength="7" value="<?=$data[contractor_ratea]?>" /></td>
    	<td><input name="contractor_rateb" size="2" maxlength="7" value="<?=$data[contractor_rateb]?>" /></td>
    	<td><input name="contractor_ratec" size="2" maxlength="7" value="<?=$data[contractor_ratec]?>" /></td>
    	<td><input name="contractor_rated" size="2" maxlength="7" value="<?=$data[contractor_rated]?>" /></td>
    	<td><input name="contractor_ratee" size="2" maxlength="7" value="<?=$data[contractor_ratee]?>" /></td>
    </tr>
    <tr>
    	<td>Client</td>
    	<td><input name="client_rate" size="2" maxlength="7" value="<?=$data[client_rate]?>" /></td>
    	<td><input name="client_ratea" size="2" maxlength="7" value="<?=$data[client_ratea]?>" /></td>
    	<td><input name="client_rateb" size="2" maxlength="7" value="<?=$data[client_rateb]?>" /></td>
    	<td><input name="client_ratec" size="2" maxlength="7" value="<?=$data[client_ratec]?>" /></td>
    	<td><input name="client_rated" size="2" maxlength="7" value="<?=$data[client_rated]?>" /></td>
    	<td><input name="client_ratee" size="2" maxlength="7" value="<?=$data[client_ratee]?>" /></td>
	</tr>
</table>
<?
$i=0;
$q2="SELECT * FROM ps_penalties WHERE packetID='$_GET[id]' AND product='OTD'";
$r2=@mysql_query($q2) or die ("Query: $q2<br>".mysql_error());
while ($d2=mysql_fetch_array($r2,MYSQL_ASSOC)){$i++;
	$def=$d2[defendantID];
	$list .= "<tr><td>".id2name($d2[serverID])."</td><td>".$data["name$def"]."</td><td>[".strtoupper(stripslashes($d2[description]))."] - ".id2name($d2[entryID])." ".justDate($d2[entryDate])."</td></tr>";
}
if ($list != ''){
	echo "<table width='100%' border='1' style='border-collapse:collapse;'><tr><td colspan='3'><b style='color:red;'>PENALTIES</b></td></tr><tr><td>Server</td><td>Defendant</td><td>Description</td></tr>$list<tr><td colspan='3' align='right' style='font-weight:bold;'>TOTAL PENALTIES: $i</td></tr></table>";
}
?>

</fieldset>    

</td></tr><tr><td valign="top">
<fieldset>
	<legend>Client Accounting Details</legend>
<table cellspacing="0">
	<tr>
    	<td></td>
		<td>Bill</td>
        <td>First</td>
    	<td>Second</td>
    	<td>Third</td>
    </tr>
    <tr>
    	<td>Client Check</td>
		<td></td>
    	<td><input tabindex="4" name="client_check" size="4" maxlength="30" value="<?=$data[client_check]?>" /></td>
    	<td><input name="client_checka" size="4" maxlength="30" value="<?=$data[client_checka]?>" /></td>
    	<td><input name="client_checkb" size="4" maxlength="30" value="<?=$data[client_checkb]?>" /></td>
	</tr>
    <tr>
		<td>Process Service: <?=$data[service_status]?></td>
		<td><input tabindex="1" name="bill410" size="2" maxlength="7" value="<?=$data[bill410]?>" /></td>
		<td><input tabindex="5" name="code410" size="2" maxlength="7" value="<?=$data[code410]?>" /></td>
    	<td><input name="code410a" size="2" maxlength="7" value="<?=$data[code410a]?>" /></td>
    	<td><input name="code410b" size="2" maxlength="7" value="<?=$data[code410b]?>" /></td>
	</tr>        
    <tr>
    	<td>Mailing Services: <?=$data[mailing_status]?></td>
		<td><input tabindex="2" name="bill420" size="2" maxlength="7" value="<?=$data[bill420]?>" /></td>
    	<td><input tabindex="6" name="code420" size="2" maxlength="7" value="<?=$data[code420]?>" /></td>
    	<td><input name="code420a" size="2" maxlength="7" value="<?=$data[code420a]?>" /></td>
    	<td><input name="code420b" size="2" maxlength="7" value="<?=$data[code420b]?>" /></td>
	</tr>        
	<tr>
    	<td>HB472 Compliance: <span class="<?=substr($lossMit,0,1)?>"><?=$data[lossMit]?></span></td>
		<td><? if ($data[attorneys_id] == 70 || $data[attorneys_id] == 80){ echo $data[bill450];}else{ echo "<input tabindex='2' name='bill450' size='2' maxlength='7' value='$data[bill450]' />"; }?></td>
    	<td><input tabindex="6" name="code450" size="2" maxlength="7" value="<?=$data[code450]?>" /></td>
    	<td><input name="code450a" size="2" maxlength="7" value="<?=$data[code450a]?>" /></td>
    	<td><input name="code450b" size="2" maxlength="7" value="<?=$data[code450b]?>" /></td>
	</tr>
    <tr>
    	<td>Filing Services: <?=$data[filing_status]?></td>
		<td><input tabindex="3" name="bill430" size="2" maxlength="7" value="<?=$data[bill430]?>" /></td>
    	<td><input tabindex="7" name="code430" size="2" maxlength="30" value="<?=$data[code430]?>" /></td>
    	<td><input name="code430a" size="2" maxlength="30" value="<?=$data[code430a]?>" /></td>
    	<td><input name="code430b" size="2" maxlength="30" value="<?=$data[code430b]?>" /></td>
	</tr>        
    <tr>
    	<td>Code: Skip Trace Services</td>
		<td><input name="bill440" size="2" maxlength="7" value="<?=$data[bill440]?>" /></td>
    	<td><input name="code440" size="2" maxlength="30" value="<?=$data[code440]?>" /></td>
    	<td><input name="code440a" size="2" maxlength="30" value="<?=$data[code440a]?>" /></td>
    	<td><input name="code440b" size="2" maxlength="30" value="<?=$data[code440b]?>" /></td>
	</tr>        
    <tr>
    	<td style="border-top:solid 1px;">Total Payment</td>
		<td>$<?=$data[bill410]+$data[bill420]+$data[bill430]+$data[bill440]+$data[bill450];?></td>
    	<td style="border-top:solid 1px;"><input tabindex="8" name="client_paid" size="2" maxlength="7" value="<?=$data[client_paid]?>" /></td>
    	<td style="border-top:solid 1px;"><input name="client_paida" size="2" maxlength="7" value="<?=$data[client_paida]?>" /></td>
    	<td style="border-top:solid 1px;"><input name="client_paidb" size="2" maxlength="7" value="<?=$data[client_paidb]?>" /></td>
	</tr>
</table>

</fieldset>    
</td></tr><tr><td>
<fieldset>
<legend>Occupant Notices</legend>
<table>
<?
$q2="SELECT * FROM occNotices WHERE clientFile='".$data[client_file]."'";
$r2=@mysql_query($q2) or die("Query: $q1<br>".mysql_error());
while ($d2=mysql_fetch_array($r2,MYSQL_ASSOC)){
	$notices .= "<tr><td>$d2[requirements] Sent ".dateExplode($d2[sendDate])." For Packet ".$d2[packet_id]." - $".$d2[bill]."</td></tr>";
}
if ($notices != ''){
	echo $notices;
}else{
	echo "<tr><td>NONE</td></tr>";
}
?>
</table>
</fieldset>
</td></tr>

<table><tr>

<td>
<fieldset>
<legend>Accounting Notes</legend>
<textarea name="accountingNotes" cols="40" rows="3"><?=stripslashes($data[accountingNotes])?></textarea>
<input name="submit" type="submit" style="background-color:#00FF00; cursor:pointer; font-size:24px; position:absolute; top:0; right:0px;"  value="SAVE"/>
</fieldset>
</td>
<td>
<fieldset>
<legend>Client Alert</legend>
<textarea name="extended_notes" cols="40" rows="3"><?=stripslashes($data[extended_notes])?></textarea>
</fieldset>
</td>
<td>
<fieldset>
<legend>Op. Notes</legend>
<textarea name="processor_notes" cols="40" rows="3"><?=stripslashes($data[processor_notes])?></textarea>
</fieldset>
</td>
</tr></table>

</form>

</td></tr>
</table>


<table><tr><td rowspan="2">
<FIELDSET>
<LEGEND ACCESSKEY=C>Persons to Serve</LEGEND>
<table>
<tr>
<td nowrap>1<input size="30" name="name1" value="<?=$data[name1]?>" /> <input <? if ($data[onAffidavit1]=='checked'){echo "checked";} ?> type="checkbox" value="checked" name="onAffidavit1"></td><? $mult=1;?>
</tr><tr>
<td nowrap>2<input size="30" name="name2" value="<?=$data[name2]?>" /> <input <? if ($data[onAffidavit2]=='checked'){echo "checked";} ?> type="checkbox" value="checked" name="onAffidavit2"></td><? if ($data[name2]){$mult++;}?>
</tr><tr>
<td nowrap>3<input size="30" name="name3" value="<?=$data[name3]?>" /> <input <? if ($data[onAffidavit3]=='checked'){echo "checked";} ?> type="checkbox" value="checked" name="onAffidavit3"></td><? if ($data[name3]){$mult++;}?>
</tr><tr>
<td nowrap>4<input size="30" name="name4" value="<?=$data[name4]?>" /> <input <? if ($data[onAffidavit4]=='checked'){echo "checked";} ?> type="checkbox" value="checked" name="onAffidavit4"></td><? if ($data[name4]){$mult++;}?>
</tr><tr>
<td nowrap>5<input size="30" name="name5" value="<?=$data[name5]?>" /> <input <? if ($data[onAffidavit5]=='checked'){echo "checked";} ?> type="checkbox" value="checked" name="onAffidavit5"></td><? if ($data[name5]){$mult++;}?>
</tr><tr>
<td nowrap>6<input size="30" name="name6" value="<?=$data[name6]?>" /> <input <? if ($data[onAffidavit6]=='checked'){echo "checked";} ?> type="checkbox" value="checked" name="onAffidavit6"></td><? if ($data[name6]){$mult++;}?>
</tr>
</table>
</fieldset>

</td><td>

<FIELDSET>
<LEGEND class="a" ACCESSKEY=C><a href="http://staff.mdwestserve.com/otd/dispatcher.php?aptsut=&address=<?=$data[address1]?>&city=<?=$data[city1]?>&state=<?=$data[state1]?>&miles=5" target="_Blank">Mortgage / Deed of Trust</a><input type="checkbox" checked><br><?=id2name($data[server_id]);?></LEGEND>
<table>
<tr>
<td><input id="address" name="address" size="30" value="<?=$data[address1]?>" /></td>
</tr>
<tr>
<td><input size="20" name="city" value="<?=$data[city1]?>" /><input size="2" name="state" value="<?=$data[state1]?>" /><input size="4" name="zip" value="<?=$data[zip1]?>" /></td>
</tr>
</table>    
</FIELDSET>

</td><td>

<FIELDSET>
<LEGEND class="a" ACCESSKEY=C><a href="http://staff.mdwestserve.com/otd/dispatcher.php?aptsut=&address=<?=str_replace('#','',$data[address1a])?>&city=<?=$data[city1a]?>&state=<?=$data[state1a]?>&miles=5" target="_Blank">Possible Place of Abode</a> <input type="checkbox"><br><?=id2name($data[server_ida]);?></LEGEND>
<table>
<tr>
<td><input name="addressa" size="30" value="<?=$data[address1a]?>" /></td>
</tr>
<tr>
<td><input name="citya" size="20" value="<?=$data[city1a]?>" /><input size="2" name="statea" value="<?=$data[state1a]?>" /><input size="4" name="zipa" value="<?=$data[zip1a]?>" /></td>
</tr>
</table>    
</FIELDSET>

</td><td>

<FIELDSET>
<LEGEND class="a" ACCESSKEY=C><a href="http://staff.mdwestserve.com/otd/dispatcher.php?aptsut=&address=<?=$data[address1b]?>&city=<?=$data[city1b]?>&state=<?=$data[state1b]?>&miles=5" target="_Blank">Possible Place of Abode</a> <input type="checkbox"><br><?=id2name($data[server_idb]);?></LEGEND>
<table>
<tr>
<td><input name="addressb" size="30" value="<?=$data[address1b]?>" /></td>
</tr>
<tr>
<td><input name="cityb" size="20" value="<?=$data[city1b]?>" /><input size="2" name="stateb" value="<?=$data[state1b]?>" /><input size="4" name="zipb" value="<?=$data[zip1b]?>" /></td>
</tr>
</table>    
</FIELDSET>
</td><td>
<FIELDSET>
<LEGEND class="a" ACCESSKEY=C><a href="http://staff.mdwestserve.com/otd/dispatcher.php?aptsut=&address=<?=$data[pobox]?>&city=<?=$data[pocity]?>&state=<?=$data[postate]?>&miles=5" target="_Blank">Mail Only 1</a> <input type="checkbox"></LEGEND>
<table>
<tr>
<td><input name="pobox" size="30" value="<?=$data[pobox]?>" /></td>
</tr>
<tr>
<td><input name="pocity" size="20" value="<?=$data[pocity]?>" /><input size="2" name="postate" value="<?=$data[postate]?>" /><input size="4" name="pozip" value="<?=$data[pozip]?>" /></td>
</tr>
</table>    
</FIELDSET>
</td></tr><tr><td>

<FIELDSET>
<LEGEND class="a" ACCESSKEY=C><a href="http://staff.mdwestserve.com/otd/dispatcher.php?aptsut=&address=<?=$data[address1c]?>&city=<?=$data[city1c]?>&state=<?=$data[state1c]?>&miles=5" target="_Blank">Possible Place of Abode</a> <input type="checkbox"><br><?=id2name($data[server_idc]);?></LEGEND>
<table>
<tr>
<td><input name="addressc" value="<?=$data[address1c]?>" size="30" /></td>
</tr>
<tr>
<td><input name="cityc" size="20" value="<?=$data[city1c]?>" /><input size="2" name="statec" value="<?=$data[state1c]?>" /><input size="4" name="zipc" value="<?=$data[zip1c]?>" /></td>
</tr>
</table>    
</FIELDSET>

</td><td>

<FIELDSET>
<LEGEND class="a" ACCESSKEY=C><a href="http://staff.mdwestserve.com/otd/dispatcher.php?aptsut=&address=<?=$data[address1d]?>&city=<?=$data[city1d]?>&state=<?=$data[state1d]?>&miles=5" target="_Blank">Possible Place of Abode</a> <input type="checkbox"><br><?=id2name($data[server_idd]);?></LEGEND>
<table>
<tr>
<td><input name="addressd" size="30" value="<?=$data[address1d]?>" /></td>
</tr>
<tr>
<td><input name="cityd" size="20" value="<?=$data[city1d]?>" /><input size="2" name="stated" value="<?=$data[state1d]?>" /><input size="4" name="zipd" value="<?=$data[zip1d]?>" /></td>
</tr>
</table>    
</FIELDSET>

</td><td>

<FIELDSET>
<LEGEND class="a" ACCESSKEY=C><a href="http://staff.mdwestserve.com/otd/dispatcher.php?aptsut=&address=<?=$data[address1e]?>&city=<?=$data[city1e]?>&state=<?=$data[state1e]?>&miles=5" target="_Blank">Possible Place of Abode</a> <input type="checkbox"><br><?=id2name($data[server_ide]);?></LEGEND>
<table>
<tr>
<td><input name="addresse" size="30" value="<?=$data[address1e]?>" /></td>
</tr>
<tr>
<td><input name="citye" size="20" value="<?=$data[city1e]?>" /><input size="2" name="statee" value="<?=$data[state1e]?>" /><input size="4" name="zipe" value="<?=$data[zip1e]?>" /></td>
</tr>
</table>    
</FIELDSET>
</td><td>
<FIELDSET>
<LEGEND class="a" ACCESSKEY=C><a href="http://staff.mdwestserve.com/otd/dispatcher.php?aptsut=&address=<?=$data[pobox2]?>&city=<?=$data[pocity2]?>&state=<?=$data[pozip2]?>&miles=5" target="_Blank">Mail Only 2</a> <input type="checkbox"></LEGEND>
<table>
<tr>
<td><input name="pobox2" size="30" value="<?=$data[pobox2]?>" /></td>
</tr>
<tr>
<td><input name="pocity2" size="20" value="<?=$data[pocity2]?>" /><input size="2" name="postate2" value="<?=$data[postate2]?>" /><input size="4" name="pozip2" value="<?=$data[pozip2]?>" /></td>
</tr>
</table>    
</FIELDSET>
</td></tr></table>
<? mysql_close();?>