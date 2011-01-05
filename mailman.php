<?
include 'lock.php';
include 'common.php';
?>
<script>
function lockedAlert(name){
	alert("This file has not had all data entry confirmed.  As data entry was performed by "+name+", please ensure that this is verified by someone else.  Upon verification, mailman will unlock to allow printing.");
}
</script>
<?
function washOTD($OTD){
	$otdStr=str_replace('portal//var/www/dataFiles/service/orders/','PS_PACKETS/',$OTD);
	$otdStr=str_replace('data/service/orders/','PS_PACKETS/',$otdStr);
	$otdStr=str_replace('portal/','',$otdStr);
	if (!strpos($otdStr,'mdwestserve.com')){
		$otdStr="http://mdwestserve.com/".$otdStr;
	}
	return $otdStr;
}

function washURI2($uri){
	$return=str_replace('portal//var/www/dataFiles/service/orders/','PS_PACKETS/',$uri);
	$return=str_replace('data/service/orders/','PS_PACKETS/',$return);
	$return=str_replace('portal/','',$return);
	//$return=str_replace('http://mdwestserve.com','http://alpha.mdwestserve.com',$return);
	return $return;
}

$_SESSION[printed]=0;
$_SESSION[ready]=0;
$_SESSION[letters]=0;
function statusColor($status){
	if ($status == "UNKNOWN"){ return "#FFcccc"; }
	if ($status == "Printed Awaiting Postage"){ $_SESSION[printed]++; return "#FFFFcc"; }
	if ($status == "Mailed First Class and Certified Return Receipt"){ return "#ccFFcc"; }
	if ($status == "Mailed First Class"){ return "#ccccFF"; }
	if ($status == "Mailed by Client"){ return "#999999"; }
	$_SESSION[ready]++;
	return "#00ff00";
}

function article($packet,$add){
	$var=$packet."-".strtoupper($add)."X";
	$q="select * from usps where packet = '$var'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	if ($d["article"] != ''){
		return "<div style='background-color:green; display:inline;'>";
	}else{
		return "<div style='background-color:pink; display:inline;'>";
	}
}

function buildFromMatrix($packet){
	$qm="SELECT * FROM mailMatrix WHERE packetID='$packet'";
	$rm=@mysql_query($qm) or die ("Query: $qm<br>".mysql_error());
	$dm=mysql_fetch_array($rm, MYSQL_ASSOC);
	if ($dm[packetID] != ''){
		$count=0;
		$data .= "<td>";
		while ($count < 6){$count++;
			if ($dm["add$count"] != ''){
				$_SESSION[letters] = $_SESSION[letters]+2;
				$data .= " ".article($packet,$count)."<a target='_Blank' href='http://staff.mdwestserve.com/greencard.php?packet=$packet&def=$count&card=return&svc=OTD'>[$packet-$count]</a></div>";
			}
			foreach(range('a','e') as $letter){
				$var=$count.$letter;
				if($dm["add$var"] != ''){
					$_SESSION[letters] = $_SESSION[letters]+2;
					$data .= " ".article($packet,$var)."<a target='_Blank' href='http://staff.mdwestserve.com/greencard.php?packet=$packet&def=$count&add=$letter&card=return&svc=OTD'>[$packet-$var]</a></div>";	
				}
			}
			$field="add".$count."PO";
			if ($dm["$field"] != ''){
				$_SESSION[letters] = $_SESSION[letters]+2;
				$data .= " ".article($packet,$count."PO")."<a target='_Blank' href='http://staff.mdwestserve.com/greencard.php?packet=$packet&def=$count&add=PO&card=return&svc=OTD'>[$packet-".$count."PO]</a></div>";	
			}
			$field="add".$count."PO2";
			if ($dm["$field"] != ''){
				$_SESSION[letters] = $_SESSION[letters]+2;
				$data .= " ".article($packet,$count."PO2")."<a target='_Blank' href='http://staff.mdwestserve.com/greencard.php?packet=$packet&def=$count&add=PO2&card=return&svc=OTD'>[$packet-".$count."PO2]</a></div>";	
			}
		}
	}else{
		$data='';
	}
	return $data;
}

