<?
include 'common.php';
hardLog('Occupant Envelope Printout','user');

$_SESSION[inc] = 0;
function printSet($packet){
	$r=@mysql_query("select address1, address1a, address1b, address1c, address1d, address1e, city1, city1a, city1b, city1c, city1d, city1e, state1, state1a, state1b, state1c, state1d, state1e, zip1, zip1a, zip1b, zip1c, zip1d, zip1e, address2, address2a, address2b, address2c, address2d, address2e, city2, city2a, city2b, city2c, city2d, city2e, state2, state2a, state2b, state2c, state2d, state2e, zip2, zip2a, zip2b, zip2c, zip2d, zip2e, address3, address3a, address3b, address3c, address3d, address3e, city3, city3a, city3b, city3c, city3d, city3e, state3, state3a, state3b, state3c, state3d, state3e, zip3, zip3a, zip3b, zip3c, zip3d, zip3e, address4, address4a, address4b, address4c, address4d, address4e, city4, city4a, city4b, city4c, city4d, city4e, state4, state4a, state4b, state4c, state4d, state4e, zip4, zip4a, zip4b, zip4c, zip4d, zip4e, address5, address5a, address5b, address5c, address5d, address5e, city5, city5a, city5b, city5c, city5d, city5e, state5, state5a, state5b, state5c, state5d, state5e, zip5, zip5a, zip5b, zip5c, zip5d, zip5e, address6, address6a, address6b, address6c, address6d, address6e, city6, city6a, city6b, city6c, city6d, city6e, state6, state6a, state6b, state6c, state6d, state6e, zip6, zip6a, zip6b, zip6c, zip6d, zip6e, pobox, pobox2, pocity, pocity2, postate, postate2, pozip, pozip2 from ps_packets where packet_id = '$packet'");
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	$card = $_GET[card];
	$name = "OCCUPANT";
	$line1 = $d["address1"];
	$csz = $d["city1"].', '.$d["state1"].' '.$d["zip1"];
	$cord = "OTD$packet";

	?>
	<table style='page-break-after:always' align='center'><tr><td>
	<IMG SRC="http://staff.mdwestserve.com/barcode.php?barcode=<?=$cord?>&width=400&height=40"><br>
	<img  src="http://staff.mdwestserve.com/envelopecard.jpg.php?name=<?=strtoupper($name)?>&line1=<?=strtoupper(str_replace('#','no. ',$line1))?>&csz=<?=strtoupper($csz)?>">
	</td></tr></table>
	<?
}
function getPacketData($packet){
	$q="select address1, address1a, address1b, address1c, address1d, address1e, city1, city1a, city1b, city1c, city1d, city1e, state1, state1a, state1b, state1c, state1d, state1e, zip1, zip1a, zip1b, zip1c, zip1d, zip1e, address2, address2a, address2b, address2c, address2d, address2e, city2, city2a, city2b, city2c, city2d, city2e, state2, state2a, state2b, state2c, state2d, state2e, zip2, zip2a, zip2b, zip2c, zip2d, zip2e, address3, address3a, address3b, address3c, address3d, address3e, city3, city3a, city3b, city3c, city3d, city3e, state3, state3a, state3b, state3c, state3d, state3e, zip3, zip3a, zip3b, zip3c, zip3d, zip3e, address4, address4a, address4b, address4c, address4d, address4e, city4, city4a, city4b, city4c, city4d, city4e, state4, state4a, state4b, state4c, state4d, state4e, zip4, zip4a, zip4b, zip4c, zip4d, zip4e, address5, address5a, address5b, address5c, address5d, address5e, city5, city5a, city5b, city5c, city5d, city5e, state5, state5a, state5b, state5c, state5d, state5e, zip5, zip5a, zip5b, zip5c, zip5d, zip5e, address6, address6a, address6b, address6c, address6d, address6e, city6, city6a, city6b, city6c, city6d, city6e, state6, state6a, state6b, state6c, state6d, state6e, zip6, zip6a, zip6b, zip6c, zip6d, zip6e, pobox, pobox2, pocity, pocity2, postate, postate2, pozip, pozip2, name1, name2, name3, name4, name5, name6 from ps_packets where packet_id = '$packet'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);	
	printSet($packet);	
	return $data;
}

function occNotice($fn){
	$r=@mysql_query("select * from occNotices where clientFile = '$fn'");
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	if($d[sendDate] >= '2010-06-01'){
		return 1;
	}else{
		return 0;
	}
}

$q="select client_file, packet_id, mail_status, uspsVerify from ps_packets where date_received >= '2010-06-01' AND attorneys_id='3' order by packet_id";
$r=@mysql_query($q);
 while($d=mysql_fetch_array($r, MYSQL_ASSOC)){ $i++;
	if (occNotice($d[client_file]) != 0){
		getPacketData($d[packet_id]);
		$_SESSION[inc] = $_SESSION[inc]+1;
	}
 }
?>
<script>document.title='<?=$_SESSION[letters]?> Occupant Envelopes';</script>
