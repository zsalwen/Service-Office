<?
include 'common.php';
include 'mail.class.php';
hardLog('Envelope Master Printer','user');

$_SESSION[inc] = 0;
function printSet($packet,$def,$add){
	$_SESSION[inc] = $_SESSION[inc]+2;
	$address=$def.$add;
	$r=@mysql_query("select otd, name1, name2, name3, name4, name5, name6, address1, address1a, address1b, address1c, address1d, address1e, city1, city1a, city1b, city1c, city1d, city1e, state1, state1a, state1b, state1c, state1d, state1e, zip1, zip1a, zip1b, zip1c, zip1d, zip1e, address2, address2a, address2b, address2c, address2d, address2e, city2, city2a, city2b, city2c, city2d, city2e, state2, state2a, state2b, state2c, state2d, state2e, zip2, zip2a, zip2b, zip2c, zip2d, zip2e, address3, address3a, address3b, address3c, address3d, address3e, city3, city3a, city3b, city3c, city3d, city3e, state3, state3a, state3b, state3c, state3d, state3e, zip3, zip3a, zip3b, zip3c, zip3d, zip3e, address4, address4a, address4b, address4c, address4d, address4e, city4, city4a, city4b, city4c, city4d, city4e, state4, state4a, state4b, state4c, state4d, state4e, zip4, zip4a, zip4b, zip4c, zip4d, zip4e, address5, address5a, address5b, address5c, address5d, address5e, city5, city5a, city5b, city5c, city5d, city5e, state5, state5a, state5b, state5c, state5d, state5e, zip5, zip5a, zip5b, zip5c, zip5d, zip5e, address6, address6a, address6b, address6c, address6d, address6e, city6, city6a, city6b, city6c, city6d, city6e, state6, state6a, state6b, state6c, state6d, state6e, zip6, zip6a, zip6b, zip6c, zip6d, zip6e, pobox, pobox2, pocity, pocity2, postate, postate2, pozip, pozip2, lossMit from ps_packets where packet_id = '$packet'");
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	// postage calculation
	$mail = new postage;
	$mail -> pdf = $d[otd];
	if ($d[lossMit] == 'PRELIMINARY'){
		$mail -> lender = $d[lossMit];
	}
	$mail -> greenEnvelopes = 1;
	$weight = $mail -> weight();
	$mail -> weight = $weight;
	$cost = $mail -> cost();
	// end
	$card = $_GET[card];
	$name = $d["name$def"];
	if ($add == 'PO'){
		$line1 = $d[pobox];
		$csz = $d[pocity].', '.$d[postate].' '.$d[pozip];
	}elseif($add == 'PO2'){
		$line1 = $d[pobox2];
		$csz = $d[pocity2].', '.$d[postate2].' '.$d[pozip2];
	}else{
		$line1 = $d["address$address"];
		$csz = $d["city$address"].', '.$d["state$address"].' '.$d["zip$address"];
	}
	$cord = "$packet-$def$add".'X';

	?>
	<table style='page-break-after:always' align='left' width='100%' style='font-size:11px; font-variant:small-caps;'><tr><td>
	<br><br><br><br><br><br><br><br><br><br><div style='padding-left:30px;'>MDWestServe, Inc.<br>300 E JOPPA RD STE 1102<br>TOWSON MD 21286-3012</div><IMG SRC="http://staff.mdwestserve.com/barcode.php?barcode=<?=$cord?>&width=400&height=40"><br><small style="padding-left:155px"><b>**<?=$cost?>**</b></small>
	</td></tr><tr><td style='padding-left:100px;'><img src="http://staff.mdwestserve.com/small.logo.gif"></td></tr><tr><td style='font-size:25px;' align='left'><div style='padding-top:20px; padding-left:500px; text-align:left; width:300px;'><?=$name?><br><?=$line1?><br><?=$csz?></div></td></tr></table>
	<table style='page-break-after:always' align='left' width='100%' style='font-size:11px; font-variant:small-caps;'><tr><td>
	<br><br><br><br><br><br><br><br><br><br><div style='padding-left:30px;'>MDWestServe, Inc.<br>300 E JOPPA RD STE 1102<br>TOWSON MD 21286-3012</div><IMG SRC="http://staff.mdwestserve.com/barcode.php?barcode=<?=$cord?>&width=400&height=40"><br><small style="padding-left:155px"><b>**<?=$cost?>**</b></small>
	</td></tr><tr><td style='padding-left:100px;'><img src="http://staff.mdwestserve.com/small.logo.gif"></td></tr><tr><td style='font-size:25px;' align='left'><div style='padding-top:20px; padding-left:500px; text-align:left; width:300px;'><?=$name?><br><?=$line1?><br><?=$csz?></div></td></tr></table>
	<?
}
function getMatrixData($packet){
	$qm="SELECT * FROM mailMatrix WHERE packetID='$packet'";
	$rm=@mysql_query($qm);
	$dm=mysql_fetch_array($rm, MYSQL_ASSOC);
	$i=0;
	while ($i < 6){$i++;
		if ($dm["add$i"] != ''){
			printSet($packet,$i,'');
		}
		foreach(range('a','e') as $letter){
			$var=$i.$letter;
			if($dm["add$var"] != ''){
				printSet($packet,$i,$letter);
			}
		}
		$field=$i."PO";
		if ($dm["add$field"] != ''){
			printSet($packet,$i,'PO');
		}
		$field=$i."PO2";
		if ($dm["add$field"] != ''){
			printSet($packet,$i,'PO2');	
		}
	}
}
function getPacketData($packet){
	$q="select address1, address1a, address1b, address1c, address1d, address1e, city1, city1a, city1b, city1c, city1d, city1e, state1, state1a, state1b, state1c, state1d, state1e, zip1, zip1a, zip1b, zip1c, zip1d, zip1e, address2, address2a, address2b, address2c, address2d, address2e, city2, city2a, city2b, city2c, city2d, city2e, state2, state2a, state2b, state2c, state2d, state2e, zip2, zip2a, zip2b, zip2c, zip2d, zip2e, address3, address3a, address3b, address3c, address3d, address3e, city3, city3a, city3b, city3c, city3d, city3e, state3, state3a, state3b, state3c, state3d, state3e, zip3, zip3a, zip3b, zip3c, zip3d, zip3e, address4, address4a, address4b, address4c, address4d, address4e, city4, city4a, city4b, city4c, city4d, city4e, state4, state4a, state4b, state4c, state4d, state4e, zip4, zip4a, zip4b, zip4c, zip4d, zip4e, address5, address5a, address5b, address5c, address5d, address5e, city5, city5a, city5b, city5c, city5d, city5e, state5, state5a, state5b, state5c, state5d, state5e, zip5, zip5a, zip5b, zip5c, zip5d, zip5e, address6, address6a, address6b, address6c, address6d, address6e, city6, city6a, city6b, city6c, city6d, city6e, state6, state6a, state6b, state6c, state6d, state6e, zip6, zip6a, zip6b, zip6c, zip6d, zip6e, pobox, pobox2, pocity, pocity2, postate, postate2, pozip, pozip2, name1, name2, name3, name4, name5, name6 from ps_packets where packet_id = '$packet'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);	
	$i=0;
	while ($i < 6){$i++;
		if ($d["name$i"]){
			printSet($packet,$i,'');	
			foreach(range('a','e') as $letter){
				$address=$i.$letter;
				if ($d["address$address"]){
					printSet($packet,$i,$letter);
				}
			}
			if ($d[pobox]){
				printSet($packet,$i,'PO');
			}
			if ($d[pobox2]){
				printSet($packet,$i,'PO2');
			}
		}
	}
	return $data;
}