function buildFromPacket($packet){
	$q="select address1, address1a, address1b, address1c, address1d, address1e, city1, city1a, city1b, city1c, city1d, city1e, state1, state1a, state1b, state1c, state1d, state1e, zip1, zip1a, zip1b, zip1c, zip1d, zip1e, address2, address2a, address2b, address2c, address2d, address2e, city2, city2a, city2b, city2c, city2d, city2e, state2, state2a, state2b, state2c, state2d, state2e, zip2, zip2a, zip2b, zip2c, zip2d, zip2e, address3, address3a, address3b, address3c, address3d, address3e, city3, city3a, city3b, city3c, city3d, city3e, state3, state3a, state3b, state3c, state3d, state3e, zip3, zip3a, zip3b, zip3c, zip3d, zip3e, address4, address4a, address4b, address4c, address4d, address4e, city4, city4a, city4b, city4c, city4d, city4e, state4, state4a, state4b, state4c, state4d, state4e, zip4, zip4a, zip4b, zip4c, zip4d, zip4e, address5, address5a, address5b, address5c, address5d, address5e, city5, city5a, city5b, city5c, city5d, city5e, state5, state5a, state5b, state5c, state5d, state5e, zip5, zip5a, zip5b, zip5c, zip5d, zip5e, address6, address6a, address6b, address6c, address6d, address6e, city6, city6a, city6b, city6c, city6d, city6e, state6, state6a, state6b, state6c, state6d, state6e, zip6, zip6a, zip6b, zip6c, zip6d, zip6e, pobox, pobox2, pocity, pocity2, postate, postate2, pozip, pozip2, name1, name2, name3, name4, name5, name6 from ps_packets where packet_id = '$packet'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	$count=0;
	$data .= "<td>";
	while ($count < 6){$count++;
		if ($d["name$count"]){
			$_SESSION[letters] = $_SESSION[letters]+2;
			$data .= " ".article($packet,$count)."<a target='_Blank' href='http://staff.mdwestserve.com/greencard.php?packet=$packet&def=$count&card=return&svc=OTD'>[$packet-$count]</a></div>";
			foreach(range('a','e') as $letter){
				if($d["address$count$letter"]){
					$_SESSION[letters] = $_SESSION[letters]+2;
					$data .= " ".article($packet,$count.$letter)."<a target='_Blank' href='http://staff.mdwestserve.com/greencard.php?packet=$packet&def=$count&add=$letter&card=return&svc=OTD'>[$packet-$count$letter]</a></div>";
				}
			}
			if ($d['pobox']){
				$_SESSION[letters] = $_SESSION[letters]+2;
				$data .= " ".article($packet,$count."PO")."<a target='_Blank' href='http://staff.mdwestserve.com/greencard.php?packet=$packet&def=$count&add=PO&card=return&svc=OTD'>[$packet-".$count."PO]</a></div>";	
			}
			if ($d['pobox2']){
				$_SESSION[letters] = $_SESSION[letters]+2;
				$data .= " ".article($packet,$count."PO2")."<a target='_Blank' href='http://staff.mdwestserve.com/greencard.php?packet=$packet&def=$count&add=PO2&card=return&svc=OTD'>[$packet-".$count."PO2]</a></div>";	
			}
		}
	}
	return $data;
}

