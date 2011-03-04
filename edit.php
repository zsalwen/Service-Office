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

// select packet and build query / html options for something like number of addresses or names and instruction set's
if ($_GET[packet] && $_GET[packet] < '20000'){
	$query = "SELECT *, CONCAT(TIMEDIFF( NOW(), date_received)) as hours FROM ps_packets where packet_id='$_GET[packet]'";
	hardLog('loaded legacy otd order for '.$_GET[packet],'user');
}elseif($_GET[packet] && $_GET[packet] >= '20000'){
	$query = "SELECT *, CONCAT(TIMEDIFF( NOW(), date_received)) as hours FROM packet where id='$_GET[packet]'";
	hardLog('loaded normalized order for '.$_GET[packet],'user');
}else{
	if($_GET[start]){
		$query = "SELECT *, CONCAT(TIMEDIFF( NOW(), date_received)) as hours FROM ps_packets where process_status='READY' and qualityControl='' and packet_id >= '$_GET[start]' order by packet_id ";
	}else{
		$query = "SELECT *, CONCAT(TIMEDIFF( NOW(), date_received)) as hours FROM packets where status='NEW' and process_status <> 'CANCELLED' and process_status <> 'DUPLICATE' AND process_status <> 'DAMAGED PDF' and process_status <> 'DUPLICATE/DIFF-PDF' order by RAND() ";
		hardLog('loaded NEW normalized order for '.$d[packet_id],'user');
	}
}

