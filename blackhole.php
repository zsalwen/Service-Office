<?
include 'common.php';
hardLog('Post Service Report','user');
session_start();
opLog($_COOKIE[psdata][name]." Loaded Assigned Cases");
$_SESSION[active]='';
$_SESSION[active2]='';

$_SESSION[cap] = -1;
if ($_GET[cap]){
$_SESSION[cap] = $_GET[cap];
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
	if ($hours > 48){ return "00FF00; color:000000 !important;"; }
}

function withCourier($packet){
	$q="SELECT * from docuTrack WHERE packet='$packet' and document='OUT WITH COURIER'";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	if ($d[packet]){
		$document="<div class='note' style='background-color:#ffFF00;'><small>".$d[binder]." OUT WITH COURIER by ".$d[location]."</small></div>";
	}
	return $document;
}


function prepExplode($packet){
	$q="SELECT timeline FROM ps_packets WHERE packet_id='$packet'";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		$timeline=explode('<br>',$d[timeline]);
		for($i = 0; $i < count($timeline); $i++){
			if (strpos($timeline[$i], "Prepared Affidavits for Filing")){
				if ($timeList != ''){
					$timeList .= "<br>$timeline[$i]";
				}else{
					$timeList = "<br><div class='note' style='background-color:#6699CC;'><small>$timeline[$i]";
				}
			}
		}
	}
	$timeList .= "</small></div>";
	return $timeList;
}