function getPacketData($packet){
	$q="select * from ps_packets where packet_id = '$packet'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);//$data = "<td>$d[packet_id]-$defendant</td>";	
	$data .= "<tr bgcolor='".statusColor($d[mail_status])."'>
	<td nowrap class='noprint'>
	<li><a href='?mail4=$packet'>Open Papers to Print (And Mark Printed)</a></li>
	<li><a href='".washOTD($d[otd])."'>Right Click to Save PDF</a></li>
	";
	if ($packet >= 12435 && $d[lossMit] != "N/A - OLD L" && $d[attorneys_id] == 1){
		$data .= "<li><a href='http://staff.mdwestserve.com/otd/stuffPacket.2.php?packet=$packet&mail=1' target='_blank'><span style='color:green; background-color:black; font-weight:bold;'>GREEN HB472 Envelopes</span></a></li>";
	}elseif($packet >= 12435 && $d[lossMit] != "N/A - OLD L" && $d[attorneys_id] != 1){
		$data .= "<li><a href='http://staff.mdwestserve.com/otd/stuffPacket.bgw.php?packet=$packet&mail=1' target='_blank'><span style='color:white; background-color:black; font-weight:bold;'>WHITE HB472 Envelopes</span></a></li>";
	}
	$data .= "</td><td nowrap class='noprint' style='background-color:#FF0000;'>";
	if($_COOKIE[psdata][level] == "Operations"){
		if ($d[service_status] == "MAIL ONLY"){
			$data .= "<a style='background-color:#000000; color:#FFFFFF' href='?mail5=$packet&sStatus=MAIL ONLY'><strong>CloseOut & Email</strong></a>";
		}else{
			$data .= "<a style='background-color:#000000; color:#FFFFFF' href='?mail5=$packet'><strong>CloseOut</strong></a>";
		}
	}
	$data .= "</td>";
	$qm="SELECT packetID FROM mailMatrix WHERE packetID='$packet'";
	$rm=@mysql_query($qm) or die ("Query: $qm<br>".mysql_error());
	$dm=mysql_fetch_array($rm, MYSQL_ASSOC);
	if ($dm[packetID] != ""){
		$data .= buildFromMatrix($packet);
	}else{
		$data .= buildFromPacket($packet);
	}
	$closeOut=strtotime($d['closeOut']);
	if ($closeOut > time()){
		$color="style='color: red;'";
	}elseif($closeOut < time()){
		$color="style='color: green;'";
	}else{
		$color="style='color: yellow;'";
	}
	$closeOut=date('m/d/Y',$closeOut);
	$data .= "<br><b $color>Mail to Be Sent On ".$closeOut."</b>";
	if ($d['processor_notes']){
		$data .= '<br>'.$d['processor_notes'];	
	}
	$data .= '</td>';
	//$data .= "</a></td>";
	$data .= "<td";
	if ($d[rush] == 'checked'){
		$data .= " bgcolor='#FF0000'";
	}elseif($d[priority] == 'checked'){
		$data .= " bgcolor='#00FFFF'";
	}
	$data .=">".$d["affidavit_status"];
	if ($d[rush] == 'checked'){
		$data .= "<br>RUSH";
	}elseif($d[priority] == 'checked'){
		$data .= " <br>PRIORITY";
	}
	$data .="</td>";	
	$data .= "<td>".$d["mail_status"]."</td><td>(".id2attorney($d[attorneys_id]).")<br>$d[circuit_court]</td>";	
	return $data;
}
//will return list with pop up js alerts instead of links
function lockFromMatrix($packet,$entry_id){
	$qm="SELECT * FROM mailMatrix WHERE packetID='$packet'";
	$rm=@mysql_query($qm) or die ("Query: $qm<br>".mysql_error());
	$dm=mysql_fetch_array($rm, MYSQL_ASSOC);
	if ($dm[packetID] != ''){
		$count=0;
		$data .= "<td>";
		while ($count < 6){$count++;
			if ($dm["add$count"] != ''){
				$_SESSION[letters] = $_SESSION[letters]+2;
				$data .= " ".article($packet,$count)."<span onclick='lockedAlert(\'".id2name($entry_id)."\')'>[$packet-$count]</span></div>";
			}
			foreach(range('a','e') as $letter){
				$var=$count.$letter;
				if($dm["add$var"] != ''){
					$_SESSION[letters] = $_SESSION[letters]+2;
					$data .= " ".article($packet,$var)."<span onclick='lockedAlert(\'".id2name($entry_id)."\')'>[$packet-$var]</span></div>";	
				}
			}
			$field="add".$count."PO";
			if ($dm["$field"] != ''){
				$_SESSION[letters] = $_SESSION[letters]+2;
				$data .= " ".article($packet,$count."PO")."<span onclick='lockedAlert(\'".id2name($entry_id)."\')'>[$packet-".$count."PO]</span></div>";	
			}
			$field="add".$count."PO2";
			if ($dm["$field"] != ''){
				$_SESSION[letters] = $_SESSION[letters]+2;
				$data .= " ".article($packet,$count."PO2")."<span onclick='lockedAlert(\'".id2name($entry_id)."\')'>[$packet-".$count."PO2]</span></div>";	
			}
		}
	}else{
		$data='';
	}
	return $data;
}
//will return list with pop up js alerts instead of links
function lockFromPacket($packet){
	$q="select address1, address1a, address1b, address1c, address1d, address1e, city1, city1a, city1b, city1c, city1d, city1e, state1, state1a, state1b, state1c, state1d, state1e, zip1, zip1a, zip1b, zip1c, zip1d, zip1e, address2, address2a, address2b, address2c, address2d, address2e, city2, city2a, city2b, city2c, city2d, city2e, state2, state2a, state2b, state2c, state2d, state2e, zip2, zip2a, zip2b, zip2c, zip2d, zip2e, address3, address3a, address3b, address3c, address3d, address3e, city3, city3a, city3b, city3c, city3d, city3e, state3, state3a, state3b, state3c, state3d, state3e, zip3, zip3a, zip3b, zip3c, zip3d, zip3e, address4, address4a, address4b, address4c, address4d, address4e, city4, city4a, city4b, city4c, city4d, city4e, state4, state4a, state4b, state4c, state4d, state4e, zip4, zip4a, zip4b, zip4c, zip4d, zip4e, address5, address5a, address5b, address5c, address5d, address5e, city5, city5a, city5b, city5c, city5d, city5e, state5, state5a, state5b, state5c, state5d, state5e, zip5, zip5a, zip5b, zip5c, zip5d, zip5e, address6, address6a, address6b, address6c, address6d, address6e, city6, city6a, city6b, city6c, city6d, city6e, state6, state6a, state6b, state6c, state6d, state6e, zip6, zip6a, zip6b, zip6c, zip6d, zip6e, pobox, pobox2, pocity, pocity2, postate, postate2, pozip, pozip2, name1, name2, name3, name4, name5, name6, entry_id from ps_packets where packet_id = '$packet'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	$count=0;
	$data .= "<td>";
	while ($count < 6){$count++;
		if ($d["name$count"]){
			$_SESSION[letters] = $_SESSION[letters]+2;
			$data .= " ".article($packet,$count)."<span onclick='lockedAlert(".id2name($d[entry_id]).")'>[$packet-$count]</span></div>";
			foreach(range('a','e') as $letter){
				if($d["address$count$letter"]){
					$_SESSION[letters] = $_SESSION[letters]+2;
					$data .= " ".article($packet,$count.$letter)."<span onclick='lockedAlert(".id2name($d[entry_id]).")'>[$packet-$count$letter]</span></div>";
				}
			}
			if ($d['pobox']){
				$_SESSION[letters] = $_SESSION[letters]+2;
				$data .= " ".article($packet,$count."PO")."<span onclick='lockedAlert(".id2name($d[entry_id]).")'>[$packet-".$count."PO]</span></div>";	
			}
			if ($d['pobox2']){
				$_SESSION[letters] = $_SESSION[letters]+2;
				$data .= " ".article($packet,$count."PO2")."<span onclick='lockedAlert(".id2name($d[entry_id]).")'>[$packet-".$count."PO2]</span></div>";	
			}
		}
	}
	return $data;
}
//function to create locked entry for a file if data entry has not been confirmed
function lockPacket($packet){
	$q="select * from ps_packets where packet_id = '$packet'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);//$data = "<td>$d[packet_id]-$defendant</td>";	
	$data .= "<tr bgcolor='#CCCCCC' id='OTD$packet'>
	<td nowrap class='noprint'>
	<li><span onclick='lockedAlert(".id2name($d[entry_id]).")'>Open Papers to Print (And Mark Printed)</span></li>
	<li><span onclick='lockedAlert(".id2name($d[entry_id]).")'>Right Click to Save PDF</span></li>
	";
	$data .= "</td><td nowrap class='noprint' style='background-color:#CCCCCC;'>";
	if($_COOKIE[psdata][level] == "Operations"){
	$data .= "<span style='background-color:#CCCCCC; color:#FFFFFF' onclick='lockedAlert(".id2name($d[entry_id]).")'><strong>CloseOut";
	if ($d[service_status] == "MAIL ONLY"){
		$data .= " & Email";
	}
	$data .= "</strong></span>";
	}
	$data .= "</td>";
	$qm="SELECT packetID FROM mailMatrix WHERE packetID='$packet'";
	$rm=@mysql_query($qm) or die ("Query: $qm<br>".mysql_error());
	$dm=mysql_fetch_array($rm, MYSQL_ASSOC);
	if ($dm[packetID] != ""){
		$data .= lockFromMatrix($packet,$d[entry_id]);
	}else{
		$data .= lockFromPacket($packet);
	}
	$closeOut=strtotime($d['closeOut']);
	if ($closeOut > time()){
		$color="style='color: red;'";
	}elseif($closeOut < time()){
		$color="style='color: green;'";
	}else{
		$color="style='color: yellow;'";
	}
	$closeOut=date('m/d/Y',$closeOut);
	$data .= "<br><b $color>Mail to Be Sent On ".$closeOut."</b>";
	if ($d['processor_notes']){
		$data .= '<br>'.$d['processor_notes'];	
	}
	$data .= '</td>';
	//$data .= "</a></td>";
	$data .= "<td";
	if ($d[rush] == 'checked'){
		$data .= " bgcolor='#FF0000'";
	}elseif($d[priority] == 'checked'){
		$data .= " bgcolor='#00FFFF'";
	}
	$data .=">".$d["affidavit_status"];
	if ($d[rush] == 'checked'){
		$data .= "<br>RUSH";
	}elseif($d[priority] == 'checked'){
		$data .= " <br>PRIORITY";
	}
	$data .="</td>";	
	$data .= "<td>".$d["mail_status"]."</td>";	
	return $data;
}

