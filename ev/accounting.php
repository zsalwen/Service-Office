<?
include 'common.php';
hardLog('access payment information for '.$_GET[id],'user');

mysql_connect();
mysql_select_db('core');
function dupCheck($field,$string){
	$r=@mysql_query("select * from evictionPackets where $field = '$string'");
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
	$q = "SELECT payInstructions FROM attorneys WHERE attorneys_id = '$attID' LIMIT 0,1";
	$r = @mysql_query($q) or die(mysql_error());
	$d = mysql_fetch_array($r, MYSQL_ASSOC);
	return $prefix.trim($d[payInstructions]);
}
function justDate($dt){
	$date=explode(' ',$dt);
	return $date[0];
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

	$rxx=@mysql_query("select * from psActivity where today='".date('Y-m-d')."' LIMIT 0,1") or die(mysql_error());
	$dxx=mysql_fetch_array($rxx,MYSQL_ASSOC);
	$count=$dxx[clientPayment]+1;
	@mysql_query("update psActivity set clientPayment = '$count' where today='".date('Y-m-d')."'") or die(mysql_error());
	echo "Saved! - $count for the day...";

	$q1 = "UPDATE evictionPackets, ps_pay SET 

									ps_pay.bill410='$_POST[bill410]',
									ps_pay.bill420='$_POST[bill420]',
									ps_pay.bill430='$_POST[bill430]',
									ps_pay.bill440='$_POST[bill440]',
									ps_pay.bill460='$_POST[bill460]',
									ps_pay.code410='$_POST[code410]',
									ps_pay.code410a='$_POST[code410a]',
									ps_pay.code420='$_POST[code420]',
									ps_pay.code420a='$_POST[code420a]',
									ps_pay.code430='$_POST[code430]',
									ps_pay.code430a='$_POST[code430a]',
									ps_pay.code440='$_POST[code440]',
									ps_pay.code440a='$_POST[code440a]',
									ps_pay.code460='$_POST[code460]',
									ps_pay.code460a='$_POST[code460a]',
									ps_pay.contractor_rate='$_POST[contractor_rate]', 
									ps_pay.contractor_paid='$_POST[contractor_paid]',
									ps_pay.contractor_check='$_POST[contractor_check]', 
									ps_pay.contractor_ratea='$_POST[contractor_ratea]', 
									ps_pay.contractor_paida='$_POST[contractor_paida]',
									ps_pay.contractor_checka='$_POST[contractor_checka]', 
									ps_pay.client_rate='$_POST[client_rate]', 
									ps_pay.client_ratea='$_POST[client_ratea]', 
									ps_pay.client_paid='$_POST[client_paid]',
									ps_pay.client_paida='$_POST[client_paida]',
									ps_pay.client_check='$_POST[client_check]',
									ps_pay.client_checka='$_POST[client_checka]',
									evictionPackets.accountingNotes='".addslashes($_POST[accountingNotes])."'
										WHERE evictionPackets.eviction_id='$_POST[id]' AND evictionPackets.eviction_id=ps_pay.packetID AND ps_pay.product='EV'";
	$r1 = @mysql_query ($q1) or die(mysql_error());
	
//addNote($_POST[id],$_COOKIE[userdata][name].': Entered Payment on '.date('m/d/Y'));
	

	
	
	//echo $q1;
	if ($_POST[qc]){
		echo "<script>window.location='http://service.mdwestserve.com/ev_wizard.php?jump=$_POST[id]-1&mailDate=$_POST[qc]'</script>";
	}
	echo "<script>automation();</script>";
}
$q1 = "SELECT * FROM evictionPackets, ps_pay WHERE evictionPackets.eviction_id = $_GET[id] AND evictionPackets.eviction_id=ps_pay.packetID AND ps_pay.product='EV' LIMIT 0,1";		
$r1 = @mysql_query ($q1) or die(mysql_error());
$data = mysql_fetch_array($r1, MYSQL_ASSOC);




?>
<script>
document.title = "Accounting #<?=$data[eviction_id];?>";

