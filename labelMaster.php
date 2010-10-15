<?
include 'common.php';
hardLog('Address Label Printout','user');

$_SESSION[inc] = 0;
function printSet($packet,$def,$add){
	$_SESSION[inc] = $_SESSION[inc]+1;
	$r=@mysql_query("select name1, name2, name3, name4, name5, name6, address1, address1a, address1b, address1c, address1d, address1e, city1, city1a, city1b, city1c, city1d, city1e, state1, state1a, state1b, state1c, state1d, state1e, zip1, zip1a, zip1b, zip1c, zip1d, zip1e, address2, address2a, address2b, address2c, address2d, address2e, city2, city2a, city2b, city2c, city2d, city2e, state2, state2a, state2b, state2c, state2d, state2e, zip2, zip2a, zip2b, zip2c, zip2d, zip2e, address3, address3a, address3b, address3c, address3d, address3e, city3, city3a, city3b, city3c, city3d, city3e, state3, state3a, state3b, state3c, state3d, state3e, zip3, zip3a, zip3b, zip3c, zip3d, zip3e, address4, address4a, address4b, address4c, address4d, address4e, city4, city4a, city4b, city4c, city4d, city4e, state4, state4a, state4b, state4c, state4d, state4e, zip4, zip4a, zip4b, zip4c, zip4d, zip4e, address5, address5a, address5b, address5c, address5d, address5e, city5, city5a, city5b, city5c, city5d, city5e, state5, state5a, state5b, state5c, state5d, state5e, zip5, zip5a, zip5b, zip5c, zip5d, zip5e, address6, address6a, address6b, address6c, address6d, address6e, city6, city6a, city6b, city6c, city6d, city6e, state6, state6a, state6b, state6c, state6d, state6e, zip6, zip6a, zip6b, zip6c, zip6d, zip6e, pobox, pobox2, pocity, pocity2, postate, postate2, pozip, pozip2 from ps_packets where packet_id = '$packet'");
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	$card = $_GET[card];
	$name = $d["name$def"];
	if ($add == 'PO'){
		$po = strtoupper($d[pobox]);
		$line1 = $d[pobox];
		$csz = $d[pocity].', '.$d[postate].' '.$d[pozip];
	}elseif($add == 'PO2'){
		$po = strtoupper($d[pobox2]);
		$line1 = $d[pobox2];
		$csz = $d[pocity2].', '.$d[postate2].' '.$d[pozip2];
	}else{
		$line1 = $d["address$def$add"];
		$csz = $d["city$def$add"].', '.$d["state$def$add"].' '.$d["zip$def$add"];
		$art = $_GET[art];
	}
	$cord = "$packet-$def$add".'X';

	?>
	<table
	<?
		if ($_SESSION[inc]%5){
			
		}else{
			echo "style='page-break-after: always;'";
		}
	?>
	><tr><td valign='top' style="padding-top:50px;">
	<IMG SRC="barcode.php?barcode=<?=$cord?>&width=250&height=40"><br>
	<img  src="http://staff.mdwestserve.com/returncard.jpg.php?name=<?=strtoupper($name)?>&line1=<?=strtoupper(str_replace('#','no. ',$line1))?>&csz=<?=strtoupper($csz)?>&art=<?=$art?>&cord=<?=$cord?>&case_no=<?=str_replace('0','&Oslash;',strtoupper($d[case_no]))?>"><? if($card=='mail'){echo "<img src='gfx/mail.logo.gif'>";}?></div>
	<?
	
	?>
	</td><td valign='top' style="padding-top:50px;">
		<IMG SRC="barcode.php?barcode=<?=$cord?>&width=250&height=40"><br>

	<img src="http://staff.mdwestserve.com/returncard.jpg.php?name=<?=strtoupper($name)?>&line1=<?=strtoupper(str_replace('#','no. ',$line1))?>&csz=<?=strtoupper($csz)?>&art=<?=$art?>&cord=<?=$cord?>*&case_no=<?=str_replace('0','&Oslash;',strtoupper($d[case_no]))?>"><? if($card=='mail'){echo "<img src='gfx/mail.logo.gif'>";}?></div>
	
	</td></tr></table>
	<?
}
function getPacketData($packet){
	$q="select address1, address1a, address1b, address1c, address1d, address1e, city1, city1a, city1b, city1c, city1d, city1e, state1, state1a, state1b, state1c, state1d, state1e, zip1, zip1a, zip1b, zip1c, zip1d, zip1e, address2, address2a, address2b, address2c, address2d, address2e, city2, city2a, city2b, city2c, city2d, city2e, state2, state2a, state2b, state2c, state2d, state2e, zip2, zip2a, zip2b, zip2c, zip2d, zip2e, address3, address3a, address3b, address3c, address3d, address3e, city3, city3a, city3b, city3c, city3d, city3e, state3, state3a, state3b, state3c, state3d, state3e, zip3, zip3a, zip3b, zip3c, zip3d, zip3e, address4, address4a, address4b, address4c, address4d, address4e, city4, city4a, city4b, city4c, city4d, city4e, state4, state4a, state4b, state4c, state4d, state4e, zip4, zip4a, zip4b, zip4c, zip4d, zip4e, address5, address5a, address5b, address5c, address5d, address5e, city5, city5a, city5b, city5c, city5d, city5e, state5, state5a, state5b, state5c, state5d, state5e, zip5, zip5a, zip5b, zip5c, zip5d, zip5e, address6, address6a, address6b, address6c, address6d, address6e, city6, city6a, city6b, city6c, city6d, city6e, state6, state6a, state6b, state6c, state6d, state6e, zip6, zip6a, zip6b, zip6c, zip6d, zip6e, pobox, pobox2, pocity, pocity2, postate, postate2, pozip, pozip2, name1, name2, name3, name4, name5, name6 from ps_packets where packet_id = '$packet'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);	
	printSet($packet,1,'');	
	if($d['address1a']){
		printSet($packet,1,'a');	
	}
	if ($d['address1b']){
		printSet($packet,1,'b');	
	}
	if ($d['address1c']){
		printSet($packet,1,'c');	
	}
	if ($d['address1d']){
		printSet($packet,1,'d');	
	}
	if ($d['address1e']){
		printSet($packet,1,'e');	
	}
	if($d['address2']){	
 		printSet($packet,2,'');	
	}
	if($d['address2a']){
		printSet($packet,2,'a');	
	}
	if ($d['address2b']){
		printSet($packet,2,'b');	
	}
	if ($d['address2c']){
		printSet($packet,2,'c');	
	}
	if ($d['address2d']){
		printSet($packet,2,'d');	
	}
	if ($d['address2e']){
		printSet($packet,2,'e');	
	}
	if($d['address3']){
		printSet($packet,3,'');	
	}
	if($d['address3a']){
		printSet($packet,3,'a');	
	}
	if ($d['address3b']){
		printSet($packet,3,'b');	
	}
	if ($d['address3c']){
		printSet($packet,3,'c');	
	}
	if ($d['address3d']){
		printSet($packet,3,'d');	
	}
	if ($d['address3e']){
		printSet($packet,3,'e');	
	}
	if($d['address4']){
		printSet($packet,4,'');	
	}
	if($d['address4a']){
		printSet($packet,4,'a');	
	}
	if ($d['address4b']){
		printSet($packet,4,'b');	
	}
	if ($d['address4c']){
		printSet($packet,4,'c');	
	}
	if ($d['address4d']){
		printSet($packet,4,'d');	
	}
	if ($d['address4e']){
		printSet($packet,4,'e');	
	}
	if($d['address5']){
		printSet($packet,5,'');	
	}
	if($d['address5a']){
		printSet($packet,5,'a');	
	}
	if ($d['address5b']){
		printSet($packet,5,'b');	
	}
	if ($d['address5c']){
		printSet($packet,5,'c');	
	}
	if ($d['address5d']){
		printSet($packet,5,'d');	
	}
	if ($d['address5e']){
		printSet($packet,5,'e');	
	}
	if($d['address6']){
		printSet($packet,6,'');	
	}
	if($d['address6a']){
		printSet($packet,6,'a');	
	}
	if ($d['address6b']){
		printSet($packet,6,'b');	
	}
	if ($d['address6c']){
		printSet($packet,6,'c');	
	}
	if ($d['address6d']){
		printSet($packet,6,'d');	
	}
	if ($d['address6e']){
		printSet($packet,6,'e');	
	}
	if ($d['pobox']){
		printSet($packet,1,'PO');	
	}
	if ($d['pobox'] && $d['name2']){
		printSet($packet,2,'PO');	
	}
	if ($d['pobox'] && $d['name3']){
		printSet($packet,3,'PO');	
	}
	if ($d['pobox'] && $d['name4']){
		printSet($packet,4,'PO');	
	}
	if ($d['pobox'] && $d['name5']){
		printSet($packet,5,'PO');	
	}
	if ($d['pobox'] && $d['name6']){
		printSet($packet,6,'PO');	
	}
	if ($d['pobox2']){
		printSet($packet,1,'PO2');	
	}
	if ($d['pobox2'] && $d['name2']){
		printSet($packet,2,'PO2');	
	}
	if ($d['pobox2'] && $d['name3']){
		printSet($packet,3,'PO2');	
	}
	if ($d['pobox2'] && $d['name4']){
		printSet($packet,4,'PO2');	
	}
	if ($d['pobox2'] && $d['name5']){
		printSet($packet,5,'PO2');	
	}
	if ($d['pobox2'] && $d['name6']){
		printSet($packet,6,'PO2');	
	}
	return $data;
}

