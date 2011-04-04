<?
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

hardLog('Active Service Report','user');
session_start();
//opLog($_COOKIE[psdata][name]." Loaded Assigned Cases");
$_SESSION[active]='';
$_SESSION[active2]='';
$_SESSION[active3]='';

$_SESSION[cap] = -1;
if ($_GET[cap]){
	$_SESSION[cap] = $_GET[cap];
}

function id2name($id){
	$q="SELECT name FROM ps_users WHERE id = '$id' LIMIT 0,1";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
return $d[name];
}

function id2attorney($id){
	$q="SELECT display_name FROM attorneys WHERE attorneys_id = '$id' LIMIT 0,1";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
return $d[display_name];
}
function initals($str){
	$str = explode(' ',$str);
	return strtoupper(substr($str[0],0,1).substr($str[1],0,1).substr($str[2],0,1).substr($str[3],0,1));
}
function id2server($id){
	$q=@mysql_query("SELECT name from ps_users where id='$id' LIMIT 0,1") or die(mysql_error());
	$d=mysql_fetch_array($q, MYSQL_ASSOC);
	return initals($d[name]);
}

function stripHours($date){
	$hours = explode(':',$date);
	return $hours[0];
}

function colorCode($hours,$packet,$letter){
	if ($hours <= 120){ $html = "00FF00"; }
	if ($hours > 120 && $hours <= 168){ $html = "ffFF00"; }
	if ($hours > 168 &&  $hours <= 288){ $html = "ff0000; color:000000;"; }
	if ($hours > 288){ $html = "000000; color:ffffff"; }
	if ($packet != ''){
		$r=@mysql_query("SELECT serveComplete, serveCompletea, serveCompleteb, serveCompletec, serveCompleted, serveCompletee from ps_packets where packet_id = '$packet' LIMIT 0,1");
		$d=mysql_fetch_array($r, MYSQL_ASSOC);
		if ($letter != '' && $d["serveComplete$letter"] == '+'){
			$html .= ";border-style:solid;border-width:5px;border-color:CCFF00";
		}elseif($letter != '' && $d["serveComplete$letter"] == '-'){
			$html .= ";border-style:solid;border-width:5px;border-color:FF00FF";
		}
	}
	return $html;
}

