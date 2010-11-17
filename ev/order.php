<?php
include 'common.php';


include 'lock.php';
?>
<script>
function prompter(packetID,newDate,oldDate){
	var reply = prompt("Please enter your reason for updating the Est. Close Date", "")
	if (reply == null){
		alert("That is not a valid reason")
		window.location="http://staff.mdwestserve.com/ev/order.php?packet="+packetID;
	}
	else{
		window.location="http://staff.mdwestserve.com/ev/tlEntry.php?packet="+packetID+"&entry="+reply+"&newDate="+newDate+"&oldDate="+oldDate,"EV Timeline Entry";
	}
}
</script>
<?
if ($_GET[packet]){
	opLog($_COOKIE[psdata][name]." Loaded Order #$_GET[packet]");
}else{
	opLog($_COOKIE[psdata][name]." Loaded Data Entry");
}

function webservice($clientFile){
	$select_query = "Select create_id From defendants  Where filenumber = '$clientFile'";
	$result = mysql_query($select_query);
	$data = mysql_fetch_array($result,MYSQL_ASSOC);
	if ($data[create_id]) {
		return true;
	}
}

function dupCheck($string){
	$r=@mysql_query("select * from evictionPackets where client_file LIKE '%$string%'");
	$c=mysql_num_rows($r);
	if ($c == 1){
		$return="class='single'";
		//$return[1]=$c;
	}else{
		$return="class='duplicate'";
		//$return[1]=$c;
	}
	return $return;
}

function stripHours($date){
	$hours = explode(':',$date);
	return $hours[0];
}

function colorCode($hours,$status){
	if ($status == "CANCELLED" || $status == "FILED WITH COURT" || $status == "FILED WITH COURT - FBS"){
		return "00FF00";
	}else{
		if ($hours <= 250){ return "00FF00"; }
		if ($hours > 250 && $hours <= 300){ return "ffFF00"; }
		if ($hours > 300){ return "ff0000"; }
	}
	return "FFFFFF";
}

function dbCleaner($str){
	$str = trim($str);
	$str = addslashes($str);
	$str = strtoupper($str);
	//$str = ucwords($str);
	return $str;
}

function mkCC($str){
	$q="SELECT * FROM county";
	$r=@mysql_query($q);
	$option = '<option>'.$str.'</option>';
	while($d=mysql_fetch_array($r, MYSQL_ASSOC)){;
		$option .= '<option>'.$d[name].'</option>';
	}
	return $option;
}

function photoCount($packet){
	$count=0;
	$q="SELECT photo1a, photo1b, photo1c, name1, name2, name3, name4, name5, name6 FROM evictionPackets WHERE eviction_id='$packet'";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	$i=0;
	while ($i < 6){$i++;
		if ($d["name$i"]){
			foreach(range('a','m') as $letter){
				$current="photo".$i.$letter;
				if($d["$current"] != ''){$count++;}
			}
		}
	}
	return $count;
}

function id2email($id){
	$q=@mysql_query("SELECT email from ps_users where id='$id'") or die(mysql_error());
	$d=mysql_fetch_array($q, MYSQL_ASSOC);
	return $d[email];
}

function attorneyCustomLang($att,$str){
	$r=@mysql_query("SELECT * FROM ps_str_replace where attorneys_id = '$att'");
	while ($d=mysql_fetch_array($r, MYSQL_ASSOC)){
		if ($d['str_search'] && $d['str_replace'] && $str && $att){
			$str = str_replace($d['str_search'], strtoupper($d['str_replace']), $str);
			$str = str_replace(strtoupper($d['str_search']), strtoupper($d['str_replace']), $str);
			//echo "<script>alert('Replacing ".strtoupper($d['str_search'])." with ".strtoupper($d['str_replace']).".');< /script>";
		}
	}
	return $str;
}
function historyList($id,$attorneys_id){
		$qn="SELECT * FROM evictionHistory WHERE eviction_id = '$id' order by defendant_id, history_id ASC";
		$rn=@mysql_query($qn) or die ("Query: $qn<br>".mysql_error());
		$counter=0;
		while ($dn=mysql_fetch_array($rn, MYSQL_ASSOC)){$counter++;
			$action_str=str_replace('<LI>','',strtoupper($dn[action_str]));
			$action_str=str_replace('</LI>','',$action_str);
				$list .=  "<hr><li>#$dn[history_id] : ".id2server($dn[serverID]).' '.$dn[wizard].'<br>'.stripslashes(attorneyCustomLang($attorneys_id,$action_str));
				if ($dn[wizard] == 'BORROWER' || $dn[wizard] == 'NOT BORROWER'){
					$list .=  '<br>'.attorneyCustomLang($attorneys_id,$dn[residentDesc]);
				}
				$list .= "</li>";
		}
		return $list;
}

function attachmentList($packet,$type){
	$list = "<fieldset><legend>Electronic File Storage</legend>";
	mysql_select_db('core');
	if ($type == 'EV'){
		$packet='EV'.$packet;
	}
	$r=@mysql_query("select * from ps_affidavits where packetID = '$packet' order by defendantID");
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		$affidavit=$d[affidavit];
		$affidavit=str_replace('http://mdwestserve.com/ps/affidavits/','http://mdwestserve.com/affidavits/',$affidavit);
		$list .= "<li><a href='$affidavit'>$d[method]</a></li>";
	}
	$list .= "</fieldset>";
	return $list;
}

function exportStatus($a,$b,$p){
	if ($a && $b){ return 'REQUEST BY '.id2name($a).' APPROVED BY '.id2name($b); }
	if ($a && !$b){
		$r=@mysql_query("SELECT * FROM evictionHistory WHERE eviction_id='$p'");
		$d=mysql_num_rows($r);
		if ($d){ echo "<script>alert('!! Export Warning !! This eviction has $d history items.');</script>"; }
		return 'EXPORT APPROVAL REQUESTED BY '.id2name($a);
	}
	if (!$a){ return 'ACTIVE DATABASE'; }
}

