<html>
<head>
<script src="edit.js"></script>
<link rel="stylesheet" type="text/css" href="edit.css" />
<?PHP
if (!$_COOKIE[psdata][user_id]){
	error_log(date('h:iA j/n/y')." SECURITY PREVENTED ACCESS to ".$_SERVER['SCRIPT_NAME']." by ".$_SERVER["REMOTE_ADDR"]."\n", 3, '/logs/user.log');
	header ('Location: http://staff.mdwestserve.com'); 
}
date_default_timezone_set('America/New_York');
include 'edit.functions.php';
mysql_connect();
mysql_select_db('core');
include 'edit.post.php';
$id=$_COOKIE[psdata][user_id];

if ($_GET[packet] && $_GET[packet] < '20000'){
die('This edit page is for packet 20000 and above, please use the ev/otd/standard versions for legacy packets.');
}

if($_GET[packet]){
	$query = "SELECT *, CONCAT(TIMEDIFF( NOW(), date_received)) as hours FROM packet where id='$_GET[packet]'";
	hardLog('loaded normalized order for '.$_GET[packet],'user');
}else{
	if($_GET[start]){
		$query = "SELECT *, CONCAT(TIMEDIFF( NOW(), date_received)) as hours FROM packet where process_status='READY' and qualityControl='' and id >= '$_GET[start]' order by id ";
	}else{
		$query = "SELECT *, CONCAT(TIMEDIFF( NOW(), date_received)) as hours FROM packet where status='NEW' and process_status <> 'CANCELLED' and process_status <> 'DUPLICATE' AND process_status <> 'DAMAGED PDF' and process_status <> 'DUPLICATE/DIFF-PDF' order by RAND() ";
		hardLog('loaded NEW normalized order for '.$d[id],'user');
	}
}