function colorCode2($hours){
	if ($hours <= -24){ return "000000; color:FFFFFF !important;"; }
	if ($hours > -24 && $hours <= 0){ return "FF0000; color:000000 !important;"; }
	if ($hours > 0 && $hours <= 24){ return "FFFF00; color:000000 !important;"; }
	if ($hours > 48){ return "00FF00; color:000000 !important;"; }
}
function abbrCounty($str){
	if ($str == "ALLEGANY"){
		return "ALGNY";
	}elseif($str == "ANNE ARUNDEL"){
		return "AA";
	}elseif($str == "BALTIMORE"){
		return "BALCO";
	}elseif($str == "BALTIMORE CITY"){
		return "BALCI";
	}elseif($str == "CALVERT"){
		return "CAL";
	}elseif($str == "CAROLINE"){
		return "CARLN";
	}elseif($str == "CARROLL"){
		return "CARR";
	}elseif($str == "CECIL"){
		return "CEC";
	}elseif($str == "CHARLES"){
		return "CHAR";
	}elseif($str == "DORCHESTER"){
		return "DOR";
	}elseif($str == "FREDERICK"){
		return "FRED";
	}elseif($str == "GARRETT"){
		return "GAR";
	}elseif($str == "HARFORD"){
		return "HAR";
	}elseif($str == "HOWARD"){
		return "HOW";
	}elseif($str == "KENT"){
		return "KENT";
	}elseif($str == "MONTGOMERY"){
		return "MONT";
	}elseif($str == "PRINCE GEORGES"){
		return "PG";
	}elseif($str == "QUEEN ANNES"){
		return "QA";
	}elseif($str == "SOMERSET"){
		return "SOM";
	}elseif($str == "ST MARYS"){
		return "SM";
	}elseif($str == "TALBOT"){
		return "TAL";
	}elseif($str == "WASHINGTON"){
		return "WASH";
	}elseif($str == "WICOMICO"){
		return "WIC";
	}elseif($str == "WORCESTER"){
		return "WOR";
	}
}
function justDate($dt){
	$date=explode(' ',$dt);
	return $date[0];
}
function justDate2($dt){
	$date=explode(' ',$dt);
	$date2=explode('-',$date[0]);
	if ($date2[0] != date('Y')){
		return $date[0];
	}else{
		return $date2[1]."-".$date2[2];
	}
}
function serverActiveList($id,$letter){ $_SESSION[active]++;
	$data='<ol>';
	$r=@mysql_query("select packet_id, avoidDOT, reopenDate, date_received, filing_status, request_close, request_closea, request_closeb, request_closec, request_closed, request_closee, affidavit_status, service_status, circuit_court, dispatchDate, attorneys_id, estFileDate, rush, TIMEDIFF( NOW(), date_received) as hours, DATEDIFF( CURDATE(), reopenDate) as reopenHours, DATEDIFF(estFileDate, CURDATE()) as estHours from ps_packets where server_id$letter='$id' and (process_status = 'Assigned' OR process_status = 'ASSIGNED') order by  packet_id");
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){ $_SESSION[active2]++;
		$_SESSION[active3]++;
		$estHours=($d[estHours]*24)-date('G');
		if ($d[filing_status] == 'REOPENED'){
			$hours=$d[reopenHours]*24;
			$reopenDate=explode('-',$d[reopenDate]);
			$reopenDate=$reopenDate[1].'-'.$reopenDate[2];
			$reopen = " <span style='background-color:#FFFFFF; color:000000 !important;'><small>ReO:&nbsp;$reopenDate</small></span>";
		}else{
			$hours=stripHours($d[hours]);
			$reopen='';
		}
		$reopen .= " <span style='background-color:#AAAAAA; color:FFFFFF;'>DISP:&nbsp;".justDate2($d[dispatchDate])."</span>";
		if ($d[avoidDOT] != ''){
			$reopen .= " <span style='background-color:#000000; color:FF0000; border: 3px solid red; font-weight:bold;'>AvoidDOT</span>";
		}
		if ($d)
		$estFileDate=explode('-',$d[estFileDate]);
		$estFileDate=$estFileDate[1].'-'.$estFileDate[2];
		$reopen .= " <span title='$estHours Hours Remaining' style='background-color:".colorCode2($estHours)." border: 1px solid black;'>FILE:&nbsp;".$estFileDate."</span>";
		if ($d[rush] != ''){
			$reopen .= " <span style='background-color:#000000; color:FF00FF; border: 3px solid black; font-weight:bold;'>RUSH</span>";
		}
		if ($hours > $_SESSION[cap]){
			$data .= "<li title='Affidavit: $d[affidavit_status] Service Status: $d[service_status]' style='background-color:".colorCode($hours,$d[packet_id],$letter).";'>";
			if ($d[request_close] || $d[request_closea] || $d[request_closeb] || $d[request_closec] || $d[request_closed] || $d[request_closee]){
				$data .= "<a href='http://service.mdwestserve.com/wizard.php?jump=".$d[packet_id]."-1' target='_blank' style='background-color:#00FFFF;'><b>QC</b></a> ";
			}
			$data .= "<a href='http://staff.mdwestserve.com/otd/order.php?packet=$d[packet_id]' target='_Blank'>OTD$d[packet_id]</a>: <strong>".$hours."</strong> ".abbrCounty(strtoupper($d[circuit_court]))." <em> <small>[".id2attorney($d[attorneys_id])."]</small></em> ".$reopen."</li>";
		}
	}
	$data.='</ol>';
	return $data;
}

//begin evictionPackets functions:******************************************************
function evictionActiveList($id){ $_SESSION[active]++;
$data='<ol>';
$r=@mysql_query("select eviction_id, date_received, request_close, affidavit_status, service_status, circuit_court, dispatchDate, attorneys_id, estFileDate, TIMEDIFF( NOW(), date_received) as hours, DATEDIFF(estFileDate, CURDATE()) as estHours from evictionPackets where server_id='$id' and (process_status = 'Assigned' OR process_status = 'ASSIGNED') order by  eviction_id");
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){ $_SESSION[active2]++;
	$estHours=($d[estHours]*24)-date('G');
	$_SESSION[active3]++;
	if (stripHours($d[hours]) > $_SESSION[cap]){
		if ($d[request_close]){
			$mod="<a href='http://service.mdwestserve.com/ev_wizard.php?jump=".$d[eviction_id]."-1' target='_blank' style='background-color:#00FFFF;'><b>QC</b></a> ";
		}else{
			$mod="";
		}
		$estFileDate=explode('-',$d[estFileDate]);
		$estFileDate=$estFileDate[1].'-'.$estFileDate[2];
		$data .= "<li title='Affidavit: $d[affidavit_status] Service Status: $d[service_status]' style='background-color:".colorCode(stripHours($d[hours]),'','').";'>".$mod."<a href='http://staff.mdwestserve.com/ev/order.php?packet=$d[eviction_id]' target='_Blank'>EV$d[eviction_id]</a>: <strong>".stripHours($d[hours])."</strong> ".abbrCounty(strtoupper($d[circuit_court]))." <em> <small>[".id2attorney($d[attorneys_id])."]</small></em><span style='background-color:#AAAAAA; color:FFFFFF;'>DISP: ".justDate2($d[dispatchDate])."</span><span title='$estHours Hours Remaining' style='background-color:".colorCode2($estHours)." border: 1px solid black;'>FILE: $estFileDate</span></li>";
	}
}
$data.='</ol>';
return $data;
}

