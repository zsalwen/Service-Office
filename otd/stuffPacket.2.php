<?
include 'common.php';
//hardLog('Occupant Envelope Printout','user');
$_SESSION[inc] = 0;
function washAdd($str){
	$str=str_replace('#','no. ',$str);
	$str=str_replace('&','and ',$str);
	return strtoupper($str);
}
function washAdd2($str){
	$str=str_replace('#','no. ',$str);
	$str=str_replace('&','and ',$str);
	return $str;
}
function att2envelope($attID){
	$r=@mysql_query("SELECT envID FROM attorneys WHERE attorneys_id = '$attID'");
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	return $d[envID];
}
function county2envelope($county){
	$county=strtoupper($county);
	if ($county == 'BALTIMORE'){
		$search='BALTIMORE COUNTY';
	}elseif($county == 'PRINCE GEORGES'){
		$search='PRINCE GEORGE';
	}elseif($county == 'ST MARYS'){
		$search='ST. MARY';
	}elseif($county == 'QUEEN ANNES'){
		$search='QUEEN ANNE';
	}else{
		$search=$county;
	}
	$r=@mysql_query("SELECT envID FROM envelopeImage WHERE to1 LIKE '%$search%' AND addressType='COURT'");
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	return $d[envID];
}
function envPrint($id,$times,$lossMit){
	$r=@mysql_query("SELECT * FROM envelopeImage WHERE envID = '$id'");
	$d=mysql_fetch_array($r,MYSQL_ASSOC); 
	//echo washAdd($d[to1]).'<br>'.washAdd($d[to2]).'<br>'.washAdd($d[to3]);
	if (stripos($d[to1],"COURT")){
		$client='';
	}else{
		$client="YES";
	}
	$i=0;
	while ($i < $times){$i++;
		$_SESSION[inc] = $_SESSION[inc]+1;
		?>
		<table style='page-break-after:always' align='center'><tr><td>
		<div style='width:400px;height:40px;'></div>
		<? if ($_GET[sb]){ ?>
		<img  src="http://staff.mdwestserve.com/envelopecard.bursonhb472.jpg.php?line1=<?=washAdd2($d[to1])?>&line2=<?=washAdd2($d[to2])?>&csz=<?=washAdd2($d[to3])?>&client=<?=$client?>&lossMit=<?=$lossMit?>">
		<? }else{ ?>
		<img  src="http://staff.mdwestserve.com/envelopecard.hb472.jpg.php?line1=<?=washAdd($d[to1])?>&line2=<?=washAdd($d[to2])?>&csz=<?=washAdd($d[to3])?>&client=<?=$client?>&lossMit=<?=$lossMit?>">
		<? } ?>
		</td></tr></table>
		<?
	}
}
function buildFromPacket($packet,$times,$mail){
	$r=@mysql_query("select name1, name2, name3, name4, name5, name6, address1, address1a, address1b, address1c, address1d, address1e, city1, city1a, city1b, city1c, city1d, city1e, state1, state1a, state1b, state1c, state1d, state1e, zip1, zip1a, zip1b, zip1c, zip1d, zip1e, address2, address2a, address2b, address2c, address2d, address2e, city2, city2a, city2b, city2c, city2d, city2e, state2, state2a, state2b, state2c, state2d, state2e, zip2, zip2a, zip2b, zip2c, zip2d, zip2e, address3, address3a, address3b, address3c, address3d, address3e, city3, city3a, city3b, city3c, city3d, city3e, state3, state3a, state3b, state3c, state3d, state3e, zip3, zip3a, zip3b, zip3c, zip3d, zip3e, address4, address4a, address4b, address4c, address4d, address4e, city4, city4a, city4b, city4c, city4d, city4e, state4, state4a, state4b, state4c, state4d, state4e, zip4, zip4a, zip4b, zip4c, zip4d, zip4e, address5, address5a, address5b, address5c, address5d, address5e, city5, city5a, city5b, city5c, city5d, city5e, state5, state5a, state5b, state5c, state5d, state5e, zip5, zip5a, zip5b, zip5c, zip5d, zip5e, address6, address6a, address6b, address6c, address6d, address6e, city6, city6a, city6b, city6c, city6d, city6e, state6, state6a, state6b, state6c, state6d, state6e, zip6, zip6a, zip6b, zip6c, zip6d, zip6e, pobox, pobox2, pocity, pocity2, postate, postate2, pozip, pozip2, lossMit, attorneys_id, circuit_court from ps_packets where packet_id = '$packet'");
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	$i=0;
	$toCounty=county2envelope($d[circuit_court]);
	$toAttorney=att2envelope($d[attorneys_id]);
	while ($i < 6){$i++;
		if ($d["name$i"]){
			if ($d[lossMit] == 'FINAL'){
				envPrint($toCounty,$times,$d[lossMit]);
			}
			envPrint($toAttorney,$times,$d[lossMit]);
		}
		if ($mail != ''){
			foreach(range('a','e') as $letter){
				$var=$i.$letter;
				if ($d["address$var"]){
					$var=strtoupper($var);
					if ($d[lossMit] == 'FINAL'){
						envPrint($toCounty,$times,$d[lossMit]);
					}
					envPrint($toAttorney,$times,$d[lossMit]);
				}
			}
			if ($d[pobox]){
				$var=$i."PO";
				if ($d[lossMit] == 'FINAL'){
					envPrint($toCounty,$times,$d[lossMit]);
				}
				envPrint($toAttorney,$times,$d[lossMit]);
			}
			if ($d[pobox2]){
				$var=$i."PO2";
				if ($d[lossMit] == 'FINAL'){
					envPrint($toCounty,$times,$d[lossMit]);
				}
				envPrint($toAttorney,$times,$d[lossMit]);
			}
		}
	}
}
function buildFromMatrix($packet,$times,$mail){
	$q="SELECT * from mailMatrix where packetID='$packet' AND product='OTD'";
	$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	$r1=@mysql_query("SELECT lossMit, circuit_court, attorneys_id from ps_packets where packet_id='$packet'");
	$d1=mysql_fetch_array($r1,MYSQL_ASSOC);
	$i=0;
	$toCounty=county2envelope($d1[circuit_court]);
	$toAttorney=att2envelope($d1[attorneys_id]);
	$lossMit=$d1[lossMit];
	while ($i < 6){$i++;
		$var='';
		if (trim($d["add$i"]) != ''){
			if ($lossMit != 'PRELIMINARY'){
				envPrint($toCounty,$times,$d1[lossMit]);
			}
			envPrint($toAttorney,$times,$d1[lossMit]);
		}
		if ($mail != ''){
			foreach(range('a','e') as $letter){
				$var=$i.$letter;
				if (trim($d["add$var"]) != ''){
					if ($lossMit != 'PRELIMINARY'){
						envPrint($toCounty,$times,$d1[lossMit]);
					}
					envPrint($toAttorney,$times,$d1[lossMit]);
				}
			}
			$var=$i."PO";
			if (trim($d["add$var"]) != ''){
				if ($lossMit != 'PRELIMINARY'){
					envPrint($toCounty,$times,$d1[lossMit]);
				}
				envPrint($toAttorney,$times,$d1[lossMit]);
			}
			$var=$i."PO2";
			if (trim($d["add$var"]) != ''){
				if ($lossMit != 'PRELIMINARY'){
					envPrint($toCounty,$times,$d1[lossMit]);
				}
				envPrint($toAttorney,$times,$d1[lossMit]);
			}
		}
	}
}
if ($_GET[mail]){
	$mail=1;
	$times=2;
}else{
	$mail='';
	$times=1;
}
$display=0;
if ($_GET[packet]){$display++;
	$packet=$_GET[packet];
	$q="select packet_id from ps_packets where packet_id='$_GET[packet]'";
	$r=@mysql_query($q);
	 while($d=mysql_fetch_array($r, MYSQL_ASSOC)){ $i++;
		$r=@mysql_query("SELECT packetID from mailMatrix where packetID='$packet' AND product='OTD'");
		$d=mysql_fetch_array($r,MYSQL_ASSOC);
		if ($d[packetID]){
			buildFromMatrix($packet,$times,$mail);
		}else{
			buildFromPacket($packet,$times,$mail);
		}
	 }
	error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Printing GREEN Envelopes For OTD$_GET[packet] \n",3,"/logs/user.log");
}elseif($_GET[id]){$display++;
	envPrint($_GET[id],$times,"");
	$r=@mysql_query("SELECT to1 FROM envelopeImage WHERE envID = '$_GET[id]'");
	$d=mysql_fetch_array($r,MYSQL_ASSOC); 
	error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Printing GREEN Envelopes For [$d[to1]] \n",3,"/logs/user.log");
}elseif($_GET[mail]){
	$qd="select packet_id from ps_packets where process_status = 'READY TO MAIL' AND mail_status <> 'Printed Awaiting Postage' AND attorneys_id <> '70' AND lossMit <> '' AND lossMit <> 'N/A - OLD L' AND (uspsVerify='' OR qualityControl='') order by packet_id ASC";
	$rd=@mysql_query($qd) or die ("Query: $qd<br>".mysql_error());
	$dd=mysql_num_rows($rd);
	if ($dd > 0){
		if ($dd == 1){
			$dd2=mysql_fetch_array($rd, MYSQL_ASSOC);
			echo "<script>alert('PACKET [".$dd2[packet_id]."] IS IN THE MAIL QUEUE, BUT HAS NOT BEEN COMPLETELY VERIFIED.  NO ENVELOPE STUFFINGS MAY BE PRINTED UNTIL THIS IS REMEDIED.')</script>";
		}else{
			while($dd2=mysql_fetch_array($rd,MYSQL_ASSOC)){
					$list .= " [".$dd2[packet_id]."]";
			}
			echo "<script>alert('PACKETS$list ARE IN THE MAIL QUEUE, BUT HAVE NOT BEEN COMPLETELY VERIFIED.  NO ENVELOPE STUFFINGS MAY BE PRINTED UNTIL THIS IS REMEDIED.')</script>";
		}
	}else{
		$q="select packet_id from ps_packets where process_status = 'READY TO MAIL' AND mail_status <> 'Printed Awaiting Postage' AND attorneys_id <> '70' AND lossMit <> '' AND lossMit <> 'N/A - OLD L' order by packet_id ASC";
		$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
		while($d=mysql_fetch_array($r, MYSQL_ASSOC)){ $i++;
			$r2=@mysql_query("SELECT packetID from mailMatrix where packetID='$d[packet_id]' AND product='OTD'");
			$d2=mysql_fetch_array($r2,MYSQL_ASSOC);
			if ($d2[packetID]){$display++;
				buildFromMatrix($d[packet_id],$times,$mail);
			}else{$display++;
				buildFromPacket($d[packet_id],$times,$mail);
			}
		}
	}
}
if ($display < 1){
	echo "<h1>NO PACKETS TO DISPLAY</h1>";
}
?>
<script>document.title='HB472 Green Non-Windowed Envelopes';</script>