function getEvictionData($eviction){
	$q="select * from evictionPackets where eviction_id = '$eviction'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	$data .= "<tr bgcolor='".statusColor($d[mail_status])."'>
	<td nowrap class='noprint'>
	<li><a href='?mail6=$eviction'>Open Papers to Print (And Mark Printed)</a></li>
	";
	$data .= "</td><td nowrap class='noprint' style='background-color:#FF0000;'>";
	if($_COOKIE[psdata][level] == "Operations"){
		$data .= "<a style='background-color:#000000; color:#FFFFFF' href='?mail7=$eviction'><strong>CloseOut</strong></a>";
	}
	$data .= "</td>";
	$_SESSION[letters] = $_SESSION[letters]+2;
	$data .= "<td>".article("EV".$eviction,1)."<a target='_Blank' href='http://staff.mdwestserve.com/greencard.php?packet=$eviction&def=1&card=return&svc=EV'>$eviction-1</a></div>";
	$closeOut=strtotime($d['closeOut']);
	if ($closeOut > time()){
		$color="style='color: red;'";
	}elseif($closeOut < time()){
		$color="style='color: green;'";
	}else{
		$color="style='color: yellow;'";
	}
	$closeOut=date('m/d/Y',$closeOut);
	$data .= "<br><b $color>Mail to Be Sent On ".$closeOut."</b>";
	if ($d['processor_notes']){
		$data .= '<br>'.$d['processor_notes'];	
	}
	$data .= '</td>'.$data2;
	$data .= "</td>";
	$data .= "</a></td>";
	$data .= "<td";
	if ($d[rush] == 'checked'){
		$data .= " bgcolor='#FF0000'";
	}elseif($d[priority] == 'checked'){
		$data .= " bgcolor='#00FFFF'";
	}
	$data .=">".$d["affidavit_status"];
	if ($d[rush] == 'checked'){
		$data .= "<br>RUSH";
	}elseif($d[priority] == 'checked'){
		$data .= " <br>PRIORITY";
	}
	$data .="</td>";	
	$data .= "<td>".$d["mail_status"]."</td><td>(".id2attorney($d[attorneys_id]).")<br>$d[circuit_court]</td>";	
	return $data;
}