function standardActiveList($id,$letter){ $_SESSION[active]++;
	$data='<ol>';
	$r=@mysql_query("select packet_id, avoidDOT, reopenDate, date_received, filing_status, request_close, request_closea, request_closeb, request_closec, request_closed, request_closee, affidavit_status, service_status, circuit_court, dispatchDate, attorneys_id, estFileDate, rush, TIMEDIFF( NOW(), date_received) as hours, DATEDIFF( CURDATE(), reopenDate) as reopenHours, DATEDIFF(estFileDate, CURDATE()) as estHours from standard_packets where server_id$letter='$id' and (process_status = 'Assigned' OR process_status = 'ASSIGNED') order by  packet_id") or die (mysql_error());
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){ $_SESSION[active2]++;
		$_SESSION[active3]++;
		$estHours=($d[estHours]*24)-date('G');
		if ($d[filing_status] == 'REOPENED'){
			$hours=$d[reopenHours]*24;
			$reopenDate=explode('-',$d[reopenDate]);
			$reopenDate=$reopenDate[1].'-'.$reopenDate[2];
			$reopen = " <span style='background-color:#FFFFFF; color:000000 !important;'><small>ReO:&nbsp;$reopenDate</small></span>";
		}else{
			$hours=stripHours($d[hours]);
			$reopen='';
		}
		$reopen .= " <span style='background-color:#AAAAAA; color:FFFFFF;'>DISP:&nbsp;".justDate2($d[dispatchDate])."</span>";
		if ($d[avoidDOT] != ''){
			$reopen .= " <span style='background-color:#000000; color:FF0000; border: 3px solid red; font-weight:bold;'>AvoidDOT</span>";
		}
		if ($d)
		$estFileDate=explode('-',$d[estFileDate]);
		$estFileDate=$estFileDate[1].'-'.$estFileDate[2];
		$reopen .= " <span title='$estHours Hours Remaining' style='background-color:".colorCode2($estHours)." border: 1px solid black;'>FILE:&nbsp;".$estFileDate."</span>";
		if ($d[rush] != ''){
			$reopen .= " <span style='background-color:#000000; color:FF00FF; border: 3px solid black; font-weight:bold;'>RUSH</span>";
		}
		if ($hours > $_SESSION[cap]){
			$data .= "<li title='Affidavit: $d[affidavit_status] Service Status: $d[service_status]' style='background-color:".colorCode($hours,$d[packet_id],$letter).";'>";
			if ($d[request_close] || $d[request_closea] || $d[request_closeb] || $d[request_closec] || $d[request_closed] || $d[request_closee]){
				$data .= "<a href='http://service.mdwestserve.com/wizard.php?jump=".$d[packet_id]."-1' target='_blank' style='background-color:#00FFFF;'><b>QC</b></a> ";
			}
			$data .= "<a href='http://staff.mdwestserve.com/standard/order.php?packet=$d[packet_id]' target='_Blank'>S$d[packet_id]</a>: <strong>".$hours."</strong> ".abbrCounty(strtoupper($d[circuit_court]))." <em> <small>[".id2attorney($d[attorneys_id])."]</small></em> ".$reopen."</li>";
		}
	}
	$data.='</ol>';
	return $data;
}