function EVprintSet($packet,$def){
	$_SESSION[inc] = $_SESSION[inc]+2;
	$r=@mysql_query("select name1, name2, name3, name4, name5, name6, address1, address1a, address1b, address1c, address1d, address1e, city1, city1a, city1b, city1c, city1d, city1e, state1, state1a, state1b, state1c, state1d, state1e, zip1, zip1a, zip1b, zip1c, zip1d, zip1e, address2, address2a, address2b, address2c, address2d, address2e, city2, city2a, city2b, city2c, city2d, city2e, state2, state2a, state2b, state2c, state2d, state2e, zip2, zip2a, zip2b, zip2c, zip2d, zip2e, address3, address3a, address3b, address3c, address3d, address3e, city3, city3a, city3b, city3c, city3d, city3e, state3, state3a, state3b, state3c, state3d, state3e, zip3, zip3a, zip3b, zip3c, zip3d, zip3e, address4, address4a, address4b, address4c, address4d, address4e, city4, city4a, city4b, city4c, city4d, city4e, state4, state4a, state4b, state4c, state4d, state4e, zip4, zip4a, zip4b, zip4c, zip4d, zip4e, address5, address5a, address5b, address5c, address5d, address5e, city5, city5a, city5b, city5c, city5d, city5e, state5, state5a, state5b, state5c, state5d, state5e, zip5, zip5a, zip5b, zip5c, zip5d, zip5e, address6, address6a, address6b, address6c, address6d, address6e, city6, city6a, city6b, city6c, city6d, city6e, state6, state6a, state6b, state6c, state6d, state6e, zip6, zip6a, zip6b, zip6c, zip6d, zip6e, pobox, pobox2, pocity, pocity2, postate, postate2, pozip, pozip2 from evictionPackets where eviction_id = '$packet'");
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	$card = $_GET[card];
	$name = "OCCUPANT";
	$line1 = $d["address$def"];
	$csz = $d["city$def"].', '.$d["state$def"].' '.$d["zip$def"];
	$cord = "EV$packet-$def".'X';
	// postage calculation
	$mail = new postage;
	$mail -> pdf = $d[otd];
	$weight = $mail -> weight();
	$mail -> weight = $weight;
	$cost = $mail -> cost();
	$cost = number_format($cost,2);
	// end
	?>
	<table style='page-break-after:always' align='left' width='100%' style='font-size:11px; font-variant:small-caps;'><tr><td>
	<br><br><br><br><br><br><br><br><br><br><div style='padding-left:30px;'>MDWestServe, Inc.<br>300 E JOPPA RD STE 1102<br>TOWSON MD 21286-3012</div><IMG SRC="http://staff.mdwestserve.com/barcode.php?barcode=<?=$cord?>&width=400&height=40"><br><small style="padding-left:155px"><b>**<?=$cost?>**</b></small>
	</td></tr><tr><td style='padding-left:100px;'><img src="http://staff.mdwestserve.com/small.logo.gif"></td></tr><tr><td style='font-size:25px;' align='left'><div  style='padding-top:20px; padding-left:500px; text-align:left; width:300px;'><?=$name?><br><?=$line1?><br><?=$csz?></div></td></tr></table>
	<table style='page-break-after:always' align='left' width='100%' style='font-size:11px; font-variant:small-caps;'><tr><td>
	<br><br><br><br><br><br><br><br><br><br><div style='padding-left:30px;'>MDWestServe, Inc.<br>300 E JOPPA RD STE 1102<br>TOWSON MD 21286-3012</div><IMG SRC="http://staff.mdwestserve.com/barcode.php?barcode=<?=$cord?>&width=400&height=40"><br><small style="padding-left:155px"><b>**<?=$cost?>**</b></small>
	</td></tr><tr><td style='padding-left:100px;'><img src="http://staff.mdwestserve.com/small.logo.gif"></td></tr><tr><td style='font-size:25px;' align='left'><div  style='padding-top:20px; padding-left:500px; text-align:left; width:300px;'><?=$name?><br><?=$line1?><br><?=$csz?></div></td></tr></table>
	<?
}
if ($_GET[OTD] && $_GET[start] && $_GET[stop]){
	$q="select packet_id from ps_packets where process_status='READY TO MAIL' AND packet_id >= '$_GET[start]' AND packet_id <= '$_GET[stop]' order by packet_id";
	$r=@mysql_query($q);
	 while($d=mysql_fetch_array($r, MYSQL_ASSOC)){ $i++;
		$qm="SELECT packetID FROM mailMatrix WHERE packetID='$d[packet_id]'";
		$rm=@mysql_query($qm) or die ("Query: $qm<br>".mysql_error());
		$dm=mysql_fetch_array($rm, MYSQL_ASSOC);
		if ($dm[packetID] != ''){
			getMatrixData($d[packet_id]);
		}else{
			getPacketData($d[packet_id]);
		}
	 }
}elseif ($_GET[EV] && $_GET[start] && $_GET[stop]){
	$q="select eviction_id from evictionPackets where process_status='READY TO MAIL' AND packet_id >= '$_GET[start]' AND packet_id <= '$_GET[stop]' order by eviction_id";
	$r=@mysql_query($q);
	 while($d=mysql_fetch_array($r, MYSQL_ASSOC)){ $i++;
		EVprintSet($d[eviction_id],1);
	 }
}elseif ($_GET[OTD]){
	$qm="SELECT packetID FROM mailMatrix WHERE packetID='$_GET[OTD]'";
	$rm=@mysql_query($qm);
	$dm=mysql_fetch_array($rm, MYSQL_ASSOC);
	if ($dm[packetID] != ''){
		getMatrixData($_GET[OTD]);
	}else{
		getPacketData($_GET[OTD]);
	}
}elseif($_GET[EV]){
	EVprintSet($_GET[EV],1);
}else{
	$qd="select ps_packets.packet_id, evictionPackets.eviction_id from ps_packets, evictionPackets where ps_packets.process_status = 'READY TO MAIL' AND ps_packets.mail_status <> 'Printed Awaiting Postage' AND (ps_packets.uspsVerify='' OR ps_packets.qualityControl='') AND evictionPackets.process_status = 'READY TO MAIL' AND evictionPackets.mail_status <> 'Printed Awaiting Postage' AND (evictionPackets.uspsVerify='' OR evictionPackets.qualityControl='' ) order by ps_packets.packet_id, evictionPackets.eviction_id ASC";
	$rd=@mysql_query($qd) or die ("Query: $qd<br>".mysql_error());
	$dd=mysql_num_rows($rd);
	if ($dd > 0){
		if ($dd == 1){
			$dd2=mysql_fetch_array($rd, MYSQL_ASSOC);
			if ($dd2[packet_id]){
				echo "<script>alert('PACKET [".$dd2[packet_id]."] IS IN THE MAIL QUEUE, BUT HAS NOT BEEN COMPLETELY VERIFIED.  NO ENVELOPE STUFFINGS MAY BE PRINTED UNTIL THIS IS REMEDIED.')</script>";
			}elseif($dd2[eviction_id]){
				echo "<script>alert('EVICTION [".$dd2[eviction_id]."] IS IN THE MAIL QUEUE, BUT HAS NOT BEEN COMPLETELY VERIFIED.  NO ENVELOPE STUFFINGS MAY BE PRINTED UNTIL THIS IS REMEDIED.')</script>";
			}
		}else{
			while($dd2=mysql_fetch_array($rd,MYSQL_ASSOC)){
				if ($dd2[packet_id]){
					$list .= " [OTD".$dd2[packet_id]."]";
				}elseif($dd2[eviction_id]){
					$list .= " [EV".$dd2[eviction_id]."]";
				}
			}
			echo "<script>alert('FILES$list ARE IN THE MAIL QUEUE, BUT HAVE NOT BEEN COMPLETELY VERIFIED.  NO ENVELOPE STUFFINGS MAY BE PRINTED UNTIL THIS IS REMEDIED.')</script>";
		}
	}else{
		$q="select packet_id, mail_status from ps_packets where process_status = 'READY TO MAIL' AND mail_status <> 'Printed Awaiting Postage' order by mail_status, packet_id";
		$r=@mysql_query($q);
		while($d=mysql_fetch_array($r, MYSQL_ASSOC)){ $i++;
			$qm="SELECT packetID FROM mailMatrix WHERE packetID='$d[packet_id]'";
			$rm=@mysql_query($qm) or die ("Query: $qm<br>".mysql_error());
			$dm=mysql_fetch_array($rm, MYSQL_ASSOC);
			if ($dm[packetID] != ''){
				getMatrixData($d[packet_id]);
			}else{
				getPacketData($d[packet_id]);
			}
		}
		$q="select eviction_id, mail_status from evictionPackets where process_status = 'READY TO MAIL' AND mail_status <> 'Printed Awaiting Postage' order by mail_status, eviction_id";
		$r=@mysql_query($q);
		while($d=mysql_fetch_array($r, MYSQL_ASSOC)){ $i++;
			EVprintSet($d[eviction_id],1);
		}
	}
}
?>
<script>document.title='<?=$_SESSION[inc]?> Envelope Master Printer';</script>
