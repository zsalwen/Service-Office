<?
date_default_timezone_set('America/New_York');
include 'security.php';
if($_SERVER["SERVER_NAME"] == "staff2.mdwestserve.com"){
ini_set('mysql.default_host', '10.0.0.4');
$thisDB = '10.0.0.4';
}elseif($_SERVER["SERVER_NAME"] == "staff1.mdwestserve.com"){
ini_set('mysql.default_host', 'mdws2.mdwestserve.com');
$thisDB = 'mdws2.mdwestserve.com';
}else{
$thisDB = 'mdws1.mdwestserve.com';
}

mysql_connect();
mysql_select_db('core');
$q="UPDATE ps_users SET location='".$_SERVER['PHP_SELF']."', online_now='".time()."' WHERE id = '".$_COOKIE[psdata][user_id]."'";
@mysql_query($q);
include '/gitbox/Service-Office/lock.php'; 

function hardLog($str,$type){
	if ($type == "user"){
		$log = "/logs/user.log";
	}
	if ($type == "contractor"){
		$log = "/logs/contractor.log";
	}
	if ($type == "debug"){
		$log = "/logs/debug.log";
	}
	if ($log){
		error_log(date('h:iA n/j/y')." ".$_COOKIE[psdata][name]." ".$_SERVER["REMOTE_ADDR"]." ".trim($str)."\n", 3, $log);
	}
}
hardLog('Post Service Report','user');
session_start();
//opLog($_COOKIE[psdata][name]." Loaded Assigned Cases");
$_SESSION[active]='';
$_SESSION[active2]='';

$_SESSION[cap] = -1;
if ($_GET[cap]){
$_SESSION[cap] = $_GET[cap];
}

function id2name($id){
	$q="SELECT name FROM ps_users WHERE id = '$id'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
return $d[name];
}

function id2attorney($id){
	$q="SELECT display_name FROM attorneys WHERE attorneys_id = '$id'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
return $d[display_name];
}

function stripHours($date){
$hours = explode(':',$date);
return $hours[0];
}

function colorCode($hours,$packet,$letter){
	if ($hours <= 120){ $html = "009900"; }
	if ($hours > 120 && $hours <= 168){ $html = "FFFFCC"; }
	if ($hours > 168 && $hours <= 288){ $html = "FFCCFF"; }
	if ($hours > 288){ $html = "000000; color:ffffff"; }
	if ($packet != ''){
		$r=@mysql_query("SELECT serveComplete, serveCompletea, serveCompleteb, serveCompletec, serveCompleted, serveCompletee from ps_packets where packet_id = '$packet'");
		$d=mysql_fetch_array($r, MYSQL_ASSOC);
		if ($d["serveComplete$letter"] == '+'){
			$html .= ";border-style:solid;border-width:5px;border-color:CCFFBB;";
		}elseif($d["serveComplete$letter"] == '-'){
			$html .= ";border-style:solid;border-width:5px;border-color:FFCCFF;";
		}
	}
	return $html;
}

function colorCode2($hours){
	if ($hours <= -24){ return "000000; color:FFFFFF !important;"; }
	if ($hours > -24 && $hours <= 0){ return "FF0000; color:000000 !important;"; }
	if ($hours > 0 && $hours <= 24){ return "FFFF00; color:000000 !important;"; }
	if ($hours > 24 && $hours <= 48){ return "CCFF00; color:000000 !important;"; }
	if ($hours > 48){ return "00FF00; color:000000 !important;"; }
}

function withCourier($packet,$prefix){
	$search=$prefix.$packet;
	$q="SELECT * from docuTrack WHERE packet='$search' and document='OUT WITH COURIER' ORDER BY trackID DESC LIMIT 0,1";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	if ($d[packet]){
		$document="<div class='note' style='background-color:#ffFF00;'><small>".$d[binder]." OUT WITH COURIER by ".$d[location]."</small></div>";
	}
	return $document;
}

function prepExplode($packet,$idType,$table){
	$q="SELECT timeline FROM $table WHERE $idType='$packet' LIMIT 0,1";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	$timeline=explode('<br>',$d[timeline]);
	for($i = 0; $i < count($timeline); $i++){
		if (strpos($timeline[$i], "Prepared Affidavits for Filing")){
				$timeList = "<br><div class='note' style='background-color:#6699CC;'><small>$timeline[$i]";
		}
	}
	$timeList .= "</small></div>";
	return $timeList;
}