function getServers(){
	$i=0;
	$r=@mysql_query("SELECT DISTINCT server_id from ps_packets WHERE process_status = 'ASSIGNED'");
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		$list["$i"] = $d[server_id];
		$exclude .= " AND server_id <> '$d[server_id]'";
		$i++;
	}
	$r=@mysql_query("SELECT DISTINCT server_id from evictionPackets WHERE process_status = 'ASSIGNED'$exclude");
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		$list["$i"] = $d[server_id];
		$exclude .= " AND server_id <> '$d[server_id]'";
		$i++;
	}
	$r=@mysql_query("SELECT DISTINCT server_id from standard_packets WHERE process_status = 'ASSIGNED'$exclude");
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		$list["$i"] = $d[server_id];
		$i++;
	}
	ksort($list);
	return $list;
}
function getServers2($letter){
	$i=0;
	$r=@mysql_query("SELECT DISTINCT server_id$letter from ps_packets WHERE process_status = 'ASSIGNED' AND server_id$letter <> ''");
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		$list["$i"] = $d["server_id$letter"];
		$exclude .= " AND server_id <> '".$d["server_id$letter"]."'";
		$i++;
	}
	$r=@mysql_query("SELECT DISTINCT server_id$letter from standard_packets WHERE process_status = 'ASSIGNED' AND server_id$letter <> ''$exclude");
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		$list["$i"] = $d["server_id$letter"];
		$i++;
	}
	ksort($list);
	return $list;
}
?>
<table>
<tr><td valign="top">

<? 
$r66=@mysql_query("select packet_id, server_id, server_ida, server_idb, server_idc, server_idd, server_ide, affidavit_status2, filing_status, rush, TIMEDIFF( NOW(), date_received) as hours, DATEDIFF( CURDATE(), reopenDate) as reopenHours from ps_packets where affidavit_status2 <> '' order by packet_id"); 
while ($d66=mysql_fetch_array($r66,MYSQL_ASSOC)){
	if ($d66[affidavit_status2] == 'REOPENED'){
		$hours=$d66[reopenHours]*24;
	}else{
		$hours=stripHours($d66[hours]);
	}
	if ($d66[rush] != ''){
		$reopen .= "-<span style='background-color:#000000; color:FF00FF; border: 3px solid black; font-weight:bold;'>RUSH</span>";
	}
	$list .= "<li><a style='background-color:".colorCode($hours,$d66[packet_id],'')."; font-weight:normal !important;' href='http://staff.mdwestserve.com/otd/order.php?packet=$d66[packet_id]' target='_Blank'>OTD$d66[packet_id]: $d66[affidavit_status2]";
	if ($d66[server_id]){ $list .= '('.id2server($d66[server_id]).')';}
	if ($d66[server_ida]){ $list .= '('.id2server($d66[server_ida]).')';}
	if ($d66[server_idb]){ $list .= '('.id2server($d66[server_idb]).')';}
	if ($d66[server_idc]){ $list .= '('.id2server($d66[server_idc]).')';}
	if ($d66[server_idd]){ $list .= '('.id2server($d66[server_idd]).')';}
	if ($d66[server_ide]){ $list .= '('.id2server($d66[server_ide]).')';}
	$list .= "</a>$reopen</li>";
}

$r67=@mysql_query("select eviction_id, server_id, affidavit_status2, TIMEDIFF( NOW(), date_received) as hours from evictionPackets where affidavit_status2 <> '' order by eviction_id");
while ($d67=mysql_fetch_array($r67,MYSQL_ASSOC)){
	$hours=stripHours($d67[hours]);
	$list .= "<li><a style='background-color:".colorCode($hours,$d67[packet_id],'')."; font-weight:normal !important;' href='http://staff.mdwestserve.com/ev/order.php?packet=$d67[eviction_id]' target='_Blank'>EV$d67[eviction_id]: $d67[affidavit_status2]";
	if ($d67[server_id]){ $list .= '('.id2server($d67[server_id]).')';}
	$list .="</a></li>";
}

$r66=@mysql_query("select packet_id, server_id, server_ida, server_idb, server_idc, server_idd, server_ide, affidavit_status2, filing_status, rush, TIMEDIFF( NOW(), date_received) as hours, DATEDIFF( CURDATE(), reopenDate) as reopenHours from standard_packets where affidavit_status2 <> '' order by packet_id"); 
while ($d66=mysql_fetch_array($r66,MYSQL_ASSOC)){
	if ($d66[affidavit_status2] == 'REOPENED'){
		$hours=$d66[reopenHours]*24;
	}else{
		$hours=stripHours($d66[hours]);
	}
	if ($d66[rush] != ''){
		$reopen .= "-<span style='background-color:#000000; color:FF00FF; border: 3px solid black; font-weight:bold;'>RUSH</span>";
	}
	$list .= "<li><a style='background-color:".colorCode($hours,$d66[packet_id],'')."; font-weight:normal !important;' href='http://staff.mdwestserve.com/standard/order.php?packet=$d66[packet_id]' target='_Blank'>S$d66[packet_id]: $d66[affidavit_status2]";
	if ($d66[server_id]){ $list .= '('.id2server($d66[server_id]).')';}
	if ($d66[server_ida]){ $list .= '('.id2server($d66[server_ida]).')';}
	if ($d66[server_idb]){ $list .= '('.id2server($d66[server_idb]).')';}
	if ($d66[server_idc]){ $list .= '('.id2server($d66[server_idc]).')';}
	if ($d66[server_idd]){ $list .= '('.id2server($d66[server_idd]).')';}
	if ($d66[server_ide]){ $list .= '('.id2server($d66[server_ide]).')';}
	$list .= "</a>$reopen</li>";
}