// build main packet array
$r=@mysql_query($query) or die($query.'<br>'.mysql_error());
$d=mysql_fetch_array($r, MYSQL_ASSOC);
?>
</head>
<body style="padding:0px;">
<? 
if (!$d[id]){ // do we really have a good packet id?
?>

<center>
<form>Jump to packet <input name="packet"></form><br><br>
<a href="edit.php?archive=<?=$_GET[packet]?>">Have you checked <b>the archives</b> for packet <?=$_GET[packet]?>?</a>
</center>

<? 
}else{ // ok we have a good packet number let's go ahead and build the html

$packet=$d[id];

include 'edit.testing.php'; // make sure we have main packet array before testing packet
?>
<form method="post">
<!-- prior values to submit for compare -->
<input type="hidden" name="uspsVerify" value="<?=$d[uspsVerify]?>">







<fieldset>
<legend>Server and Staff Assignments</legend>
<?
$rSSA=@mysql_query("select * from instruction where packet_id = '$packet'");
while($dSSA=mysql_fetch_array($rSSA,MYSQL_ASSOC)){
 echo "<li><input type='checkbox'>".serverID($dSSA[server_id])." on ".nameID($dSSA[name_id])." at ".addressID($dSSA[address_id])."</li>";
}
?>
</fieldset>

<fieldset>
<legend>Online File Storage <a href='upload.php' target='preview'>Upload</a>, <a href="#" onclick="window.open('lightboard.php?packet=<?=$d[id]?>','Lightboard','menubar=0,resizable=1,status=0,width=800,height=600') ">PDF Lightboard</a></legend>
<?
$rOFS=@mysql_query("select * from attachment where packet_id = '$packet'");
while($dOFS=mysql_fetch_array($rOFS,MYSQL_ASSOC)){
 echo "<li onClick=\"parent.frames['pane2'].location.href = '$dOFS[absolute_url]' \">$dOFS[instruction_id] $dOFS[id] $dOFS[user_id] $dOFS[server_id] $dOFS[processed]  $dOFS[uri]</li>";
}
?>
</fieldset>

<FIELDSET style="padding:0px;">

<? if ($d[possibleDuplicate]){?>
<div style="background-color:#ff0000" align="center">Duplicate Warning Level: <?=$d[possibleDuplicate]?></div>
<? } ?>
<table width="100%" style="padding:0px;"><tr>
<?
$dupCheck=dupCheck($d[client_file]);
?>
<td valign="top" <?=$dupCheck?>>
<FIELDSET style="padding:0px;">
<LEGEND ACCESSKEY=C><?=id2attorney($d[attorneys_id]);?> File Data <input type="submit" style="background-color:#00FF00; font-weight:bold; width:100px;" value="SAVE"></LEGEND>
<table>
<tr>
<td>Client&nbsp;File </td>
<td><input name="client_file" value="<?=$d[client_file]?>" /></td>
</tr>
<tr>
<td><a href="http://casesearch.courts.state.md.us/inquiry/inquiryDetail.jis?caseId=<?=str_replace('-','',trim($d[case_no]))?>&detailLoc=<? if ($d[circuit_court] == "MONTGOMERY"){ echo "MCCI";}elseif($d[circuit_court] == "PRINCE GEORGES"){echo "PGV";}else{ echo "CC";} ?>" target="preview">Case&nbsp;Number</a></td>
<td><input name="case_no" value="<?=$d[case_no]?>" /></td>
</tr>
<tr>
<td>Circuit&nbsp;Court</a></td>
<td><select name="circuit_court"><?=mkCC($d[circuit_court]);?></select></td>
</tr>
<tr>
<td>Est. Close</td>
<td><input name="estFileDate" value="<?=$d[estFileDate]?>"></td>
</tr>
<?
$rXX=@mysql_query("select name, phone from courier where courierID = '$d[courierID]'");
$dXX=mysql_fetch_array($rXX,MYSQL_ASSOC);
if ($dXX[phone]){
	$phone="-".$dXX[phone];
}
?>
<tr>
<td>Courier</td>
<td><select name="courierID"><option value="<?=$d[courierID]?>"><?=$dXX[name]?><?=$phone?></option>
<?
$CCr=@mysql_query("select * from courier WHERE isActive='1'");
while($CCd=mysql_fetch_array($CCr,MYSQL_ASSOC)){
if ($CCd[phone]){
	$phone="-".$CCd[phone];
}else{
	$phone='';
}
?>
<option value="<?=$CCd[courierID]?>"><?=$CCd[name]?><?=$phone?> (<?=$CCd[courierID]?>)</option>
<? }?></select></td>
</tr>
<tr>
<td>File Closed</td>
<td><input name="fileDate" value="<?=$d[fileDate]?>"><input type="submit" name="sendToClient" style="background-color:#66CCFF; font-weight:bold; width:140px;" value="SEND TO CLIENT"></td>
</tr>
<tr>
<td>File Reopened</td>
<td><input name="reopenDate" value="<?=$d[reopenDate]?>"><input type="submit" name="reopen" style="background-color:#FFFF00; font-weight:bold; width:70px;" value="REOPEN"></td>
</tr>
<tr>
<td>Alt. Plaintiff</td>
<td><input name="altPlaintiff" value="<?=$d[altPlaintiff]?>"></td>
</tr>
<tr>
<td>Addl&nbsp;Docs</td>
<td><input name="addlDocs" value="<?=$d[addlDocs]?>"></td>
</tr>
<tr>
<td>Loss Mitigation</td>
<td><? if ($d[lossMit] != ''){ echo "<select name='lossMit'><option>$d[lossMit]</option>";}elseif($d[status] == 'NEW'){ echo "<select name='lossMit' class='italic'><option value='FINAL' class='italic'>FINAL</option>"; }else{ echo "<select name='lossMit' class='italic'><option value='' class='italic'></option>"; } ?>
<option>FINAL</option>
<option>PRELIMINARY</option>
<option>N/A - OLD LAW</option>
<?
if($d[lossMit] != ''){
?>
<option value=''></option>
<? } ?>
</select><div style='display:inline;<? if ($d[avoidDOT] == 'checked'){ echo " background-color:#FF0000;"; }?>'><input <? if ($d[avoidDOT] == 'checked'){echo "checked";}?> type='checkbox' value='checked' name='avoidDOT'> Only Post @ DOT</div>
</td>
</tr>
<tr>
<td colspan='2'><div style=" font-size:12px; background-color:ffffff; padding:0px;">
<?
mysql_select_db('core');
$q5="SELECT * FROM ps_affidavits WHERE packetID = '$d[id]' order by defendantID";
$r5=@mysql_query($q5) or die ("Query: $q5<br>".mysql_error());
while ($d5=mysql_fetch_array($r5, MYSQL_ASSOC)){
		$defname = $d["name".$d5[defendantID]];
		echo "<li><a target='_blank' href='".str_replace('ps/','',$d5[affidavit])."'><strong>".$defname."</strong>: $d5[method]</a></li>";
}
?>
</div></td></tr>
<? if ($d[attorney_notes]){ ?>
<tr>
<td colspan='2'>Instructions: <?=$d[date_received];?><br><?=$d[attorney_notes];?></td>
</tr>
<? }?>
</table>
</FIELDSET>
<?
if ($dupCheck == "class='duplicate'"){
	echo dupList($d[client_file],$d[id]);
}
?>
</td>

<?
$otdStr=str_replace('portal//var/www/dataFiles/service/orders/','packet/',$d[otd]);
$otdStr=str_replace('data/service/orders/','packet/',$otdStr);
$otdStr=str_replace('portal/','',$otdStr);
//$otdStr=str_replace('mdwestserve.com','alpha.mdwestserve.com',$otdStr);
/*if (!$otdStr){
	$otdStr=$d[otd];
}*/
if (!strpos($otdStr,'mdwestserve.com')){
	$otdStr="http://mdwestserve.com/".$otdStr;
}
if ($d[id] > 3620 && $d[reopenDate] != ''){
	$checkLink="serviceSheet.php?packet=$d[id]&autoPrint=1";
}else{
	$checkLink="oldServiceSheet.php?packet=$d[id]&autoPrint=1";
}
$q5="SELECT DISTINCT serverID from ps_history WHERE packet_id='$d[id]'";
$r5=@mysql_query($q5) or die(mysql_error());
$i=0;
$data5=mysql_num_rows($r5);
if ($data5 > 0){
while ($d5=mysql_fetch_array($r5, MYSQL_ASSOC)){$i++;
$q6="SELECT * FROM ps_history WHERE serverID='$d5[serverID]' and id='$d[id]'";
$r6=@mysql_query($q6) or die(mysql_error());
$d6=mysql_num_rows($r6);
if ($i == '1'){
if ($d6 > 1){
$server = $d6." ".initals(id2name($d5[serverID]));
}else{
$server = $d6." ".initals(id2name($d5[serverID]));
}
}else{
if ($d6 > 1){
$server .= ", ".$d6." ".initals(id2name($d5[serverID]));
}else{
$server .= ", ".$d6." ".initals(id2name($d5[serverID]));
}
}
}
}else{
$server="none";
}
$ri=@mysql_query("SELECT packetID FROM ps_instructions WHERE packetID='$d[id]'") or die (mysql_error());
$di=mysql_fetch_array($ri,MYSQL_ASSOC);
if ($di[packetID]){
	$customBG="style='background-color:green;'";
}else{
	$customBG="style='background-color:red;'";
}
$rc=@mysql_query("SELECT * FROM ps_history WHERE packet_id='$d[id]' AND wizard='CERT MAILING' LIMIT 0,1");
$dc=mysql_fetch_array($rc,MYSQL_ASSOC);
?>

</td></tr></table>





<table  id="track" width="100%"><tr><td align='center'>
<FIELDSET>
<LEGEND ACCESSKEY=C>docuTrack: in-house document tracking solution</LEGEND>
<table width="100%" border="1" style="border-collapse:collapse;" cellspacing='0' cellpadding='2'>
<tr>
	<td>Document</td>
	<td>Defendant</td>
	<td>Signer</td>
	<td>Processor</td>
	<td>Timestamp</td>
</tr>
<? 
$r92=@mysql_query("select * from docuTrack where packet = '$d[id]' order by trackID desc");
while($d92=mysql_fetch_array($r92,MYSQL_ASSOC)){
if ($d92[defendant] == 'OCC'){
	$defname = "OCCUPANT";
}elseif ($d92[defendant] == 'CERT'){
	$defname = "CERTIFICATE";
}else{
	$defname = $d["name".$d92[defendant]];
}
if ($d92[server]){
	$signer = id2name($d92[server]);
}else{
	$signer = "Version 1 Barcode";
}?>
<tr>
	<td><?=$d92[document]?></td>
	<td><?=$defname?></td>
	<td><?=$signer?></td>
	<td><?=$d92[location]?></td>
	<td><?=$d92[binder]?></td>
</tr>
<? } ?>
</table>    
</FIELDSET></td></tr></table>








<table width="100%" id="status" style="display:none; font-size:11px; padding:0px;">
<input type="hidden" name="id" value="<?=$d[id]?>" />
<tr>
<? if ($_GET[packet]){?>
<td align="center" width="25%">Client Status<br><select name="status"><option><?=$d[status]?></option>
<?
$q1="SELECT DISTINCT status from packet WHERE status <> ''";
$r1=@mysql_query($q1) or die("Query: $q1<br>".mysql_error());
while ($d1=mysql_fetch_array($r1, MYSQL_ASSOC)){
?>
<option><?=$d1[status]?></option>
<? } ?>
<option value=""></option>
</select></td>
<? }?>
<td align="center" width="25%">Service Status<br><select name="service_status"><option><?=$d[service_status]?></option>
<?
$q1="SELECT DISTINCT service_status from packet WHERE service_status <> ''";
$r1=@mysql_query($q1) or die("Query: $q1<br>".mysql_error());
while ($d1=mysql_fetch_array($r1, MYSQL_ASSOC)){
?>
<option><?=$d1[service_status]?></option>
<? } ?>
<option value=""></option>
</select></td>
<td align="center" width="25%">Filing Status<br><select name="filing_status"><option><?=$d[filing_status]?></option>
<?
$q1="SELECT DISTINCT filing_status from packet WHERE filing_status <> '' AND filing_status <> 'REOPENED'";
$r1=@mysql_query($q1) or die("Query: $q1<br>".mysql_error());
while ($d1=mysql_fetch_array($r1, MYSQL_ASSOC)){
?>
<option><?=strtoupper($d1[filing_status])?></option>
<? } ?>
<option>REOPENED</option>
<option value=""></option>
</select></td></tr><tr>
<td align="center" width="25%">Process Status<br><select name="process_status"><option><?=$d[process_status]?></option>
<?
$q2="SELECT DISTINCT process_status from packet WHERE process_status <> ''";
$r2=@mysql_query($q2) or die("Query: $q2<br>".mysql_error());
while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)){
?>
<option><?=$d2[process_status]?></option>
<? } ?>
<option value=""></option>
</select></td>
<td align="center" width="25%"><table><tr><td>Affidavit Status<br><select name="affidavit_status"><option><?=$d[affidavit_status]?></option>
<?
$q3="SELECT DISTINCT affidavit_status from packet WHERE affidavit_status <> ''";
$r3=@mysql_query($q3) or die("Query: $q3<br>".mysql_error());
while ($d3=mysql_fetch_array($r3, MYSQL_ASSOC)){
?>
<option><?=$d3[affidavit_status]?></option>
<? } ?>
<option value=""></option>
</select>