// build main packet array
$r=@mysql_query($query) or die($query.'<br>'.mysql_error());
$d=mysql_fetch_array($r, MYSQL_ASSOC);
?>
</head>
<body style="padding:0px;">
<?=$query;?>
<? 
if (!$d[packet_id] && !$d[eviction_id] && !$d[id]){ // do we really have a good packet id?
?>

<center>
<form>Jump to packet <input name="packet"></form><br><br>
<a href="edit.php?archive=<?=$_GET[packet]?>">Have you checked <b>the archives</b> for packet <?=$_GET[packet]?>?</a>
</center>

<? 
}else{ // ok we have a good packet number let's go ahead and build the html

if ($d[packet_id]){
 $packet=$d[packet_id];
}elseif($d[eviction_id]){
 $packet=$d[eviction_id];
}elseif($d[id]){
 $packet=$d[id];
}

include 'edit.testing.php'; // make sure we have main packet array before testing packet
?>
<form method="post">
<!-- prior values to submit for compare -->
<input type="hidden" name="uspsVerify" value="<?=$d[uspsVerify]?>">



<!-- Start Service Timeline Toolbar -->
<table align="center" style="padding:0px;"><tr>
<? $test1 = getTime($packet,'Data Entry');?>
<td><div class="<?=$test1[css];?>"><?=$test1[event];?><br><?=$test1[eDate];?></div></td>
<? $test2 = getTime($packet,'Dispatched');?>
<td><div class="<?=$test2[css];?>"><?=$test2[event];?><br><?=$test2[eDate];?></div></td>
<? $test3 = getTime($packet,'Completing Service');?>
<? if (!$test3[eDate] && $test2[eDate]){ ?>
<td><div class="active">Service In Progress<br><?=date('m/d/y');?></div></td>
<? } else{ ?>
<td><div class="pending">Service In Progress<br></div></td>
<? } ?>
<td><div class="<?=$test3[css];?>"><?=$test3[event];?><br><?=$test3[eDate];?></div></td>
<? $test4 = getTime($packet,'Confirmed Filing');?>
<? if (!$test4[eDate] && $test3[eDate]){ ?>
<td><div class="active">Post-Service<br><?=date('m/d/y');?></div></td>
<? } else{ ?>
<td><div class="pending">Post-Service<br></div></td>
<? } ?>
<? if($test4[eDate]){ ?>
<td><div class="<?=$test4[css];?>"><?=$test4[event];?><br><?=$test4[eDate];?></div></td>
<? }else{ ?>
<td><div class="alert">Estimated Close<br><?=getClose($packet);?></div></td>
<? }?>
<td><div class="alert"style="font-size:10px;"><a href="?packet=<?=$packet?>&rescan='<?=time();?>'">RESCAN</a><hr><?=$rescanStatus;?></div></td>
<td><div class="alert"style="font-size:10px;"><a href="?packet=<?=$packet?>&export='<?=time();?>'">EXPORT</a><hr><?=$exportStatus;?></div></td>
</tr></table>
<!-- End Service Timeline Toolbar -->

<table width="100%" style='background-color:<?=colorCode(stripHours($d[hours]),$d[filing_status]);?>; padding:0px;'>
<tr>
<td valign="top">

<!-- start left pane -->
<fieldset>
<legend>Server and Staff Assignments <a href='upload.php' target='preview'>Upload</a>, <a href="#" onclick="window.open('lightboard.php?packet=<?=$d[packet_id]?>','Lightboard','menubar=0,resizable=1,status=0,width=800,height=600') ">PDF Lightboard</a></legend>
<?
$rSSA=@mysql_query("select * from instruction where packet_id = '$packet'");
while($dSSA=mysql_fetch_array($rSSA,MYSQL_ASSOC)){
 echo "<li><input type='checkbox'>".serverID($dSSA[server_id])." on ".nameID($dSSA[name_id])." at ".addressID($dSSA[address_id])."</li>";
}
?>
</fieldset>

<fieldset>
<legend>Online File Storage</legend>
<?
$rOFS=@mysql_query("select * from attachment where packet_id = '$packet'");
while($dOFS=mysql_fetch_array($rOFS,MYSQL_ASSOC)){
 echo "<li><a href='$dOFS[url]' target='preview'>$dOFS[instruction_id] $dOFS[packet_id] $dOFS[user_id] $dOFS[server_id] $dOFS[processed]</li>";
}
?>
</fieldset>

<FIELDSET style="padding:0px;">
<div style="background-color:#FFFFFF; padding:0px;" align="center">
<table width="100%"  style="padding:0px; font-size: 11px;"><tr><td align="center">
<? if (!$d[uspsVerify]){?><a href="supernova.php?packet=<?=$d[packet_id]?>" target="preview">!!!Verify Addresses!!!</a><? }else{ ?><img src="http://www.usps.com/common/images/v2header/usps_hm_ci_logo2-159x36x8.gif" ><br>Verified by <? echo $d[uspsVerify]; } ?>
<?
// $deadline needs to be dynamic at some point
$received=strtotime($d[date_received]);
$deadline=$received+432000;
$deadline=date('F jS Y',$deadline);
$days=number_format((time()-$received)/86400,0);
$hours=number_format((time()-$received)/3600,0);
?>
 </td><td align="center">
<? if(!$d[caseVerify]){ ?> <a href="validateCase.php?case=<?=$d[case_no]?>&packet=<?=$d[packet_id]?>&county=<?=$d[circuit_court]?>" target="preview">!!!Verify Case Number!!!</a><? }else{ ?><img src="http://www.courts.state.md.us/newlogosm.gif"><br>Verified by <? echo $d[caseVerify]; }?>
</td><td align="center">
<? if(!$d[qualityControl]){ ?> <a href="entryVerify.php?packet=<?=$d[packet_id]?><? if ($d[service_status] == 'MAIL ONLY'){ echo '&matrix=1';} ?>&frame=no" target="preview">!!!Verify Data Entry!!!</a><? }else{ ?><img src="http://staff.mdwestserve.com/small.logo.gif" height="41" width="41"><br>Verified by <? echo $d[qualityControl]; }?>
</td><td align="center"><div style="font-size:15pt" ><?=$hours?> Hours || <?=$days?> Days<br>Deadline: <?=$deadline?><div></td></tr></table>
</div>
<? if ($d[possibleDuplicate]){?>
<div style="background-color:#ff0000" align="center">Duplicate Warning Level: <?=$d[possibleDuplicate]?></div>
<? } ?>
<table width="100%" style="padding:0px;"><tr>
<?
$dupCheck=dupCheck($d[client_file]);
?>
<td valign="top" <?=$dupCheck?>>
<FIELDSET style="padding:0px;">
<LEGEND ACCESSKEY=C><?=id2attorney($d[attorneys_id]);?> File Data <input type="submit" name="submit" style="background-color:#00FF00; font-weight:bold; width:100px;" value="SAVE"></LEGEND>
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
$q5="SELECT * FROM ps_affidavits WHERE packetID = '$d[packet_id]' order by defendantID";
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
	echo dupList($d[client_file],$d[packet_id]);
}
?>
</td>
<td valign="top">
<FIELDSET style="padding:0px;">
<LEGEND ACCESSKEY=C>Legacy Names</LEGEND>
<table>
<? if($d[name1]){ // legacy ?>
<tr>
<td nowrap>1<input size="20" name="name1" id="name1" value="<?=stripslashes($d[name1])?>" /><input <? if ($d[onAffidavit1]=='checked'){echo "checked";} ?> type="checkbox" value="checked" name="onAffidavit1"></td><? $mult=1;?>
</tr>
<? } ?>
<? if($d[name2]){ // legacy ?>
<tr>
<td nowrap>2<input size="20" name="name2" id="name2" value="<?=stripslashes($d[name2])?>" /><input <? if ($d[onAffidavit2]=='checked'){echo "checked";} ?> type="checkbox" value="checked" name="onAffidavit2"></td><? if ($d[name2]){$mult++;}?>
</tr>
<? }?>
<? if($d[name3]){ // legacy ?>
<tr>
<td nowrap>3<input size="20" name="name3" id="name3" value="<?=stripslashes($d[name3])?>" /><input <? if ($d[onAffidavit3]=='checked'){echo "checked";} ?> type="checkbox" value="checked" name="onAffidavit3"></td><? if ($d[name3]){$mult++;}?>
</tr>
<? } ?>
<? if($d[name4]){ // legacy ?>
<tr>
<td nowrap>4<input size="20" name="name4" id="name4" value="<?=stripslashes($d[name4])?>" /><input <? if ($d[onAffidavit4]=='checked'){echo "checked";} ?> type="checkbox" value="checked" name="onAffidavit4"></td><? if ($d[name4]){$mult++;}?>
</tr>
<? }?>
<? if($d[name5]){ // legacy ?>
<tr>
<td nowrap>5<input size="20" name="name5" id="name5" value="<?=stripslashes($d[name5])?>" /><input <? if ($d[onAffidavit5]=='checked'){echo "checked";} ?> type="checkbox" value="checked" name="onAffidavit5"></td><? if ($d[name5]){$mult++;}?>
</tr>
<? }?>
<? if($d[name6]){ // legacy ?>
<tr>
<td nowrap>6<input size="20" name="name6" id="name6" value="<?=stripslashes($d[name6])?>" /><input <? if ($d[onAffidavit6]=='checked'){echo "checked";} ?> type="checkbox" value="checked" name="onAffidavit6"></td><? if ($d[name6]){$mult++;}?>
</tr>
<? } ?>
</table>
</FIELDSET>
<?
$otdStr=str_replace('portal//var/www/dataFiles/service/orders/','PS_PACKETS/',$d[otd]);
$otdStr=str_replace('data/service/orders/','PS_PACKETS/',$otdStr);
$otdStr=str_replace('portal/','',$otdStr);
//$otdStr=str_replace('mdwestserve.com','alpha.mdwestserve.com',$otdStr);
/*if (!$otdStr){
	$otdStr=$d[otd];
}*/
if (!strpos($otdStr,'mdwestserve.com')){
	$otdStr="http://mdwestserve.com/".$otdStr;
}
if ($d[packet_id] > 3620 && $d[reopenDate] != ''){
	$checkLink="serviceSheet.php?packet=$d[packet_id]&autoPrint=1";
}else{
	$checkLink="oldServiceSheet.php?packet=$d[packet_id]&autoPrint=1";
}
$q5="SELECT DISTINCT serverID from ps_history WHERE packet_id='$d[packet_id]'";
$r5=@mysql_query($q5) or die(mysql_error());
$i=0;
$data5=mysql_num_rows($r5);
if ($data5 > 0){
while ($d5=mysql_fetch_array($r5, MYSQL_ASSOC)){$i++;
$q6="SELECT * FROM ps_history WHERE serverID='$d5[serverID]' and packet_id='$d[packet_id]'";
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
$ri=@mysql_query("SELECT packetID FROM ps_instructions WHERE packetID='$d[packet_id]'") or die (mysql_error());
$di=mysql_fetch_array($ri,MYSQL_ASSOC);
if ($di[packetID]){
	$customBG="style='background-color:green;'";
}else{
	$customBG="style='background-color:red;'";
}
$rc=@mysql_query("SELECT * FROM ps_history WHERE packet_id='$d[packet_id]' AND wizard='CERT MAILING' LIMIT 0,1");
$dc=mysql_fetch_array($rc,MYSQL_ASSOC);
?>
<FIELDSET style="background-color:#FFFF00; padding:0px;">
<LEGEND ACCESSKEY=C>Service Links</LEGEND>
<table style="padding:0px; font-weight:bold; border-collapse:collapse; height:150px !important; font-size:11px;" cellpadding="0" cellspacing="0">
	<tr>
		<td><a href="http://staff.mdwestserve.com/instruction.php?packet=<?=$packet?>" target="preview">Add instruction set</a></td>
	</tr>
	<tr>
		<td><a href="http://staff.mdwestserve.com/otd/minips_pay.php?id=<?=$d[packet_id]?>" target="preview">Payments</a></td>
	</tr>
	<tr>
		<td><a href="http://staff.mdwestserve.com/standardExport.php?packet=<?=$d[packet_id]?>" target="preview">Transfer</a></td>
	</tr>
	<tr>
		<td><a href="historyModify.php?packet=<?=$d[packet_id]?>&form=1" target="preview">History (<?=$server?>)</a></td>
	</tr>
	<tr>
		<td><a href="http://service.mdwestserve.com/customInstructions.php?packet=<?=$d[packet_id]?>" target="preview">Instructions <?=id2attorney($d[attorneys_id])?></a>-<a href="instructMatrix.php?packet=<?=$d[packet_id]?>" <?=$customBG?> target="preview"><small>[CUSTOMIZE]</small></a></td>
	</tr>
	<tr>
		<td><a href="<?=$otdStr?>" target="preview">OTD</a> | <a href="serviceReview.php?packet=<?=$d[packet_id]?>" target="preview">Timeline</a> | <a href="<?=$checkLink?>" target="_blank">Checklist</a></td>
	</tr>
	<tr>
		<td><a href="../photoDisplay.php?packet=<?=$d[packet_id]?>" target="preview"><?$photoCount=photoCount($d[packet_id]); echo $photoCount;?> Photo<? if($photoCount != 1){echo "s";}?></a></td>
	</tr>
	<tr>
		<td><a href="http://staff.mdwestserve.com/penalize.php?packet=<?=$d[eviction_id]?>&svc=OTD&display=1" target="preview">Penalties</a></td>
	</tr>
	<tr>
		<td><a href="mailings.php?OTD=<?=$d[packet_id]?>" target="preview">Mailings</a><? 	if (webservice($d[client_file]) && ($d[attorneys_id] == 1)){ ?> | <a href="http://staff.mdwestserve.com/otd/webservice.php?fileNumber=<?=$d[client_file];?>" target="preview">Webservice Data</a><? }?></td>
	</tr>
	<?
$FC = trim(getPage("http://data.mdwestserve.com/findFC.php?clientFile=$d[client_file]", "MDWS File Copy for Packet $d[packet_id]", '5', ''));
if ($FC != '' && $FC != '1'){
	echo "<tr><td>$FC</td></tr>";
}
$folder=getFolder($d[otd]);
$rfm='/data/service/orders/'.$folder.'/RequestforMediation.pdf';
$trioAff='/data/service/orders/'.$folder.'/TrioAffidavitService.pdf';
if (file_exists($rfm)){
	echo "<tr><td><a href='http://mdwestserve.com/PS_PACKETS/$folder/RequestforMediation.pdf' target='preview'>Request For Mediation</a></td></tr>";
}
if (file_exists($trioAff)){
	echo "<tr><td><a href='http://mdwestserve.com/PS_PACKETS/$folder/TrioAffidavitService.pdf' target='preview'>Trio Aff</a></td></tr>";
}
if ($dc[packet_id]){
	echo "<tr><td><a href='http://staff.mdwestserve.com/otd/serviceCertificate.php?packet=$d[packet_id]' target='preview'>Certificate of Service</a></td></tr>";
}
	?>
	
</table>
</FIELDSET>
</td></tr></table>
<table style="display:<? if ($_GET[packet]){ echo "block";}else{ echo "none"; }?>; padding:0px;" id="notes" width="100%"><tr><td colspan="2"><fieldset><legend>Notes</legend>
<iframe height="200px" width="700px"  frameborder="0" src="http://staff.mdwestserve.com/notes.php?packet=<?=$packet?>"></iframe></fieldset></td></tr></table>
<table style="display:none;" id="track" width="100%"><tr><td align='center'>
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
$r92=@mysql_query("select * from docuTrack where packet = '$d[packet_id]' order by trackID desc");
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

<? if(!$d[address1]){ ?>
<table width="100%" style="display:block;" id="addresses">
<? }else{ ?>
<table width="100%" style="display:none;" id="addresses">
<? } ?>
<?
$add1=strtoupper($d[address1].', '.$d[city1].', '.$d[state1].' '.$d[zip1]);
$add1a=strtoupper($d[address1a].', '.$d[city1a].', '.$d[state1a].' '.$d[zip1a]);
$add1b=strtoupper($d[address1b].', '.$d[city1b].', '.$d[state1b].' '.$d[zip1b]);
$add1c=strtoupper($d[address1c].', '.$d[city1c].', '.$d[state1c].' '.$d[zip1c]);
$add1d=strtoupper($d[address1d].', '.$d[city1d].', '.$d[state1d].' '.$d[zip1d]);
$add1e=strtoupper($d[address1e].', '.$d[city1e].', '.$d[state1e].' '.$d[zip1e]);
?>
<tr><td>
<? if($d[address1]){ ?>
<FIELDSET>
<LEGEND class="a" ACCESSKEY=C><a href="http://staff.mdwestserve.com/dispatcher.php?aptsut=&address=<?=$d[address1]?>&city=<?=$d[city1]?>&state=<?=$d[state1]?>&miles=5" target="_Blank"><img src="http://staff.mdwestserve.com/small-usps-logo.jpg" border="0"></a>&nbsp;<?=getVerify($add1);?>&nbsp;<?=id2name($d[server_id]);?><br><input name="addressType" size="55" style="font-size:10px; background-color:CCFFCC;" value="<?=$d[addressType]?>"></LEGEND>
<table>
<tr>
<td><input id="address" name="address" size="30" value="<?=$d[address1]?>" /></td>
</tr>
<tr>
<td><input size="20" name="city" id="city" value="<?=$d[city1]?>" /><input size="1" name="state" id="state" value="<?=$d[state1]?>" /><input size="4" name="zip"id="zip" value="<?=$d[zip1]?>" /></td>
</tr>
</table>    
</FIELDSET>
<? }?>
</td><td>
<? if($d[address1a]){ ?>
<FIELDSET>
<LEGEND class="a" ACCESSKEY=C><a href="http://staff.mdwestserve.com/dispatcher.php?aptsut=&address=<?=str_replace('#','',$d[address1a])?>&city=<?=$d[city1a]?>&state=<?=$d[state1a]?>&miles=5" target="_Blank"><img src="http://staff.mdwestserve.com/small-usps-logo.jpg" border="0"></a>&nbsp;<?=getVerify($add1a);?>&nbsp;<?=id2name($d[server_ida]);?><br><input name="addressTypea" size="55" style="font-size:10px; background-color:CCFFCC;" value="<?=$d[addressTypea]?>"></LEGEND>
<table>
<tr>
<td><input name="addressa" id="addressa" size="30" value="<?=$d[address1a]?>" /></td>
</tr>
<tr>
<td><input name="citya" id="citya" size="20" value="<?=$d[city1a]?>" /><input size="1" name="statea" id="statea" value="<?=$d[state1a]?>" /><input size="4" name="zipa" id="zipa" value="<?=$d[zip1a]?>" /></td>
</tr>
</table>    
</FIELDSET>
<? } ?>
</td><td>
<? if($d[address1b]){ ?>
<FIELDSET>
<LEGEND class="a" ACCESSKEY=C><a href="http://staff.mdwestserve.com/dispatcher.php?aptsut=&address=<?=$d[address1b]?>&city=<?=$d[city1b]?>&state=<?=$d[state1b]?>&miles=5" target="_Blank"><img src="http://staff.mdwestserve.com/small-usps-logo.jpg" border="0"></a>&nbsp;<?=getVerify($add1b);?>&nbsp;<?=id2name($d[server_idb]);?><br><input name="addressTypeb" size="55" style="font-size:10px; background-color:CCFFCC;" value="<?=$d[addressTypeb]?>"></LEGEND>
<table>
<tr>
<td><input name="addressb" id="addressb" size="30" value="<?=$d[address1b]?>" /></td>
</tr>
<tr>
<td><input name="cityb" id="cityb" size="20" value="<?=$d[city1b]?>" /><input size="1" name="stateb" id="stateb" value="<?=$d[state1b]?>" /><input size="4" name="zipb" id="zipb" value="<?=$d[zip1b]?>" /></td>
</tr>
</table>    
</FIELDSET>
<? } ?>
</td></tr>

<tr><td>
<? if($d[address1c]){ ?>
<FIELDSET>
<LEGEND class="a" ACCESSKEY=C><a href="http://staff.mdwestserve.com/dispatcher.php?aptsut=&address=<?=$d[address1c]?>&city=<?=$d[city1c]?>&state=<?=$d[state1c]?>&miles=5" target="_Blank"><img src="http://staff.mdwestserve.com/small-usps-logo.jpg" border="0"></a>&nbsp;<?=getVerify($add1c);?>&nbsp;<?=id2name($d[server_idc]);?><br><input name="addressTypec" size="55" style="font-size:10px; background-color:CCFFCC;" value="<?=$d[addressTypec]?>"></LEGEND>
<table>
<tr>
<td><input name="addressc" id="addressc" value="<?=$d[address1c]?>" size="30" /></td>
</tr>
<tr>
<td><input name="cityc" id="cityc" size="20" value="<?=$d[city1c]?>" /><input size="1" name="statec" id="statec" value="<?=$d[state1c]?>" /><input size="4" name="zipc" id="zipc" value="<?=$d[zip1c]?>" /></td>
</tr>
</table>    
</FIELDSET>
<? }?>
</td><td>
<? if($d[address1d]){ ?>
<FIELDSET>
<LEGEND class="a" ACCESSKEY=C><a href="http://staff.mdwestserve.com/dispatcher.php?aptsut=&address=<?=$d[address1d]?>&city=<?=$d[city1d]?>&state=<?=$d[state1d]?>&miles=5" target="_Blank"><img src="http://staff.mdwestserve.com/small-usps-logo.jpg" border="0"></a>&nbsp;<?=getVerify($add1d);?>&nbsp;<?=id2name($d[server_idd]);?><br><input name="addressTyped" size="55" style="font-size:10px; background-color:CCFFCC;" value="<?=$d[addressTyped]?>"></LEGEND>
<table>
<tr>
<td><input name="addressd" id="addressd" size="30" value="<?=$d[address1d]?>" /></td>
</tr>
<tr>
<td><input name="cityd" id="cityd" size="20" value="<?=$d[city1d]?>" /><input size="1" name="stated" id="stated" value="<?=$d[state1d]?>" /><input size="4" name="zipd" id="zipd" value="<?=$d[zip1d]?>" /></td>
</tr>
</table>    
</FIELDSET>
<? } ?>
</td><td>
<? if($d[address1e]){ ?>
<FIELDSET>
<LEGEND class="a" ACCESSKEY=C><a href="http://staff.mdwestserve.com/dispatcher.php?aptsut=&address=<?=$d[address1e]?>&city=<?=$d[city1e]?>&state=<?=$d[state1e]?>&miles=5" target="_Blank"><img src="http://staff.mdwestserve.com/small-usps-logo.jpg" border="0"></a>&nbsp;<?=getVerify($add1e);?>&nbsp;<?=id2name($d[server_ide]);?><br><input name="addressTypee" size="55" style="font-size:10px; background-color:CCFFCC;" value="<?=$d[addressTypee]?>"></LEGEND>
<table>
<tr>
<td><input name="addresse" id="addresse" size="30" value="<?=$d[address1e]?>" /></td>
</tr>
<tr>
<td><input name="citye" id="citye" size="20" value="<?=$d[city1e]?>" /><input size="1" name="statee" id="statee" value="<?=$d[state1e]?>" /><input size="4" name="zipe" id="zipe" value="<?=$d[zip1e]?>" /></td>
</tr>
</table>    
</FIELDSET>
<? } ?>
</td></tr>
</table>



<strong>
	<div align="center" style="background-color:#FFFF00">
    	<a onClick="hideshow(document.getElementById('track'))">Tracking</a> &curren; 
    	<a onClick="hideshow(document.getElementById('addresses'))">Legacy Addresses</a> &curren; 
    	<a onClick="hideshow(document.getElementById('pobox'))">Legacy Mail Only</a> &curren; 
    	<a onClick="hideshow(document.getElementById('status'))">Status</a> &curren; 
        <a onClick="hideshow(document.getElementById('servers'))">Legacy Servers</a> &curren; 
        <a onClick="hideshow(document.getElementById('notes'))">Notes</a> &curren; 
    </div>
</strong>
<table width="100%" id="pobox" style="display:none;"><tr><td>
<table width="100%">
<tr>
<td>Mail Only</td>
<td><input name="pobox" value="<?=$d[pobox]?>" /></td>
</tr>
<tr>
<td>City</td>
<td><input name="pocity" value="<?=$d[pocity]?>" /></td>
</tr>
<tr>
<td>State</td>
<td><input name="postate" value="<?=$d[postate]?>" /></td>
</tr>
<tr>
<td>ZIP</td>
<td><input name="pozip" value="<?=$d[pozip]?>" /></td>
</tr>
</table>
</td><td>
<table width="100%">
<tr>
<td>Mail Only 2</td>
<td><input name="pobox2" value="<?=$d[pobox2]?>" /></td>
</tr>
<tr>
<td>City 2</td>
<td><input name="pocity2" value="<?=$d[pocity2]?>" /></td>
</tr>
<tr>
<td>State 2</td>
<td><input name="postate2" value="<?=$d[postate2]?>" /></td>
</tr>
<tr>
<td>ZIP 2</td>
<td><input name="pozip2" value="<?=$d[pozip2]?>" /></td>
</tr>
</table>    
</td></tr></table>

<table width="100%" id="status" style="display:none; font-size:11px; padding:0px;">
<input type="hidden" name="packet_id" value="<?=$d[packet_id]?>" />
<tr>
<? if ($_GET[packet]){?>
<td align="center" width="25%">Client Status<br><select name="status"><option><?=$d[status]?></option>
<?
$q1="SELECT DISTINCT status from ps_packets WHERE status <> ''";
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
$q1="SELECT DISTINCT service_status from ps_packets WHERE service_status <> ''";
$r1=@mysql_query($q1) or die("Query: $q1<br>".mysql_error());
while ($d1=mysql_fetch_array($r1, MYSQL_ASSOC)){
?>
<option><?=$d1[service_status]?></option>
<? } ?>
<option value=""></option>
</select></td>
<td align="center" width="25%">Filing Status<br><select name="filing_status"><option><?=$d[filing_status]?></option>
<?
$q1="SELECT DISTINCT filing_status from ps_packets WHERE filing_status <> '' AND filing_status <> 'REOPENED'";
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
$q2="SELECT DISTINCT process_status from ps_packets WHERE process_status <> ''";
$r2=@mysql_query($q2) or die("Query: $q2<br>".mysql_error());
while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)){
?>
<option><?=$d2[process_status]?></option>
<? } ?>
<option value=""></option>
</select></td>
<td align="center" width="25%"><table><tr><td>Affidavit Status<br><select name="affidavit_status"><option><?=$d[affidavit_status]?></option>
<?
$q3="SELECT DISTINCT affidavit_status from ps_packets WHERE affidavit_status <> ''";
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
$q3="SELECT DISTINCT affidavit_status2 from ps_packets WHERE affidavit_status2 <> '' AND affidavit_status2 <> 'REOPENED' AND affidavit_status2 <> 'AWAITING OUT OF STATE AFFIDAVITS' AND affidavit_status2 <> 'AWAITING OUT OF STATE SERVICE' AND affidavit_status2 <> 'AWAITING MAILING'";
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
$q4="SELECT DISTINCT photoStatus from ps_packets WHERE photoStatus <> ''";
$r4=@mysql_query($q4) or die("Query: $q4<br>".mysql_error());
while ($d4=mysql_fetch_array($r4, MYSQL_ASSOC)){
?>
<option><?=$d4[photoStatus]?></option>
<? } ?>
<option value=""></option>
</select></td></tr><tr>
<td align="center" width="25%">Affidavit Type<br><select name="affidavitType"><option><?=$d[affidavitType]?></option>
<?
$q4="SELECT DISTINCT affidavitType from ps_packets WHERE affidavitType <> ''";
$r4=@mysql_query($q4) or die("Query: $q4<br>".mysql_error());
while ($d4=mysql_fetch_array($r4, MYSQL_ASSOC)){
?>
<option><?=$d4[affidavitType]?></option>
<? } ?>
<option value=""></option>
</select></td>
<td align="center" width="25%">Mail Status<br><select name="mail_status"><option><?=$d[mail_status]?></option>
<?
$q4="SELECT DISTINCT mail_status from ps_packets WHERE mail_status <> ''";
$r4=@mysql_query($q4) or die("Query: $q4<br>".mysql_error());
while ($d4=mysql_fetch_array($r4, MYSQL_ASSOC)){
?>
<option><?=$d4[mail_status]?></option>
<? } ?>
<option value=""></option>
</select></td>
<td align='center'><? if ($d[process_status] != 'CANCELLED'){ ?><input style='font-size:11px;' name='cancelRef' value='Client Reference Email' onclick="value=''" size='25'><br><input style='background-color:pink; font-size: 10.5px;' type='button' name='cancel' value='CANCEL' onclick="confirmation(cancelRef.value);"><? }?></td>
</tr>
<tr><td align="center" colspan='3'><table align='center' style='font-size:12px;' width='100%'><tr><td align="center" width='25%'>
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
</td></tr></table></td></tr>
<tr><td align="center" colspan='3'><table align='center' style='font-size:12px;' width='100%'><tr><td align="center">
Request Close<br>
<input type="checkbox" name="request_close" <? if ($d[request_close] == 'YES'){ echo "checked";} ?> value="YES">
</td>
<td align="center">
Request Close 'A'<br>
<input type="checkbox" name="request_closea" <? if ($d[request_closea] == 'YES'){ echo "checked";} ?> value="YES">
</td>
<td align="center">
Request Close 'B'<br>
<input type="checkbox" name="request_closeb" <? if ($d[request_closeb] == 'YES'){ echo "checked";} ?> value="YES">
</td>
<td align="center">
Request Close 'C'<br>
<input type="checkbox" name="request_closec" <? if ($d[request_closec] == 'YES'){ echo "checked";} ?> value="YES">
</td>
<td align="center">
Request Close 'D'<br>
<input type="checkbox" name="request_closed" <? if ($d[request_closed] == 'YES'){ echo "checked";} ?> value="YES">
</td>
<td align="center">
Request Close 'E'<br>
<input type="checkbox" name="request_closee" <? if ($d[request_closee] == 'YES'){ echo "checked";} ?> value="YES">
</td>
</tr></table></td></tr>
<tr>
<td align="center">
<? if ($d[server_id]){ echo id2name($d[server_id]);}else{echo "Server 1";} ?> Complete<br>
<input name="serveComplete" size="1"  value="<?=$d[serveComplete]?>">
</td>
<td align="center">
<? if ($d[server_ida]){ echo id2name($d[server_ida]);}else{echo "Server 2";} ?> Complete<br>
<input name="serveCompletea" size="1"  value="<?=$d[serveCompletea]?>">
</td>
<td align="center">
<? if ($d[server_idb]){ echo id2name($d[server_idb]);}else{echo "Server 3";} ?> Complete<br>
<input name="serveCompleteb" size="1"  value="<?=$d[serveCompleteb]?>">
</td>
</tr>
<tr>
<td align="center">
<? if ($d[server_idc]){ echo id2name($d[server-idc]);}else{echo "Server 4";} ?> Complete<br>
<input name="serveCompletec" size="1"  value="<?=$d[serveCompletec]?>">
</td>
<td align="center">
<? if ($d[server_idd]){ echo id2name($d[server_idd]);}else{echo "Server 5";} ?> Complete<br>
<input name="serveCompleted" size="1"  value="<?=$d[serveCompleted]?>">
</td>
<td align="center">
<? if ($d[server_ide]){ echo id2name($d[server_ide]);}else{echo "Server 6";} ?> Complete<br>
<input name="serveCompletee" size="1"  value="<?=$d[serveCompletee]?>">
</td>
</tr>
</table>





<table width="100%"  id="servers" style="display:none;">
<tr>
<td valign="top">
<FIELDSET>
<LEGEND ACCESSKEY=C>Process Server #<?=$d[server_id]?><? if ($d[svrPrint] > 0){echo " - <small>PRINTED</small>";}?></LEGEND>
<?
mysql_select_db("core");
$r2=@mysql_query("select * from ps_users where id = '$d[server_id]'");
$d2=mysql_fetch_array($r2, MYSQL_ASSOC);
?>
<table <? if ($d[svrPrint] > 0){echo "bgcolor='#FFFFFF'";}?>>
<tr>
<td><?=$d2[company]?></td>
</tr>
<tr>
<td><?=$d2[name]?></td>
</tr>
<tr>
<td><?=$d2[phone]?></td>
</tr>
<tr>
<td><?=$d2[address]?><br><?=$d2[city]?> <?=$d2[state]?> <?=$d2[zip]?></td>
</tr>
</table>    
</FIELDSET>
</td>
<? if ($d[server_ida]){ ?>
<td valign="top">
<FIELDSET>
<LEGEND ACCESSKEY=C>Process Server "a" #<?=$d[server_ida]?><? if ($d[svrPrinta] > 0){echo " - <small>PRINTED</small>";}?></LEGEND>
<?
mysql_select_db("core");
$r2=@mysql_query("select * from ps_users where id = '$d[server_ida]'");
$d2=mysql_fetch_array($r2, MYSQL_ASSOC);
?>
<table <? if ($d[svrPrinta] > 0){echo "bgcolor='#FFFFFF'";}?>>
<tr>
<td><?=$d2[company]?></td>
</tr>
<tr>
<td><?=$d2[name]?></td>
</tr>
<tr>
<td><?=$d2[phone]?></td>
</tr>
<tr>
<td><?=$d2[address]?><br><?=$d2[city]?> <?=$d2[state]?> <?=$d2[zip]?></td>
</tr>
</table>    
</FIELDSET>
</td>
<? }?>
<? if ($d[server_idb]){ ?>
<td valign="top">
<FIELDSET>
<LEGEND ACCESSKEY=C>Process Server "b" #<?=$d[server_idb]?><? if ($d[svrPrintb] > 0){echo " - <small>PRINTED</small>";}?></LEGEND>
<?
mysql_select_db("core");
$r2=@mysql_query("select * from ps_users where id = '$d[server_idb]'");
$d2=mysql_fetch_array($r2, MYSQL_ASSOC);
?>
<table <? if ($d[svrPrintb] > 0){echo "bgcolor='#FFFFFF'";}?>>
<tr>
<td><?=$d2[company]?></td>
</tr>
<tr>
<td><?=$d2[name]?></td>
</tr>
<tr>
<td><?=$d2[phone]?></td>
</tr>
<tr>
<td><?=$d2[address]?><br><?=$d2[city]?> <?=$d2[state]?> <?=$d2[zip]?></td>
</tr>
</table>    
</FIELDSET>
</td>
<? }?>
</tr>
<tr>
<? if ($d[server_idc]){ ?>
<td valign="top">
<FIELDSET>
<LEGEND ACCESSKEY=C>Process Server "c" #<?=$d[server_idc]?><? if ($d[svrPrintc] > 0){echo " - <small>PRINTED</small>";}?></LEGEND>
<?
mysql_select_db("core");
$r2=@mysql_query("select * from ps_users where id = '$d[server_idc]'");
$d2=mysql_fetch_array($r2, MYSQL_ASSOC);
?>
<table <? if ($d[svrPrintc] > 0){echo "bgcolor='#FFFFFF'";}?>>
<tr>
<td><?=$d2[company]?></td>
</tr>
<tr>
<td><?=$d2[name]?></td>
</tr>
<tr>
<td><?=$d2[phone]?></td>
</tr>
<tr>
<td><?=$d2[address]?><br><?=$d2[city]?> <?=$d2[state]?> <?=$d2[zip]?></td>
</tr>
</table>    
</FIELDSET>
</td>
<? }?>
<? if ($d[server_idd]){ ?>
<td valign="top">
<FIELDSET>
<LEGEND ACCESSKEY=C>Process Server "d" #<?=$d[server_idd]?><? if ($d[svrPrintd] > 0){echo " - <small>PRINTED</small>";}?></LEGEND>
<?
mysql_select_db("core");
$r2=@mysql_query("select * from ps_users where id = '$d[server_idd]'");
$d2=mysql_fetch_array($r2, MYSQL_ASSOC);
?>
<table <? if ($d[svrPrintd] > 0){echo "bgcolor='#FFFFFF'";}?>>
<tr>
<td><?=$d2[company]?></td>
</tr>
<tr>
<td><?=$d2[name]?></td>
</tr>
<tr>
<td><?=$d2[phone]?></td>
</tr>
<tr>
<td><?=$d2[address]?><br><?=$d2[city]?> <?=$d2[state]?> <?=$d2[zip]?></td>
</tr>
</table>    
</FIELDSET>
</td>
<? }?>
<? if ($d[server_ide]){ ?>
<td valign="top">
<FIELDSET>
<LEGEND ACCESSKEY=C>Process Server "e" #<?=$d[server_ide]?><? if ($d[svrPrinte] > 0){echo " - <small>PRINTED</small>";}?></LEGEND>
<?
mysql_select_db("core");
$r2=@mysql_query("select * from ps_users where id = '$d[server_ide]'");
$d2=mysql_fetch_array($r2, MYSQL_ASSOC);
?>
<table <? if ($d[svrPrinte] > 0){echo "bgcolor='#FFFFFF'";}?>>
<tr>
<td><?=$d2[company]?></td>
</tr>
<tr>
<td><?=$d2[name]?></td>
</tr>
<tr>
<td><?=$d2[phone]?></td>
</tr>
<tr>
<td><?=$d2[address]?><br><?=$d2[city]?> <?=$d2[state]?> <?=$d2[zip]?></td>
</tr>
</table>    
</FIELDSET>
</td>
<? }?>
<td valign="top">
</td></tr><tr><td>
<?
$q7= "select * from ps_users where contract = 'YES' order by id ASC";
$r7=@mysql_query($q7) or die("Query: $q7<br>".mysql_error());
while ($d7=mysql_fetch_array($r7, MYSQL_ASSOC)) {
	$sList .= "<option value='$d7[id]'>";
	if ($d7[company]){ $sList .= "$d7[company], $d7[name]" ;}else{ $sList .= "$d7[name]" ;}
	$sList .= "</option>";
} ?>
<select name="server1"><? if (!$d[server_id]){ ?><option value="">Select Server </option><? }else{ ?><option value="<?=$d[server_id]?>"><?=id2name($d[server_id]);?> (Server)</option><? } ?>
<?=$sList?>
<option value=""></option>
</select>
<?
foreach(range('a','e') as $letter){
?>
<br />
<select name="server1<?=$letter?>"><? if (!$d["server_id$letter"]){ ?><option value="">Select Server '<?=strtoupper($letter)?>'</option><? }else{ ?><option value="<?=$d["server_id$letter"]?>"><?=id2name($d["server_id$letter"]);?> (Server <?=strtoupper($letter)?>)</option><? } ?>
<?=$sList?>
<option value=""></option>
</select>
<? } ?>
</td>
</tr></table>
</FIELDSET>
<? if ($_GET[start]){
	$src=str_replace('portal//var/www/dataFiles/service/orders/','PS_PACKETS/',$d[otd]);
	$src=str_replace('data/service/orders/','PS_PACKETS/',$src);
	$src=str_replace('portal/','',$src);

?>
<iframe height="285px" width="740px" name="QCOTD" src="<?=$src?>"></iframe>
<? } ?>
</td><td valign="top" width="10%">
<?
	$getFolder=getFolder($d[otd]);
	$trioAff='/data/service/orders/'.$getFolder.'/TrioAffidavitService.pdf';
	//if BGW file with client service affidavit, pop open affidavit
	if ($d[status] == 'NEW' && $d[process_status] != 'CANCELLED' && $d[process_status] != 'DUPLICATE' && $d[process_status] != 'DAMAGED PDF' && $d[process_status] != 'DUPLICATE/DIFF-PDF' && $d[attorneys_id] == 70 && file_exists($trioAff)){
		$affPath='http://mdwestserve.com/PS_PACKETS/'.$getFolder.'/TrioAffidavitService.pdf';
		echo "<script>window.open('$affPath','Trio Service Affidavit','width=600, height=800')</script>";
	}
	//only run rfmMerge if file is from BGW, is new, has an existing Request For Mediation uploaded in the same folder as the OTD, and has not already unsuccessfully tried to merge already.
	$rfm='/data/service/orders/'.$getFolder.'/RequestforMediation.pdf';
	if ($d[status] == 'NEW' && $d[process_status] != 'CANCELLED' && $d[process_status] != 'DUPLICATE' && $d[process_status] != 'DAMAGED PDF' && $d[process_status] != 'DUPLICATE/DIFF-PDF' && $d[attorneys_id] == 70 && file_exists($rfm) && ($d[prevOTD] == '' || $d[prevOTD] != $d[otd])){
		$src= "http://staff.mdwestserve.com/temp/rfmMerge.php?packet=$d[packet_id]";
	}elseif($d[status]=="NEW" || $_GET[otd] == '1'){
		$src=str_replace('portal//var/www/dataFiles/service/orders/','PS_PACKETS/',$d[otd]);
		$src=str_replace('data/service/orders/','PS_PACKETS/',$src);
		$src=str_replace('portal/','',$src);
		
		//$src=str_replace('mdwestserve.com','alpha.mdwestserve.com',$src);
		/*if (!$src){
			$src=$d[otd];
		}*/
	}elseif(!$d[uspsVerify]){
		$src="supernova.php?packet=$d[packet_id]";
	}elseif(!$d[caseVerify] && $d[case_no]){
		$src="validateCase.php?case=$d[case_no]&packet=$d[packet_id]&county=$d[circuit_court]";
	}elseif(!$d[qualityControl]){
		if ($d[service_status] == 'MAIL ONLY'){
			$src="entryVerify.php?packet=$d[packet_id]&frame=no&matrix=1";
		}else{
			$src="entryVerify.php?packet=$d[packet_id]&frame=no";
		}
	}elseif(!$d[caseVerify]){
		$src="validateCase.php?case=$d[case_no]&packet=$d[packet_id]&county=$d[circuit_court]";
	}elseif($d[process_status] == "CANCELLED" || $d[filing_status]=="FILED WITH COURT" || $d[filing_status]=="FILED WITH COURT - FBS"){
		$src="http://staff.mdwestserve.com/otd/minips_pay.php?id=$d[packet_id]";
	}else{
		$src="serviceReview.php?packet=$d[packet_id]"; 
	}

	$explode = explode("/",$d[otd]);
	$explodeCount=count($explode)-1;
?>
<table style="padding:0px;" width="100%">
	<tr>
		<td style='font-size:12px;' valign="bottom"><input name="pages" value="<?=$d[pages]?>" size="3"> # OTD Pages <?=testLink($d[otd])?> <b style="background-color:#FFFF00; padding:0px;"><?=trim($explode["$explodeCount"])?></b></td>
		<td style='font-size:12px;' valign="bottom"><input name="mailWeight" size="4" value="<?=$d[mailWeight]?>"> Mail Weight</td></form>
		<form action="http://staff.mdwestserve.com/temp/pageRemove.php"><td valign="bottom"><input type="hidden" name="id" value="<?=$d[packet_id]?>"><input type="hidden" name="type" value="OTD"><? if ($_GET[packet]){ ?><input type="hidden" name="packet" value="<?=$d[packet_id]?>"><? } ?><input name="skip" onclick="value=''" value="Remove Page #"> <input type="submit" value="GO!"></td></form>
	</tr>
	<tr>
		<td colspan="3" valign="bottom">
		<input name="otd" value="<?=$d[otd]?>" size="80"> <? if($d[status]=="NEW"){ echo "<a href='renameOTD.php?packet=$d[packet_id]&test=1'>FIX OTD LINK</a>";}else{echo "<a href='renameOTD.php?packet=$d[packet_id]'>FIX</a>";} ?><?=searchList($d[packet_id]);?>
		</td>
	</tr>
</table>
<? if (webservice($d[client_file]) && ($d[attorneys_id] == 1)){
		echo "<table align='center'><tr><td>";
		include "http://staff.mdwestserve.com/otd/webservice.php?fileNumber=$d[client_file]";
		echo "</td></tr></table>";
	}
?>
<iframe height="622px" width="900px" name="preview" id="preview" src="<?=$src?>" ></iframe>
</td></tr></table>

<? } // end good packet form?>
<script>document.title='<?=$_GET[packet]?>|<?=$d[status]?>|<?=$d[service_status]?>|<?=$d[process_status]?>|<?=$d[affidavit_status]?>|<?=$d[filing_status]?>|<?=$d[affidavit_status2]?>'</script>
<? 
if ($_GET[type]){
	echo $_GET[type];
}


mysql_close();
$headers = apache_request_headers();
$lb = $headers["X-Forwarded-Host"];
$mirror = $_SERVER['HTTP_HOST'];
?>
<center style="padding:0px;">Mysql Closed on <?=$mirror;?> from <?=$lb;?></center>
</body>
</html>