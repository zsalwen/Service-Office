<?
include 'common.php';
hardLog('Eviction Occupant Envelope Printout','user');

$_SESSION[inc] = 0;
function printSet($packet){
	$_SESSION[inc] = $_SESSION[inc]+1;
	$r=@mysql_query("select address1, city1, state1, zip1, address2, city2, state2, zip2, address3, city3, state3, zip3, address4, city4, state4, zip4, address5, city5, state5, zip5, address6, city6, state6, zip6 from evictionPackets where eviction_id = '$packet'");
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	$card = $_GET[card];
	$name = "OCCUPANT";
	$line1 = $d["address1"];
	$csz = $d["city1"].', '.$d["state1"].' '.$d["zip1"];
	$cord = "EV$packet";

	?>
	<table style='page-break-after:always' align='center'><tr><td>
	<IMG SRC="http://staff.mdwestserve.com/barcode.php?barcode=<?=$cord?>&width=400&height=40"><br>
	<img  src="http://staff.mdwestserve.com/envelopecard.jpg.php?name=<?=strtoupper($name)?>&line1=<?=strtoupper(str_replace('#','no. ',$line1))?>&csz=<?=strtoupper($csz)?>">
	</td></tr></table>
	<?
}
function getEvictionData($packet){
	$q="select aaddress1, city1, state1, zip1, address2, city2, state2, zip2, address3, city3, state3, zip3, address4, city4, state4, zip4, address5, city5, state5, zip5, address6, city6, state6, zip6, name1, name2, name3, name4, name5, name6 from evictionPackets where eviction_id = '$packet'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);	
	printSet($packet);	
	return $data;
}

function occNotice($fn){
	$r=@mysql_query("select * from occNotices where clientFile = '$fn'");
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	if($d[sendDate]){
		return 1;
	}else{
		return 0;
	}
}

$q="select client_file, eviction_id, mail_status, uspsVerify from evictionPackets where (process_status = 'ASSIGNED' OR process_status = 'READY' OR process_status='SERVICE COMPLETED') AND filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND affidavit_status <> 'CANCELLED' AND (attorneys_id='1') order by eviction_id";
$r=@mysql_query($q);
 while($d=mysql_fetch_array($r, MYSQL_ASSOC)){ $i++;
	if (occNotice($d[client_file]) == 0){
		getEvictionData($d[eviction_id]);
	}
 }
?>
<script>document.title='<?=$_SESSION[letters]?> Occupant Envelopes';</script>
