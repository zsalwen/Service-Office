<?
include 'common.php';
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
function month2num($month){
	if (strtoupper($month) == 'JANUARY' || $month == 1){
		return '1';
	}elseif (strtoupper($month) == 'FEBRUARY' || $month == 2){
		return '2';
	}elseif (strtoupper($month) == 'MARCH' || $month == 3){
		return '3';
	}elseif (strtoupper($month) == 'APRIL' || $month == 4){
		return '4';
	}elseif (strtoupper($month) == 'MAY' || $month == 5){
		return '5'; 
	}elseif (strtoupper($month) == 'JUNE' || $month == 6){
		return '6';
	}elseif (strtoupper($month) == 'JULY' || $month == 7){
		return '7';
	}elseif (strtoupper($month) == 'AUGUST' || $month == 8){
		return '8';
	}elseif (strtoupper($month) == 'SEPTEMBER' || $month == 9){
		return '9';
	}elseif (strtoupper($month) == 'OCTOBER' || $month == 10){
		return '10';
	}elseif (strtoupper($month) == 'NOVEMBER' || $month == 11){
		return '11';
	}elseif (strtoupper($month) == 'DECEMBER' || $month == 12){
		return '12'; 
	}else{
		return $month;
	}
}
function dateExplode($date){
	$date=explode('-',$date);
	$date=monthConvert($date[1])." ".$date[2].", ".$date[0];
	return $date;
}
function addZero($num){
	if (strlen($num) == 1){
		return "0".$num;
	}else{
		return $num;
	}
}
function dateImplode($date){
	$str=explode(' AT ',$date);
	$time=str_replace(' ','',$str[1]);
	$date2=explode(' ',$str[0]);
	$month=month2num(trim($date2[0]));
	$day=str_replace(',','',$date2[1]);
	$year=$date2[2];
	return $year.'-'.addZero($month).'-'.addZero($day);
}
function postDateImplode($date){
	$str=explode(' ',$date);
	$time=$str[3].$str[4];
	$month=month2num(trim($str[0]));
	$day=str_replace(',','',$str[1]);
	$year=$str[2];
	return $year.'-'.addZero($month).'-'.addZero($day);
}
function mailExplode($histID){
	$qh="SELECT action_str FROM ps_history WHERE history_id='$histID'";
	$rh=@mysql_query($qh) or die (mysql_error());
	$dh=mysql_fetch_array($rh,MYSQL_ASSOC);
	if ($dh != ''){
		$action=explode('.</LI>',strtoupper($dh[action_str]));
		$dt=explode('ON ',$action[0]);
		$count=count($dt)-1;
		$return=postDateImplode(trim($dt["$count"]));
	}
	return $return;
}

function deliveryExplode($histID){
	$qh="SELECT action_str FROM ps_history WHERE history_id='$histID'";
	$rh=@mysql_query($qh) or die (mysql_error());
	$dh=mysql_fetch_array($rh,MYSQL_ASSOC);
	if ($dh != ''){
		$action=explode('DATE OF SERVICE: ',strtoupper($dh[action_str]));
		$dt=explode('<BR>',$action[1]);
		$return=dateImplode($dt[0]);
	}
	return $return;
}
function updateCO($co,$packet){
	$q="SELECT closeOut, service_status FROM ps_packets WHERE packet_id='$packet'";
	$r=@mysql_query($q) or die (mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	if ($d[service_status] != 'CANCELLED' && $d[service_status] != 'IN PROGRESS'){
		if ($d[closeOut] == '0000-00-00'){
			@mysql_query("UPDATE ps_packets SET closeOut='$co' WHERE packet_id='$packet'");
			echo "<div style='border:2px solid red;'>$packet :: $co</div>";
			hardLog('updating closeOut to '.$co.' For OTD'.$packet,'debug');
		}else{
			if ($d[closeOut] < $co){
				@mysql_query("UPDATE ps_packets SET closeOut='$co' WHERE packet_id='$packet'");
				echo "<div style='border:2px solid red;'>$packet :: $co</div>";
				hardLog('updating closeOut to '.$co.' For OTD'.$packet,'debug');
			}
		}
	}
}
	$q="SELECT packet_id, service_status from ps_packets WHERE closeOut='0000-00-00' AND packet_id >= '$_GET[start]'";
	$r=@mysql_query($q) or die(mysql_error());
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		$packet=$d[packet_id];
		if ($d[service_status] == 'CANCELLED' || $d[service_status] == 'IN PROGRESS'){
			echo "<div>$packet :: $d[service_status]</div>";
		}else{
			$q10a="SELECT history_id from ps_history WHERE packet_id='$packet' AND (wizard='BORROWER' OR wizard='NOT BORROWER')";
			$r10a=@mysql_query($q10a) or die(mysql_error());
			//also Mailing Details
			$q10b="SELECT history_id from ps_history WHERE packet_id='$packet' AND (wizard='MAILING DETAILS')";
			$r10b=@mysql_query($q10b) or die(mysql_error());
			while ($d10a=mysql_fetch_array($r10a, MYSQL_ASSOC)){
				$closeOut='';
				$closeOut = deliveryExplode($d10a[history_id]);
				updateCO($closeOut,$packet);
				echo "<div>$packet :: $closeOut</div>";
			}
			while ($d10b=mysql_fetch_array($r10b, MYSQL_ASSOC)){
				$closeOut='';
				$closeOut = mailExplode($d10b[history_id]);
				updateCO($closeOut,$packet);
				echo "<div>$packet :: $closeOut</div>";
			}
		}
	}
?>