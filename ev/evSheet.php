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
function dateExplode($date){
	$date=explode('-',$date);
	$date=monthConvert($date[1])." ".$date[2].", ".$date[0];
	return $date;
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
	$date=str_replace(' AT ',' ',$date);
	$str=explode(' ',$date);
	$time=$str[3].$str[4];
	$month=month2num(trim($str[0]));
	$day=str_replace(',','',$str[1]);
	$year=$str[2];
	return $month.'/'.$day.'/'.$year.' @ '.$time;
}
function nameDef($defendant,$packet){
	$r=@mysql_query("SELECT name".$defendant." from evictionPackets WHERE eviction_id='$packet'") or die (mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	return strtoupper($d["name$defendant"]);
}
function deliveryExplode($eviction,$defendant){
	$qh="SELECT action_str, serverID, address, resident, wizard FROM evictionHistory WHERE eviction_id='$eviction' AND defendant_id='$defendant' AND (WIZARD='BORROWER' OR WIZARD='NOT BORROWER') AND onAffidavit='checked'";
	$rh=@mysql_query($qh) or die (mysql_error());
	$dh=mysql_fetch_array($rh,MYSQL_ASSOC);
	if ($dh != ''){
		$action=explode('DATE OF SERVICE: ',strtoupper($dh[action_str]));
		$dt=explode('<BR>',$action[1]);
		$return[0]=dateImplode($dt[0]);
		$return[1]=id2name($dh[serverID]);
		if ($dh[wizard] == 'BORROWER'){
			$return[1] .= " To: ".nameDef($defendant,$eviction);
		}else{
			$return[1] .= " To: ".strtoupper($dh[resident]);
		}
		$address=explode(',',$dh[address]);
		$return[2]=substr($address[0],0,16);
	}
	return $return;
}
function attemptExplode($eviction,$defendant,$address,$type){
	$qh="SELECT action_str, serverID FROM evictionHistory WHERE eviction_id='$eviction' AND defendant_id='$defendant' AND action_str LIKE '%$address%' AND WIZARD='$type' AND onAffidavit='checked'";
	$rh=@mysql_query($qh) or die (mysql_error());
	$dh=mysql_fetch_array($rh,MYSQL_ASSOC);
	if ($dh != ''){
		$action=explode('</LI>',strtoupper($dh[action_str]));
		$dt=explode('<BR>',$action[1]);
		if ($type == 'POSTING DETAILS'){
			$return[0]=postDateImplode($dt[0]);
		}else{
			$return[0]=dateImplode($dt[0]);
		}
		$return[1]=id2name($dh[serverID]);
	}
	return $return;
}
function historyList($eviction,$defendant,$attorneys_id){
	$qn="SELECT * FROM evictionHistory WHERE eviction_id = '$eviction' and defendant_id = '$defendant' order by history_id ASC";
	$rn=@mysql_query($qn);
	$list = "<div>";
	$counter=0;
	while ($dn=mysql_fetch_array($rn, MYSQL_ASSOC)){$counter++;
		$action_str=str_replace('<LI>','',strtoupper($dn[action_str]));
		$action_str=str_replace('</LI>','',$action_str);
			$list .=  "<div class='list'>#$dn[history_id] : ".id2server($dn[serverID]).' '.$dn[wizard].'<br>'.stripslashes(attorneyCustomLang($attorneys_id,$action_str));
			if ($dn[wizard] == 'NOT BORROWER'){
				$list .=  '<br>'.attorneyCustomLang($attorneys_id,$dn[residentDesc]);
			}
			$list .= "</div>";
	}
	$list .=  "</div>";
	return $list;
}
function evSheet($eviction){
	$q="SELECT * FROM evictionPackets WHERE eviction_id='$eviction'";
	$r=@mysql_query($q) or die(mysql_error());
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	$date=date("m/d/Y h:i:s A");
	ob_start();
	?>
	<style>
	body{padding:0px; margin:0px;}
	td{font-size:11px;}
	div{font-size:10px; text-align:left;}
	div.list {border-bottom:solid 1px; font-size:10px;}
	</style>
	<table width="80%" align="center"><tr><td>
	<fieldset>
	<legend accesskey="C">Quality Control for EVICTION <?=$d[eviction_id]?> - <b>Printed <?=$date?></b></legend>
	<fieldset>
	<legend>Billing Matrix</legend>
	<table width="100%" align="center"><tr>
	<td width="20%" align="left" style="border-bottom:solid 1px;"><b>Service: <?=$d[bill410]?></b></td>
	<td width="20%" align="left" style="border-bottom:solid 1px;"><b>Mailing: <?=$d[bill420]?></b></td>
	<td width="20%" align="left" style="border-bottom:solid 1px;"><b>Filing: <? if ($d[attorneys_id] == 1 && $d[circuit_court] == 'PRINCE GEORGES'){ echo "<b>MAIL2CLIENT</b>";}else{ echo $d[bill430];}?></b></td>
	<td width="20%" align="left" style="border-bottom:solid 1px;"><b>Total: <? echo ($d[bill410]+$d[bill420]+$d[bill430]);?></b></td>
	</tr></table>
	</fieldset>
	<table width="100%" align="center"><tr><td>
	<? 
	$i=0;
	while ($i < 6){$i++;
		if ($d["name$i"]){
			if ($i == 3 || $i == 5){
				echo "</td></tr><tr><td>";
			}elseif($i != 1){
				echo "</td><td>";
			}
			?>
			<fieldset>
			<legend accesskey="C"><?=$d["name$i"]?></legend>
			<table align="center">
				<tr>
					<td><input type="checkbox"> <?=$d[address1]?>, <?=$d[city1]?>, <?=$d[state1]?> <?=$d[zip1]?></td>
				</tr>
			</table>
			</fieldset>
	<? 	}
	} ?>
	</td></tr></table>
	<table width="100%" align="center" cellspacing="0" cellpadding="0"><tr><td>
	<?
	if ($d["name1"]){ ?>
	<fieldset>
	<legend accesskey="C"><u>Process Service on Occupant</u>:</legend>
	<table cellspacing="0" align="center">
	<?
	$delivery='';
	$delivery=deliveryExplode($eviction,1);
	if ($delivery != ''){
		$dt=$delivery[0];
		$server=$delivery[1];
		$deliveryAddress="@ ".$delivery[2];
	}else{
		$dt='';
		$server='';
		$deliveryAddress='';
	}?>
		<tr>
			<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> Personal Delivery</td>
			<td width="30px" style="border-bottom:solid 1px">Date:</td>
			<td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
			<td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
		</tr>
	<?
	$address=strtoupper($d[address1]);
	$attempt='';
	$attempt=attemptExplode($eviction,1,$address,"FIRST EFFORT");
	if ($attempt != ''){
		$dt=$attempt[0];
		$server=$attempt[1];
	}else{
		$dt='';
		$server='';
	}
	?>
		<tr>
			<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 1st Attempt At <?=$d[address1]?></td>
			<td width="30px" style="border-bottom:solid 1px">Date:</td>
			<td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
			<td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
		</tr>
	<?
	$address=strtoupper($d[address1]);
	$attempt='';
	$attempt=attemptExplode($eviction,1,$address,"SECOND EFFORT");
	if ($attempt != ''){
		$dt=$attempt[0];
		$server=$attempt[1];
	}else{
		$dt='';
		$server='';
	}
	?>
		<tr>
			<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 2nd Attempt At <?=$d[address1]?></td>
			<td width="30px" style="border-bottom:solid 1px">Date:</td>
			<td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
			<td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
		</tr>
	<?
	$address=strtoupper($d[address1]);
	$attempt='';
	$attempt=attemptExplode($eviction,1,$address,"POSTING DETAILS");
	if ($attempt != ''){
		$dt=$attempt[0];
		$server=$attempt[1];
	}else{
		$dt='';
		$server='';
	}
	?>
		<tr>
			<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> Posting</td>
			<td width="30px" style="border-bottom:solid 1px">Date:</td>
			<td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
			<td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
		</tr>
		<tr>
			<td colspan="4" align="center"><?=historyList($eviction,1,$d[attorneys_id])?></td>
		</tr>
	</table>
	</fieldset>
	<? } ?>
	</td></tr></table>
	<table width="80%" align="center" cellspacing="0">
		<tr>
			<td><? if($d[date_received]){ echo ' '.$d[date_received];}else{?>___________ <? }?></td>
			<td><?=id2attorney($d[attorneys_id])?></td>
			<td><?=$d[client_file]?></td>
			<td><?=$d[circuit_court]?></td>
			<td><?=$d[case_no]?></td>
		</tr>
	</table>
	</fieldset>
	</td></tr></table>
	<?
	$html = ob_get_clean();
	return $html;
}
$eviction=$_GET[id];
echo evSheet($eviction);
if ($_GET[autoPrint] == 1){
echo "<script>
if (window.self) window.print();
self.close();
</script>";
}
?>