if ($list != ''){
	echo "<fieldset style='background-color:FF0000;width: 600px;'><legend>Custom Watch List</legend>$list</fieldset></td><td align='right'>";
}?>
<center><form>Start at <input name="cap" value="<?=$_SESSION[cap];?>" size="3" /> hours <input type="submit" value="Go!" /></form></center>
</td></tr><tr><td valign='top' colspan='2'>
<table>
<tr><td valign='top'><?
$servers=getServers();
$i=0;
while ($i < count($servers)){
	echo "<fieldset><legend>Slot 1: ".id2name($servers["$i"])." #".$servers["$i"]."</legend>".serverActiveList($servers["$i"],'').evictionActiveList($servers["$i"]).standardActiveList($servers["$i"],'')."</fieldset>";
	$i++;
}
/*$q="SELECT DISTINCT server_id from ps_packets WHERE process_status = 'ASSIGNED'";
$r=@mysql_query($q);
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
echo "<fieldset><legend>Slot 1: ".id2name($d[server_id])." #$d[server_id]</legend>".serverActiveList($d[server_id],'')."</fieldset>";
}*/
?></td><td valign='top'><?
$serversa=getServers2('a');
$i=0;
while ($i < count($serversa)){
	echo "<fieldset><legend>Slot 2: ".id2name($serversa["$i"])." #".$serversa["$i"]."</legend>".serverActiveList($serversa["$i"],'a').standardActiveList($serversa["$i"],'a')."</fieldset>";
	$i++;
}
?></td><td valign='top'><?
$serversb=getServers2('b');
$i=0;
while ($i < count($serversb)){
	echo "<fieldset><legend>Slot 3: ".id2name($serversb["$i"])." #".$serversb["$i"]."</legend>".serverActiveList($serversb["$i"],'b').standardActiveList($serversb["$i"],'b')."</fieldset>";
	$i++;
}
?></td><td valign='top'><?
$serversc=getServers2('c');
$i=0;
while ($i < count($serversc)){
	echo "<fieldset><legend>Slot 4: ".id2name($serversc["$i"])." #".$serversc["$i"]."</legend>".serverActiveList($serversc["$i"],'c').standardActiveList($serversc["$i"],'c')."</fieldset>";
	$i++;
}
?></td><td valign='top'><?
$serversd=getServers2('d');
$i=0;
while ($i < count($serversd)){
	echo "<fieldset><legend>Slot 5: ".id2name($serversd["$i"])." #".$serversd["$i"]."</legend>".serverActiveList($serversd["$i"],'d').standardActiveList($serversd["$i"],'d')."</fieldset>";
	$i++;
}
?></td><td valign='top'><?
$serverse=getServers2('e');
$i=0;
while ($i < count($serverse)){
	echo "<fieldset><legend>Slot 6: ".id2name($serverse["$i"])." #".$serverse["$i"]."</legend>".serverActiveList($serverse["$i"],'e').standardActiveList($serverse["$i"],'e')."</fieldset>";
	$i++;
}
?></td></tr></table>
<style>
body { padding:0px; margin:0px; margin-left:10px;}
fieldset {font-size:12px; padding:0px; background-color:#CCCCCC}
legend { border:solid 1px; padding-left:5px; padding-right:5px; background-color:#66CCFF; }
ol, table { padding:0px;}
li { border-bottom:solid 1px #CCCCCC; padding:0px; }
a {font-size:none; text-decoration:none; font-weight:bold;}
a:hover {font-size:underline overline; color:#6600FF;}
a:visited {font-weight:bold; color:CC6600;}
</style>
<script>document.title='<?=$_SESSION[active3]?> Active <?=$_SESSION[active]?> Servers <?=$_SESSION[active2]?> Services';</script>
</td></tr></table>
<meta http-equiv="refresh" content="600" />
<? mysql_close(); ?>