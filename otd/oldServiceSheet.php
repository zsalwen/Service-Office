<?php

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
	$str=explode(' ',$date);
	$time=$str[1].$str[2];
	$date=$str[0];
	return $date.' @ '.$time;
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
				if ($dn[wizard] == 'NOT BORROWER'){
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
	$qh="SELECT action_str, serverID, address, wizard, resident FROM ps_history WHERE packet_id='$packet' AND defendant_id='$defendant' AND (WIZARD='BORROWER' OR WIZARD='NOT BORROWER')";
	$rh=@mysql_query($qh) or die (mysql_error());
	$dh=mysql_fetch_array($rh,MYSQL_ASSOC);
	if ($dh != ''){
		$action=explode('DATE OF SERVICE: ',strtoupper($dh[action_str]));
		$dt=explode('<BR>',$action[1]);
		$return[0]=dateImplode($dt[0]);
		$return[1]=id2name($dh[serverID]);
		if ($dh[wizard] == 'BORROWER'){
			$return[1] .= " To: ".nameDef($defendant,$packet);
		}else{
			$return[1] .= " To: ".strtoupper($dh[resident]);
		}
		$address=explode(',',$dh[address]);
		$return[2]=substr($address[0],0,16);
	}
	return $return;
}
function attemptExplode($packet,$defendant,$address,$type){
	$qh="SELECT action_str, serverID FROM ps_history WHERE packet_id='$packet' AND defendant_id='$defendant' AND address LIKE '%$address%' AND WIZARD='$type'";
	$rh=@mysql_query($qh) or die (mysql_error());
	$dh=mysql_fetch_array($rh,MYSQL_ASSOC);
	if ($dh != ''){
		$action=explode('</LI>',strtoupper($dh[action_str]));
		$dt=explode('<BR>',$action[1]);
		$return[0]=dateImplode($dt[0]);
		$return[1]=id2name($dh[serverID]);
	}
	return $return;
}
$packet=$_GET[packet];
$q="SELECT * from ps_packets, ps_pay WHERE ps_packets.packet_id='$packet' AND ps_packets.packet_id=ps_pay.packetID AND ps_pay.product='OTD'";
$r=@mysql_query($q) or die(mysql_error());
$d=mysql_fetch_array($r, MYSQL_ASSOC);
$date=date("m/d/Y h:i:s A");
?>
<style>
body{padding:0px; margin:0px;}
td{font-size:11px;}
div{font-size:10px; text-align:left;}
fieldset, legend, div {padding:0px;}
table, tr, td {padding: 0px; border-collapse:collapse;}
input {padding:0px;}
div.list {border-bottom:solid 1px;}
</style>
<table width="100%" align="center"><tr><td>
<fieldset>
<legend accesskey="C">Quality Control for Process Serving Packet <?=$d[packet_id]?> - <b>Printed <?=$date?></b></legend>
<fieldset>
<legend>Billing Matrix</legend>
<table width="100%" align="center"><tr>
<td width="25%" align="left" style="border-bottom:solid 1px;"><b>Service: <?=$d[bill410]?></b></td>
<td width="25%" align="left" style="border-bottom:solid 1px;"><b>Mailing: <?=$d[bill420]?></b></td>
<td width="25%" align="left" style="border-bottom:solid 1px;"><b>Filing: <?=$d[bill430]?></b></td>
<td width="25%" align="left" style="border-bottom:solid 1px;"><b>Total: <? echo ($d[bill410]+$d[bill420]+$d[bill430]);?></b></td>
<?
$q2="SELECT * FROM occNotices WHERE packet_id='$d[packet_id]'";
$r2=@mysql_query($q2) or die("Query: $q1<br>".mysql_error());
while ($d2=mysql_fetch_array($r2,MYSQL_ASSOC)){
	$notices .= "<tr><td colspan='4' align='center'>$d2[requirements] Notice Sent ".dateExplode($d2[sendDate])." - $".$d2[bill]."</td></tr>";
}
if ($notices != ''){
	echo $notices;
}else{
	echo "<tr><td colspan='4' align='center'>NO NOTICES SENT FOR THIS PACKET</td></tr>";
}
?>
</td></tr></table>
</fieldset>
<table width="100%" align="center"><tr><td>
<? if ($d[name1]){ ?>
<fieldset>
<legend accesskey="C"><?=$d[name1]?></legend>
<table align="center">
    <tr>
        <td><input type="checkbox"> <?=$d[address1]?>, <?=$d[city1]?>, <?=$d[state1]?> <?=$d[zip1]?></td>
    </tr>
    <? if ($d[address1a]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[address1a]?>, <?=$d[city1a]?>, <?=$d[state1a]?> <?=$d[zip1a]?></td>
    </tr>
    <? } ?>
    <? if ($d[address1b]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[address1b]?>, <?=$d[city1b]?>, <?=$d[state1b]?> <?=$d[zip1b]?></td>
    </tr>
    <? } ?>
    <? if ($d[address1c]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[address1c]?>, <?=$d[city1c]?>, <?=$d[state1c]?> <?=$d[zip1c]?></td>
    </tr>
    <? } ?>
    <? if ($d[address1d]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[address1d]?>, <?=$d[city1d]?>, <?=$d[state1d]?> <?=$d[zip1d]?></td>
    </tr>
    <? } ?>
    <? if ($d[address1e]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[address1e]?>, <?=$d[city1e]?>, <?=$d[state1e]?> <?=$d[zip1e]?></td>
    </tr>
    <? } ?>
	<? if ($d[pobox]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[pobox]?>, <?=$d[pocity]?>, <?=$d[postate]?> <?=$d[pozip]?></td>
    </tr>
    <? } ?>
	<? if ($d[pobox2]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[pobox2]?>, <?=$d[pocity2]?>, <?=$d[postate2]?> <?=$d[pozip2]?></td>
    </tr>
    <? } ?>
</table>
</fieldset>
<? } ?>
<? if ($d[name2]){ ?>
</td><td width="50%">
<fieldset>
<legend accesskey="C"><?=$d[name2]?></legend>
<table align="center">
    <tr>
        <td><input type="checkbox"> <?=$d[address2]?>, <?=$d[city2]?>, <?=$d[state2]?> <?=$d[zip2]?></td>
    </tr>
    <? if ($d[address2a]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[address2a]?>, <?=$d[city2a]?>, <?=$d[state2a]?> <?=$d[zip2a]?></td>
    </tr>
    <? } ?>
    <? if ($d[address2b]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[address2b]?>, <?=$d[city2b]?>, <?=$d[state2b]?> <?=$d[zip2b]?></td>
    </tr>
    <? } ?>
    <? if ($d[address2c]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[address2c]?>, <?=$d[city2c]?>, <?=$d[state2c]?> <?=$d[zip2c]?></td>
    </tr>
    <? } ?>
    <? if ($d[address2d]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[address2d]?>, <?=$d[city2d]?>, <?=$d[state2d]?> <?=$d[zip2d]?></td>
    </tr>
    <? } ?>
    <? if ($d[address2e]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[address2e]?>, <?=$d[city2e]?>, <?=$d[state2e]?> <?=$d[zip2e]?></td>
    </tr>
    <? } ?>
	<? if ($d[pobox]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[pobox]?>, <?=$d[pocity]?>, <?=$d[postate]?> <?=$d[pozip]?></td>
    </tr>
    <? } ?>
	<? if ($d[pobox2]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[pobox2]?>, <?=$d[pocity2]?>, <?=$d[postate2]?> <?=$d[pozip2]?></td>
    </tr>
    <? } ?>
</table>
</fieldset>
<? } ?>
<? if ($d[name3]){ ?>
</td></tr><tr><td>
<fieldset>
<legend accesskey="C"><?=$d[name3]?></legend>
<table align="center">
    <tr>
        <td><input type="checkbox"> <?=$d[address3]?>, <?=$d[city3]?>, <?=$d[state3]?> <?=$d[zip3]?></td>
    </tr>
    <? if ($d[address3a]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[address3a]?>, <?=$d[city3a]?>, <?=$d[state3a]?> <?=$d[zip3a]?></td>
    </tr>
    <? } ?>
    <? if ($d[address3b]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[address3b]?>, <?=$d[city3b]?>, <?=$d[state3b]?> <?=$d[zip3b]?></td>
    </tr>
    <? } ?>
    <? if ($d[address3c]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[address3c]?>, <?=$d[city3c]?>, <?=$d[state3c]?> <?=$d[zip3c]?></td>
    </tr>
    <? } ?>
    <? if ($d[address3d]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[address3d]?>, <?=$d[city3d]?>, <?=$d[state3d]?> <?=$d[zip3d]?></td>
    </tr>
    <? } ?>
    <? if ($d[address3e]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[address3e]?>, <?=$d[city3e]?>, <?=$d[state3e]?> <?=$d[zip3e]?></td>
    </tr>
    <? } ?>
	<? if ($d[pobox]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[pobox]?>, <?=$d[pocity]?>, <?=$d[postate]?> <?=$d[pozip]?></td>
    </tr>
    <? } ?>
	<? if ($d[pobox2]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[pobox2]?>, <?=$d[pocity2]?>, <?=$d[postate2]?> <?=$d[pozip2]?></td>
    </tr>
    <? } ?>
</table>
</fieldset>
<? } ?>
<? if ($d[name4]){ ?>
</td><td>
<fieldset>
<legend accesskey="C"><?=$d[name4]?></legend>
<table align="center">
    <tr>
        <td><input type="checkbox"> <?=$d[address4]?>, <?=$d[city4]?>, <?=$d[state4]?> <?=$d[zip4]?></td>
    </tr>
    <? if ($d[address4a]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[address4a]?>, <?=$d[city4a]?>, <?=$d[state4a]?> <?=$d[zip4a]?></td>
    </tr>
    <? } ?>
    <? if ($d[address4b]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[address4b]?>, <?=$d[city4b]?>, <?=$d[state4b]?> <?=$d[zip4b]?></td>
    </tr>
    <? } ?>
    <? if ($d[address4c]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[address4c]?>, <?=$d[city4c]?>, <?=$d[state4c]?> <?=$d[zip4c]?></td>
    </tr>
    <? } ?>
    <? if ($d[address4d]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[address4d]?>, <?=$d[city4d]?>, <?=$d[state4d]?> <?=$d[zip4d]?></td>
    </tr>
    <? } ?>
    <? if ($d[address4e]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[address4e]?>, <?=$d[city4e]?>, <?=$d[state4e]?> <?=$d[zip4e]?></td>
    </tr>
    <? } ?>
	<? if ($d[pobox]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[pobox]?>, <?=$d[pocity]?>, <?=$d[postate]?> <?=$d[pozip]?></td>
    </tr>
    <? } ?>
	<? if ($d[pobox2]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[pobox2]?>, <?=$d[pocity2]?>, <?=$d[postate2]?> <?=$d[pozip2]?></td>
    </tr>
    <? } ?>
</table>
</fieldset>
<? } ?>
<? if ($d[name5]){ ?>
</td></tr><tr><td>
<fieldset>
<legend accesskey="C"><?=$d[name5]?></legend>
<table align="center">
    <tr>
        <td><input type="checkbox"> <?=$d[address5]?>, <?=$d[city5]?>, <?=$d[state5]?> <?=$d[zip5]?></td>
    </tr>
    <? if ($d[address5a]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[address5a]?>, <?=$d[city5a]?>, <?=$d[state5a]?> <?=$d[zip5a]?></td>
    </tr>
    <? } ?>
    <? if ($d[address5b]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[address5b]?>, <?=$d[city5b]?>, <?=$d[state5b]?> <?=$d[zip5b]?></td>
    </tr>
    <? } ?>
    <? if ($d[address5c]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[address5c]?>, <?=$d[city5c]?>, <?=$d[state5c]?> <?=$d[zip5c]?></td>
    </tr>
    <? } ?>
    <? if ($d[address5d]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[address5d]?>, <?=$d[city5d]?>, <?=$d[state5d]?> <?=$d[zip5d]?></td>
    </tr>
    <? } ?>
    <? if ($d[address5e]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[address5e]?>, <?=$d[city5e]?>, <?=$d[state5e]?> <?=$d[zip5e]?></td>
    </tr>
    <? } ?>
	<? if ($d[pobox]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[pobox]?>, <?=$d[pocity]?>, <?=$d[postate]?> <?=$d[pozip]?></td>
    </tr>
    <? } ?>
	<? if ($d[pobox2]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[pobox2]?>, <?=$d[pocity2]?>, <?=$d[postate2]?> <?=$d[pozip2]?></td>
    </tr>
    <? } ?>
</table>
</fieldset>
<? } ?>
<? if ($d[name6]){ ?>
</td><td>
<fieldset>
<legend accesskey="C"><?=$d[name6]?></legend>
<table align="center">
    <tr>
        <td><input type="checkbox"> <?=$d[address6]?>, <?=$d[city6]?>, <?=$d[state6]?> <?=$d[zip6]?></td>
    </tr>
    <? if ($d[address6a]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[address6a]?>, <?=$d[city6a]?>, <?=$d[state6a]?> <?=$d[zip6a]?></td>
    </tr>
    <? } ?>
    <? if ($d[address6b]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[address6b]?>, <?=$d[city6b]?>, <?=$d[state6b]?> <?=$d[zip6b]?></td>
    </tr>
    <? } ?>
    <? if ($d[address6c]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[address6c]?>, <?=$d[city6c]?>, <?=$d[state6c]?> <?=$d[zip6c]?></td>
    </tr>
    <? } ?>
    <? if ($d[address6d]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[address6d]?>, <?=$d[city6d]?>, <?=$d[state6d]?> <?=$d[zip6d]?></td>
    </tr>
    <? } ?>
    <? if ($d[address6e]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[address6e]?>, <?=$d[city6e]?>, <?=$d[state6e]?> <?=$d[zip6e]?></td>
    </tr>
    <? } ?>
	<? if ($d[pobox]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[pobox]?>, <?=$d[pocity]?>, <?=$d[postate]?> <?=$d[pozip]?></td>
    </tr>
    <? } ?>
	<? if ($d[pobox2]){?>
    <tr>
        <td><input type="checkbox"> <?=$d[pobox2]?>, <?=$d[pocity2]?>, <?=$d[postate2]?> <?=$d[pozip2]?></td>
    </tr>
    <? } ?>
</table>
</fieldset>
<? } ?>
</td></tr></table>
<table width="100%" align="center" cellspacing="0" cellpadding="0" align="center"><tr><td>
<fieldset>
<legend accesskey="C"><u>Process Service on <?=$d[name1]?></u>:</legend>
<table cellspacing="0" align="center">
<?
$delivery='';
$delivery=deliveryExplode($packet,1);
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
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> Personal Delivery <?=$deliveryAddress?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1]);
$attempt='';
$attempt=attemptExplode($packet,1,$address,"FIRST EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 1st Attempt At <?=substr($d[address1],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1]);
$attempt='';
$attempt=attemptExplode($packet,1,$address,"SECOND EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 2nd Attempt At <?=substr($d[address1],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<? if ($d[address1a]){
$address=strtoupper($d[address1a]);
$attempt='';
$attempt=attemptExplode($packet,1,$address,"FIRST EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}?>

	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 1st Attempt At <?=substr($d[address1a],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1a]);
$attempt='';
$attempt=attemptExplode($packet,1,$address,"SECOND EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 2nd Attempt At <?=substr($d[address1a],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<? } ?>
<? if ($d[address1b]){
$address=strtoupper($d[address1b]);
$attempt='';
$attempt=attemptExplode($packet,1,$address,"FIRST EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 1st Attempt At <?=substr($d[address1b],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1b]);
$attempt='';
$attempt=attemptExplode($packet,1,$address,"SECOND EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 2nd Attempt At <?=substr($d[address1b],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<? } ?>
<? if ($d[address1c]){
$address=strtoupper($d[address1c]);
$attempt='';
$attempt=attemptExplode($packet,1,$address,"FIRST EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 1st Attempt At <?=substr($d[address1c],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1c]);
$attempt='';
$attempt=attemptExplode($packet,1,$address,"SECOND EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 2nd Attempt At <?=substr($d[address1c],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<? } ?>
<? if ($d[address1d]){
$address=strtoupper($d[address1d]);
$attempt='';
$attempt=attemptExplode($packet,1,$address,"FIRST EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 1st Attempt At <?=substr($d[address1d],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1d]);
$attempt='';
$attempt=attemptExplode($packet,1,$address,"SECOND EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 2nd Attempt At <?=substr($d[address1d],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<? } ?>
<? if ($d[address1e]){
$address=strtoupper($d[address1e]);
$attempt='';
$attempt=attemptExplode($packet,1,$address,"FIRST EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 1st Attempt At <?=substr($d[address1e],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1e]);
$attempt='';
$attempt=attemptExplode($packet,1,$address,"SECOND EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 2nd Attempt At <?=substr($d[address1e],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<? } 
$address=strtoupper($d[address1]);
$attempt='';
$attempt=attemptExplode($packet,1,$address,"POSTING DETAILS");
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
		<td colspan="4" align="center"><?=historyList($_GET[packet],1,$d[attorneys_id])?></td>
	</tr>
</table>
</fieldset>
<?
//defendant 2 attempts
 if ($d[name2]){ 
$delivery='';
$delivery=deliveryExplode($packet,2);
if ($delivery != ''){
	$dt=$delivery[0];
	$server=$delivery[1];
	$deliveryAddress="@ ".$delivery[2];
}else{
	$dt='';
	$server='';
	$deliveryAddress='';
}
?>
</td></tr><tr><td>
<fieldset>
<legend accesskey="C"><u>Process Service on <?=$d[name2]?></u>:</legend>
<table cellspacing="0" align="center">
    <tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> Personal Delivery <?=$deliveryAddress?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1]);
$attempt='';
$attempt=attemptExplode($packet,2,$address,"FIRST EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 1st Attempt At <?=substr($d[address1],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1]);
$attempt='';
$attempt=attemptExplode($packet,2,$address,"SECOND EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 2nd Attempt At <?=substr($d[address1],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<? if ($d[address1a]){
$address=strtoupper($d[address1a]);
$attempt='';
$attempt=attemptExplode($packet,2,$address,"FIRST EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 1st Attempt At <?=substr($d[address1a],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1a]);
$attempt='';
$attempt=attemptExplode($packet,2,$address,"SECOND EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 2nd Attempt At <?=substr($d[address1a],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<? } ?>
<? if ($d[address1b]){
$address=strtoupper($d[address1b]);
$attempt='';
$attempt=attemptExplode($packet,2,$address,"FIRST EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 1st Attempt At <?=substr($d[address1b],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1b]);
$attempt='';
$attempt=attemptExplode($packet,2,$address,"SECOND EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 2nd Attempt At <?=substr($d[address1b],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<? } ?>
<? if ($d[address1c]){
$address=strtoupper($d[address1c]);
$attempt='';
$attempt=attemptExplode($packet,2,$address,"FIRST EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 1st Attempt At <?=substr($d[address1c],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1c]);
$attempt='';
$attempt=attemptExplode($packet,2,$address,"SECOND EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 2nd Attempt At <?=substr($d[address1c],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<? } ?>
<? if ($d[address1d]){
$address=strtoupper($d[address1d]);
$attempt='';
$attempt=attemptExplode($packet,2,$address,"FIRST EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 1st Attempt At <?=substr($d[address1d],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1d]);
$attempt='';
$attempt=attemptExplode($packet,2,$address,"SECOND EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 2nd Attempt At <?=substr($d[address1d],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<? } ?>
<? if ($d[address1e]){
$address=strtoupper($d[address1e]);
$attempt='';
$attempt=attemptExplode($packet,2,$address,"FIRST EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 1st Attempt At <?=substr($d[address1e],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1e]);
$attempt='';
$attempt=attemptExplode($packet,2,$address,"SECOND EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 2nd Attempt At <?=substr($d[address1e],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<? }
$address=strtoupper($d[address1]);
$attempt='';
$attempt=attemptExplode($packet,2,$address,"POSTING DETAILS");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> Posting</td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
	<tr>
		<td colspan="4" align="center"><?=historyList($_GET[packet],2,$d[attorneys_id])?></td>
	</tr>
</table>
</fieldset>
<? } ?>
<? 
//defendant 3 attempts
 if ($d[name3]){ 
$delivery='';
$delivery=deliveryExplode($packet,3);
if ($delivery != ''){
	$dt=$delivery[0];
	$server=$delivery[1];
	$deliveryAddress="@ ".$delivery[2];
}else{
	$dt='';
	$server='';
	$deliveryAddress='';
}
?>
</td></tr><tr><td>
<fieldset>
<legend accesskey="C"><u>Process Service on <?=$d[name3]?></u>:</legend>
<table cellspacing="0" align="center">
    <tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> Personal Delivery <?=$deliveryAddress?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1]);
$attempt='';
$attempt=attemptExplode($packet,3,$address,"FIRST EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 1st Attempt At <?=substr($d[address1],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1]);
$attempt='';
$attempt=attemptExplode($packet,3,$address,"SECOND EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 2nd Attempt At <?=substr($d[address1],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<? if ($d[address1a]){
$address=strtoupper($d[address1a]);
$attempt='';
$attempt=attemptExplode($packet,3,$address,"FIRST EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 1st Attempt At <?=substr($d[address1a],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1a]);
$attempt='';
$attempt=attemptExplode($packet,3,$address,"SECOND EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 2nd Attempt At <?=substr($d[address1a],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<? } ?>
<? if ($d[address1b]){
$address=strtoupper($d[address1b]);
$attempt='';
$attempt=attemptExplode($packet,3,$address,"FIRST EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 1st Attempt At <?=substr($d[address1b],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1b]);
$attempt='';
$attempt=attemptExplode($packet,3,$address,"SECOND EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 2nd Attempt At <?=substr($d[address1b],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<? } ?>
<? if ($d[address1c]){
$address=strtoupper($d[address1c]);
$attempt='';
$attempt=attemptExplode($packet,3,$address,"FIRST EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 1st Attempt At <?=substr($d[address1c],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1c]);
$attempt='';
$attempt=attemptExplode($packet,3,$address,"SECOND EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 2nd Attempt At <?=substr($d[address1c],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<? } ?>
<? if ($d[address1d]){
$address=strtoupper($d[address1d]);
$attempt='';
$attempt=attemptExplode($packet,3,$address,"FIRST EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 1st Attempt At <?=substr($d[address1d],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1d]);
$attempt='';
$attempt=attemptExplode($packet,3,$address,"SECOND EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 2nd Attempt At <?=substr($d[address1d],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<? } ?>
<? if ($d[address1e]){
$address=strtoupper($d[address1e]);
$attempt='';
$attempt=attemptExplode($packet,3,$address,"FIRST EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 1st Attempt At <?=substr($d[address1e],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1e]);
$attempt='';
$attempt=attemptExplode($packet,3,$address,"SECOND EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 2nd Attempt At <?=substr($d[address1e],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<? }
$address=strtoupper($d[address1]);
$attempt='';
$attempt=attemptExplode($packet,3,$address,"POSTING DETAILS");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> Posting</td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
	<tr>
		<td colspan="4" align="center"><?=historyList($_GET[packet],3,$d[attorneys_id])?></td>
	</tr>
</table>
</fieldset>
<? } ?>
<? if ($d[name4]){
//defendant 4 attempts
$delivery='';
$delivery=deliveryExplode($packet,4);
if ($delivery != ''){
	$dt=$delivery[0];
	$server=$delivery[1];
	$deliveryAddress="@ ".$delivery[2];
}else{
	$dt='';
	$server='';
	$deliveryAddress='';
}
?>
</td></tr><tr><td>
<fieldset>
<legend accesskey="C"><u>Process Service on <?=$d[name4]?></u>:</legend>
<table cellspacing="0" align="center">
    <tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> Personal Delivery <?=$deliveryAddress?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1]);
$attempt='';
$attempt=attemptExplode($packet,4,$address,"FIRST EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 1st Attempt At <?=substr($d[address1],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1]);
$attempt='';
$attempt=attemptExplode($packet,4,$address,"SECOND EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 2nd Attempt At <?=substr($d[address1],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<? if ($d[address1a]){
$address=strtoupper($d[address1a]);
$attempt='';
$attempt=attemptExplode($packet,4,$address,"FIRST EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 1st Attempt At <?=substr($d[address1a],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1a]);
$attempt='';
$attempt=attemptExplode($packet,4,$address,"SECOND EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 2nd Attempt At <?=substr($d[address1a],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<? } ?>
<? if ($d[address1b]){
$address=strtoupper($d[address1b]);
$attempt='';
$attempt=attemptExplode($packet,4,$address,"FIRST EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 1st Attempt At <?=substr($d[address1b],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1b]);
$attempt='';
$attempt=attemptExplode($packet,4,$address,"SECOND EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 2nd Attempt At <?=substr($d[address1b],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<? } ?>
<? if ($d[address1c]){
$address=strtoupper($d[address1c]);
$attempt='';
$attempt=attemptExplode($packet,4,$address,"FIRST EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 1st Attempt At <?=substr($d[address1c],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1c]);
$attempt='';
$attempt=attemptExplode($packet,4,$address,"SECOND EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 2nd Attempt At <?=substr($d[address1c],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<? } ?>
<? if ($d[address1d]){
$address=strtoupper($d[address1d]);
$attempt='';
$attempt=attemptExplode($packet,4,$address,"FIRST EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 1st Attempt At <?=substr($d[address1d],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1d]);
$attempt='';
$attempt=attemptExplode($packet,4,$address,"SECOND EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 2nd Attempt At <?=substr($d[address1d],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<? } ?>
<? if ($d[address1e]){
$address=strtoupper($d[address1e]);
$attempt='';
$attempt=attemptExplode($packet,4,$address,"FIRST EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 1st Attempt At <?=substr($d[address1e],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1e]);
$attempt='';
$attempt=attemptExplode($packet,4,$address,"SECOND EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 2nd Attempt At <?=substr($d[address1e],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<? }
$address=strtoupper($d[address1]);
$attempt='';
$attempt=attemptExplode($packet,4,$address,"POSTING DETAILS");
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
		<td colspan="4" align="center"><?=historyList($_GET[packet],4,$d[attorneys_id])?></td>
	</tr>
</table>
</fieldset>
<? } ?>
<? 
//defendant 5 attempts
 if ($d[name5]){
$delivery='';
$delivery=deliveryExplode($packet,5);
if ($delivery != ''){
	$dt=$delivery[0];
	$server=$delivery[1];
	$deliveryAddress="@ ".$delivery[2];
}else{
	$dt='';
	$server='';
	$deliveryAddress='';
}
?>
</td></tr><tr><td>
<fieldset>
<legend accesskey="C"><u>Process Service on <?=$d[name5]?></u>:</legend>
<table cellspacing="0" align="center">
    <tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> Personal Delivery <?=$deliveryAddress?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1]);
$attempt='';
$attempt=attemptExplode($packet,5,$address,"FIRST EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 1st Attempt At <?=substr($d[address1],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1]);
$attempt='';
$attempt=attemptExplode($packet,5,$address,"SECOND EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 2nd Attempt At <?=substr($d[address1],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<? if ($d[address1a]){
$address=strtoupper($d[address1a]);
$attempt='';
$attempt=attemptExplode($packet,5,$address,"FIRST EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 1st Attempt At <?=substr($d[address1a],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1a]);
$attempt='';
$attempt=attemptExplode($packet,5,$address,"SECOND EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 2nd Attempt At <?=substr($d[address1a],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<? } ?>
<? if ($d[address1b]){
$address=strtoupper($d[address1b]);
$attempt='';
$attempt=attemptExplode($packet,5,$address,"FIRST EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 1st Attempt At <?=substr($d[address1b],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1b]);
$attempt='';
$attempt=attemptExplode($packet,5,$address,"SECOND EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 2nd Attempt At <?=substr($d[address1b],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<? } ?>
<? if ($d[address1c]){
$address=strtoupper($d[address1c]);
$attempt='';
$attempt=attemptExplode($packet,5,$address,"FIRST EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 1st Attempt At <?=substr($d[address1c],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1c]);
$attempt='';
$attempt=attemptExplode($packet,5,$address,"SECOND EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 2nd Attempt At <?=substr($d[address1c],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<? } ?>
<? if ($d[address1d]){
$address=strtoupper($d[address1d]);
$attempt='';
$attempt=attemptExplode($packet,5,$address,"FIRST EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 1st Attempt At <?=substr($d[address1d],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1d]);
$attempt='';
$attempt=attemptExplode($packet,5,$address,"SECOND EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 2nd Attempt At <?=substr($d[address1d],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<? } ?>
<? if ($d[address1e]){
$address=strtoupper($d[address1e]);
$attempt='';
$attempt=attemptExplode($packet,5,$address,"FIRST EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 1st Attempt At <?=substr($d[address1e],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1e]);
$attempt='';
$attempt=attemptExplode($packet,5,$address,"SECOND EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 2nd Attempt At <?=substr($d[address1e],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<? }
$address=strtoupper($d[address1]);
$attempt='';
$attempt=attemptExplode($packet,5,$address,"POSTING DETAILS");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> Posting</td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
	<tr>
		<td colspan="4" align="center"><?=historyList($_GET[packet],5,$d[attorneys_id])?></td>
	</tr>
</table>
</fieldset>
<? } ?>
<? if ($d[name6]){
//defendant 6 attempts
$delivery='';
$delivery=deliveryExplode($packet,6);
if ($delivery != ''){
	$dt=$delivery[0];
	$server=$delivery[1];
	$deliveryAddress="@ ".$delivery[2];
}else{
	$dt='';
	$server='';
	$deliveryAddress='';
}
?>
</td></tr><tr><td>
<fieldset>
<legend accesskey="C"><u>Process Service on <?=$d[name6]?></u>:</legend>
<table cellspacing="0" align="center">
    <tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> Personal Delivery <?=$deliveryAddress?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1]);
$attempt='';
$attempt=attemptExplode($packet,6,$address,"FIRST EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 1st Attempt At <?=substr($d[address1],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1]);
$attempt='';
$attempt=attemptExplode($packet,6,$address,"SECOND EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 2nd Attempt At <?=substr($d[address1],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<? if ($d[address1a]){
$address=strtoupper($d[address1a]);
$attempt='';
$attempt=attemptExplode($packet,6,$address,"FIRST EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 1st Attempt At <?=substr($d[address1a],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1a]);
$attempt='';
$attempt=attemptExplode($packet,6,$address,"SECOND EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 2nd Attempt At <?=substr($d[address1a],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<? } ?>
<? if ($d[address1b]){
$address=strtoupper($d[address1b]);
$attempt='';
$attempt=attemptExplode($packet,6,$address,"FIRST EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 1st Attempt At <?=substr($d[address1b],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1b]);
$attempt='';
$attempt=attemptExplode($packet,6,$address,"SECOND EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 2nd Attempt At <?=substr($d[address1b],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<? } ?>
<? if ($d[address1c]){
$address=strtoupper($d[address1c]);
$attempt='';
$attempt=attemptExplode($packet,6,$address,"FIRST EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 1st Attempt At <?=substr($d[address1c],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1c]);
$attempt='';
$attempt=attemptExplode($packet,6,$address,"SECOND EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 2nd Attempt At <?=substr($d[address1c],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<? } ?>
<? if ($d[address1d]){
$address=strtoupper($d[address1d]);
$attempt='';
$attempt=attemptExplode($packet,6,$address,"FIRST EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 1st Attempt At <?=substr($d[address1d],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1d]);
$attempt='';
$attempt=attemptExplode($packet,6,$address,"SECOND EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 2nd Attempt At <?=substr($d[address1d],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<? } ?>
<? if ($d[address1e]){
$address=strtoupper($d[address1e]);
$attempt='';
$attempt=attemptExplode($packet,6,$address,"FIRST EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 1st Attempt At <?=substr($d[address1e],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<?
$address=strtoupper($d[address1e]);
$attempt='';
$attempt=attemptExplode($packet,6,$address,"SECOND EFFORT");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}else{
	$dt='';
	$server='';
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> 2nd Attempt At <?=substr($d[address1e],0,20)?></td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
<? }
$address=strtoupper($d[address1]);
$attempt='';
$attempt=attemptExplode($packet,6,$address,"POSTING DETAILS");
if ($attempt != ''){
	$dt=$attempt[0];
	$server=$attempt[1];
}
?>
	<tr>
    	<td width="250px" style="border-bottom:solid 1px"><input type="checkbox"> Posting</td>
        <td width="30px" style="border-bottom:solid 1px">Date:</td>
        <td width="170px" style="border-bottom:solid 1px"><?if ($dt != ''){ echo $dt;}else{echo '&nbsp;';}?></td>
        <td width="300px" style="border-bottom:solid 1px">By: <?=$server?></td>
    </tr>
	<tr>
		<td colspan="4" align="center"><?=historyList($_GET[packet],6,$d[attorneys_id])?></td>
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
if ($_GET[autoPrint] == 1){
echo "<script>
if (window.self) window.print();
self.close();
</script>";
}
?>