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
function dateImplode($date){
	$str=explode(' AT ',$date);
	$time=str_replace(' ','',$str[1]);
	$date2=explode(' ',$str[0]);
	$month=month2num(trim($date2[0]));
	$day=str_replace(',','',$date2[1]);
	$year=$date2[2];
	return $month.'/'.$day.'/'.$year.' @ '.$time;
}
function postDateImplode($date){
	$str=explode(' ',$date);
	$time=$str[3].$str[4];
	$month=month2num(trim($str[0]));
	$day=str_replace(',','',$str[1]);
	$year=$str[2];
	return $month.'/'.$day.'/'.$year.' @ '.$time;
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
function historyList($packet,$defendant,$attorneys_id){
	$qn="SELECT * FROM ps_history WHERE packet_id = '$packet' and defendant_id = '$defendant' order by history_id ASC";
		$rn=@mysql_query($qn);
		$list = "<div>";
		$counter=0;
		while ($dn=mysql_fetch_array($rn, MYSQL_ASSOC)){$counter++;
			$action_str=str_replace('<LI>','',strtoupper($dn[action_str]));
			$action_str=str_replace('</LI>','',$action_str);
				$list .=  "<div class='list'>#$dn[history_id] : ".id2server($dn[serverID]).' '.$dn[wizard].'<br>'.stripslashes(attorneyCustomLang($attorneys_id,$action_str));
				if ($dn[wizard] == 'BORROWER' || $dn[wizard] == 'NOT BORROWER'){
					$list .=  '<br>'.attorneyCustomLang($attorneys_id,$dn[residentDesc]);
				}
				$list .= "</div>";
		}
		$list .=  "</div>";
		return $list;
}
function nameDef($defendant,$packet){
	$r=@mysql_query("SELECT name".$defendant." from ps_packets WHERE packet_id='$packet'") or die (mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	return strtoupper($d["name$defendant"]);
}
function deliveryExplode($packet,$defendant){
	$qh="SELECT action_str, serverID, address, wizard, resident, residentDesc FROM ps_history WHERE packet_id='$packet' AND defendant_id='$defendant' AND (WIZARD='BORROWER' OR WIZARD='NOT BORROWER')";
	$rh=@mysql_query($qh) or die (mysql_error());
	$dh=mysql_fetch_array($rh,MYSQL_ASSOC);
	if ($dh != ''){
		$action=explode('DATE OF SERVICE: ',strtoupper($dh[action_str]));
		$dt=explode('<BR>',$action[1]);
		$dt=dateImplode($dt[0]);
		$serverID=id2name($dh[serverID]);
		if ($dh[wizard] == 'BORROWER'){
			$serverID .= " To: ".nameDef($defendant,$packet);
		}else{
			$serverID .= " To: ".strtoupper($dh[resident]);
		}
		$address=explode(',',$dh[address]);
		$PDAddress=substr($address[0],0,16);
		$residentDesc=$dh[residentDesc];
		$entry="<table align='center'>
			<tr>
			<td width='300px' style='border-bottom:solid 1px'>Personal Delivery @ ".$PDAddress."</td>
			<td width='30px' style='border-bottom:solid 1px'>Date:</td>
			<td width='140px' style='border-bottom:solid 1px'>".$dt."</td>
			<td width='200px' style='border-bottom:solid 1px'>By: ".$serverID."</td>
			<td width='600px' style='border-bottom:solid 1px'>Desc: ".$residentDesc."</td>
			</tr>
			</table>";
	}
	return $entry;
}
function descExplode($str){
	$action_str=explode('<BR>',$str);
	$count=count($action_str)-1;
	return $action_str["$count"];
}
function attemptExplode($packet,$defendant,$address,$type){
	$qh="SELECT action_str, serverID FROM ps_history WHERE packet_id='$packet' AND defendant_id='$defendant' AND action_str LIKE '%$address%' AND WIZARD='$type'";
	$rh=@mysql_query($qh) or die (mysql_error());
	while ($dh=mysql_fetch_array($rh,MYSQL_ASSOC)){
		if ($dh != ''){
			$action=explode('</LI>',strtoupper($dh[action_str]));
			$dt=explode('<BR>',$action[1]);
			$attAddress=$dt[1];
			if ($type == 'POSTING DETAILS'){
				$dt=postDateImplode($dt[0]);
			}else{
				$dt=dateImplode($dt[0]);
			}
			$serverID=id2name($dh[serverID]);
			$desc=descExplode($dh[action_str]);
			$entry .= "<table align='center'>
				<tr>
				<td width='350px' style='border-bottom:solid 1px'>$attAddress</td>
				<td width='30px' style='border-bottom:solid 1px'>Date:</td>
				<td width='140px' style='border-bottom:solid 1px'>$dt</td>
				<td width='150px' style='border-bottom:solid 1px'>By: $serverID</td>
				<td width='600px' style='border-bottom:solid 1px'>Desc: $desc</td>
				</tr>
				</table>";
		}
	}
	return $entry;
}
?>
<style>
body{padding:0px; margin:0px;}
td{font-size:11px;}
div{font-size:10px; text-align:left;}
fieldset, legend, div, table, tr, td, input {padding:0px;}
div.list {border-bottom:solid 1px;}
</style>
<?
//service summary (for client updates)
$packet=$_GET[packet];
$q="SELECT * from ps_packets WHERE packet_id='$packet'";
$r=@mysql_query($q) or die(mysql_error());
$d=mysql_fetch_array($r, MYSQL_ASSOC);
$i=0;
$date=date("m/d/Y h:i:s A");
echo "<div class='list'>Service Summary for Process Serving Packet $packet - <b>Printed $date</b></div>";
while ($i < 6){$i++;
	if ($d["name$i"]){
		echo "<fieldset>";
		echo "<legend>".$d["name$i"]."</legend>";
		echo deliveryExplode($packet,$i);
		if ($d["address$i"]){
			$address=strtoupper($d["address$i"]);
			echo attemptExplode($packet,$i,$address,"FIRST EFFORT");
			echo attemptExplode($packet,$i,$address,"SECOND EFFORT");
			echo attemptExplode($packet,$i,$address,"POSTING DETAILS");
		}
		foreach(range('a','e') as $letter){
			if ($d["address$i$letter"]){
				$address=strtoupper($d["address$i$letter"]);
				echo attemptExplode($packet,$i,$address,"FIRST EFFORT");
				echo attemptExplode($packet,$i,$address,"SECOND EFFORT");
				echo attemptExplode($packet,$i,$address,"POSTING DETAILS");
			}
		}
		/*$q1="SELECT * from ps_history WHERE packet_id='$packet' AND defendant_id='$i'"
		$r1=@mysql_query($q1) or die(mysql_error());
		while ($d1=mysql_fetch_array($r1, MYSQL_ASSOC)){
			
		}*/		
		echo "</fieldset>";
	}
}

?>