function search($search,$string){
	$pos = strpos($string, $search);
	if ($pos === false) {
		$pass = "";
	} else {
		$pass = $string;
	}
	return $pass;
}

function getClose($packet){
	$r=@mysql_query("select estFileDate from evictionPackets where eviction_id = '$packet'");
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	return $d[estFileDate];
}

function getTime($packet,$event){
	$r=@mysql_query("select timeline from evictionPackets where eviction_id = '$packet'");
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	$explode = explode('<br>',$d[timeline]);
	foreach ($explode as $key => $value) {
		if (search($event,$value)){
			$search = search($event,$value);
		}
	}
	$array = array();
	if ($search){
		$array[css] = "done";
	}else{
		$array[css] = "pending";
	}
	$array[event] = $event;
	$array[eDate] = substr($search,0,17);
	return $array;
}


if ($_POST[sendToClient]){
	$today=date('Y-m-d');
	@mysql_query("UPDATE evictionPackets SET fileDate='$today', estFileDate='$today', filing_status='SEND TO CLIENT' WHERE eviction_id='$_GET[packet]'");
	timeline($_GET[packet],$_COOKIE[psdata][name]." Marked File Send to Client");
}

if ($_POST[submit]){
if ($_GET[packet]){
ev_timeline($_GET[packet],$_COOKIE[psdata][name]." Updated Order");
$q=@mysql_query("SELECT * from evictionPackets WHERE eviction_id='$_POST[eviction_id]'") or die (mysql_error());
$d=mysql_fetch_array($q, MYSQL_ASSOC);
if ($_POST[estFileDate] != $d[estFileDate]){
	/*//if estFileDate has been changed, send email to service@mdwestserve.com
	$to = "Service Updates <mdwestserve@gmail.com>";
	$subject = "Estimated File Date Updated for Eviction $d[eviction_id] ($d[client_file]), From $d[estFileDate] To $_POST[estFileDate]";
	$headers  = "MIME-Version: 1.0 \n";
	$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
	$headers .= "From: ".$_COOKIE[psdata][name]." <".$_COOKIE[psdata][email]."> \n";
	$body="Service for Eviction $d[eviction_id] (<strong>$d[client_file]</strong>) has been modified by ".$_COOKIE[psdata][name].", Estimated File Date was changed From $d[estFileDate] To $_POST[estFileDate].";
	$body .= "<br><br>(410) 828-4568<br>service@mdwestserve.com<br>MDWestServe, Inc.";
	$headers .= "Cc: Service Updates <service@mdwestserve.com> \n";
	mail($to,$subject,$body,$headers);
	//make timeline entry
	ev_timeline($_POST[eviction_id],$_COOKIE[psdata][name]." Updated Est. Close from $d[estFileDate] to $_POST[estFileDate]");*/
	$newClose=1;
	$oldFileDate=$d[estFileDate];
}
$case_no=trim($_POST[case_no]);
// un dbCleaner on all items
if ($newClose != 1){
	$estQ="estFileDate='$_POST[estFileDate]',
	";
}
	$query="UPDATE evictionPackets SET process_status='$_POST[process_status]',
	filing_status='$_POST[filing_status]',
	service_status='$_POST[service_status]',
	fileDate='$_POST[fileDate]', ".$estQ."
	movant='".addslashes($_POST[movant])."',
	altDocs='$_POST[altDocs]',
	courierID='$_POST[courierID]',
	reopenDate='$_POST[reopenDate]',
	status='$_POST[status]',
	affidavit_status='$_POST[affidavit_status]',
	affidavit_status2='$_POST[affidavit_status2]',
	photoStatus='$_POST[photoStatus]',
	mail_status='$_POST[mail_status]',
	affidavitType='$_POST[affidavitType]',
	onAffidavit1='$_POST[onAffidavit1]',
	onAffidavit2='$_POST[onAffidavit2]',
	onAffidavit3='$_POST[onAffidavit3]',
	onAffidavit4='$_POST[onAffidavit4]',
	onAffidavit5='$_POST[onAffidavit5]',
	onAffidavit6='$_POST[onAffidavit6]',
	refile='$_POST[refile]',
	amendedAff='$_POST[amendedAff]',
	mailWeight='$_POST[mailWeight]',
	pages='$_POST[pages]',
	rush='$_POST[rush]',
	priority='$_POST[priority]',
	request_close='$_POST[request_close]',
	client_file='".strtoupper($_POST[client_file])."',
	case_no='".str_replace('�',0,$case_no)."',
	altPlaintiff='".dbCleaner($_POST[altPlaintiff])."',
	circuit_court='".strtoupper($_POST[circuit_court])."'
	WHERE eviction_id='$_POST[eviction_id]'";
	@mysql_query($query) or die("Query: $query<br>".mysql_error());
}else{
$case_no=trim($_POST[case_no]);
@mysql_query("UPDATE evictionPackets SET process_status='$_POST[process_status]',
	filing_status='$_POST[filing_status]',
	service_status='$_POST[service_status]',
	entry_id='$id',
	fileDate='$_POST[fileDate]',
	estFileDate='$_POST[estFileDate]',
	movant='".addslashes($_POST[movant])."',
	altDocs='$_POST[altDocs]',
	reopenDate='$_POST[reopenDate]',
	affidavit_status='$_POST[affidavit_status]',
	affidavit_status2='$_POST[affidavit_status2]',
	photoStatus='$_POST[photoStatus]',
	onAffidavit1='$_POST[onAffidavit1]',
	onAffidavit2='$_POST[onAffidavit2]',
	onAffidavit3='$_POST[onAffidavit3]',
	onAffidavit4='$_POST[onAffidavit4]',
	onAffidavit5='$_POST[onAffidavit5]',
	onAffidavit6='$_POST[onAffidavit6]',
	refile='$_POST[refile]',
	amendedAff='$_POST[amendedAff]',
	mailWeight='$_POST[mailWeight]',
	rush='$_POST[rush]',
	priority='$_POST[priority]',
	pages='$_POST[pages]',
	request_close='$_POST[request_close]',
	mail_status='$_POST[mail_status]',
	affidavitType='$_POST[affidavitType]',
	client_file='".strtoupper($_POST[client_file])."',
	case_no='".str_replace('�',0,$case_no)."',
	process_status='READY',
	status='RECIEVED',
	circuit_court='".strtoupper($_POST[circuit_court])."',
	altPlaintiff='".dbCleaner($_POST[altPlaintiff])."'
	WHERE eviction_id='$_POST[eviction_id]'") or die(mysql_error());
	ev_timeline($_POST[eviction_id],$_COOKIE[psdata][name]." Performed Data Entry");
// here is where we will automate the address check
?><script>window.open('supernova.php?packet=<?=$_POST[eviction_id];?>&close=1',   'supernova',   'width=600, height=800'); </script><?
}
$updateQ='';
if (isset($_POST[server1])){
	$updateQ .= "server_id='$_POST[server1]'|";
}
$r=mysql_query("SELECT name1, name2, name3, name4, name5, name6, address1, city1, state1, zip1 from evictionPackets WHERE eviction_id='$_POST[eviction_id]'");
$d=mysql_fetch_array($r, MYSQL_ASSOC) or die(mysql_error());
$nC=0;
while ($nC < 6){$nC++;
	if ($_POST["name$nC"] || ($_POST["name$nC"] != $d["name$nC"])){
		$updateQ .= "name$nC='".addslashes($_POST["name$nC"])."'|";
	}
}
if ($_POST[address] || ($_POST[address] != $d[address1])){
	$updateQ .= "address1='".addslashes($_POST[address])."'|";
}
if ($_POST[city] || ($_POST[city] != $d[city1])){
	$updateQ .= "city1='".addslashes($_POST[city])."'|";
}
if ($_POST[state] || ($_POST[state] != $d[state1])){
	$updateQ .= "state1='".addslashes($_POST[state])."'|";
}
if ($_POST[zip] || ($_POST[zip] != $d[zip1])){
	$updateQ .= "zip1='".addslashes($_POST[zip])."'|";
}
echo "<script>alert('[".trim($updateQ)."]')</script>";
if (trim($updateQ) != ''){
	//remove last "|"
	$updateQ=substr($updateQ,-1,1);
	//replace other "|"s with commas
	$updateQ=str_replace("|",", ",$updateQ);
	//submit query
	$query2="UPDATE evictionPackets SET ".$updateQ." WHERE eviction_id='$_POST[eviction_id]'";
	@mysql_query($query2) or die("Query: $query2<br>[$updateQ]<br>".mysql_error());
}
if ($_GET[packet] && $newClose == 1){
	echo "<script>prompter('$_POST[eviction_id]','$_POST[estFileDate]','$oldFileDate');</script>";
	//prevent further updates.
	die("<a href='order.php?packet=$_GET[packet]'>RELOAD ORDER</a>");
}elseif ($_GET[packet]){
	header ('Location: order.php?packet='.$_GET[packet]);
}else{
	if ($_GET[start]){
		header ('Location: order.php?start='.$_GET[start]);
	}else{
		?><script>window.location.href='order.php';</script><? }
	}
}



if ($_GET[packet]){
	$r=@mysql_query("SELECT *, CONCAT(TIMEDIFF( NOW(), date_received)) as hours FROM evictionPackets where eviction_id='$_GET[packet]'");
	hardLog('loaded order for '.$_GET[packet],'user');
}else{
	if($_GET[start]){
		$r=@mysql_query("SELECT *, CONCAT(TIMEDIFF( NOW(), date_received)) as hours FROM evictionPackets where status='NEW' and process_status <> 'CANCELLED' AND process_status <> 'DUPLICATE' AND process_status <> 'DAMAGED PDF' and process_status <> 'DUPLICATE/DIFF-PDF' and eviction_id >= '$_GET[start]' order by eviction_id ");
	}else{
		$r=@mysql_query("SELECT *, CONCAT(TIMEDIFF( NOW(), date_received)) as hours FROM evictionPackets where status='NEW' and process_status <> 'CANCELLED' and process_status <> 'DUPLICATE' AND process_status <> 'DAMAGED PDF' and process_status <> 'DUPLICATE/DIFF-PDF' order by RAND() ");
		$test55 = 1;
	}
}
$d=mysql_fetch_array($r, MYSQL_ASSOC);

if ($test55){
	hardLog('loaded NEW order for '.$d[eviction_id],'user');
}
if ($_GET[cancel] == 1){
	if ($d[process_status] == 'ASSIGNED'){
		//if file is currently assigned, send email to server.
		$to = "Service Updates <mdwestserve@gmail.com>";
		$subject = "Cancelled Service for Eviction $d[eviction_id] ($d[client_file])";
		$headers  = "MIME-Version: 1.0 \n";
		$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
		$headers .= "From: ".$_COOKIE[psdata][name]." <service.cancelled@mdwestserve.com> \n";
		$body="Service for Eviction $d[eviction_id] (<strong>$d[client_file]</strong>) has been cancelled by ".$_COOKIE[psdata][name].", if service is still in progress, please contact MDWestServe for instructions on how to proceed.";
		$body .= "<br><br>(410) 828-4568<br>service@mdwestserve.com<br>MDWestServe, Inc.";
		if ($d[server_id]){
			$serverID=$d[server_id];
			$sCount[$serverID]++;
			$headers .= "Cc: Service Updates <".id2email($d[server_id])."> \n";
			mail($to,$subject,$body,$headers);
		}
		echo "<div style='background-color:#00FF00; font-size:11px;'>$headers<hr>$body</div>";
	}
	@mysql_query("UPDATE evictionPackets SET process_status = 'CANCELLED', service_status='CANCELLED', status='CANCELLED', affidavit_status='CANCELLED', payAuth='1' where eviction_id='$d[eviction_id]'");
	timeline($d[eviction_id],$_COOKIE[psdata][name]." Cancelled Order PER ".$_GET[cancelRef]);
	error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Cancelled Process Service PER ".$_GET[cancelRef]." for EV$d[eviction_id] ($d[client_file]) [RECEIVED: $d[date_received]]",3,"/logs/user.log");
	// email client
	$to = "Service Updates <mdwestserve@gmail.com>";
	$subject = "Cancelled Service for Eviction $d[eviction_id] ($d[client_file])";
	$headers  = "MIME-Version: 1.0 \n";
	$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
	$headers .= "From: ".$_COOKIE[psdata][name]." <service.cancelled@mdwestserve.com> \n";
	$attR = @mysql_query("select ps_to from attorneys where attorneys_id = '$d[attorneys_id]'");
	$attD = mysql_fetch_array($attR, MYSQL_BOTH);
	$c=-1;
	$cc = explode(',',$attD[ps_to]);
	$ccC = count($cc)-1;
	while ($c++ < $ccC){
		$headers .= "Cc: Service Updates <".trim($cc[$c])."> \n";
	}
	if (stripos($headers,$_GET[cancelRef]) == false){
		$headers .= "Cc: Service Updates <".$_GET[cancelRef]."> \n";
	}
	$headers .= "Cc: Service Updates <service@mdwestserve.com> \n";
	$history=historyList($d[eviction_id],$d[attorneys_id]);
	if (strpos($history,'"')){
		$history=str_replace('"','\"',$history);
	}
	$attachmentList=attachmentList($d[eviction_id],'EV');
	$body ="<strong>Thank you for selecting MDWestServe as Your Process Service Provider.</strong><br>
	Service for Eviction $d[eviction_id] (<strong>$d[client_file]</strong>) is cancelled by ".$_COOKIE[psdata][name]." per ".$_GET[cancelRef].", closeout documents as follows:
	$attachmentList
	<div style='border:solid 1px;'>Service in $d[circuit_court] COUNTY was $d[service_status]. Filing status was $d[filing_status].<br>
	<center><h2>HISTORY ITEMS:</h2>
	$history
	</center></div><br>";
	$body .= "<br><br>(410) 828-4568<br>service@mdwestserve.com<br>MDWestServe, Inc.";
	mail($to,$subject,$body,$headers);
	echo "<div style='background-color:#00FF00; font-size:11px;'>$headers<hr>$body</div>";
	$r=@mysql_query("select * from evictionPackets where eviction_id='$d[eviction_id]'");
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
}
?>
<center style="padding:0px;">
<?
$packet=$d[eviction_id];
$id=$_COOKIE[psdata][user_id];
//begin export commands
$rTest=@mysql_query("select * from EVexportRequests where evictionID = '$packet'") or die (mysql_error());
$dTest=mysql_fetch_array($rTest,MYSQL_ASSOC);
if ($_GET[export]){
	if(!$dTest[byID]){
		mail('patrick@mdwestserve.com',$_COOKIE[psdata][name].' requested export '.$packet,$_COOKIE[psdata][name].' requested export of eviction '.$packet);
		@mysql_query("INSERT INTO EVexportRequests (evictionID,byID) values ('$packet','".$_COOKIE[psdata][user_id]."') ");
		echo "<script>automation();</script>";
	}elseif($dTest[byID] != $_COOKIE[psdata][user_id]){
		mail('patrick@mdwestserve.com',$_COOKIE[psdata][name].' approved '.$packet,$_COOKIE[psdata][name].' approved export of eviction '.$packet);
		@mysql_query("UPDATE EVexportRequests set confirmID = '".$_COOKIE[psdata][user_id]."' where evictionID = '$packet'");
		echo "<script>automation();</script>";
	}else{	
		echo "<script>alert('You cannot approve exports you requested silly goose!');</script>";
	}
}
$rTest=@mysql_query("select * from EVexportRequests where evictionID = '$packet'") or die (mysql_error());
$dTest=mysql_fetch_array($rTest,MYSQL_ASSOC);
$exportStatus = exportStatus($dTest[byID],$dTest[confirmID],$packet);
//end export commands
?>
<style>
.done {
	height:50px;
	width:175px;
	font-size:12pt;
	text-align:center;
	background-color:ccffcc;
	border:ridge 3px #FF0000;
	}	
.active {
	height:50px;
	width:175px;
	font-size:12pt;
	text-align:center;
	background-color:ffffcc;
	border:ridge 3px #FFFF00;
	}
.alert	{
	height:50px;
	width:175px;
	font-size:12pt;
	text-align:center;
	background-color:ffcccc;
	border:ridge 3px #FF0000;
		}
.pending	{
	height:50px;
	width:175px;
	font-size:12pt;
	text-align:center;
	background-color:cccccc;
	border:ridge 3px #FF0000;
		}
a { text-decoration:none}
table { padding:0px; margin:0px; font-size:14px;}
body { margin:0px; padding:0px;}
input, select { margin:0px; background-color:#CCFFFF; font-variant:small-caps; }
td { margin:0px; padding:0px; font-variant:small-caps;}
legend {margin:0px; border:solid 1px #FF0000; background-color:#cccccc; padding:0px;}
legend.a {margin:0px; border:solid 1px #FF0000; background-color:#cccccc; padding:0px; font-size:14px}
fieldset {margin:0px; padding:0px; background-color:#FFFFFF; }
.single{background-color:#00FF00}
.duplicate{background-color:#FF0000}
</style>
<table align="center"><tr>
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
<td><div class="alert"style="font-size:10px;"><a href="?packet=<?=$_GET[packet]?>&export='<?=time();?>'">EXPORT</a><hr><?=$exportStatus;?></div></td>
</tr></table>
</center>


<? if (!$d[eviction_id]){ ?>
<form>No new files to update, enter Eviction ID to view order <input name="packet"></form>
<? }else{ ?>
<body>
<script type="text/javascript">
function confirmation(email) {
	if (email != ''){
		var answer = confirm("Are you sure that you want to cancel service per "+email+"? Emails will be sent to the client and server, should service be active.  Make sure that you have entered a valid client email address for reference.");
		if (answer){
			window.location = "http://staff.mdwestserve.com/ev/order.php?packet=<?=$d[eviction_id]?>&cancelRef="+email+"&cancel=1";
		}
		else{
			alert("ABORTED");
			self.close();
		}
	}
	else{
		alert(email+"::NEED VALID EMAIL ADDRESS.  ABORTED::");
		self.close();
	}
}
function hideshow(which){
if (!document.getElementById)
return
if (which.style.display=="block")
which.style.display="none"
else
which.style.display="block"
}

function ClipBoard()
{
holdtext.innerText = copytext.innerText;
Copied = holdtext.createTextRange();
Copied.execCommand("Copy");
}</script>
<form method="post">

<table width="100%" style='background-color:<?=colorCode(stripHours($d[hours]),$d[filing_status]);?>'>
<tr>
<td valign="top">
<FIELDSET height="100%">

<div style="background-color:#FFFFFF" align="center">
<table width="100%"><tr><td align="center">
<? if (!$d[uspsVerify]){?><a href="supernova.php?packet=<?=$d[eviction_id]?>" target="preview">!!!Verify Addresses!!!</a><? }else{ ?><img src="http://www.usps.com/common/images/v2header/usps_hm_ci_logo2-159x36x8.gif" ><br>Verified by <? echo $d[uspsVerify]; } ?>
<?
$received=strtotime($d[date_received]);
$deadline=$received+432000;
$deadline=date('F jS Y',$deadline);
$days=number_format((time()-$received)/86400,0);
$hours=number_format((time()-$received)/3600,0);
?>
 </td><td align="center">
<? if(!$d[caseVerify]){ ?> <a href="validateCase.php?case=<?=$d[case_no]?>&packet=<?=$d[eviction_id]?>&county=<?=$d[circuit_court]?>" target="preview">!!!Verify Case Number!!!</a><? }else{ ?><img src="http://www.courts.state.md.us/newlogosm.gif"><br>Verified by <? echo $d[caseVerify]; }?>
</td><td align="center">
<? if(!$d[qualityControl]){ ?> <a href="entryVerify.php?packet=<?=$d[eviction_id]?>&frame=no" target="preview">!!!Verify Data Entry!!!</a><? }else{ ?><img src="http://staff.mdwestserve.com/small.logo.gif" height="41" width="41"><br>Verified by <? echo $d[qualityControl]; }?>
</td><td align="center"><div style="font-size:30pt" ><?=$hours?> Hours<div><div style="font-size:15pt" ><?=$days?> Days || File Deadline: <?=$deadline?><div></td></tr></table>
</div>
<? if ($d[possibleDuplicate]){?>
<div style="background-color:#ff0000" align="center">Duplicate Warning Level: <?=$d[possibleDuplicate]?></div>
<? } ?>


<a name="case_no"></a>
<table width="100%"><tr>
<?
$dupCheck=dupCheck($d[client_file]);
?>
<td valign="top" <?=$dupCheck?>>
<FIELDSET>
<LEGEND ACCESSKEY=C><?=id2attorney($d[attorneys_id]);?> Data <input type="submit" name="submit" style="background-color:#00FF00; font-weight:bold; width:100px;" value="SAVE"></LEGEND>
<table bgcolor="#FFFFFF"><tr><td valign="top">
<table valign="top">
<tr>
<td>Client&nbsp;File </td>
<td><input name="client_file" value="<?=$d[client_file]?>" /></td>
</tr>
<tr>
<td><a href="http://casesearch.courts.state.md.us/inquiry/inquiryDetail.jis?caseId=<?=str_replace('-','',trim($d[case_no]))?>&detailLoc=<? if ($d[circuit_court] == "MONTGOMERY"){ echo "MCCI";}elseif($d[circuit_court] == "PRINCE GEORGES"){echo "PGV";}else{ echo "CC";} ?>" target="preview">Case&nbsp;Number</a></td>
<td><input name="case_no" value="<?=$d[case_no]?>" /> </td>
</tr>
<tr>
<td>Circuit&nbsp;Court</a></td>
<td><select name="circuit_court"><?=mkCC($d[circuit_court]);?></select><input type="submit" name="sendToClient" style="background-color:#66CCFF; font-weight:bold; width:140px;" value="SEND TO CLIENT"></td>
</tr>
<tr>
<?
$rXX=@mysql_query("select name from courier where courierID = '$d[courierID]'");
$dXX=mysql_fetch_array($rXX,MYSQL_ASSOC);
?>
<td>Courier</td>
<td><select name="courierID"><option value="<?=$d[courierID]?>"><?=$dXX[name]?></option>
<?
$CCr=@mysql_query("select * from courier");
while($CCd=mysql_fetch_array($CCr,MYSQL_ASSOC)){
?>
<option value="<?=$CCd[courierID]?>"><?=$CCd[name]?> (<?=$CCd[courierID]?>)</option>
<? }?></select></td>
</tr>
<tr>
<td>Alt Plaintiff</td>
<td><input size="37" name="altPlaintiff" value="<?=$d[altPlaintiff]?>" /></td>
</tr>
<tr>
<td valign="top">Movant</td>
<td><textarea name="movant" rows="2" cols="28"><?=stripslashes($d[movant])?></textarea></td>
</tr>
<tr>
<td>Service&nbsp;Docs</td>
<td><select name="altDocs" style="font-size:10px;"><? if($d[altDocs] != ''){echo "<option>$d[altDocs]</option>";} ?><option>MOTION FOR JUDGMENT AWARDING POSSESSION</option><option>90 DAY NOTICE TO OCCUPANT</option></select></td>
</tr>
</table>
</td><td>
<table>
<tr>
<td>Est. Closed</td>
<td><input size="8" name="estFileDate" value="<?=$d[estFileDate]?>"></td>
</tr>
<tr>
<td>File Closed</td>
<td><input size="8" name="fileDate" value="<?=$d[fileDate]?>"></td>
</tr>
</table>
</td></tr></table>
<table width="100%"><tr>
<td colspan='2'><div style=" font-size:12px; background-color:ffffff; border:solid 1px #ffff00;">
<?
mysql_select_db('core');
$affID="EV".$d[eviction_id];
$q5="SELECT * FROM ps_affidavits WHERE packetID = '$affID' order by defendantID";
$r5=@mysql_query($q5) or die ("Query: $q5<br>".mysql_error());
while ($d5=mysql_fetch_array($r5, MYSQL_ASSOC)){
		$defname = $d["name".$d5[defendantID]];
		echo "<li><a target='_blank' href='".str_replace('ps/','',$d5[affidavit])."'><strong>".$defname."</strong>: $d5[method]</a></li>";
}
?>
<a href="evUpload.php?eviction=<?=$d[eviction_id]?>" target="preview">Upload More Documents</a>
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
	echo "<br><a href='http://staff.mdwestserve.com/search.php?q=".$d[client_file]."&field=client_file' target='_blank'>Check Possible Duplicates</a>";
}
?>
</td>
<td valign="top">
<FIELDSET>
<LEGEND ACCESSKEY=C>Persons Served</LEGEND>
<table>
<tr>
<td nowrap>1<input size="20" name="name1" value="<?=stripslashes($d[name1])?>" /><input <? if ($d[onAffidavit1]=='checked'){echo "checked";} ?> type="checkbox" value="checked" name="onAffidavit1"></td><? $mult=1;?>
</tr><tr>
<td nowrap>2<input size="20" name="name2" value="<?=stripslashes($d[name2])?>" /><input <? if ($d[onAffidavit2]=='checked'){echo "checked";} ?> type="checkbox" value="checked" name="onAffidavit2"></td><? if ($d[name2]){$mult++;}?>
</tr><tr>
<td nowrap>3<input size="20" name="name3" value="<?=stripslashes($d[name3])?>" /><input <? if ($d[onAffidavit3]=='checked'){echo "checked";} ?> type="checkbox" value="checked" name="onAffidavit3"></td><? if ($d[name3]){$mult++;}?>
</tr><tr>
<td nowrap>4<input size="20" name="name4" value="<?=stripslashes($d[name4])?>" /><input <? if ($d[onAffidavit4]=='checked'){echo "checked";} ?> type="checkbox" value="checked" name="onAffidavit4"></td><? if ($d[name4]){$mult++;}?>
</tr><tr>
<td nowrap>5<input size="20" name="name5" value="<?=stripslashes($d[name5])?>" /><input <? if ($d[onAffidavit5]=='checked'){echo "checked";} ?> type="checkbox" value="checked" name="onAffidavit5"></td><? if ($d[name5]){$mult++;}?>
</tr><tr>
<td nowrap>6<input size="20" name="name6" value="<?=stripslashes($d[name6])?>" /><input <? if ($d[onAffidavit6]=='checked'){echo "checked";} ?> type="checkbox" value="checked" name="onAffidavit6"></td><? if ($d[name6]){$mult++;}?>
</tr>

</table>
</FIELDSET>
<?
$q5="SELECT DISTINCT serverID from evictionHistory WHERE eviction_id='$d[eviction_id]'";
$r5=@mysql_query($q5) or die(mysql_error());
$i=0;
$data5=mysql_num_rows($r5);
if ($data5 > 0){
	while ($d5=mysql_fetch_array($r5, MYSQL_ASSOC)){$i++;
		$q6="SELECT * FROM evictionHistory WHERE serverID='$d5[serverID]' and eviction_id='$d[eviction_id]'";
		$r6=@mysql_query($q6) or die(mysql_error());
		$d6=mysql_num_rows($r6);
		if ($i == '1'){
			$server = $d6." ".initals(id2name($d5[serverID]));
		}else{
			$server .= ", ".$d6." ".initals(id2name($d5[serverID]));
		}
	}
}else{
	$server="none";
}
$otdStr=str_replace('portal//var/www/dataFiles/service/orders/','PS_PACKETS/',$d[otd]);
$otdStr=str_replace('data/service/orders/','PS_PACKETS/',$otdStr);
$otdStr=str_replace('portal/','',$otdStr);
if (!strpos($otdStr,'mdwestserve.com')){
	$otdStr="http://mdwestserve.com/".$otdStr;
}
?>
<FIELDSET style="background-color:#FFFF00; padding:0px;">
<LEGEND ACCESSKEY=C>Service Links</LEGEND>
<table style="padding:0px; font-weight:bold; border-collapse:collapse; height:150px !important; font-size:small;" cellpadding="0" cellspacing="0">
	<tr>
		<td><a href="accounting.php?id=<?=$d[eviction_id]?>" target="preview">*Payments*</a></td>
	</tr>
	<tr>	
		<td><a href="evictionHistoryModify.php?id=<?=$d[eviction_id]?>&form=1" target="preview">History (<?=$server?>)</a></td>
	</tr>
	<tr>
		<td><a href="http://service.mdwestserve.com/ev_customInstructions.php?id=<?=$d[eviction_id]?>" target="preview">Instructions (<?=id2attorney($d[attorneys_id])?>)</a></td>
	</tr>
	<tr>
		<td><a href="<?=$otdStr?>" target="preview">JAP</a></td>
	</tr>
	<tr>
		<td><a href="ev_write_invoice.php?id=<?=$d[eviction_id]?>" target="preview">*Invoice*</a></td>
	</tr>
	<tr>
		<td><a href="serviceReview.php?packet=<?=$d[eviction_id]?>" target="preview">Timeline</a></td>
	</tr>
	<tr>
		<td><a href="evSheet.php?id=<?=$d[eviction_id]?>&autoPrint=1" target="_blank">Checklist</a></td>
	</tr>
	<tr>
		<td><a href="photoDisplay.php?packet=<?=$d[eviction_id]?>" target="preview"><?$photoCount=photoCount($d[eviction_id]); echo $photoCount;?> Photo<? if($photoCount != 1){echo "s";}?></a></td>
	</tr>
	<tr>
		<td><a href="mailings.php?EV=<?=$d[eviction_id]?>" target="preview">Mailings</a><? 	if (webservice($d[client_file]) && ($d[attorneys_id] == 1)){ ?> | <a href="http://staff.mdwestserve.com/otd/webservice.php?fileNumber=<?=$d[client_file];?>" target="preview">Webservice Data</a><? }?></td>
	</tr>
	<?
$FC = trim(getPage("http://data.mdwestserve.com/findFC.php?clientFile=$d[client_file]", "MDWS File Copy for Eviction $d[eviction_id]", '5', ''));
if ($FC != '' && $FC != '1'){
	echo "<tr><td>'$FC'</td></tr>";
}
	?>
</table>
</FIELDSET>
</td>
</tr></table>
<table style="display:block;" id="nnotes" width="100%">
<tr><td colspan="2"><fieldset><legend>Notes</legend>
<iframe height="200px" width="600px"  frameborder="0" src="http://staff.mdwestserve.com/notes.php?eviction=<?=$d[eviction_id]?>"></iframe></fieldset></td></tr></table>
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
$r92=@mysql_query("select * from docuTrack where packet = '$affID' order by trackID desc");
while($d92=mysql_fetch_array($r92,MYSQL_ASSOC)){
$defname = $d["name".$d92[defendant]];
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

<table width="100%" style="display:block;" id="addresses"><tr><td valign="top">

<FIELDSET>
<LEGEND class="a" ACCESSKEY=C><a href="http://mdwestserve.com/ps/dispatcher.php?aptsut=&address=<?=$d[address1]?>&city=<?=$d[city1]?>&state=<?=$d[state1]?>&miles=5" target="_Blank">Mortgage / Deed of Trust</a><input type="checkbox" checked><br><?=id2name($d[server_id]);?></LEGEND>
<table>
<tr>
<td><input id="address" name="address" size="30" value="<?=$d[address1]?>" /></td>
</tr>
<tr>
<td><input size="20" name="city" value="<?=$d[city1]?>" /><input size="2" name="state" value="<?=$d[state1]?>" /><input size="4" name="zip" value="<?=$d[zip1]?>" /></td>
</tr>
</table>    
</FIELDSET>
</td><td valign="top">

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
<select name="server1"><? if (!$d[server_id]){ ?><option value="">Select Server </option><? }else{ ?><option value="<?=$d[server_id]?>"><?=id2name($d[server_id]);?> (Server)</option><? } ?>
<?
$q7= "select * from ps_users where contract = 'YES' order by id ASC";
$r7=@mysql_query($q7) or die("Query: $q7<br>".mysql_error());
while ($d7=mysql_fetch_array($r7, MYSQL_ASSOC)) {
?>
<option value="<?=$d7[id]?>"><? if ($d7[company]){echo $d7[company].', '.$d7[name] ;}else{echo $d7[name] ;}?></option>
<?        } ?>
<option value=""></option>
</select>

</td></tr>
</table>



<!-----------------------------------------------
menu bar here
------------------------------------------------>

<table width="100%" id="status" style="display:none;">
<input type="hidden" name="eviction_id" value="<?=$d[eviction_id]?>" />
<tr>
<? if ($_GET[packet]){?>
<td align="center" width="25%">Client Status<br><select name="status"><option><?=$d[status]?></option>
<?
$q1="SELECT DISTINCT status from evictionPackets WHERE status <> ''";
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
$q1="SELECT DISTINCT service_status from evictionPackets WHERE service_status <> ''";
$r1=@mysql_query($q1) or die("Query: $q1<br>".mysql_error());
while ($d1=mysql_fetch_array($r1, MYSQL_ASSOC)){
?>
<option><?=$d1[service_status]?></option>
<? } ?>
<option value=""></option>
</select></td>
<td align="center" width="25%">Filing Status<br><select name="filing_status"><option><?=$d[filing_status]?></option>
<?
$q1="SELECT DISTINCT filing_status from evictionPackets WHERE filing_status <> '' AND filing_status <> 'DO NOT FILE'";
$r1=@mysql_query($q1) or die("Query: $q1<br>".mysql_error());
while ($d1=mysql_fetch_array($r1, MYSQL_ASSOC)){
?>
<option><?=strtoupper($d1[filing_status])?></option>
<? } ?>
<option>DO NOT FILE</option>
<option value=""></option>
</select></td></tr><tr>
<td align="center" width="25%">Process Status<br><select name="process_status"><option><?=$d[process_status]?></option>
<?
$q2="SELECT DISTINCT process_status from evictionPackets WHERE process_status <> ''";
$r2=@mysql_query($q2) or die("Query: $q2<br>".mysql_error());
while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)){
?>
<option><?=$d2[process_status]?></option>
<? } ?>
<option value=""></option>
</select></td>
<td align="center" width="25%"><table><tr><td>Affidavit Status<br><select name="affidavit_status"><option><?=$d[affidavit_status]?></option>
<?
$q3="SELECT DISTINCT affidavit_status from evictionPackets WHERE affidavit_status <> ''";
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
$q3="SELECT DISTINCT affidavit_status2 from evictionPackets WHERE affidavit_status2 <> ''";
$r3=@mysql_query($q3) or die("Query: $q3<br>".mysql_error());
while ($d3=mysql_fetch_array($r3, MYSQL_ASSOC)){
?>
<option><?=$d3[affidavit_status2]?></option>
<? } ?>
<option>AWAITING MAILING</option>
<option value=""></option>
</select></td>
</td></tr></table>
</td>
<td align="center">Photo Status<br><select name="photoStatus"><option><?=$d[photoStatus]?></option>
<?
$q4="SELECT DISTINCT photoStatus from evictionPackets WHERE photoStatus <> ''";
$r4=@mysql_query($q4) or die("Query: $q4<br>".mysql_error());
while ($d4=mysql_fetch_array($r4, MYSQL_ASSOC)){
?>
<option><?=$d4[photoStatus]?></option>
<? } ?>
<option value=""></option>
</select>
</td></tr><tr>
<td align="center" width="25%">Affidavit Type<br><select name="affidavitType"><option><?=$d[affidavitType]?></option>
<?
$q4="SELECT DISTINCT affidavitType from evictionPackets WHERE affidavitType <> ''";
$r4=@mysql_query($q4) or die("Query: $q4<br>".mysql_error());
while ($d4=mysql_fetch_array($r4, MYSQL_ASSOC)){
?>
<option><?=$d4[affidavitType]?></option>
<? } ?>
<option value=""></option>
</select></td>
<td align="center" width="25%">Mail Status<br><select name="mail_status"><option><?=$d[mail_status]?></option>
<?
$q4="SELECT DISTINCT mail_status from evictionPackets WHERE mail_status <> ''";
$r4=@mysql_query($q4) or die("Query: $q4<br>".mysql_error());
while ($d4=mysql_fetch_array($r4, MYSQL_ASSOC)){
?>
<option><?=$d4[mail_status]?></option>
<? } ?>
<option value=""></option>
</select></td>
<td align="center">
<? if ($d[process_status] != 'CANCELLED'){ ?><input style='font-size:11px;' name='cancelRef' value='Client Reference Email' onclick="value=''" size='25'><br><input style='background-color:pink; font-size: 10.5px;' type='button' name='cancel' value='CANCEL' onclick="confirmation(cancelRef.value);"><? }?>
</td>
</tr>
<tr>
<td align="center" colspan='3'>
<table align="center"  width='100%'><tr><td align="center">
Rush Service<br>
<input type="checkbox" name="rush" <? if ($d[rush] == 'checked'){ echo "checked";} ?> value="checked">
</td><td align="center" style="padding-left:5px">
Priority Service<br>
<input type="checkbox" name="priority" <? if ($d[priority] == 'checked'){ echo "checked";} ?> value="checked">
</td><td align="center" style="padding-left:5px">
Amended Affidavit<br>
<input type="checkbox" name="amendedAff" <? if ($d[amendedAff] == 'checked'){ echo "checked";} ?> value="checked">
</td><td align="center" style="padding-left:5px">
Refile<br>
<input type="checkbox" name="refile" <? if ($d[refile] == 'checked'){ echo "checked";} ?> value="checked">
</td><td align="center" style="padding-left:5px">
Request Close<br>
<input type="checkbox" name="request_close" <? if ($d[request_close] == 'YES'){ echo "checked";} ?> value="YES">
</td></tr></table>
</td>
</tr>
</table>






<strong>
	<div align="center" style="background-color:#FFFF00">
    	<a onClick="hideshow(document.getElementById('track'))">Tracking</a> &curren; 
    	<a onClick="hideshow(document.getElementById('status'))">Status</a> &curren; 
		<a onClick="hideshow(document.getElementById('nnotes'))">Notes</a>
    </div>
</strong>

</FIELDSET>
</td><td valign="top" width="10%">

<?
	if($d[status]=="NEW" || $_GET[otd] == '1'){ 
		$src=str_replace('portal//var/www/dataFiles/service/orders/','PS_PACKETS/',$d[otd]);
		$src=str_replace('data/service/orders/','PS_PACKETS/',$src);
		$src=str_replace('portal/','',$src);
	}elseif(!$d[uspsVerify]){
		$src="supernova.php?packet=$d[eviction_id]";
	}elseif(!$d[caseVerify] && $d[case_no]){
		$src="validateCase.php?case=$d[case_no]&packet=$d[eviction_id]&county=$d[circuit_court]";
	}elseif(!$d[qualityControl]){
		$src="entryVerify.php?packet=$d[eviction_id]&frame=no";
	}elseif(!$d[caseVerify]){
		$src="validateCase.php?case=$d[case_no]&packet=$d[eviction_id]&county=$d[circuit_court]";
	}elseif($d[process_status] == "CANCELLED" || $d[filing_status]=="FILED WITH COURT" || $d[filing_status]=="FILED WITH COURT - FBS"){
		$src="http://staff.mdwestserve.com/ev/accounting.php?id=$d[eviction_id]";
	}else{
		$src="serviceReview.php?packet=$d[eviction_id]"; 
	}
?>
<table>
	<tr>
		<td style='font-size:12px;' valign="bottom"><input name="pages" value="<?=$d[pages]?>" size="4"> # JAP Pages</td>
		<td style='font-size:12px;' valign="bottom"><input name="mailWeight" size="4" value="<?=$d[mailWeight]?>"> Mail Weight</td></form>
		<form action="http://staff.mdwestserve.com/temp/pageRemove.php"><input type="hidden" name="id" value="<?=$d[eviction_id]?>"><input type="hidden" name="type" value="EV">
		<? if ($_GET[packet]){ ?>
		<input type="hidden" name="packet" value="<?=$d[eviction_id]?>">
		<? } ?>
		<td valign="bottom"><input name="skip" onclick="value=''" value="Remove Page #"> <input type="submit" value="GO!"></td></form>
	</tr>
	<tr>
		<td colspan="3">
		<input name="otd" value="<?=$d[otd]?>" size="80"> <? if($d[status]=="NEW"){ echo "<a href='renameJAP.php?packet=$d[eviction_id]&test=1'>FIX JAP LINK</a>";}else{echo "<a href='renameJAP.php?packet=$d[eviction_id]'>FIX</a>";} ?>
		</td>
	</tr>
</table>
<? if (webservice($d[client_file]) && ($d[attorneys_id] == 1)){
		echo "<table align='center'><tr><td>";
		include "http://staff.mdwestserve.com/otd/webservice.php?fileNumber=$d[client_file]";
		echo "</td></tr></table>";
	}
?>
<iframe height="640px" width="900px" name="preview" id="preview" src="<?=$src?>" ></iframe>
</td></tr></table>
<? }?>
<script>document.title='EV<?=$_GET[packet]?>|<?=$d[status]?>|<?=$d[service_status]?>|<?=$d[process_status]?>|<?=$d[affidavit_status]?>'</script>
<?
$r=@mysql_query("select * from fileWatch where clientFile = $d[client_file]");
while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
echo "<script>alert('$d[message]');</script>";
}
?>

<? include 'footer.php'; ?>