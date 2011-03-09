<?
include 'common.php';
hardLog('access eviction payment information for '.$_GET[id],'user');
mysql_connect();
mysql_select_db('core');
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

	$q1 = "UPDATE evictionPackets, ps_pay SET 

									ps_pay.bill410='$_POST[bill410]',
									ps_pay.bill420='$_POST[bill420]',
									ps_pay.bill430='$_POST[bill430]',
									ps_pay.bill440='$_POST[bill440]',
									ps_pay.code410='$_POST[code410]',
									ps_pay.code410a='$_POST[code410a]',
									ps_pay.code410b='$_POST[code410b]',
									ps_pay.code420='$_POST[code420]',
									ps_pay.code420a='$_POST[code420a]',
									ps_pay.code420b='$_POST[code420b]',
									ps_pay.code430='$_POST[code430]',
									ps_pay.code430a='$_POST[code430a]',
									ps_pay.code430b='$_POST[code430b]',
									ps_pay.code440='$_POST[code440]',
									ps_pay.code440a='$_POST[code440a]',
									ps_pay.code440b='$_POST[code440b]',
									ps_pay.contractor_rate='$_POST[contractor_rate]', 
									ps_pay.contractor_paid='$_POST[contractor_paid]',
									ps_pay.contractor_check='$_POST[contractor_check]', 
									ps_pay.contractor_ratea='$_POST[contractor_ratea]', 
									ps_pay.contractor_paida='$_POST[contractor_paida]',
									ps_pay.contractor_checka='$_POST[contractor_checka]', 
									ps_pay.contractor_rateb='$_POST[contractor_rateb]', 
									ps_pay.contractor_paidb='$_POST[contractor_paidb]',
									ps_pay.contractor_checkb='$_POST[contractor_checkb]', 
									ps_pay.contractor_ratec='$_POST[contractor_ratec]', 
									ps_pay.contractor_paidc='$_POST[contractor_paidc]',
									ps_pay.contractor_checkc='$_POST[contractor_checkc]', 
									ps_pay.contractor_rated='$_POST[contractor_rated]', 
									ps_pay.contractor_paidd='$_POST[contractor_paidd]',
									ps_pay.contractor_checkd='$_POST[contractor_checkd]', 
									ps_pay.contractor_ratee='$_POST[contractor_ratee]', 
									ps_pay.contractor_paide='$_POST[contractor_paide]',
									ps_pay.contractor_checke='$_POST[contractor_checke]', 
									ps_pay.client_rate='$_POST[client_rate]', 
									ps_pay.client_ratea='$_POST[client_ratea]', 
									ps_pay.client_rateb='$_POST[client_rateb]', 
									ps_pay.client_paid='$_POST[client_paid]',
									ps_pay.client_paida='$_POST[client_paida]',
									ps_pay.client_paidb='$_POST[client_paidb]',
									ps_pay.client_check='$_POST[client_check]',
									ps_pay.client_checka='$_POST[client_checka]',
									ps_pay.client_checkb='$_POST[client_checkb]',
									ps_pay.client_ratec='$_POST[client_ratec]', 
									ps_pay.client_rated='$_POST[client_rated]', 
									ps_pay.client_ratee='$_POST[client_ratee]', 
									ps_pay.client_paidc='$_POST[client_paidc]',
									ps_pay.client_paidd='$_POST[client_paidd]',
									ps_pay.client_paide='$_POST[client_paide]',
									ps_pay.client_checkc='$_POST[client_checkc]',
									ps_pay.client_checkd='$_POST[client_checkd]',
									ps_pay.client_checke='$_POST[client_checke]',
									evictionPackets.accountingNotes='".addslashes($_POST[accountingNotes])."'
										WHERE evictionPackets.eviction_id='$_POST[id]' AND evictionPackets.eviction_id=ps_pay.packetID AND ps_pay.product='EV'";
	$r1 = @mysql_query ($q1) or die(mysql_error());
	if ($_POST[qc]){
		echo "<script>window.location='http://service.mdwestserve.com/ev_wizard.php?jump=$_POST[id]-1&mailDate=$_POST[qc]'</script>";
	}
	echo "<script>automation();</script>";
}
$q1 = "SELECT * FROM evictionPackets, ps_pay WHERE evictionPackets.eviction_id = $_GET[id] AND evictionPackets.eviction_id=ps_pay.packetID AND ps_pay.product='EV'";		
$r1 = @mysql_query ($q1) or die(mysql_error());
$data = mysql_fetch_array($r1, MYSQL_ASSOC);
?>
<script>
document.title = "Eviction Accounting #<?=$data[eviction_id];?>";
</script>
<body bgcolor="#99CCFF">
<style>
fieldset { background-color:#FFFFFF;  border:solid 1px #000000;}
.altset { background-color:#FFFFFF;  border:solid 1px #000000;}
.altset2 { background-color:#FFFFFF;  border:solid 1px #000000;}
legend, input, select { padding:0px; background-color:#FFFFCC; border:solid 1px #000000;}
td { font-variant:small-caps }
</style>
<form id="acc" name="acc" method="post">
<input type="hidden" name="id" value="<?=$_GET[id]?>" />
<? if ($_GET[qc]){ ?>
<input type="hidden" name="qc" value="<?=$_GET[qc]?>" />
<center style="font-weight:bold; font-size:14px;">ALL FILES MUST HAVE COST ENTERED BEFORE QUALITY CONTROL IS PROCESSED</center>
<? } ?>
<a href="http://mdwestserve.com/ps/ps_write_invoice.php?id=<?=$data[eviction_id];?>" target="_Blank">PS Write Invoice</a>
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
    	<td><input tabindex="1" name="client_check" size="4" maxlength="30" value="<?=$data[client_check]?>" /></td>
    	<td><input name="client_checka" size="4" maxlength="30" value="<?=$data[client_checka]?>" /></td>
    	<td><input name="client_checkb" size="4" maxlength="30" value="<?=$data[client_checkb]?>" /></td>
	</tr>
    <tr>
		<td>Process Service: <?=$data[service_status]?></td>
		<td><input name="bill410" size="2" maxlength="7" value="<?=$data[bill410]?>" /></td>
		<td><input tabindex="2" name="code410" size="2" maxlength="7" value="<?=$data[code410]?>" /></td>
    	<td><input name="code410a" size="2" maxlength="7" value="<?=$data[code410a]?>" /></td>
    	<td><input name="code410b" size="2" maxlength="7" value="<?=$data[code410b]?>" /></td>
	</tr>        
    <tr>
    	<td>Mailing Services: <?=$data[mailing_status]?></td>
		<td><input name="bill420" size="2" maxlength="7" value="<?=$data[bill420]?>" /></td>
    	<td><input tabindex="3" name="code420" size="2" maxlength="7" value="<?=$data[code420]?>" /></td>
    	<td><input name="code420a" size="2" maxlength="7" value="<?=$data[code420a]?>" /></td>
    	<td><input name="code420b" size="2" maxlength="7" value="<?=$data[code420b]?>" /></td>
	</tr>        
    <tr>
    	<td>Filing Services: <?=$data[filing_status]?></td>
		<td><input name="bill430" size="2" maxlength="7" value="<?=$data[bill430]?>" /></td>
    	<td><input tabindex="4" name="code430" size="2" maxlength="30" value="<?=$data[code430]?>" /></td>
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
		<td>$<?=$data[bill410]+$data[bill420]+$data[bill430]+$data[bill440]+$data[bill460];?></td>
    	<td style="border-top:solid 1px;"><input tabindex="5" name="client_paid" size="2" maxlength="7" value="<?=$data[client_paid]?>" /></td>
    	<td style="border-top:solid 1px;"><input name="client_paida" size="2" maxlength="7" value="<?=$data[client_paida]?>" /></td>
    	<td style="border-top:solid 1px;"><input name="client_paidb" size="2" maxlength="7" value="<?=$data[client_paidb]?>" /></td>
	</tr>
</table>
</fieldset><br>
<table><tr>
<td>
<fieldset>
<legend>Accounting Notes</legend>
<textarea name="accountingNotes" cols="40" rows="8"><?=stripslashes($data[accountingNotes])?></textarea>
<input name="submit" type="submit" style="background-color:#00FF00; cursor:pointer; font-size:24px; position:absolute; top:0; right:0px;"  value="SAVE"/>
</fieldset>
</td>
<td>
<fieldset>
<legend>Client Alert</legend>
<textarea name="extended_notes" cols="40" rows="8"><?=stripslashes($data[extended_notes])?></textarea>
</fieldset>
</td>
<td>
<fieldset>
<legend>Op. Notes</legend>
<textarea name="processor_notes" cols="40" rows="8"><?=stripslashes($data[processor_notes])?></textarea>
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
</td></tr></table>
<? mysql_close(); ?>