function serverActiveList($id,$packet){ $_SESSION[active]++;
	$data='<ol>';
	if ($packet != '0' && $packet != ''){
		$r=@mysql_query("select packet_id, avoidDOT, affidavit_status, service_status, filing_status, circuit_court, attorneys_id, estFileDate, case_no, caseLookupFlag, reopenDate, affidavit_status2, rush, TIMEDIFF( NOW(), date_received) as hours, DATEDIFF( CURDATE(), reopenDate) as reopenHours, DATEDIFF(estFileDate, CURDATE()) as estHours from ps_packets where server_id='$id' and affidavit_status = 'SERVICE CONFIRMED' and filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND packet_id < '$packet'   order by  packet_id");
	}else{
		$r=@mysql_query("select packet_id, avoidDOT, affidavit_status, service_status, filing_status, circuit_court, attorneys_id, estFileDate, case_no, caseLookupFlag, reopenDate, affidavit_status2, rush, TIMEDIFF( NOW(), date_received) as hours, DATEDIFF( CURDATE(), reopenDate) as reopenHours, DATEDIFF(estFileDate, CURDATE()) as estHours from ps_packets where server_id='$id' and affidavit_status = 'SERVICE CONFIRMED' and filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT'    order by  packet_id");
	}
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
			$withCourier=withCourier($d[packet_id]);
			if($withCourier != ''){
				$case.=$withCourier;
			}else{
				$prepExplode = prepExplode($d[packet_id]);
				if ($prepExplode != ''){
					$case .= $prepExplode;
				}
			}
			$data .= "<li title='Affidavit: $d[affidavit_status] Service Status: $d[service_status]' style='background-color:".colorCode($hours,$d[packet_id],'').";'><a href='http://staff.mdwestserve.com/otd/order.php?packet=$d[packet_id]' target='_Blank'>$d[packet_id]</a>: <strong>".$hours."</strong> $d[circuit_court] <em> <small>[".id2attorney($d[attorneys_id])."]</small></em>".$case."</li>";
		}
	}
	$data.='</ol>';
	return $data;
}
function serverActiveLista($id,$packet){
	$data='<ol>';
	if ($packet != '0' && $packet != ''){
		$r=@mysql_query("select packet_id, avoidDOT, affidavit_status, service_status, filing_status, circuit_court, attorneys_id, estFileDate, case_no, caseLookupFlag, reopenDate, affidavit_status2, rush, TIMEDIFF( NOW(), date_received) as hours, DATEDIFF( CURDATE(), reopenDate) as reopenHours, DATEDIFF(estFileDate, CURDATE()) as estHours from ps_packets where server_ida='$id' and affidavit_status = 'SERVICE CONFIRMED' and filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND packet_id < '$packet'   order by  packet_id");
	}else{
		$r=@mysql_query("select packet_id, avoidDOT, affidavit_status, service_status, filing_status, circuit_court, attorneys_id, estFileDate, case_no, caseLookupFlag, reopenDate, affidavit_status2, rush, TIMEDIFF( NOW(), date_received) as hours, DATEDIFF( CURDATE(), reopenDate) as reopenHours, DATEDIFF(estFileDate, CURDATE()) as estHours from ps_packets where server_ida='$id' and affidavit_status = 'SERVICE CONFIRMED' and filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT'    order by  packet_id");
	}
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){ 
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
			$withCourier=withCourier($d[packet_id]);
			if($withCourier != ''){
				$case.=$withCourier;
			}else{
				$prepExplode = prepExplode($d[packet_id]);
				if ($prepExplode != ''){
					$case .= $prepExplode;
				}
			}
			$data .= "<li title='Affidavit: $d[affidavit_status] Service Status: $d[service_status]' style='background-color:".colorCode($hours,$d[packet_id],'a').";'><a href='http://staff.mdwestserve.com/otd/order.php?packet=$d[packet_id]' target='_Blank'>$d[packet_id]</a>: <strong>".$hours."</strong> $d[circuit_court] <em> <small>[".id2attorney($d[attorneys_id])."]</small></em>".$case."</li>";
		}
	}
	$data.='</ol>';
	return $data;
}
function serverActiveListb($id,$packet){ 
	$data='<ol>';
	if ($packet != '0' && $packet != ''){
		$r=@mysql_query("select packet_id, avoidDOT, affidavit_status, service_status, filing_status, circuit_court, attorneys_id, estFileDate, case_no, caseLookupFlag, reopenDate, affidavit_status2, rush, TIMEDIFF( NOW(), date_received) as hours, DATEDIFF( CURDATE(), reopenDate) as reopenHours, DATEDIFF(estFileDate, CURDATE()) as estHours from ps_packets where server_idb='$id' and affidavit_status = 'SERVICE CONFIRMED' and filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND packet_id < '$packet'   order by  packet_id");
	}else{
		$r=@mysql_query("select packet_id, avoidDOT, affidavit_status, service_status, filing_status, circuit_court, attorneys_id, estFileDate, case_no, caseLookupFlag, reopenDate, affidavit_status2, rush, TIMEDIFF( NOW(), date_received) as hours, DATEDIFF( CURDATE(), reopenDate) as reopenHours, DATEDIFF(estFileDate, CURDATE()) as estHours from ps_packets where server_idb='$id' and affidavit_status = 'SERVICE CONFIRMED' and filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT'    order by  packet_id");
	}
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){ 
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
			$withCourier=withCourier($d[packet_id]);
			if($withCourier != ''){
				$case.=$withCourier;
			}else{
				$prepExplode = prepExplode($d[packet_id]);
				if ($prepExplode != ''){
					$case .= $prepExplode;
				}
			}
			$data .= "<li title='Affidavit: $d[affidavit_status] Service Status: $d[service_status]' style='background-color:".colorCode($hours,$d[packet_id],'b').";'><a href='http://staff.mdwestserve.com/otd/order.php?packet=$d[packet_id]' target='_Blank'>$d[packet_id]</a>: <strong>".$hours."</strong> $d[circuit_court] <em> <small>[".id2attorney($d[attorneys_id])."]</small></em>".$case."</li>";
		}
	}
	$data.='</ol>';
	return $data;
}
function serverActiveListc($id,$packet){ 
	$data='<ol>';
	if ($packet != '0' && $packet != ''){
		$r=@mysql_query("select packet_id, avoidDOT, affidavit_status, service_status, filing_status, circuit_court, attorneys_id, estFileDate, case_no, caseLookupFlag, reopenDate, affidavit_status2, rush, TIMEDIFF( NOW(), date_received) as hours, DATEDIFF( CURDATE(), reopenDate) as reopenHours, DATEDIFF(estFileDate, CURDATE()) as estHours from ps_packets where server_idc='$id' and affidavit_status = 'SERVICE CONFIRMED' and filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND packet_id < '$packet'   order by  packet_id");
	}else{
		$r=@mysql_query("select packet_id, avoidDOT, affidavit_status, service_status, filing_status, circuit_court, attorneys_id, estFileDate, case_no, caseLookupFlag, reopenDate, affidavit_status2, rush, TIMEDIFF( NOW(), date_received) as hours, DATEDIFF( CURDATE(), reopenDate) as reopenHours, DATEDIFF(estFileDate, CURDATE()) as estHours from ps_packets where server_idc='$id' and affidavit_status = 'SERVICE CONFIRMED' and filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT'    order by  packet_id");
	}
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
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
			$withCourier=withCourier($d[packet_id]);
			if($withCourier != ''){
				$case.=$withCourier;
			}else{
				$prepExplode = prepExplode($d[packet_id]);
				if ($prepExplode != ''){
					$case .= $prepExplode;
				}
			}
			$data .= "<li title='Affidavit: $d[affidavit_status] Service Status: $d[service_status]' style='background-color:".colorCode($hours,$d[packet_id],'c').";'><a href='http://staff.mdwestserve.com/otd/order.php?packet=$d[packet_id]' target='_Blank'>$d[packet_id]</a>: <strong>".$hours."</strong> $d[circuit_court] <em> <small>[".id2attorney($d[attorneys_id])."]</small></em>".$case."</li>";
		}
	}
	$data.='<li><a href="desktop.php">Procede to desktop &gt; &gt; &gt;</a></li></ol>';
	return $data;
}
function serverActiveListd($id,$packet){ 
	if ($packet != '0' && $packet != ''){
		$r=@mysql_query("select packet_id, avoidDOT, affidavit_status, service_status, filing_status, circuit_court, attorneys_id, estFileDate, case_no, caseLookupFlag, reopenDate, affidavit_status2, rush, TIMEDIFF( NOW(), date_received) as hours, DATEDIFF( CURDATE(), reopenDate) as reopenHours, DATEDIFF(estFileDate, CURDATE()) as estHours from ps_packets where server_idd='$id' and affidavit_status = 'SERVICE CONFIRMED' and filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND packet_id < '$packet'   order by  packet_id");
	}else{
		$r=@mysql_query("select packet_id, avoidDOT, affidavit_status, service_status, filing_status, circuit_court, attorneys_id, estFileDate, case_no, caseLookupFlag, reopenDate, affidavit_status2, rush, TIMEDIFF( NOW(), date_received) as hours, DATEDIFF( CURDATE(), reopenDate) as reopenHours, DATEDIFF(estFileDate, CURDATE()) as estHours from ps_packets where server_idd='$id' and affidavit_status = 'SERVICE CONFIRMED' and filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT'    order by  packet_id");
	}
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){ 
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
			$withCourier=withCourier($d[packet_id]);
			if($withCourier != ''){
				$case.=$withCourier;
			}else{
				$prepExplode = prepExplode($d[packet_id]);
				if ($prepExplode != ''){
					$case .= $prepExplode;
				}
			}
			$data .= "<li title='Affidavit: $d[affidavit_status] Service Status: $d[service_status]' style='background-color:".colorCode($hours,$d[packet_id],'d').";'><a href='http://staff.mdwestserve.com/otd/order.php?packet=$d[packet_id]' target='_Blank'>$d[packet_id]</a>: <strong>".$hours."</strong> $d[circuit_court] <em> <small>[".id2attorney($d[attorneys_id])."]</small></em>".$case."</li>";
		}
	}
	$data.='</ol>';
	return $data;
}
function serverActiveListe($id,$packet){
	$data='<ol>';
	if ($packet != '0' && $packet != ''){
		$r=@mysql_query("select packet_id, avoidDOT, affidavit_status, service_status, filing_status, circuit_court, attorneys_id, estFileDate, case_no, caseLookupFlag, reopenDate, affidavit_status2, rush, TIMEDIFF( NOW(), date_received) as hours, DATEDIFF( CURDATE(), reopenDate) as reopenHours, DATEDIFF(estFileDate, CURDATE()) as estHours from ps_packets where server_ide='$id' and affidavit_status = 'SERVICE CONFIRMED' and filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND packet_id < '$packet'   order by  packet_id");
	}else{
		$r=@mysql_query("select packet_id, avoidDOT, affidavit_status, service_status, filing_status, circuit_court, attorneys_id, estFileDate, case_no, caseLookupFlag, reopenDate, affidavit_status2, rush, TIMEDIFF( NOW(), date_received) as hours, DATEDIFF( CURDATE(), reopenDate) as reopenHours, DATEDIFF(estFileDate, CURDATE()) as estHours from ps_packets where server_ide='$id' and affidavit_status = 'SERVICE CONFIRMED' and filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT'    order by  packet_id");
	}
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){ 
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
			$withCourier=withCourier($d[packet_id]);
			if($withCourier != ''){
				$case.=$withCourier;
			}else{
				$prepExplode = prepExplode($d[packet_id]);
				if ($prepExplode != ''){
					$case .= $prepExplode;
				}
			}
			$data .= "<li title='Affidavit: $d[affidavit_status] Service Status: $d[service_status]' style='background-color:".colorCode($hours,$d[packet_id],'e').";'><a href='http://staff.mdwestserve.com/otd/order.php?packet=$d[packet_id]' target='_Blank'>$d[packet_id]</a>: <strong>".$hours."</strong> $d[circuit_court] <em> <small>[".id2attorney($d[attorneys_id])."]</small></em>".$case."</li>";
		}
	}
	$data.='</ol>';
	return $data;
}