function inverseHex( $color ){
	$color = trim($color);
	$prependHash = FALSE;
	if(strpos($color,'#')!==FALSE) {
		$prependHash = TRUE;
		$color = str_replace('#',NULL,$color);
	}
	switch($len=strlen($color)) {
		case 3:
		$color=preg_replace("/(.)(.)(.)/","\\1\\1\\2\\2\\3\\3",$color);
		break;
		case 6:
		break;
		default:
		trigger_error("Invalid hex length ($len). Must be a minimum length of (3) or maxium of (6) characters", E_USER_ERROR);
	}
	if(!preg_match('/^[a-f0-9]{6}$/i',$color)) {
		$color = htmlentities($color);
		trigger_error( "Invalid hex string #$color", E_USER_ERROR );
	}
	$r = dechex(255-hexdec(substr($color,0,2)));
	$r = (strlen($r)>1)?$r:'0'.$r;
	$g = dechex(255-hexdec(substr($color,2,2)));
	$g = (strlen($g)>1)?$g:'0'.$g;
	$b = dechex(255-hexdec(substr($color,4,2)));
	$b = (strlen($b)>1)?$b:'0'.$b;
	return ($prependHash?'#':NULL).$r.$g.$b;
}

function presaleActiveList($id,$letter,$packet){ $_SESSION[active]++;
	$data='<ol>';
	$pkt='';
	if ($packet != '0' && $packet != ''){
		$pkt=" AND packet_id < '$packet'";
	}
	$r=@mysql_query("select packet_id, avoidDOT, affidavit_status, service_status, filing_status, circuit_court, attorneys_id, estFileDate, case_no, caseLookupFlag, reopenDate, affidavit_status2, rush, TIMEDIFF( NOW(), date_received) as hours, DATEDIFF( CURDATE(), reopenDate) as reopenHours, DATEDIFF(estFileDate, CURDATE()) as estHours from ps_packets where server_id$letter='$id' and affidavit_status = 'SERVICE CONFIRMED' and filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT'$pkt order by packet_id");
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){ $_SESSION[active2]++;
		$estHours=($d[estHours]*24)-date('G');
		if ($d[affidavit_status2] == 'REOPENED'){
			$hours=$d[reopenHours]*24;
		}else{
			$hours=stripHours($d[hours]);
		}
		if ($hours > $_SESSION[cap]){
			if ($d[case_no] == '' && $d[caseLookupFlag] != '0'){
				$case="<span style='background-color:#FFFFFF; color:000000;'><small>NO CASE!</small></span>";
			}else{
				$case='';
			}
			if ($d[affidavit_status2] == 'REOPENED'){
				$reopenDate=explode('-',$d[reopenDate]);
				$reopenDate=$reopenDate[1].'-'.$reopenDate[2];
				$case .= "-<span style='background-color:#FFFFFF; color:#000000 !important;'><small>REOPENED $reopenDate</small></span>";
			}
			if ($d[rush] != ''){
				$case .= "-<span style='background-color:#000000; color:FF0000; border: 3px solid black; font-weight:bold;'>RUSH</span>";
			}
			if ($d[avoidDOT] != ''){
				$case .= "-<span style='background-color:#000000; color:FF0000; border: 3px solid red; font-weight:bold;'>AvoidDOT</span>";
			}
			$estFileDate=explode('-',$d[estFileDate]);
			$estFileDate=$estFileDate[1].'-'.$estFileDate[2];
			$case .= "&nbsp;<span title='$estHours Hours Remaining' style='background-color:".colorCode2($estHours)."; border: 1px solid black;'>FILE: $estFileDate</span>";
			$withCourier=withCourier($d[packet_id],'');
			if($withCourier != ''){
				$case.=$withCourier;
			}else{
				$prepExplode = prepExplode($d[packet_id],'packet_id','ps_packets');
				if ($prepExplode != ''){
					$case .= $prepExplode;
				}
			}
			$colorCode=colorCode($hours,$d[packet_id],$letter);
			$bgColor=substr($colorCode,0,6);
			$inverse=inverseHex($bgColor);
			if (strtolower($inverse) == 'ffffff'){
				$inverse .= "', document.getElementById('OTD$d[packet_id]').style.color='000000";
				$bgColor .= "', document.getElementById('OTD$d[packet_id]').style.color='FFFFFF";
			}
			$js = "id='OTD$d[packet_id]$letter' ";
			$mover="onmouseover=\"document.getElementById('OTD$d[packet_id]').style.textDecoration='blink', document.getElementById('OTD$d[packet_id]').style.backgroundColor='$inverse', ";
			$mout="onmouseout=\"document.getElementById('OTD$d[packet_id]').style.textDecoration='none', document.getElementById('OTD$d[packet_id]').style.backgroundColor='$bgColor', ";
			foreach(range('a','e') as $alpha){
				$mover .= "document.getElementById('OTD$d[packet_id]$alpha').style.textDecoration='blink', document.getElementById('OTD$d[packet_id]$alpha').style.backgroundColor='".str_replace("OTD$d[packet_id]","OTD$d[packet_id]$alpha",$inverse)."', ";
				$mout .= "document.getElementById('OTD$d[packet_id]$alpha').style.textDecoration='none', document.getElementById('OTD$d[packet_id]$alpha').style.backgroundColor='".str_replace("OTD$d[packet_id]","OTD$d[packet_id]$alpha",$bgColor)."', ";
			}
			$js .= substr($mover,0,-2)."\"".substr($mout,0,-2)."\"";
			$data .= "<li $js title='Affidavit: $d[affidavit_status] Service Status: $d[service_status]' style='background-color:".$colorCode.";'><a href='http://staff.mdwestserve.com/otd/order.php?packet=$d[packet_id]' target='_Blank'>OTD$d[packet_id]</a>: <strong>".$hours."</strong> $d[circuit_court] <em> <small>[".id2attorney($d[attorneys_id])."]</small></em>".$case."</li>";
		}
	}
	$data.='</ol>';
	return $data;
}

