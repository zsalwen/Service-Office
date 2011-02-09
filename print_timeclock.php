<?
session_start();
include 'functions.php';
mysql_connect();
mysql_select_db('service');
include 'security.php';
$today = $_GET[start];//date('Y-m-d');// we need to get the starting date of the pay week
$qs="SELECT * FROM paychecks WHERE period_start <= '$today' AND period_end >= '$today' ";
$rs=@mysql_query($qs) or die(mysql_error());
$ds = mysql_fetch_array($rs, MYSQL_ASSOC);
$pay_start = $ds[period_start];
$pay_end = $ds[period_end];
$user_id=$_COOKIE[psdata][user_id];
$name=$_COOKIE[psdata][name];
//log_action($_COOKIE[userdata][user_id],"Printing Time Card");
function dayTotal($punch_date,$user_id){
	$q="SELECT * FROM MDWestServeTimeClock WHERE user_id = '$user_id' AND punch_date = '$punch_date' ORDER BY punch_time ASC";
	$r= @mysql_query($q) or die(mysql_error());
	$totalMins=0;
	$totalHours=0;
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		if (strtoupper($d[action]) == 'CLOCK IN'){
			$lastLogin=strtotime($d[punch_time]);
			$hours = '';
			$mins = '';
		}else{
			$logout=strtotime($d[punch_time]);
			$rough=$logout-$lastLogin;
			$hours=floor($rough/3600);
			$mins=number_format($rough/60,0);
			$totalMins=$mins+$totalMins;
			$totalHours=$hours+$totalHours;
		}
	}
	while ($totalMins >= 60){
		$totalMins=$totalMins-60;
	}
	$return=$totalHours.':'.$totalMins;
	return $return;
}
function noteTotal($user_id,$pay_start,$pay_end){
	$i=0;
	$q = "SELECT * FROM MDWestServeTimeClock WHERE user_id = '$user_id' AND punch_date >= '$pay_start' AND punch_date <= '$pay_end' ORDER BY punch_date, punch_time";
	$r = @mysql_query($q);
	while($d = mysql_fetch_array($r, MYSQL_ASSOC)){
		if ($d[note]){$i++;	}
	}
	return $i;
}
function card($user_id,$pay_start,$pay_end,$name){
	$q = "SELECT *,DATE_FORMAT(punch_date, '%W, %M %D, %Y') as punch_date_f, DATE_FORMAT(punch_time, '%r') as punch_time_f FROM MDWestServeTimeClock WHERE user_id = '$user_id' AND punch_date >= '$pay_start' AND punch_date <= '$pay_end' ORDER BY punch_date, punch_time";
	$r = @mysql_query($q);
	?>
	<table width="800px" cellspacing="0px" style="border:solid; page-break-after:always">
		<tr>
			<td colspan="4" align="center" style="padding-top:5px; padding-bottom:13px; padding-left:50px; font-variant:small-caps; font-weight:bold;"><font size="+2"><?=$name?></font><br />The pay week of <?=$pay_start;?> to <?=$pay_end;?>.</td>
		</tr>
	<? 
	$i=0;
	$totalTime=0;
	while($d = mysql_fetch_array($r, MYSQL_ASSOC)){
		$i++;
		$q1="SELECT punch_id FROM MDWestServeTimeClock WHERE user_id = '$user_id' AND punch_date = '$d[punch_date]' ORDER BY punch_time DESC";
		$r1= @mysql_query($q1) or die(mysql_error());
		$d1=mysql_fetch_array($r1,MYSQL_ASSOC);
		if (strtoupper($d[action]) == 'CLOCK IN'){
			$punchDate=$d[punch_date];
			$lastLogin=strtotime($d[punch_time]);
			$hours = '';
			$mins = '';
			$disp="";
		}elseif(strtoupper($d[action]) == 'CLOCK OUT'){
			$logout=strtotime($d[punch_time]);
			$rough=$logout-$lastLogin;
			$hours=floor($rough/3600);
			$mins=number_format($rough/60,0);
			if ($d[punch_id] == $d1[punch_id]){
				$disp="<br><i>DAY'S TOTAL: ".dayTotal($d[punch_date],$_COOKIE[psdata][user_id])."</i>";
			}else{
				$disp="";
			}
			$nextPunch=strtotime($d1[punch_time]);
			//echo $nextPunch.'-'.$lastPunch.' = '.$burble."/60 = ".($burble/60)."<br>";
			//$totalTime=($nextPunch-$lastPunch)+$totalTime;
			while ($mins > 60){
				$mins=$mins-60;
			}
			if ($mins == 60){
				$mins=$mins-60;
				$hours=$hours+1;
			}
			$totalMins=$totalMins+$mins;
			//$mins=number_format($rough/60,0);
			$totalHours=$totalHours+$hours;
			$hours = "<b>$hours hours</b>";
			$mins = "<b>$mins mins</b>";
		}else{
			$hours = '';
			$mins = '';
		}
	?>
		<tr>
			<td style="text-align:center; border-top:solid; border-top-width:1px;"><?=$i?></td>
			<td style="text-align:left; border-top:solid; border-top-width:1px; padding-left:50px;" width="40%"><?=$d[punch_date_f];?></td>
			<td style="text-align:center; border-top:solid; border-top-width:1px;" width="30%"><?=$d[punch_time_f];?></td>
			<td style="text-align:left; border-top:solid; border-top-width:1px;font-variant:small-caps" width="30%"><?=strtoupper($d[action]);?> <?=$hours.' '.$mins;?></td>
		</tr>
	<? }
	while ($totalMins >= 60){
		$totalMins=$totalMins-60;
		$totalHours=$totalHours+1;
	}
	//$totalTime=number_format($totalTime/3600,0);
	?>
		<tr>
			<td></td>
			<td></td>
			<td  style="text-align:right; border-top:solid; border-top-width:1px;font-variant:small-caps;" colspan='2' width="30%">TOTAL: <?=$totalHours.' hours, '.$totalMins.' minutes';?></td>
		</tr>
	</table>
<? }
function noteCard($user_id,$pay_start,$pay_end,$name){
	$q = "SELECT *,DATE_FORMAT(punch_date, '%m/%d') as punch_date_f, DATE_FORMAT(punch_time, '%r') as punch_time_f FROM MDWestServeTimeClock WHERE user_id = '$user_id' AND punch_date >= '$pay_start' AND punch_date <= '$pay_end' ORDER BY punch_date, punch_time";
	$r = @mysql_query($q);
	?>
	<table width="800px" cellspacing="0px" style="border:solid; page-break-after:always">
		<tr>
			<td colspan="5" align="center" style="padding-top:5px; padding-bottom:13px; font-variant:small-caps; font-weight:bold;"><font size="+2"><?=$name?></font><br />The pay week of <?=$pay_start;?> to <?=$pay_end;?>.</td>
		</tr>
	<? 
	$i=0;
	$totalTime=0;
	while($d = mysql_fetch_array($r, MYSQL_ASSOC)){
		$i++;
		$q1="SELECT punch_id, note FROM MDWestServeTimeClock WHERE user_id = '$user_id' AND punch_date = '$d[punch_date]' ORDER BY punch_time DESC";
		$r1= @mysql_query($q1) or die(mysql_error());
		$d1=mysql_fetch_array($r1,MYSQL_ASSOC);
		if (strtoupper($d[action]) == 'CLOCK IN'){
			$punchDate=$d[punch_date];
			$lastLogin=strtotime($d[punch_time]);
			$hours = '';
			$mins = '';
			$disp="";
		}elseif(strtoupper($d[action]) == 'CLOCK OUT'){
			$logout=strtotime($d[punch_time]);
			$rough=$logout-$lastLogin;
			$hours=floor($rough/3600);
			$mins=number_format($rough/60,0);
			if ($d[punch_id] == $d1[punch_id]){
				$disp="<br><i>DAY'S TOTAL: ".dayTotal($d[punch_date],$_COOKIE[psdata][user_id])."</i>";
			}else{
				$disp="";
			}
			$nextPunch=strtotime($d1[punch_time]);
			//echo $nextPunch.'-'.$lastPunch.' = '.$burble."/60 = ".($burble/60)."<br>";
			//$totalTime=($nextPunch-$lastPunch)+$totalTime;
			while ($mins > 60){
				$mins=$mins-60;
			}
			if ($mins == 60){
				$mins=$mins-60;
				$hours=$hours+1;
			}
			$totalMins=$totalMins+$mins;
			//$mins=number_format($rough/60,0);
			$totalHours=$totalHours+$hours;
			$hours = "<b>".$hours."h</b>";
			$mins = "<b>".$mins."m</b>";
		}else{
			$hours = '';
			$mins = '';
		}
	?>
		<tr>
			<td style="text-align:center; border-top:solid; border-top-width:1px;" width='3%'><?=$i?></td>
			<td style="text-align:left; border-top:solid; border-top-width:1px;" width='4%'><?=$d[punch_date_f];?></td>
			<td style="text-align:center; border-top:solid; border-top-width:1px;" width='13%'><?=$d[punch_time_f];?></td>
			<td style="text-align:left; border-top:solid; border-top-width:1px;font-variant:small-caps" width='22%'><?=strtoupper($d[action]);?> <?=$hours.' '.$mins;?></td>
			<td style="text-align:left; border-top:solid; border-top-width:1px;font-variant:small-caps; font-size: 11px;"><?if ($d[note]){ echo $d[note];}else{ echo "&nbsp";}?></td>
		</tr>
	<? }
	while ($totalMins >= 60){
		$totalMins=$totalMins-60;
		$totalHours=$totalHours+1;
	}
	//$totalTime=number_format($totalTime/3600,0);
	?>
		<tr>
			<td></td>
			<td></td>
			<td  style="text-align:right; border-top:solid; border-top-width:1px;font-variant:small-caps;" colspan='3' width="30%">TOTAL (EXCLUDING LUNCH HOURS): <?=$totalHours.' hours, '.$totalMins.' minutes';?></td>
		</tr>
	</table>
	<? 
}
card($user_id,$pay_start,$pay_end,$name);
if (noteTotal($user_id,$pay_start,$pay_end)){
	noteCard($user_id,$pay_start,$pay_end,$name);
}
?>