</script>
<body bgcolor="#99CCFF">
<style>
fieldset { background-color:#FFFFFF;  border:solid 1px #000000;}
.altset { background-color:#FFFFFF;  border:solid 1px #000000;}
.altset2 { background-color:#FFFFFF;  border:solid 1px #000000;}
legend, input, select { padding:0px; background-color:#FFFFCC; border:solid 1px #000000;}
td { font-variant:small-caps; padding:0px; }
body, table {padding:0px;}
</style>
<form id="acc" name="acc" method="post">
<input type="hidden" name="id" value="<?=$_GET[id]?>" />
<? if ($_GET[qc]){ ?>
<input type="hidden" name="qc" value="<?=$_GET[qc]?>" />
<center style="font-weight:bold; font-size:14px;">ALL FILES MUST HAVE COST ENTERED BEFORE QUALITY CONTROL IS PROCESSED</center>
<? } ?>
<a href="http://staff.mdwestserve.com/ev/ev_write_invoice.php?id=<?=$data[eviction_id];?>" target="_Blank">Live Invoice</a>
<? if ($_GET[qc]){ ?>
<center><div style="border:1px solid;font-size:18px;"><?=id2attorney($data[attorneys_id])?><?=stripslashes(getPayInstructions($data[attorneys_id],'<br>'))?></div></center>
<? }elseif(stripslashes(getPayInstructions($data[attorneys_id],'')) != ''){ ?>
<center><div style="border:1px solid;font-size:18px; width:600px;"><?=stripslashes(getPayInstructions($data[attorneys_id],'<br>'))?></div></center>
<? }
$r1=mysql_query("SELECT * FROM gasRates ORDER BY id DESC LIMIT 0,1");
$d1=mysql_fetch_array($r1,MYSQL_ASSOC);
if ($d1[id]){
	echo "<center><div>[GAS: $d1[gasPrice] | CLIENT: $d1[client_rate]| CONTRACTOR: $d1[contractor_rate]]</div></center>";
}
?>
<table><tr><td width='50%'>
<fieldset>
	<legend>Eviction Process Service Account Details</legend>
<table width="100%">
	<tr>
    	<td></td>
        <td style="font-size:12px;"><?=id2name($data[server_id])?></td>
    	<td style="font-size:12px;"></td>
    </tr>
    <tr>
    	<td>Check</td>
    	<td><input name="contractor_check" size="2" maxlength="30" value="<?=$data[contractor_check]?>" /></td>
    	<td><input name="contractor_checka" size="2" maxlength="30" value="<?=$data[contractor_checka]?>" /></td>
	</tr>
    <tr>
    	<td>Paid</td>
    	<td><input name="contractor_paid" size="2" maxlength="7" value="<?=$data[contractor_paid]?>" /></td>
    	<td><input name="contractor_paida" size="2" maxlength="7" value="<?=$data[contractor_paida]?>" /></td>
	</tr>
	<tr>
    	<td>Quote</td>
    	<td><input name="contractor_rate" size="2" maxlength="7" value="<?=$data[contractor_rate]?>" /></td>
    	<td><input name="contractor_ratea" size="2" maxlength="7" value="<?=$data[contractor_ratea]?>" /></td>
    	<td></td>
    </tr>
    <tr>
    	<td>Client</td>
    	<td><input name="client_rate" size="2" maxlength="7" value="<?=$data[client_rate]?>" /></td>
    	<td><input name="client_ratea" size="2" maxlength="7" value="<?=$data[client_ratea]?>" /></td>
	</tr>
</table>
<?
$i=0;
$q2="SELECT * FROM ps_penalties WHERE packetID='$_GET[id]' AND product='EV'";
$r2=@mysql_query($q2) or die ("Query: $q2<br>".mysql_error());
while ($d2=mysql_fetch_array($r2,MYSQL_ASSOC)){$i++;
	$def=$d2[defendantID];
	$list .= "<tr><td>".id2name($d2[serverID])."</td><td>".$data["name$def"]."</td><td>[".strtoupper(stripslashes($d2[description]))."] - ".id2name($d2[entryID])." ".justDate($d2[entryDate])."</td></tr>";
}
if ($list != ''){
	echo "<table width='100%' border='1' style='border-collapse:collapse;'><tr><td colspan='3' align='center'><b style='color:red;'>PENALTIES</b></td></tr><tr><td>Server</td><td>Defendant</td><td>Description</td></tr>$list<tr><td colspan='3' align='right' style='font-weight:bold;'>TOTAL PENALTIES: $i</td></tr></table>";
}
?>
</fieldset>
</td><td rowspan='3' valign='top'>
<? if ($data[attorneys_id] == 1){ ?>
<fieldset>
<legend>Invoice Header</legend>
<div style='text-align:left;'><?=strtoupper($data[name1])?><br><?=strtoupper($data[address1])?><br><?=strtoupper($data[city1])?>, <?=strtoupper($data[state1])?> <?=strtoupper($data[zip1])?></div>
</fieldset>
<? } ?>
</td></tr><tr><td valign="top">
<fieldset>
	<legend>Client Accounting Details</legend>
<table cellspacing="0">
	<tr>
    	<td></td>
		<td>Bill</td>
        <td>First</td>
    	<td>Second</td>
    </tr>
    <tr>
    	<td>Client Check</td>
		<td></td>
    	<td><input tabindex="4" name="client_check" size="4" maxlength="30" value="<?=$data[client_check]?>" /></td>
    	<td><input name="client_checka" size="4" maxlength="30" value="<?=$data[client_checka]?>" /></td>
	</tr>
    <tr>
		<td>Process Service: <?=$data[service_status]?></td>
		<td><input name="bill410" tabindex="1" size="2" maxlength="7" value="<?=$data[bill410]?>" /></td>
		<td><input tabindex="5" name="code410" size="2" maxlength="7" value="<?=$data[code410]?>" /></td>
    	<td><input tabindex="6" name="code410a" size="2" maxlength="7" value="<?=$data[code410a]?>" /></td>
	</tr>        
    <tr>
    	<td>Mailing Services: <?=$data[mailing_status]?></td>
		<td><input name="bill420" tabindex="2" size="2" maxlength="7" value="<?=$data[bill420]?>" /></td>
    	<td><input name="code420" size="2" maxlength="7" value="<?=$data[code420]?>" /></td>
    	<td><input name="code420a" size="2" maxlength="7" value="<?=$data[code420a]?>" /></td>
	</tr>        
    <tr>
    	<td>Filing Services: <?=$data[filing_status]?></td>
		<td><input tabindex="3" name="bill430" size="2" maxlength="7" value="<?=$data[bill430]?>" /></td>
    	<td><input name="code430" size="2" maxlength="30" value="<?=$data[code430]?>" /></td>
    	<td><input name="code430a" size="2" maxlength="30" value="<?=$data[code430a]?>" /></td>
	</tr>        
    <tr>
    	<td>Code: Skip Trace Services</td>
		<td><input name="bill440" size="2" maxlength="7" value="<?=$data[bill440]?>" /></td>
    	<td><input name="code440" size="2" maxlength="30" value="<?=$data[code440]?>" /></td>
    	<td><input name="code440a" size="2" maxlength="30" value="<?=$data[code440a]?>" /></td>
	</tr>        
	<tr>
    	<td>Gas Surcharge</td>
		<td><input name="bill460" size="2" maxlength="7" value="<?=$data[bill460]?>" /></td>
    	<td><input name="code460" size="2" maxlength="30" value="<?=$data[code460]?>" /></td>
    	<td><input name="code460a" size="2" maxlength="30" value="<?=$data[code460a]?>" /></td>
	</tr>
    <tr>
    	<td style="border-top:solid 1px;">Total Payment</td>
		<td>$<?=$data[bill410]+$data[bill420]+$data[bill430]+$data[bill440]+$data[bill460];?></td>
    	<td style="border-top:solid 1px;"><input tabindex="7" name="client_paid" size="2" maxlength="7" value="<?=$data[client_paid]?>" /></td>
    	<td style="border-top:solid 1px;"><input name="client_paida" size="2" maxlength="7" value="<?=$data[client_paida]?>" /></td>
	</tr>
</table>

</fieldset>
</td></tr><tr><td>
<fieldset>
<legend>Occupant Notices</legend>
<table>
<?
$q2="SELECT * FROM occNotices WHERE eviction_id='$_GET[id]'";
$r2=@mysql_query($q2) or die("Query: $q1<br>".mysql_error());
while ($d2=mysql_fetch_array($r2,MYSQL_ASSOC)){
	$notices .= "<tr><td>$d2[requirements] Sent ".dateExplode($d2[sendDate])." - $".$d2[bill]."</td></tr>";
}
if ($notices != ''){
	echo $notices;
}else{
	echo "<tr><td>NONE</td></tr>";
}
?>
</table>
</fieldset>
</td></tr><tr><td colspan='2'>
</form>
<fieldset><legend>Notes</legend>
<iframe height="200px" width="750px"  frameborder="0" src="http://staff.mdwestserve.com/acctNotes.php?eviction=<?=$data[eviction_id]?>"></iframe>
</fieldset>
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
<LEGEND class="a" ACCESSKEY=C><a href="http://staff.mdwestserve.com/dispatcher.php?aptsut=&address=<?=$data[address1]?>&city=<?=$data[city1]?>&state=<?=$data[state1]?>&miles=5" target="_Blank">Mortgage / Deed of Trust</a><input type="checkbox" checked><br><?=id2name($data[server_id]);?></LEGEND>
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


</td></tr></table>
<? mysql_close();?>