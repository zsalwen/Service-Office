<<<<<<< HEAD
<?
mysql_connect();
mysql_select_db('core');
function getFolder($otd){
	$path=explode("/",$otd);
	$count=(count($path)-2);
	$folder=$path["$count"];
	return $folder;
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
function pageMaker($id,$matrix){
$r=@mysql_query("SELECT * FROM envelopeImage WHERE envID = '$id'");
$d=mysql_fetch_array($r,MYSQL_ASSOC);
ob_start(); ?>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<table style="page-break-after:always; ">
	<tr>
		<td style='line-height:1px; font-size:10px;'></td>
	</tr>
	<tr>
		<td style="line-height:12px; font-size:20px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;REQUEST FOR</td>
	</tr>
	<tr>
		<td style='line-height:13.5px; font-size:20px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;FORECLOSURE MEDIATION</td>
	</tr>
	<tr>
		<td style="line-height:4.5px; font-size:11px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=stripslashes(strtoupper($d[to1]))?></td>
	</tr>	
	<tr>
		<td style="line-height:4.5px; font-size:11px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=stripslashes(strtoupper($d[to2]))?></td>
	</tr>	
	<tr>
		<td style="line-height:0px; font-size:11px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=stripslashes(strtoupper($d[to3]))?></td>
	</tr>
</table>
<? 
$html = ob_get_clean();
return $html;
}
function prelimPageMaker($id,$matrix){
$r=@mysql_query("SELECT * FROM envelopeImage WHERE envID = '$id'");
$d=mysql_fetch_array($r,MYSQL_ASSOC);
ob_start(); ?>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<table style="page-break-after:always; ">
	<tr>
		<td style='line-height:6px; font-size:12px;'></td>
	</tr>
	<tr>
		<td style="line-height:6px; font-size:12px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=stripslashes(strtoupper($d[to1]))?></td>
	</tr>	
	<tr>
		<td style="line-height:6px; font-size:12px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=stripslashes(strtoupper($d[to2]))?></td>
	</tr>	
	<tr>
		<td style="line-height:0px; font-size:12px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=stripslashes(strtoupper($d[to3]))?></td>
	</tr>
</table>
<? 
$html = ob_get_clean();
return $html;
}
function buildFromPacket($packet){
	$r=@mysql_query("select name1, name2, name3, name4, name5, name6, address1, address1a, address1b, address1c, address1d, address1e, city1, city1a, city1b, city1c, city1d, city1e, state1, state1a, state1b, state1c, state1d, state1e, zip1, zip1a, zip1b, zip1c, zip1d, zip1e, address2, address2a, address2b, address2c, address2d, address2e, city2, city2a, city2b, city2c, city2d, city2e, state2, state2a, state2b, state2c, state2d, state2e, zip2, zip2a, zip2b, zip2c, zip2d, zip2e, address3, address3a, address3b, address3c, address3d, address3e, city3, city3a, city3b, city3c, city3d, city3e, state3, state3a, state3b, state3c, state3d, state3e, zip3, zip3a, zip3b, zip3c, zip3d, zip3e, address4, address4a, address4b, address4c, address4d, address4e, city4, city4a, city4b, city4c, city4d, city4e, state4, state4a, state4b, state4c, state4d, state4e, zip4, zip4a, zip4b, zip4c, zip4d, zip4e, address5, address5a, address5b, address5c, address5d, address5e, city5, city5a, city5b, city5c, city5d, city5e, state5, state5a, state5b, state5c, state5d, state5e, zip5, zip5a, zip5b, zip5c, zip5d, zip5e, address6, address6a, address6b, address6c, address6d, address6e, city6, city6a, city6b, city6c, city6d, city6e, state6, state6a, state6b, state6c, state6d, state6e, zip6, zip6a, zip6b, zip6c, zip6d, zip6e, pobox, pobox2, pocity, pocity2, postate, postate2, pozip, pozip2, lossMit, attorneys_id, circuit_court from ps_packets where packet_id = '$packet'");
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	$i=0;
	$page='';
	$toCounty=county2envelope($d[circuit_court]);
	$toAttorney=att2envelope($d[attorneys_id]);
	while ($i < 6){$i++;
		if ($d["name$i"]){
			if ($d[lossMit] != 'PRELIMINARY'){
				$page .= pageMaker($toCounty,"$packet-$i");
				$page .= pageMaker($toAttorney,"$packet-$i");
			}else{
				$page .= prelimPageMaker($toAttorney,"$packet-$i");
			}
		}
		foreach(range('a','e') as $letter){
			$var=$i.$letter;
			if ($d["address$var"]){
				$var=strtoupper($var);
				if ($d[lossMit] != 'PRELIMINARY'){
					$page .= pageMaker($toCounty,"$packet-$var");
					$page .= pageMaker($toAttorney,"$packet-$var");
				}else{
					$page .= prelimPageMaker($toAttorney,"$packet-$var");
				}
			}
		}
		if ($d[pobox]){
			$var=$i."PO";
			if ($d[lossMit] != 'PRELIMINARY'){
				$page .= pageMaker($toCounty,"$packet-$var");
				$page .= pageMaker($toAttorney,"$packet-$var");
			}else{
				$page .= prelimPageMaker($toAttorney,"$packet-$var");
			}
		}
		if ($d[pobox2]){
			$var=$i."PO2";
			if ($d[lossMit] != 'PRELIMINARY'){
				$page .= pageMaker($toCounty,"$packet-$var");
				$page .= pageMaker($toAttorney,"$packet-$var");
			}else{
				$page .= prelimPageMaker($toAttorney,"$packet-$var");
			}
		}
	}
	return $page;
}
function buildFromMatrix($packet){
	$q="SELECT * from mailMatrix where packetID='$packet'";
	$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	$r1=@mysql_query("SELECT lossMit, circuit_court, attorneys_id from ps_packets where packet_id='$packet'");
	$d1=mysql_fetch_array($r1,MYSQL_ASSOC);
	$i=0;
	$page='';
	$toCounty=county2envelope($d1[circuit_court]);
	$toAttorney=att2envelope($d1[attorneys_id]);
	$lossMit=$d1[lossMit];
	while ($i < 6){$i++;
		$var='';
		if (trim($d["add$i"]) != ''){
			if ($lossMit != 'PRELIMINARY'){
				$page .= pageMaker($toCounty,"$packet-$i");
				$page .= pageMaker($toAttorney,"$packet-$i");
			}else{
				$page .= prelimPageMaker($toAttorney,"$packet-$i");
			}
		}
		foreach(range('a','e') as $letter){
			$var=$i.$letter;
			if (trim($d["add$var"]) != ''){
				if ($lossMit != 'PRELIMINARY'){
					$page .= pageMaker($toCounty,"$packet-$var");
					$page .= pageMaker($toAttorney,"$packet-$var");
				}else{
					$page .= prelimPageMaker($toAttorney,"$packet-$var");
				}
			}
		}
		$var=$i."PO";
		if (trim($d["add$var"]) != ''){
			if ($lossMit != 'PRELIMINARY'){
				$page .= pageMaker($toCounty,"$packet-$var");
				$page .= pageMaker($toAttorney,"$packet-$var");
			}else{
				$page .= prelimPageMaker($toAttorney,"$packet-$var");
			}
		}
		$var=$i."PO2";
		if (trim($d["add$var"]) != ''){
			if ($lossMit != 'PRELIMINARY'){
				$page .= pageMaker($toCounty,"$packet-$var");
				$page .= pageMaker($toAttorney,"$packet-$var");
			}else{
				$page .= prelimPageMaker($toAttorney,"$packet-$var");
			}
		}
	}
	return $page;
}
if ($_GET[packet]){
	$packet=$_GET[packet];
	$r=@mysql_query("SELECT packetID from mailMatrix where packetID='$packet'");
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	if ($_GET[openDoc]){
		//need file path to display PDF
		$r1=@mysql_query("SELECT otd from ps_packets where packet_id='$packet'");
		$d1=mysql_fetch_array($r1,MYSQL_ASSOC);
		$folder=getFolder($d1[otd]);
	}
	if ($d[packetID]){
		$page=buildFromMatrix($packet);
	}else{
		$page=buildFromPacket($packet);
	}
	$pagesTotal=$page.$page;
	error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Printing Envelope Stuffings For OTD$_GET[packet] \n",3,"/logs/user.log");
}elseif($_GET[id]){
	$r=@mysql_query("SELECT * FROM envelopeImage WHERE envID = '$_GET[id]'");
	$d=mysql_fetch_array($r,MYSQL_ASSOC); 
	error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Printing Green HB472 Envelope Stuffings For [$d[to1]] \n",3,"/logs/user.log");
	if ($_GET[build3]){
		//use other variation for envelope front, with "REQUEST FOR MEDIATION" broken into two lines
		$page=pageMaker($_GET[id],'');
	}elseif ($d[addressType] == 'COURT'){
		//if court, use pageMaker (for enlarged type)
		$page=pageMaker($_GET[id],'');
	}else{
		//use pageMaker
		$page=pageMaker($_GET[id],'');
	}
	$pagesTotal=$page;
}elseif($_GET[court]){
	$r=@mysql_query("SELECT * FROM envelopeImage WHERE addressType='COURT' ORDER BY envID ASC");
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		if ($_GET[build3]){
			//use other variation for envelope front, with "REQUEST FOR MEDIATION" broken into two lines
			$page .= pageMaker($d[envID],'');
		}else{
			$page .= pageMaker($d[envID],'');
		}
	}
	$pagesTotal=$page;
	error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Printing Green HB472 Envelope Stuffings For Courts\n",3,"/logs/user.log");
}elseif($_GET[mail]){
	$qd="select packet_id from ps_packets where process_status = 'READY TO MAIL' AND mail_status <> 'Printed Awaiting Postage' AND attorneys_id <> '70' AND lossMit <> '' AND lossMit <> 'N/A - OLD L' AND (uspsVerify='' OR qualityControl='' ) order by packet_id ASC";
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
		while($d=mysql_fetch_array($r, MYSQL_ASSOC)){$i++;
			$r2=@mysql_query("SELECT packetID from mailMatrix where packetID='$d[packet_id]'");
			$d2=mysql_fetch_array($r2,MYSQL_ASSOC);
			if ($d2[packetID]){
				$page = buildFromMatrix($d[packet_id]);
			}else{
				$page = buildFromPacket($d[packet_id]);
			}
			$pagesTotal .= $page.$page;
			$today=date('Y-m-d');
			error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Printing Envelope Stuffings For $today \n",3,"/logs/user.log");
		}
	}
}elseif($_GET[mailDate]){
	$q="select packet_id from ps_packets where service_status = 'MAILING AND POSTING' AND closeOut='$_GET[mailDate]' AND attorneys_id <> '70' AND lossMit <> '' AND lossMit <> 'N/A - OLD L' order by packet_id ASC";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	while($d=mysql_fetch_array($r, MYSQL_ASSOC)){$i++;
		$r2=@mysql_query("SELECT packetID from mailMatrix where packetID='$d[packet_id]'");
		$d2=mysql_fetch_array($r2,MYSQL_ASSOC);
		if ($d2[packetID]){
			$page = buildFromMatrix($d[packet_id]);
		}else{
			$page = buildFromPacket($d[packet_id]);
		}
		$pagesTotal .= $page.$page;
		error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Printing Envelope Stuffings For OTD$d[packet_id] \n",3,"/logs/user.log");
	}
}
if ($pagesTotal != '' && $_GET[noExport] != 1){
	require_once("/thirdParty/dompdf-0.5.1/dompdf_config.inc.php");
	$old_limit = ini_set("memory_limit", "72M");
	$dompdf = new DOMPDF();
	$dompdf->load_html($pagesTotal);
	$dompdf->set_paper('letter', 'portrait');
	$dompdf->render();
	if ($_GET[mailDate]){
		//if multiple files, then place PDF in main scans folder
		$unique = "/data/service/scans/Envelope Stuffings For ".$_GET[mailDate].".PDF";
		$unique2= "http://mdwestserve.com/affidavits/Envelope Stuffings For ".$_GET[mailDate].".PDF";
		$filename="Envelope Stuffings For ".$_GET[mailDate].".PDF";
	}elseif ($_GET[mail]){
		//only process if no unconfirmed files in queue
		if ($dd == 1){
			$today=date('Y-m-d');
			//if multiple files, then place PDF in main scans folder
			$unique = "/data/service/scans/Envelope Stuffings For ".$today.".PDF";
			$unique2= "http://mdwestserve.com/affidavits/Envelope Stuffings For ".$today.".PDF";
			$filename="Envelope Stuffings For ".$today.".PDF";
		}
	}elseif($_GET[court]){
		//if multiple files, then place PDF in main scans folder
		$unique = "/data/service/scans/Envelope Stuffings For ALL Courts.PDF";
		$unique2= "http://mdwestserve.com/affidavits/Envelope Stuffings For ALL Courts.PDF";
		$filename="Envelope Stuffings For ALL Courts.PDF";
	}elseif($_GET[id]){
		//if single file for an unspecified ID, then place PDF in main scans folder
		$unique = "/data/service/scans/Envelope Stuffings For ID ".$_GET[id].".PDF";
		$unique2= "http://mdwestserve.com/affidavits/Envelope Stuffings For ID ".$_GET[id].".PDF";
		$filename="Envelope Stuffings For Packet ID ".$_GET[id].".PDF";
	}else{
		//if single file for a specific packet, then place PDF in file's folder
		$unique = "/data/service/orders/$folder/Envelope Stuffings For Packet ".$packet.".PDF";
		$unique2= "http://mdwestserve.com/PS_PACKETS/$folder/Envelope Stuffings For Packet ".$packet.".PDF";
		$filename="Envelope Stuffings For Packet OTD".$packet.".PDF";
	}
	file_put_contents($unique, $dompdf->output()); //save to disk
	if (!$dd || $dd == 1){
		if ($_GET[openDoc]){
			echo "<script>window.location='$unique2';</script>";
		}else{
			$dompdf->stream($filename);
		}
	}
}elseif($_GET[noExport] == 1){
	echo $pagesTotal;
}else{
	echo "<h1>NO PACKETS TO DISPLAY</h1>";
}
?>
=======
<?
mysql_connect();
mysql_select_db('core');
function getFolder($otd){
	$path=explode("/",$otd);
	$count=(count($path)-2);
	$folder=$path["$count"];
	return $folder;
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
function pageMaker($id,$matrix){
$r=@mysql_query("SELECT * FROM envelopeImage WHERE envID = '$id'");
$d=mysql_fetch_array($r,MYSQL_ASSOC);
ob_start(); ?>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<table style="page-break-after:always; ">
	<tr>
		<td style='line-height:1px; font-size:10px;'></td>
	</tr>
	<tr>
		<td style="line-height:12px; font-size:20px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;REQUEST FOR</td>
	</tr>
	<tr>
		<td style='line-height:13.5px; font-size:20px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;FORECLOSURE MEDIATION</td>
	</tr>
	<tr>
		<td style="line-height:4.5px; font-size:11px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=stripslashes(strtoupper($d[to1]))?></td>
	</tr>	
	<tr>
		<td style="line-height:4.5px; font-size:11px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=stripslashes(strtoupper($d[to2]))?></td>
	</tr>	
	<tr>
		<td style="line-height:0px; font-size:11px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=stripslashes(strtoupper($d[to3]))?></td>
	</tr>
</table>
<? 
$html = ob_get_clean();
return $html;
}
function prelimPageMaker($id,$matrix){
$r=@mysql_query("SELECT * FROM envelopeImage WHERE envID = '$id'");
$d=mysql_fetch_array($r,MYSQL_ASSOC);
ob_start(); ?>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<table style="page-break-after:always; ">
	<tr>
		<td style='line-height:6px; font-size:12px;'></td>
	</tr>
	<tr>
		<td style="line-height:6px; font-size:12px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=stripslashes(strtoupper($d[to1]))?></td>
	</tr>	
	<tr>
		<td style="line-height:6px; font-size:12px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=stripslashes(strtoupper($d[to2]))?></td>
	</tr>	
	<tr>
		<td style="line-height:0px; font-size:12px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=stripslashes(strtoupper($d[to3]))?></td>
	</tr>
</table>
<? 
$html = ob_get_clean();
return $html;
}
function buildFromPacket($packet){
	$r=@mysql_query("select name1, name2, name3, name4, name5, name6, address1, address1a, address1b, address1c, address1d, address1e, city1, city1a, city1b, city1c, city1d, city1e, state1, state1a, state1b, state1c, state1d, state1e, zip1, zip1a, zip1b, zip1c, zip1d, zip1e, address2, address2a, address2b, address2c, address2d, address2e, city2, city2a, city2b, city2c, city2d, city2e, state2, state2a, state2b, state2c, state2d, state2e, zip2, zip2a, zip2b, zip2c, zip2d, zip2e, address3, address3a, address3b, address3c, address3d, address3e, city3, city3a, city3b, city3c, city3d, city3e, state3, state3a, state3b, state3c, state3d, state3e, zip3, zip3a, zip3b, zip3c, zip3d, zip3e, address4, address4a, address4b, address4c, address4d, address4e, city4, city4a, city4b, city4c, city4d, city4e, state4, state4a, state4b, state4c, state4d, state4e, zip4, zip4a, zip4b, zip4c, zip4d, zip4e, address5, address5a, address5b, address5c, address5d, address5e, city5, city5a, city5b, city5c, city5d, city5e, state5, state5a, state5b, state5c, state5d, state5e, zip5, zip5a, zip5b, zip5c, zip5d, zip5e, address6, address6a, address6b, address6c, address6d, address6e, city6, city6a, city6b, city6c, city6d, city6e, state6, state6a, state6b, state6c, state6d, state6e, zip6, zip6a, zip6b, zip6c, zip6d, zip6e, pobox, pobox2, pocity, pocity2, postate, postate2, pozip, pozip2, lossMit, attorneys_id, circuit_court from ps_packets where packet_id = '$packet'");
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	$i=0;
	$page='';
	$toCounty=county2envelope($d[circuit_court]);
	$toAttorney=att2envelope($d[attorneys_id]);
	while ($i < 6){$i++;
		if ($d["name$i"]){
			if ($d[lossMit] != 'PRELIMINARY'){
				$page .= pageMaker($toCounty,"$packet-$i");
				$page .= pageMaker($toAttorney,"$packet-$i");
			}else{
				$page .= prelimPageMaker($toAttorney,"$packet-$i");
			}
		}
		foreach(range('a','e') as $letter){
			$var=$i.$letter;
			if ($d["address$var"]){
				$var=strtoupper($var);
				if ($d[lossMit] != 'PRELIMINARY'){
					$page .= pageMaker($toCounty,"$packet-$var");
					$page .= pageMaker($toAttorney,"$packet-$var");
				}else{
					$page .= prelimPageMaker($toAttorney,"$packet-$var");
				}
			}
		}
		if ($d[pobox]){
			$var=$i."PO";
			if ($d[lossMit] != 'PRELIMINARY'){
				$page .= pageMaker($toCounty,"$packet-$var");
				$page .= pageMaker($toAttorney,"$packet-$var");
			}else{
				$page .= prelimPageMaker($toAttorney,"$packet-$var");
			}
		}
		if ($d[pobox2]){
			$var=$i."PO2";
			if ($d[lossMit] != 'PRELIMINARY'){
				$page .= pageMaker($toCounty,"$packet-$var");
				$page .= pageMaker($toAttorney,"$packet-$var");
			}else{
				$page .= prelimPageMaker($toAttorney,"$packet-$var");
			}
		}
	}
	return $page;
}
function buildFromMatrix($packet){
	$q="SELECT * from mailMatrix where packetID='$packet'";
	$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	$r1=@mysql_query("SELECT lossMit, circuit_court, attorneys_id from ps_packets where packet_id='$packet'");
	$d1=mysql_fetch_array($r1,MYSQL_ASSOC);
	$i=0;
	$page='';
	$toCounty=county2envelope($d1[circuit_court]);
	$toAttorney=att2envelope($d1[attorneys_id]);
	$lossMit=$d1[lossMit];
	while ($i < 6){$i++;
		$var='';
		if (trim($d["add$i"]) != ''){
			if ($lossMit != 'PRELIMINARY'){
				$page .= pageMaker($toCounty,"$packet-$i");
				$page .= pageMaker($toAttorney,"$packet-$i");
			}else{
				$page .= prelimPageMaker($toAttorney,"$packet-$i");
			}
		}
		foreach(range('a','e') as $letter){
			$var=$i.$letter;
			if (trim($d["add$var"]) != ''){
				if ($lossMit != 'PRELIMINARY'){
					$page .= pageMaker($toCounty,"$packet-$var");
					$page .= pageMaker($toAttorney,"$packet-$var");
				}else{
					$page .= prelimPageMaker($toAttorney,"$packet-$var");
				}
			}
		}
		$var=$i."PO";
		if (trim($d["add$var"]) != ''){
			if ($lossMit != 'PRELIMINARY'){
				$page .= pageMaker($toCounty,"$packet-$var");
				$page .= pageMaker($toAttorney,"$packet-$var");
			}else{
				$page .= prelimPageMaker($toAttorney,"$packet-$var");
			}
		}
		$var=$i."PO2";
		if (trim($d["add$var"]) != ''){
			if ($lossMit != 'PRELIMINARY'){
				$page .= pageMaker($toCounty,"$packet-$var");
				$page .= pageMaker($toAttorney,"$packet-$var");
			}else{
				$page .= prelimPageMaker($toAttorney,"$packet-$var");
			}
		}
	}
	return $page;
}
if ($_GET[packet]){
	$packet=$_GET[packet];
	$r=@mysql_query("SELECT packetID from mailMatrix where packetID='$packet'");
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	if ($_GET[openDoc]){
		//need file path to display PDF
		$r1=@mysql_query("SELECT otd from ps_packets where packet_id='$packet'");
		$d1=mysql_fetch_array($r1,MYSQL_ASSOC);
		$folder=getFolder($d1[otd]);
	}
	if ($d[packetID]){
		$page=buildFromMatrix($packet);
	}else{
		$page=buildFromPacket($packet);
	}
	$pagesTotal=$page.$page;
	error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Printing Envelope Stuffings For OTD$_GET[packet] \n",3,"/logs/user.log");
}elseif($_GET[id]){
	$r=@mysql_query("SELECT * FROM envelopeImage WHERE envID = '$_GET[id]'");
	$d=mysql_fetch_array($r,MYSQL_ASSOC); 
	error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Printing Green HB472 Envelope Stuffings For [$d[to1]] \n",3,"/logs/user.log");
	if ($_GET[build3]){
		//use other variation for envelope front, with "REQUEST FOR MEDIATION" broken into two lines
		$page=pageMaker($_GET[id],'');
	}elseif ($d[addressType] == 'COURT'){
		//if court, use pageMaker (for enlarged type)
		$page=pageMaker($_GET[id],'');
	}else{
		//use pageMaker
		$page=pageMaker($_GET[id],'');
	}
	$pagesTotal=$page;
}elseif($_GET[court]){
	$r=@mysql_query("SELECT * FROM envelopeImage WHERE addressType='COURT' ORDER BY envID ASC");
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		if ($_GET[build3]){
			//use other variation for envelope front, with "REQUEST FOR MEDIATION" broken into two lines
			$page .= pageMaker($d[envID],'');
		}else{
			$page .= pageMaker($d[envID],'');
		}
	}
	$pagesTotal=$page;
	error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Printing Green HB472 Envelope Stuffings For Courts\n",3,"/logs/user.log");
}elseif($_GET[mail]){
	$qd="select packet_id from ps_packets where process_status = 'READY TO MAIL' AND mail_status <> 'Printed Awaiting Postage' AND attorneys_id <> '70' AND lossMit <> '' AND lossMit <> 'N/A - OLD L' AND (uspsVerify='' OR qualityControl='' ) order by packet_id ASC";
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
		while($d=mysql_fetch_array($r, MYSQL_ASSOC)){$i++;
			$r2=@mysql_query("SELECT packetID from mailMatrix where packetID='$d[packet_id]'");
			$d2=mysql_fetch_array($r2,MYSQL_ASSOC);
			if ($d2[packetID]){
				$page = buildFromMatrix($d[packet_id]);
			}else{
				$page = buildFromPacket($d[packet_id]);
			}
			$pagesTotal .= $page.$page;
			$today=date('Y-m-d');
			error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Printing Envelope Stuffings For $today \n",3,"/logs/user.log");
		}
	}
}elseif($_GET[mailDate]){
	$q="select packet_id from ps_packets where service_status = 'MAILING AND POSTING' AND closeOut='$_GET[mailDate]' AND attorneys_id <> '70' AND lossMit <> '' AND lossMit <> 'N/A - OLD L' order by packet_id ASC";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	while($d=mysql_fetch_array($r, MYSQL_ASSOC)){$i++;
		$r2=@mysql_query("SELECT packetID from mailMatrix where packetID='$d[packet_id]'");
		$d2=mysql_fetch_array($r2,MYSQL_ASSOC);
		if ($d2[packetID]){
			$page = buildFromMatrix($d[packet_id]);
		}else{
			$page = buildFromPacket($d[packet_id]);
		}
		$pagesTotal .= $page.$page;
		error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Printing Envelope Stuffings For OTD$d[packet_id] \n",3,"/logs/user.log");
	}
}
if ($pagesTotal != '' && $_GET[noExport] != 1){
	require_once("/thirdParty/dompdf/dompdf_config.inc.php");
	$old_limit = ini_set("memory_limit", "72M");
	$dompdf = new DOMPDF();
	$dompdf->load_html($pagesTotal);
	$dompdf->set_paper('letter', 'portrait');
	$dompdf->render();
	if ($_GET[mailDate]){
		//if multiple files, then place PDF in main scans folder
		$unique = "/data/service/scans/Envelope Stuffings For ".$_GET[mailDate].".PDF";
		$unique2= "http://mdwestserve.com/affidavits/Envelope Stuffings For ".$_GET[mailDate].".PDF";
		$filename="Envelope Stuffings For ".$_GET[mailDate].".PDF";
	}elseif ($_GET[mail]){
		//only process if no unconfirmed files in queue
		if ($dd == 1){
			$today=date('Y-m-d');
			//if multiple files, then place PDF in main scans folder
			$unique = "/data/service/scans/Envelope Stuffings For ".$today.".PDF";
			$unique2= "http://mdwestserve.com/affidavits/Envelope Stuffings For ".$today.".PDF";
			$filename="Envelope Stuffings For ".$today.".PDF";
		}
	}elseif($_GET[court]){
		//if multiple files, then place PDF in main scans folder
		$unique = "/data/service/scans/Envelope Stuffings For ALL Courts.PDF";
		$unique2= "http://mdwestserve.com/affidavits/Envelope Stuffings For ALL Courts.PDF";
		$filename="Envelope Stuffings For ALL Courts.PDF";
	}elseif($_GET[id]){
		//if single file for an unspecified ID, then place PDF in main scans folder
		$unique = "/data/service/scans/Envelope Stuffings For ID ".$_GET[id].".PDF";
		$unique2= "http://mdwestserve.com/affidavits/Envelope Stuffings For ID ".$_GET[id].".PDF";
		$filename="Envelope Stuffings For Packet ID ".$_GET[id].".PDF";
	}else{
		//if single file for a specific packet, then place PDF in file's folder
		$unique = "/data/service/orders/$folder/Envelope Stuffings For Packet ".$packet.".PDF";
		$unique2= "http://mdwestserve.com/PS_PACKETS/$folder/Envelope Stuffings For Packet ".$packet.".PDF";
		$filename="Envelope Stuffings For Packet OTD".$packet.".PDF";
	}
	file_put_contents($unique, $dompdf->output()); //save to disk
	if (!$dd || $dd == 1){
		if ($_GET[openDoc]){
			echo "<script>window.location='$unique2';</script>";
		}else{
			$dompdf->stream($filename);
		}
	}
}elseif($_GET[noExport] == 1){
	echo $pagesTotal;
}else{
	echo "<h1>NO PACKETS TO DISPLAY</h1>";
}
?>
>>>>>>> ae6b22950ae2d266a793ab3f8c1fa3d24083a459
<script>document.title='HB472 Green Envelope Stuffings';</script>