</td><td>
<td align="center" colspan='3'>Affidavit Status 2<br><select name="affidavit_status2"><option><?=$d[affidavit_status2]?></option>
<?
$q3="SELECT DISTINCT affidavit_status2 from packet WHERE affidavit_status2 <> '' AND affidavit_status2 <> 'REOPENED' AND affidavit_status2 <> 'AWAITING OUT OF STATE AFFIDAVITS' AND affidavit_status2 <> 'AWAITING OUT OF STATE SERVICE' AND affidavit_status2 <> 'AWAITING MAILING'";
$r3=@mysql_query($q3) or die("Query: $q3<br>".mysql_error());
while ($d3=mysql_fetch_array($r3, MYSQL_ASSOC)){
?>
<option><?=$d3[affidavit_status2]?></option>
<? } ?>
<option>AWAITING MAILING</option>
<option>AWAITING OUT OF STATE AFFIDAVITS</option>
<option>AWAITING OUT OF STATE SERVICE</option>
<option>REOPENED</option>
<option value=""></option>
</select></td>
</td></tr></table>
</td>
<td align="center">Photo Status<br><select name="photoStatus"><option><?=$d[photoStatus]?></option>
<?
$q4="SELECT DISTINCT photoStatus from packet WHERE photoStatus <> ''";
$r4=@mysql_query($q4) or die("Query: $q4<br>".mysql_error());
while ($d4=mysql_fetch_array($r4, MYSQL_ASSOC)){
?>
<option><?=$d4[photoStatus]?></option>
<? } ?>
<option value=""></option>
</select></td></tr><tr>
<td align="center" width="25%">Affidavit Type<br><select name="affidavitType"><option><?=$d[affidavitType]?></option>
<?
$q4="SELECT DISTINCT affidavitType from packet WHERE affidavitType <> ''";
$r4=@mysql_query($q4) or die("Query: $q4<br>".mysql_error());
while ($d4=mysql_fetch_array($r4, MYSQL_ASSOC)){
?>
<option><?=$d4[affidavitType]?></option>
<? } ?>
<option value=""></option>
</select></td>
<td align="center" width="25%">Mail Status<br><select name="mail_status"><option><?=$d[mail_status]?></option>
<?
$q4="SELECT DISTINCT mail_status from packet WHERE mail_status <> ''";
$r4=@mysql_query($q4) or die("Query: $q4<br>".mysql_error());
while ($d4=mysql_fetch_array($r4, MYSQL_ASSOC)){
?>
<option><?=$d4[mail_status]?></option>
<? } ?>
<option value=""></option>
</select></td>
<td align='center'><? if ($d[process_status] != 'CANCELLED'){ ?><div style='font-size:11px;' name='cancelRef' value='Client Reference Email' onclick="value=''" size='25' /><div style='background-color:pink; font-size: 10.5px;' type='button' name='cancel' value='CANCEL' onclick="confirmation(cancelRef.value);" /><? }?></td>
</tr>

