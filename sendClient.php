<?
mysql_connect();
mysql_select_db('core');
include 'functions.php';

function justDate($dateTime){
	$date=explode(' ',$dateTime);
	return $date[0];
}

function sentDropDown(){
	$q="select DISTINCT fileDate from ps_packets WHERE filing_status='SEND TO CLIENT'
		UNION
		select DISTINCT fileDate from evictionPackets WHERE filing_status='SEND TO CLIENT'
		order by fileDate DESC";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		$list .= "<option>$d[fileDate]</option>";
	}
	return $list;
}
?>
<style type="text/css">
    @media print {
      .noprint { display: none; }
    }
  </style> 
<table class='noprint' align="center" style="border-collapse:collapse;">
	<tr>
		<form id="form">
		<td>View Files Sent On: <select onchange="form.submit()" name="sendDate"><option><? if ($_GET[sendDate]){ echo $_GET[sendDate];}else{ echo "Select Date";}?></option><?=sentDropDown()?></select></td>
		</form>
	</tr>
</table>
<? 
$bgwTotal=0;
 if ($_GET[sendDate]){
	$date=$_GET[sendDate];
	echo "<table align='center' border='1' style='border-collapse: collapse; font-size: 13px;' width='700px'><tr><td align='center' colspan='8' style='font-size:16px;font-weight:bold;'>FILES SENT TO CLIENT ON $_GET[sendDate]</td></tr><tr><td>Packet ID</td><td>Client File</td><td>Attorney</td><td>County</td><td>Date Received</td><td>Process Status</td><td>Service Status</td><td>closeOut</td></tr>";
	$r=@mysql_query("select packet_id, client_file, attorneys_id, circuit_court, date_received, process_status, service_status, closeOut from ps_packets where filing_status = 'SEND TO CLIENT' and fileDate='".$_GET[sendDate]."' order by packet_id ASC");
	while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	if (($d[attorneys_id] == 70 || $d[attorneys_id] == 80) && $d[service_status] != 'MAIL ONLY'){$bgwTotal++;}
	?>
		<tr>
			<td><?=$d[packet_id]?></td>
			<td><?=$d[client_file]?></td>
			<td><?=id2attorney($d[attorneys_id])?></td>
			<td><?=$d[circuit_court]?></td>
			<td><?=justDate($d[date_received])?></td>
			<td><?=$d[process_status]?></td>
			<td><?=$d[service_status]?></td>
			<td><?=$d[closeOut]?></td>
		</tr>
<?	}
	$r=@mysql_query("select eviction_id, client_file, attorneys_id, circuit_court, date_received, process_status, service_status, closeOut from evictionPackets where filing_status = 'SEND TO CLIENT' and fileDate='".$_GET[sendDate]."' order by eviction_id ASC");
	while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	if ($d[attorneys_id] == 70 || $d[attorneys_id] == 80){$bgwTotal++;}
	?>
		<tr>
			<td>EV<?=$d[eviction_id]?></td>
			<td><?=$d[client_file]?></td>
			<td><?=id2attorney($d[attorneys_id])?></td>
			<td><?=$d[circuit_court]?></td>
			<td><?=justDate($d[date_received])?></td>
			<td><?=$d[process_status]?></td>
			<td><?=$d[service_status]?></td>
			<td><?=$d[closeOut]?></td>
		</tr>
<?	}
	if ($bgwTotal > 0){
		echo "<tr><td colspan='8' align='center' class='noprint' style='font-size:20px;'>CONFIRMATION EMAILS WILL BE SENT TO BGW AT 5:04 PM EST</td></tr></table>";
	}
 }
?>
<script>document.title='Files Sent to Client on <?=$date?>';</script>