//begin evictionPackets functions:******************************************************
function evPrepExplode($eviction){
	$q="SELECT timeline FROM evictionPackets WHERE eviction_id='$eviction'";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		$timeline=explode('<br>',$d[timeline]);
		for($i = 0; $i < count($timeline); $i++){
			if (strpos($timeline[$i], "Prepared Affidavits for Filing")){
				if ($timeList != ''){
					$timeList .= "<br>$timeline[$i]";
				}else{
					$timeList = "<br><div class='note' style='background-color:#6699CC;'><small>$timeline[$i]";
				}
			}
		}
	}
	$timeList .= "</small></div>";
	return $timeList;
}

function evWithCourier($eviction){
	$eviction_id="EV".$eviction;
	$q="SELECT * from docuTrack WHERE packet='$eviction_id' and document='OUT WITH COURIER'";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	if ($d){
		$document="<div class='note' style='background-color:#ffFF00;'><small>".$d[binder]." OUT WITH COURIER by ".$d[location]."</small></div>";
	}
	return $document;
}

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
			$evWithCourier=evWithCourier($d[eviction_id]);
			if($evWithCourier != ''){
				$case.=$evWithCourier;
			}else{
				$evPrepExplode = evPrepExplode($d[eviction_id]);
				if ($evPrepExplode != ''){
					$case .= $evPrepExplode;
				}
			}
			$data .= "<li title='Affidavit: $d[affidavit_status] Service Status: $d[service_status]' style='background-color:".colorCode(stripHours($d[hours]),'','').";'><a href='http://staff.mdwestserve.com/ev/order.php?packet=$d[eviction_id]' target='_Blank'>$d[eviction_id]</a>: <strong>".stripHours($d[hours])."</strong> $d[circuit_court] <em> <small>[".id2attorney($d[attorneys_id])."]</small></em>".$case."</li>";
		}
	}
	$data.='</ol>';
	return $data;
}
?>
<style>
body { padding:0px; margin:0px; margin-left:10px;}
fieldset {width:350px; font-size:12px; padding-top:0px; padding-bottom:0px; background-color:#CCCCCC}
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
<form>Only display packets below: <input name='packet' <? if ($_GET[packet]){echo "value='".$_GET[packet]."'";}else{ echo "value='0'";}?>> <input type="submit" value="Go"></form>
</td></tr><tr><td>
<center><div style="border-style:solid 1px; border-collapse:collapse; font-weight:bold; letter-spacing: 5px;background-color:00BBAA; width:800px;">FORECLOSURES</div></center>
<table><tr><td valign='top'><?
if ($_GET[packet]){
	$q="SELECT DISTINCT server_id from ps_packets where affidavit_status = 'SERVICE CONFIRMED' and filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND service_status <> 'MAIL ONLY' AND packet_id <= '$_GET[packet]'";
}else{
	$q="SELECT DISTINCT server_id from ps_packets where affidavit_status = 'SERVICE CONFIRMED' and   filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND service_status <> 'MAIL ONLY'";
}
$r=@mysql_query($q);
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
echo "<fieldset><legend>Slot 1: ".id2name($d[server_id])." #$d[server_id]</legend>".serverActiveList($d[server_id],$_GET[packet])."</fieldset>";
}
?></td><td valign='top'><?
if ($_GET[packet]){
	$q="SELECT DISTINCT server_ida from ps_packets where  affidavit_status = 'SERVICE CONFIRMED' and   filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND service_status <> 'MAIL ONLY' AND packet_id <= '$_GET[packet]'  and server_ida <> ''";
}else{
	$q="SELECT DISTINCT server_ida from ps_packets where  affidavit_status = 'SERVICE CONFIRMED' and   filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND service_status <> 'MAIL ONLY' and server_ida <> ''";
}
$r=@mysql_query($q);
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
echo "<fieldset><legend>Slot 2: ".id2name($d[server_ida])." #$d[server_ida]</legend>".serverActiveLista($d[server_ida],$_GET[packet])."</fieldset>";
}
?></td><td valign='top'><?
if ($_GET[packet]){
	$q="SELECT DISTINCT server_idb from ps_packets where  affidavit_status = 'SERVICE CONFIRMED' and   filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND packet_id AND service_status <> 'MAIL ONLY' <= '$_GET[packet]'  and server_idb <> ''";
}else{
	$q="SELECT DISTINCT server_idb from ps_packets where  affidavit_status = 'SERVICE CONFIRMED' and   filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND service_status <> 'MAIL ONLY' and server_idb <> ''";
}
$r=@mysql_query($q);
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
echo "<fieldset><legend>Slot 3: ".id2name($d[server_idb])." #$d[server_idb]</legend>".serverActiveListb($d[server_idb],$_GET[packet])."</fieldset>";
}
?></td><td valign='top'><?
if ($_GET[packet]){
	$q="SELECT DISTINCT server_idc from ps_packets where  affidavit_status = 'SERVICE CONFIRMED' and   filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND service_status <> 'MAIL ONLY' AND packet_id <= '$_GET[packet]'  and server_idc <> ''";
}else{
	$q="SELECT DISTINCT server_idc from ps_packets where  affidavit_status = 'SERVICE CONFIRMED' and   filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND service_status <> 'MAIL ONLY' and server_idc <> ''";
}
$r=@mysql_query($q);
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
echo "<fieldset><legend>Slot 4: ".id2name($d[server_idc])." #$d[server_idc]</legend>".serverActiveListc($d[server_idc],$_GET[packet])."</fieldset>";
}
?></td><td valign='top'><?
if ($_GET[packet]){
	$q="SELECT DISTINCT server_idd from ps_packets where  affidavit_status = 'SERVICE CONFIRMED' and   filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND service_status <> 'MAIL ONLY' AND packet_id <= '$_GET[packet]'  and server_idd <> ''";
}else{
	$q="SELECT DISTINCT server_idd from ps_packets where  affidavit_status = 'SERVICE CONFIRMED' and   filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND service_status <> 'MAIL ONLY' and server_idd <> ''";
}
$r=@mysql_query($q);
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
echo "<fieldset><legend>Slot 5: ".id2name($d[server_idd])." #$d[server_idd]</legend>".serverActiveListd($d[server_idd],$_GET[packet])."</fieldset>";
}
?></td><td valign='top'><?
if ($_GET[packet]){
	$q="SELECT DISTINCT server_ide from ps_packets where  affidavit_status = 'SERVICE CONFIRMED' and   filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND service_status <> 'MAIL ONLY' AND packet_id <= '$_GET[packet]'  and server_ide <> ''";
}else{
	$q="SELECT DISTINCT server_ide from ps_packets where  affidavit_status = 'SERVICE CONFIRMED' and   filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND service_status <> 'MAIL ONLY' and server_ide <> ''";
}
$r=@mysql_query($q);
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
echo "<fieldset><legend>Slot 6: ".id2name($d[server_ide])." #$d[server_ide]</legend>".serverActiveListe($d[server_ide],$_GET[packet])."</fieldset>";
}
?></td></tr></table><tr><td>
<center><div style="border-style:solid 1px; border-collapse:collapse; font-weight:bold; letter-spacing: 5px; background-color:99AAEE; width:800px;">EVICTIONS</div></center>
</td></tr><tr><td>
<table><tr><td valign='top'>
<!--------------------------------BEGIN EVICTIONS---------------------->
<?
if ($_GET[packet]){
	$q="SELECT DISTINCT server_id from evictionPackets where   affidavit_status = 'SERVICE CONFIRMED' and   filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND eviction_id <= '$_GET[packet]'";
}else{
	$q="SELECT DISTINCT server_id from evictionPackets where   affidavit_status = 'SERVICE CONFIRMED' and   filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY'   ";
}
$r=@mysql_query($q);
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
echo "<fieldset><legend>".id2name($d[server_id])." #$d[server_id]</legend>".evictionActiveList($d[server_id],$_GET[packet])."</fieldset>";
}
?>
<!--------------------------------BEGIN MAIL ONLYS------------------------------>
</td></tr></table><tr><td>
<center><div style="border-style:solid 1px; border-collapse:collapse; font-weight:bold; letter-spacing: 5px;background-color:CC66BB; width:800px;">MAIL ONLY</div></center>
<table><tr><td valign='top'><?
if ($_GET[packet]){
	$q="SELECT DISTINCT closeOut FROM ps_packets WHERE affidavit_status = 'SERVICE CONFIRMED' and filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND service_status='MAIL ONLY' AND packet_id <= '$_GET[packet]' ORDER BY closeOut ASC";
	$q2="SELECT packet_id, process_status FROM ps_packets WHERE affidavit_status = 'SERVICE CONFIRMED' and filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND service_status='MAIL ONLY' AND packet_id <= '$_GET[packet]' ORDER BY packet_id ASC";
}else{
	$q="SELECT DISTINCT closeOut FROM ps_packets WHERE affidavit_status = 'SERVICE CONFIRMED' and   filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND service_status='MAIL ONLY' ORDER BY closeOut ASC";
	$q2="SELECT packet_id, process_status FROM ps_packets WHERE affidavit_status = 'SERVICE CONFIRMED' and   filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND service_status='MAIL ONLY' ORDER BY packet_id ASC";
}
$r=@mysql_query($q);
$r2=@mysql_query($q2);
$today=date('Y-m-d');
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	if ($_GET[packet]){
		$q2="SELECT packet_id, process_status FROM ps_packets WHERE affidavit_status = 'SERVICE CONFIRMED' and filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND service_status='MAIL ONLY' AND closeOut='$d[closeOut]' AND packet_id <= '$_GET[packet]' ORDER BY packet_id ASC";
	}else{
		$q2="SELECT packet_id, process_status FROM ps_packets WHERE affidavit_status = 'SERVICE CONFIRMED' and   filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND service_status='MAIL ONLY' AND closeOut='$d[closeOut]' ORDER BY packet_id ASC";
	}
	if ($d[closeOut] > $today){
		$color="background-color:CCCCCC; color:000000;";
	}elseif($d[closeOut] == $today){
		$color="background-color:99DD66; color:000000;";
	}else{
		$color="background-color:000000; color:FFFFFF;";
	}
	echo "<fieldset><legend style='$color'>$d[closeOut]</legend><ol>";
	while ($d2=mysql_fetch_array($r2,MYSQL_ASSOC)){
		if ($d2[process_status] == 'READY TO MAIL'){
			$mailed="AWAITING MAILING";
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