</table>


<table align='center' style='font-size:12px;' width='100%'><tr><td align="center" width='25%'>
Refile<br>
<input type="checkbox" name="refile" <? if ($d[refile] == 'checked'){ echo "checked";} ?> value="checked">
</td><td align="center" width='25%' style="padding-left:5px">
Rush Service<br>
<input type="checkbox" name="rush" <? if ($d[rush] == 'checked'){ echo "checked";} ?> value="checked">
</td><td align="center" width='25%' style="padding-left:5px">
Priority Service<br>
<input type="checkbox" name="priority" <? if ($d[priority] == 'checked'){ echo "checked";} ?> value="checked">
</td><td align="center" width='25%' style="padding-left:5px">
Amended Affidavit<br>
<input type="checkbox" name="amendedAff" <? if ($d[amendedAff] == 'checked'){ echo "checked";} ?> value="checked">
</td></tr></table>



<?
	$getFolder=getFolder($d[otd]);
	$trioAff='/data/service/orders/'.$getFolder.'/TrioAffidavitService.pdf';
	//if BGW file with client service affidavit, pop open affidavit
	if ($d[status] == 'NEW' && $d[process_status] != 'CANCELLED' && $d[process_status] != 'DUPLICATE' && $d[process_status] != 'DAMAGED PDF' && $d[process_status] != 'DUPLICATE/DIFF-PDF' && $d[attorneys_id] == 70 && file_exists($trioAff)){
		$affPath='http://mdwestserve.com/packet/'.$getFolder.'/TrioAffidavitService.pdf';
		echo "<script>window.open('$affPath','Trio Service Affidavit','width=600, height=800')</script>";
	}
	//only run rfmMerge if file is from BGW, is new, has an existing Request For Mediation uploaded in the same folder as the OTD, and has not already unsuccessfully tried to merge already.
	$rfm='/data/service/orders/'.$getFolder.'/RequestforMediation.pdf';
	if ($d[status] == 'NEW' && $d[process_status] != 'CANCELLED' && $d[process_status] != 'DUPLICATE' && $d[process_status] != 'DAMAGED PDF' && $d[process_status] != 'DUPLICATE/DIFF-PDF' && $d[attorneys_id] == 70 && file_exists($rfm) && ($d[prevOTD] == '' || $d[prevOTD] != $d[otd])){
		$src= "http://staff.mdwestserve.com/temp/rfmMerge.php?packet=$d[id]";
	}elseif($d[status]=="NEW" || $_GET[otd] == '1'){
		$src=str_replace('portal//var/www/dataFiles/service/orders/','packet/',$d[otd]);
		$src=str_replace('data/service/orders/','packet/',$src);
		$src=str_replace('portal/','',$src);
		
		//$src=str_replace('mdwestserve.com','alpha.mdwestserve.com',$src);
		/*if (!$src){
			$src=$d[otd];
		}*/
	}elseif(!$d[uspsVerify]){
		$src="supernova.php?packet=$d[id]";
	}elseif(!$d[caseVerify] && $d[case_no]){
		$src="validateCase.php?case=$d[case_no]&packet=$d[id]&county=$d[circuit_court]";
	}elseif(!$d[qualityControl]){
		if ($d[service_status] == 'MAIL ONLY'){
			$src="entryVerify.php?packet=$d[id]&frame=no&matrix=1";
		}else{
			$src="entryVerify.php?packet=$d[id]&frame=no";
		}
	}elseif(!$d[caseVerify]){
		$src="validateCase.php?case=$d[case_no]&packet=$d[id]&county=$d[circuit_court]";
	}elseif($d[process_status] == "CANCELLED" || $d[filing_status]=="FILED WITH COURT" || $d[filing_status]=="FILED WITH COURT - FBS"){
		$src="http://staff.mdwestserve.com/otd/minips_pay.php?id=$d[id]";
	}else{
		$src="serviceReview.php?packet=$d[id]"; 
	}

	$explode = explode("/",$d[otd]);
	$explodeCount=count($explode)-1;