if (isset($_GET['mail4'])){
	//OTD open papers to print (and mark printed)
	$q4="SELECT otd FROM ps_packets WHERE packet_id='".$_GET['mail4']."'";
	$r4=@mysql_query($q4) or die (mysql_error());
	$d4=mysql_fetch_array($r4, MYSQL_ASSOC);
	$href=washURI2($d4[otd]);
	echo "<script>window.open('".$href."', 'print')</script>";
	psActivity("mailPrint");
	timeline($_GET['mail4'],$_COOKIE[psdata][name]." Printed Mail");
	@mysql_query("update ps_packets set mail_status = 'Printed Awaiting Postage' where packet_id = '".$_GET['mail4']."' ");
	echo "<script>window.location.href='mailman.php?mailDate=".$_GET[mailDate]."';</script>";
}
if (isset($_GET['mail5'])){
	//OTD closeOut
	@mysql_query("update ps_packets set process_status='SERVICE COMPLETED', mail_status='Mailed First Class and Certified Return Receipt' where packet_id = '".$_GET['mail5']."' ");
	timeline($_GET[mail5],$_COOKIE[psdata][name]." Confirmed Mail Sent");
	//if file is Mail Only, email client
	if ($_GET[sStatus] == 'MAIL ONLY'){
		echo "<script>window.open('http://staff.mdwestserve.com/emailBGW.php?OTD=".$_GET[mail5]."', 'emailBGW'); </script>";
		timeline($_GET[mail5],$_COOKIE[psdata][name]." Emailed Client Service Confirmation");
	}
	echo "<script>window.location.href='mailman.php?mailDate=".$_GET[mailDate]."';</script>";
}
if (isset($_GET['mail6'])){
	//EV open papers to print (and mark printed)
	$q4="SELECT otd FROM evictionPackets WHERE eviction_id='".$_GET['mail6']."'";
	$r4=@mysql_query($q4) or die (mysql_error());
	$d4=mysql_fetch_array($r4, MYSQL_ASSOC);
	$href=washURI2($d4[otd]);
	echo "<script>window.open('".$href."', 'print')</script>";
	psActivity("mailPrint");
	ev_timeline($_GET['mail6'],$_COOKIE[psdata][name]." Printed Mail");
	@mysql_query("update evictionPackets set mail_status = 'Printed Awaiting Postage' where eviction_id = '".$_GET['mail6']."' ");
	echo "<script>window.location.href='mailman.php?mailDate=".$_GET[mailDate]."';</script>";
}
if (isset($_GET['mail7'])){
	//EV closeOut
	@mysql_query("update evictionPackets set process_status='SERVICE COMPLETED', mail_status='Mailed First Class and Certified Return Receipt' where eviction_id = '".$_GET['mail7']."' ");
	ev_timeline($_GET[mail6],$_COOKIE[psdata][name]." Confirmed Mail Sent");
	header("Location: mailman.php?mailDate=".$_GET[mailDate]);
}
?>
<style type="text/css">
    @media print {
      .noprint { display: none; }
    }
	a { text-decoration:none; color:#000000; }
	td { padding:5px; }
  </style> 
<div align="center"><font size="+2">READY TO MAIL</font></div>

<?
$me=$_COOKIE[psdata][user_id];
if($_COOKIE[psdata][level] == "Operations"){
	$qd="select ps_packets.packet_id, evictionPackets.eviction_id from ps_packets, evictionPackets where ps_packets.process_status = 'READY TO MAIL' AND ps_packets.mail_status <> 'Printed Awaiting Postage' AND (ps_packets.uspsVerify='' OR ps_packets.qualityControl='' ) AND evictionPackets.process_status = 'READY TO MAIL' AND evictionPackets.mail_status <> 'Printed Awaiting Postage' AND (evictionPackets.uspsVerify='' OR evictionPackets.qualityControl='' ) order by ps_packets.packet_id, evictionPackets.eviction_id ASC";
	$rd=@mysql_query($qd) or die ("Query: $qd<br>".mysql_error());
	$dd=mysql_num_rows($rd);
	if ($dd > 0){
		if ($dd == 1){
			$dd2=mysql_fetch_array($rd, MYSQL_ASSOC);
			if ($dd2[packet_id]){
				$msg = "PACKET [".$dd2[packet_id]."] IS IN THE MAIL QUEUE, BUT HAS NOT BEEN COMPLETELY VERIFIED.  NO ENVELOPE STUFFINGS MAY BE PRINTED UNTIL THIS IS REMEDIED.";
			}elseif($dd2[eviction_id]){
				$msg = "EVICTION [".$dd2[eviction_id]."] IS IN THE MAIL QUEUE, BUT HAS NOT BEEN COMPLETELY VERIFIED.  NO ENVELOPE STUFFINGS MAY BE PRINTED UNTIL THIS IS REMEDIED.";
			}
		}else{
			while($dd2=mysql_fetch_array($rd,MYSQL_ASSOC)){
				if ($dd2[packet_id]){
					$list .= " [OTD".$dd2[packet_id]."]";
				}elseif($dd2[eviction_id]){
					$list .= " [EV".$dd2[eviction_id]."]";
				}
			}
			$msg = "FILES$list ARE IN THE MAIL QUEUE, BUT HAVE NOT BEEN COMPLETELY VERIFIED.  NO ENVELOPE STUFFINGS MAY BE PRINTED UNTIL THIS IS REMEDIED.";
		}
		echo "<div align='center'><font size='+2'>$msg</font></div>";
	}
	$q="select packet_id, mail_status, qualityControl from ps_packets where (process_status = 'READY TO MAIL' OR mail_status='Printed Awaiting Postage') and rush='checked' order by mail_status, packet_id";
}else{
	$q="select packet_id, mail_status, qualityControl from ps_packets where (process_status = 'READY TO MAIL' OR mail_status='Printed Awaiting Postage') and rush='checked' and (server_id = '$me' OR server_ida = '$me' OR server_idb = '$me' OR server_idc = '$me' OR server_idd = '$me' OR server_ide = '$me' ) order by mail_status, packet_id";
}
$r=@mysql_query($q);?>

<table width="100%" border="1" style="border-collapse:collapse; padding:5px;">
<tr><td colspan='6' align='center' style='text-spacing: 5px; background-color:99AAEE; background-color:00BBAA; font-weight:bold;'>FORECLOSURES</td></tr>
<? while($d=mysql_fetch_array($r, MYSQL_ASSOC)){ $i++;
		if ($d[qualityControl] != ''){
			echo getPacketData($d[packet_id]);
		}else{
			echo lockPacket($d[packet_id]);
		}
		?>
    </tr>    
<? } ?>

<?
if($_COOKIE[psdata][level] == "Operations"){
$q="select packet_id, mail_status, qualityControl from ps_packets where (process_status = 'READY TO MAIL' OR mail_status='Printed Awaiting Postage') and rush <> 'checked' order by mail_status, packet_id";
}else{
$q="select packet_id, mail_status, qualityControl from ps_packets where (process_status = 'READY TO MAIL' OR mail_status='Printed Awaiting Postage') and rush <> 'checked' and (server_id = '$me' OR server_ida = '$me' OR server_idb = '$me' OR server_idc = '$me' OR server_idd = '$me' OR server_ide = '$me' ) order by mail_status, packet_id";
}
$r=@mysql_query($q);

 while($d=mysql_fetch_array($r, MYSQL_ASSOC)){ $i++;
		if ($d[qualityControl] != ''){
			echo getPacketData($d[packet_id]);
		}else{
			echo lockPacket($d[packet_id]);
		}
	?>
    </tr>    
<? }
//pull Evictions!
if($_COOKIE[psdata][level] == "Operations"){
$q="select eviction_id, mail_status from evictionPackets where (process_status = 'READY TO MAIL' OR mail_status='Printed Awaiting Postage') and rush='checked' order by mail_status, eviction_id";
}else{
$q="select eviction_id, mail_status from evictionPackets where (process_status = 'READY TO MAIL' OR mail_status='Printed Awaiting Postage') and rush='checked' AND (server_id = '$me' OR server_ida = '$me' OR server_idb = '$me' OR server_idc = '$me' OR server_idd = '$me' OR server_ide = '$me') order by mail_status, eviction_id";
}
$r=@mysql_query($q);?>
<? while($d=mysql_fetch_array($r, MYSQL_ASSOC)){ $i++;?>
		<?=getEvictionData($d[eviction_id])?>
    </tr>    
<? }
echo "<tr><td colspan='6' align='center' style='text-spacing: 5px; background-color:99AAEE; font-weight:bold;'>EVICTIONS</td></tr>";
if($_COOKIE[psdata][level] == "Operations"){
$q="select eviction_id, mail_status from evictionPackets where (process_status = 'READY TO MAIL' OR mail_status='Printed Awaiting Postage') and rush <> 'checked' order by mail_status, eviction_id";
}else{
$q="select eviction_id, mail_status from evictionPackets where (process_status = 'READY TO MAIL' OR mail_status='Printed Awaiting Postage') and rush <> 'checked' and (server_id = '$me' OR server_ida = '$me' OR server_idb = '$me' OR server_idc = '$me' OR server_idd = '$me' OR server_ide = '$me' ) order by mail_status, eviction_id";
}
$r=@mysql_query($q);

 while($d=mysql_fetch_array($r, MYSQL_ASSOC)){ $i++;?>
		<?=getEvictionData($d[eviction_id])?>
    </tr>    
<? } ?>
<?
// standard queries
if ($_GET['mail99']){
	@mysql_query("update standard_packets set process_status = 'READY FOR AFFIDAVITS' where packet_id = $_GET[mail99]");
	mail('service@mdwestserve.com','Standard Packet '.$_GET[mail99].' ready for affidavits','s'.$_GET[mail99].' ready for affidavits by: '.$_COOKIE[psdata][name]);
	timeline($_GET['mail99'],'Status updated to READY FOR AFFIDAVITS by: '.$_COOKIE[psdata][name]);
}
?>
<tr><td colspan='6' align='center' style='text-spacing: 5px; background-color:FF00FF; font-weight:bold;'>STANDARD</td></tr>
<?
$r=@mysql_query("SELECT * FROM standard_packets WHERE process_status = 'READY TO MAIL' order by packet_id");
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
?>
	<tr style='background-color:FFccFF;'>
		<td>
		<li><a href="?mail99=<?=$d[packet_id]?>">Mark Mail Complete</a></li>
		<li><a href="http://staff.mdwestserve.com/standard/order.php?packet=<?=$d[packet_id]?>" target="_Blank">View Order <?=$d[packet_id]?></a></li>
		
		</td>
		<td> - - - </td>
		<td><?
		$i3=0;
		while ($i3 < 6){$i3++;
			if($d["name$i3"]){ echo $d[packet_id]."-$i3, "; }
		}
		?></td>
		<td><?=$d[service_status]?></td>
		<td><?=$d[mail_status]?></td>
	</tr>
<? }?>
</table>
<?
include 'footer.php';
?>
<script>document.title='<?=$_SESSION[letters]?> Letters <?=$_SESSION[ready]?> Queued <?=$_SESSION[printed]?> Printed';</script>