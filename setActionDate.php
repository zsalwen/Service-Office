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
function makePM($time){
	$time=explode(':',$time);
	$time[0]=$time[0]+12;
	if ($time[0] == 24){
		$time[0]=12;
	}
	$time=implode(':',$time);
	return $time;
}
function dateImplode($date){
	$str=explode(' AT ',$date);
	$time=$str[1];
	$time=explode(' ',$time);
	if ($time[1] == 'PM'){
		$time=makePM($time[0].":00");
	}else{
		$time=$time[0].":00";
	}
	$date2=explode(' ',$str[0]);
	$month=month2num(trim($date2[0]));
	$day=str_replace(',','',$date2[1]);
	$year=$date2[2];
	return $year.'-'.addZero($month).'-'.addZero($day)." $time";
}
function postDateImplode($date){
	$str=explode(' ',$date);
	if ($str[4] == 'PM'){
		$time=makePM($str[3].":00");
	}else{
		$time=$str[3].":00";
	}
	$month=month2num(trim($str[0]));
	$day=str_replace(',','',$str[1]);
	$year=$str[2];
	return $year.'-'.addZero($month).'-'.addZero($day)." $time";
}
function mailExplode($histID){
	$qh="SELECT action_str FROM ps_history WHERE history_id='$histID'";
	$rh=@mysql_query($qh) or die (mysql_error());
	$dh=mysql_fetch_array($rh,MYSQL_ASSOC);
	if ($dh != ''){
		$action=explode('.</LI>',strtoupper($dh[action_str]));
		$dt=explode('ON ',$action[0]);
		$count=count($dt)-1;
		$return=postDateImplode(trim($dt["$count"])." 00:00");
	}
	return $return;
}
function attemptExplode($histID){
	$qh="SELECT action_str, wizard FROM ps_history WHERE history_id='$histID'";
	$rh=@mysql_query($qh) or die (mysql_error());
	$dh=mysql_fetch_array($rh,MYSQL_ASSOC);
	if ($dh != ''){
		$action=explode('</LI>',strtoupper($dh[action_str]));
		$dt=explode('<BR>',$action[1]);
		if ($dh[wizard] == 'POSTING DETAILS'){
			$return=postDateImplode($dt[0]);
		}else{
			$return=dateImplode($dt[0]);
		}
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


function EVmailExplode($histID){
	$qh="SELECT action_str FROM evictionHistory WHERE history_id='$histID'";
	$rh=@mysql_query($qh) or die (mysql_error());
	$dh=mysql_fetch_array($rh,MYSQL_ASSOC);
	if ($dh != ''){
		$action=explode('.</LI>',strtoupper($dh[action_str]));
		$dt=explode('ON ',$action[0]);
		$count=count($dt)-1;
		$return=postDateImplode(trim($dt["$count"])." 00:00");
	}
	return $return;
}
function EVattemptExplode($histID){
	$qh="SELECT wizard, action_str FROM evictionHistory WHERE history_id-'$histID'";
	$rh=@mysql_query($qh) or die (mysql_error());
	$dh=mysql_fetch_array($rh,MYSQL_ASSOC);
	if ($dh != ''){
		$action=explode('</LI>',strtoupper($dh[action_str]));
		$dt=explode('<BR>',$action[1]);
		if ($dh[wizard] == 'POSTING DETAILS'){
			$return=postDateImplode($dt[0]);
		}else{
			$return=dateImplode($dt[0]);
		}
	}
	if ($histID == 4824){
		echo $dt[0];
	}
	return $return;
}
function EVdeliveryExplode($histID){
	$qh="SELECT action_str FROM evictionHistory WHERE history_id='$histID'";
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


$packet=3678;
$q10a="SELECT * from ps_history WHERE packet_id > '$packet' AND actionDate='0000-00-00 00:00:00' ORDER BY history_id ASC";
$r10a=@mysql_query($q10a) or die(mysql_error());
while ($d10a=mysql_fetch_array($r10a, MYSQL_ASSOC)){
	$dt='';
	if ($d10a[wizard] == 'BORROWER' || $d10a[wizard] == 'NOT BORROWER'){
		$dt = deliveryExplode($d10a[history_id]);
		if ($dt != ''){
			@mysql_query("UPDATE ps_history SET actionDate='$dt' WHERE history_id='$d10a[history_id]'") or die (mysql_error());
			echo "<div>$d10a[history_id] :: OTD$d10a[packet_id] :: $dt</div>";
		}else{
			echo "<div style='background-color:red;'>$d10a[history_id] :: OTD$d10a[packet_id] :: $d10a[wizard] :: $dt</div>";
		}	
	}elseif($d10a[action_type] == 'Attempted Service' || $d10a[wizard] == 'POSTING DETAILS'){
		$dt = attemptExplode($d10a[history_id]);
		if ($dt != ''){
			@mysql_query("UPDATE ps_history SET actionDate='$dt' WHERE history_id='$d10a[history_id]'") or die (mysql_error());
			echo "<div>$d10a[history_id] :: OTD$d10a[packet_id] :: $dt</div>";
		}else{
			echo "<div style='background-color:red;'>$d10a[history_id] :: OTD$d10a[packet_id] :: $d10a[wizard] :: $dt</div>";
		}
	}elseif($d10a[wizard] == 'MAILING DETAILS' || $d10a[wizard] == 'INVALID' || $d10a[wizard] == 'CERT MAILING'){
		$dt = mailExplode($d10a[history_id]);
		if ($dt != ''){
			@mysql_query("UPDATE ps_history SET actionDate='$dt' WHERE history_id='$d10a[history_id]'") or die (mysql_error());
			echo "<div>$d10a[history_id] :: OTD$d10a[packet_id] :: $dt</div>";
		}else{
			echo "<div style='background-color:red;'>$d10a[history_id] :: OTD$d10a[packet_id] :: $d10a[wizard] :: $dt</div>";
		}
	}else{
		echo "<div style='background-color:red;'>$d10a[history_id] :: OTD$d10a[packet_id] :: $d10a[wizard]</div>";
	}
}
$q10b="SELECT * from evictionHistory ORDER BY history_id ASC";
$r10b=@mysql_query($q10b) or die(mysql_error());
while ($d10b=mysql_fetch_array($r10b, MYSQL_ASSOC)){
	$dt='';
	if ($d10b[wizard] == 'BORROWER' || $d10b[wizard] == 'NOT BORROWER'){
		$dt = EVdeliveryExplode($d10b[history_id]);
		if ($dt != ''){
			@mysql_query("UPDATE evictionHistory SET actionDate='$dt' WHERE history_id='$d10b[history_id]'") or die (mysql_error());
			echo "<div>$d10b[history_id] :: EV$d10b[eviction_id] :: $dt</div>";
		}else{
			echo "<div style='background-color:red;'>$d10b[history_id] :: EV$d10b[eviction_id] :: $d10b[wizard] :: $dt</div>";
		}
	}elseif($d10b[action_type] == 'Attempted Service' || $d10b[wizard] == 'POSTING DETAILS'){
		$dt = EVattemptExplode($d10b[history_id]);
		if ($dt != ''){
			@mysql_query("UPDATE evictionHistory SET actionDate='$dt' WHERE history_id='$d10b[history_id]'") or die (mysql_error());
			echo "<div>$d10b[history_id] :: EV$d10b[eviction_id] :: $dt</div>";
		}else{
			echo "<div style='background-color:red;'>$d10b[history_id] :: EV$d10b[eviction_id] :: $d10b[wizard] :: $dt</div>";
		}
	}elseif($d10b[wizard] == 'MAILING DETAILS' || $d10b[wizard] == 'INVALID'){
		$dt = EVmailExplode($d10b[history_id]);
		if ($dt != ''){
			@mysql_query("UPDATE evictionHistory SET actionDate='$dt' WHERE history_id='$d10b[history_id]'") or die (mysql_error());
			echo "<div>$d10b[history_id] :: EV$d10b[eviction_id] :: $dt</div>";
		}else{
			echo "<div style='background-color:red;'>$d10b[history_id] :: EV$d10b[eviction_id] :: $d10b[wizard] :: $dt</div>";
		}
	}else{
		echo "<div style='background-color:red;'>$d10b[history_id] :: EV$d10b[eviction_id] :: $d10b[wizard]</div>";
	}
}

?>