?>

<table style="padding:0px;" width="100%">
	<tr>
		<td style='font-size:12px;' valign="bottom"><input name="pages" value="<?=$d[pages]?>" size="3"> # OTD Pages <?=testLink($d[otd])?> <b style="background-color:#FFFF00; padding:0px;"><?=trim($explode["$explodeCount"])?></b></td>
		<td style='font-size:12px;' valign="bottom"><input name="mailWeight" size="4" value="<?=$d[mailWeight]?>"> Mail Weight</td></form>
		<form action="http://staff.mdwestserve.com/temp/pageRemove.php"><td valign="bottom"><input type="hidden" name="id" value="<?=$d[id]?>"><input type="hidden" name="type" value="OTD"><? if ($_GET[packet]){ ?><input type="hidden" name="packet" value="<?=$d[id]?>"><? } ?><input name="skip" onclick="value=''" value="Remove Page #"> <input type="submit" value="GO!"></td></form>
	</tr>
	<tr>
		<td colspan="3" valign="bottom">
		<input name="otd" value="<?=$d[otd]?>" size="80"> <? if($d[status]=="NEW"){ echo "<a href='renameOTD.php?packet=$d[id]&test=1'>FIX OTD LINK</a>";}else{echo "<a href='renameOTD.php?packet=$d[id]'>FIX</a>";} ?><?=searchList($d[id]);?>
		</td>
	</tr>
</table>



<? } // end good packet test?>


<? 
if ($_GET[type]){
	echo $_GET[type];
}


mysql_close();
$headers = apache_request_headers();
$lb = $headers["X-Forwarded-Host"];
$mirror = $_SERVER['HTTP_HOST'];
?>
<center style="padding:0px;">Mysql Closed on <?=$mirror;?> from <?=$lb;?> <hr> Debug: <?=$built;?></center>
</body>
</html>