//begin evictionPackets functions:******************************************************
function evictionActiveList($id,$packet){ $_SESSION[active]++;
	$data='<ol>';
	if ($packet != '0' && $packet != ''){
		$r=@mysql_query("select eviction_id, affidavit_status, service_status, filing_status, circuit_court, attorneys_id, estFileDate, case_no, caseLookupFlag, TIMEDIFF( NOW(), date_received) as hours, DATEDIFF(estFileDate, CURDATE()) as estHours from evictionPackets where server_id='$id' and affidavit_status = 'SERVICE CONFIRMED' and filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND eviction_id < '$packet'   order by  eviction_id");
	}else{
		$r=@mysql_query("select eviction_id, affidavit_status, service_status, filing_status, circuit_court, attorneys_id, estFileDate, case_no, caseLookupFlag, TIMEDIFF( NOW(), date_received) as hours, DATEDIFF(estFileDate, CURDATE()) as estHours from evictionPackets where server_id='$id' and affidavit_status = 'SERVICE CONFIRMED' and filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT'    order by  eviction_id");
	}
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){ $_SESSION[active2]++;
		$estHours=($d[estHours]*24)-date('G');
		if (stripHours($d[hours]) > $_SESSION[cap]){
			if ($d[case_no] == '' && $d[caseLookupFlag] != '0'){
				$case="<span style='background-color:#FFFFFF'><small>NO CASE!</small></span>";
			}else{
				$case='';
			}
			if ($d[affidavit_status2] == 'REOPENED'){
				$case .= " - <span style='background-color:#FFFFFF'><small>REOPENED</small></span>";
			}
			$estFileDate=explode('-',$d[estFileDate]);
			$estFileDate=$estFileDate[1].'-'.$estFileDate[2];
			$case .= "&nbsp;<span title='$estHours Hours Remaining' style='background-color:".colorCode2($estHours)."; border: 1px solid black;'>FILE: $estFileDate</span>";
			$evWithCourier=withCourier($d[eviction_id],'EV');
			if($evWithCourier != ''){
				$case.=$evWithCourier;
			}else{
				$evPrepExplode = prepExplode($d[eviction_id],'eviction_id','evictionPackets');
				if ($evPrepExplode != ''){
					$case .= $evPrepExplode;
				}
			}
			$colorCode=colorCode(stripHours($d[hours]),'','');
			$bgColor=substr($colorCode,0,6);
			$inverse=inverseHex($bgColor);
			if (strtolower($inverse) == 'ffffff'){
				$inverse .= "', document.getElementById('EV$d[eviction_id]').style.color='000000";
				$bgColor .= "', document.getElementById('EV$d[eviction_id]').style.color='FFFFFF";
			}
			$js = "id='EV$d[eviction_id]' onmouseover=\"document.getElementById('EV$d[eviction_id]').style.textDecoration='blink', document.getElementById('EV$d[eviction_id]').style.backgroundColor='$inverse'\" onmouseout=\"document.getElementById('EV$d[eviction_id]').style.textDecoration='none', document.getElementById('EV$d[eviction_id]').style.backgroundColor='$bgColor'\"";
			$data .= "<li $js title='Affidavit: $d[affidavit_status] Service Status: $d[service_status]' style='background-color:".$colorCode.";'><a href='http://staff.mdwestserve.com/ev/order.php?packet=$d[eviction_id]' target='_Blank'>EV$d[eviction_id]</a>: <strong>".stripHours($d[hours])."</strong> $d[circuit_court] <em> <small>[".id2attorney($d[attorneys_id])."]</small></em>".$case."</li>";
		}
	}
	$data.='</ol>';
	return $data;
}
function getServers($packet){
	$i=0;
	if ($packet != ''){
		$pkt=" AND packet_id <= '$_GET[packet]'";
		$pkt2=" AND eviction_id <= '$_GET[packet]'";
	}
	$r=@mysql_query("SELECT DISTINCT server_id from ps_packets where affidavit_status = 'SERVICE CONFIRMED' and filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND service_status <> 'MAIL ONLY'$pkt");
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		$list["$i"] = $d[server_id];
		$exclude .= " AND server_id <> '$d[server_id]'";
		$i++;
	}
	$r=@mysql_query("SELECT DISTINCT server_id from evictionPackets where   affidavit_status = 'SERVICE CONFIRMED' and   filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY'$pkt2$exclude");
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		$list["$i"] = $d[server_id];
		$i++;
	}
	if ($list != ''){
		ksort($list);
		return $list;
	}
}
$pkt='';
if ($_GET[packet]){
	$pkt=" AND packet_id <= '$_GET[packet]'";
	$pkt2=" AND eviction_id <= '$_GET[packet]'";
}
?>
<style>
body { padding:0px; margin:0px; margin-left:10px;}
fieldset {width:350px; font-size:12px; padding-top:0px; padding-bottom:0px; padding-right:0px; background-color:#CCCCCC}
.note {width: 340px; padding-left:10px;}
legend { border:solid 1px; padding:5px; background-color:#66CCFF; }
ol { padding:0px;}
li { border-bottom:solid 1px #CCCCCC; }
a {font-size:none; text-decoration:none; font-weight:bold;}
a:hover {font-size:underline overline; color:#6600FF;}
a:visited {font-weight:bold; color:CC6600;}
head { display: block; }
background-color:#6699CC; background-repeat: no-repeat; }
</style>
<table><tr><td valign="top">
<form style='display:inline;'>Only display packets below: <input name='packet' <? if ($_GET[packet]){echo "value='".$_GET[packet]."'";}else{ echo "value='0'";}?>> <input type="submit" value="Go"></form>
</td></tr><tr><td>
<table><tr><td valign='top'><?
$i=0;
$servers=getServers($_GET[packet]);
while ($i < count($servers)){
	echo "<fieldset><legend>Slot 1: ".id2name($servers["$i"])." #".$servers["$i"]."</legend>".presaleActiveList($servers["$i"],'',$_GET[packet]).evictionActiveList($servers["$i"],$_GET[packet])."</fieldset>";
	$i++;
}
/*$q="SELECT DISTINCT server_id from ps_packets where affidavit_status = 'SERVICE CONFIRMED' and filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND service_status <> 'MAIL ONLY'$pkt";
$r=@mysql_query($q);
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
echo "<fieldset><legend>Slot 1: ".id2name($d[server_id])." #$d[server_id]</legend>".presaleActiveList($d[server_id],'',$_GET[packet])."</fieldset>";
}*/
?></td><td valign='top'><?
$q="SELECT DISTINCT server_ida from ps_packets where  affidavit_status = 'SERVICE CONFIRMED' and   filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND service_status <> 'MAIL ONLY'$pkt and server_ida <> ''";
$r=@mysql_query($q);
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
echo "<fieldset><legend>Slot 2: ".id2name($d[server_ida])." #$d[server_ida]</legend>".presaleActiveList($d[server_ida],'a',$_GET[packet])."</fieldset>";
}
?></td><td valign='top'><?
$q="SELECT DISTINCT server_idb from ps_packets where  affidavit_status = 'SERVICE CONFIRMED' and   filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND service_status <> 'MAIL ONLY'$pkt and server_idb <> ''";
$r=@mysql_query($q);
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
echo "<fieldset><legend>Slot 3: ".id2name($d[server_idb])." #$d[server_idb]</legend>".presaleActiveList($d[server_idb],'b',$_GET[packet])."</fieldset>";
}
?></td><td valign='top'><?
$q="SELECT DISTINCT server_idc from ps_packets where  affidavit_status = 'SERVICE CONFIRMED' and   filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND service_status <> 'MAIL ONLY'$pkt and server_idc <> ''";
$r=@mysql_query($q);
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
echo "<fieldset><legend>Slot 4: ".id2name($d[server_idc])." #$d[server_idc]</legend>".presaleActiveList($d[server_idc],'c',$_GET[packet])."</fieldset>";
}
?></td><td valign='top'><?
$q="SELECT DISTINCT server_idd from ps_packets where  affidavit_status = 'SERVICE CONFIRMED' and   filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND service_status <> 'MAIL ONLY'$pkt  and server_idd <> ''";
$r=@mysql_query($q);
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
echo "<fieldset><legend>Slot 5: ".id2name($d[server_idd])." #$d[server_idd]</legend>".presaleActiveList($d[server_idd],'d',$_GET[packet])."</fieldset>";
}
?></td><td valign='top'><?
$q="SELECT DISTINCT server_ide from ps_packets where  affidavit_status = 'SERVICE CONFIRMED' and   filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND service_status <> 'MAIL ONLY'$pkt and server_ide <> ''";
$r=@mysql_query($q);
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
echo "<fieldset><legend>Slot 6: ".id2name($d[server_ide])." #$d[server_ide]</legend>".presaleActiveList($d[server_ide],'e',$_GET[packet])."</fieldset>";
}
?></td></tr></table>
</td></tr><tr><td>
<center><div style="border-style:solid 1px; border-collapse:collapse; font-weight:bold; letter-spacing: 5px;background-color:CC66BB; width:800px;">MAIL ONLY</div></center>
<table><tr><td valign='top'><?
$q="SELECT DISTINCT closeOut FROM ps_packets WHERE filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND service_status='MAIL ONLY'$pkt ORDER BY closeOut ASC";
//$q2="SELECT packet_id, process_status FROM ps_packets WHERE affidavit_status = 'SERVICE CONFIRMED' and filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND service_status='MAIL ONLY'$pkt ORDER BY packet_id ASC";
$r=@mysql_query($q);
$today=date('Y-m-d');
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$q2="SELECT packet_id, process_status FROM ps_packets WHERE filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND service_status='MAIL ONLY' AND closeOut='$d[closeOut]'$pkt ORDER BY packet_id ASC";
	$r2=@mysql_query($q2);
	if ($d[closeOut] > $today){
		$color="background-color:CCCCCC; color:000000;";
	}elseif($d[closeOut] == $today){
		$color="background-color:99DD66; color:000000;";
	}elseif($d[closeOut] == '0000-00-00'){
		$color="background-color:FF0000; color:FFFFFF;";
	}else{
		$color="background-color:000000; color:FFFFFF;";
	}
	echo "<fieldset><legend style='$color'>$d[closeOut]</legend><ol>";
	while ($d2=mysql_fetch_array($r2,MYSQL_ASSOC)){
		if ($d2[process_status] == 'READY TO MAIL'){
			$mailed="AWAITING MAILING";
		}elseif($d2[process_status] == 'AWAITING CONFIRMATION'){
			$mailed="AWAITING CONFIRMATION";
		}else{
			$mailed="MAILED";
		}
		echo "<li style='$color'><a href='/otd/order.php?packet=$d2[packet_id]' target='_blank'>$d2[packet_id]</a> :: $mailed</li>";
	}
	echo "</ol></fieldset>";
}
?></td></tr></table>
</td></tr></table>
<? mysql_close();?>
<script>document.title='Blackhole <?=$_SESSION[active]?> Servers <?=$_SESSION[active2]?> Services';</script>