function printSetEV($packet,$def){
	$_SESSION[inc] = $_SESSION[inc]+1;
	$r=@mysql_query("select name1, name2, name3, name4, name5, name6, address1, city1, state1, zip1, address2, city2, state2, zip2, address3, city3, state3, zip3, address4, city4, state4, zip4, address5, city5, state5, zip5, address6, city6, state6, zip6 from evictionPackets where eviction_id = '$packet'");
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	$card = $_GET[card];
	$name = "OCCUPANT";
	$line1 = $d["address$def"];
	$csz = $d["city$def"].', '.$d["state$def"].' '.$d["zip$def"];
	$art = $_GET[art];
	$cord = "EV$packet-$def".'X';

	?>
	<table
	<?
		if ($_SESSION[inc]%5){
			
		}else{
			echo "style='page-break-after: always;'";
		}
	?>
	><tr><td valign='top' style="padding-top:50px;">
	<IMG SRC="barcode.php?barcode=<?=$cord?>&width=250&height=40"><br>
	<img  src="http://staff.mdwestserve.com/returncard.jpg.php?name=<?=strtoupper($name)?>&line1=<?=strtoupper(str_replace('#','no. ',$line1))?>&csz=<?=strtoupper($csz)?>&art=<?=$art?>&cord=<?=$cord?>&case_no=<?=str_replace('0','&Oslash;',strtoupper($d[case_no]))?>"><? if($card=='mail'){echo "<img src='gfx/mail.logo.gif'>";}?></div>
	<?
	
	?>
	</td><td valign='top' style="padding-top:50px;">
		<IMG SRC="barcode.php?barcode=<?=$cord?>&width=250&height=40"><br>

	<img src="http://staff.mdwestserve.com/returncard.jpg.php?name=<?=strtoupper($name)?>&line1=<?=strtoupper(str_replace('#','no. ',$line1))?>&csz=<?=strtoupper($csz)?>&art=<?=$art?>&cord=<?=$cord?>*&case_no=<?=str_replace('0','&Oslash;',strtoupper($d[case_no]))?>"><? if($card=='mail'){echo "<img src='gfx/mail.logo.gif'>";}?></div>
	
	</td></tr></table>
	<?
}
?>

<?
$q="select packet_id, mail_status, uspsVerify from ps_packets where process_status = 'READY TO MAIL' AND mail_status <> 'Printed Awaiting Postage' order by packet_id";
$r=@mysql_query($q);
 while($d=mysql_fetch_array($r, MYSQL_ASSOC)){ $i++;
	if ($d[uspsVerify] != ''){
		getPacketData($d[packet_id]);
	}else{
		echo "<script>alert('Addresses have not been verified for packet $d[packet_id]!')</script>";
	}
 } 
$q="select eviction_id, mail_status, uspsVerify from evictionPackets where process_status = 'READY TO MAIL' AND mail_status <> 'Printed Awaiting Postage' order by eviction_id";
$r=@mysql_query($q);
 while($d=mysql_fetch_array($r, MYSQL_ASSOC)){ $i++;
	if ($d[uspsVerify] != ''){
		printSetEV($d[eviction_id],1);
	}else{
		echo "<script>alert('Addresses have not been verified for eviction $d[eviction_id]!')</script>";
	}
} ?>
<script>document.title='<?=$_SESSION[inc]?> Lables';</script>
