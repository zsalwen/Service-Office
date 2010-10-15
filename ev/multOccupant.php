<?
mysql_connect();
mysql_select_db('core');
include 'functions.php';
function includeOcc($packet,$mailerID){
	include "http://staff.mdwestserve.com/ev/evictionNotice.php?packet=$packet&mailerID=$mailerID";
	include "http://staff.mdwestserve.com/ev/noticeAffidavit.php?packet=$packet";
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
function occDropDown(){
	$r=@mysql_query("select DISTINCT sendDate from occNotices order by sendDate DESC");
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		$list .= "<option>$d[sendDate]</option>";
	}
	return $list;
}
if (!$_GET[current]){?>
<table align="center" style="border-collapse:collapse;">
	<tr>
		<td><a href="?current=1">Print Current Eviction Notices</a></td>
		<form id="form">
		<td>View Notices Sent On: <select onchange="form.submit()" name="sendDate"><option><? if ($_GET[sendDate]){ echo $_GET[sendDate];}else{ echo "Select Date";}?></option><?=occDropDown()?></select></td>
		</form>
	</tr>
</table>
<? }
if ($_GET[current]){
	$q="select client_file, eviction_id, mail_status, uspsVerify from evictionPackets where (process_status = 'ASSIGNED' OR process_status = 'READY' OR process_status='SERVICE COMPLETED') AND filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND affidavit_status <> 'CANCELLED' AND (attorneys_id='1') order by eviction_id";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	 while($d=mysql_fetch_array($r, MYSQL_ASSOC)){ $i++;
		if (occNotice($d[client_file]) == 0){
			includeOcc($d[eviction_id],$_COOKIE[psdata][user_id]);
		}
	 }
 }
 if ($_GET[sendDate]){
	echo "<table align='center' style='border-collapse:collapse;'><tr><td align='center' colspan='5' style='font-size:16px;font-weight:bold;'>VIEWING NOTICES SENT $_GET[sendDate]</td></tr>";
	$r=@mysql_query("SELECT * from occNotices where sendDate='$_GET[sendDate]'");
	while($d=mysql_fetch_array($r,MYSQL_ASSOC)){?>
		<tr>
			<td>EV<?=$d[eviction_id]?>,OTD<?=$d[packet_id]?></td>
			<td><?=$d[client_file]?></td>
			<td><?=id2attorney($d[attorneysID])?></td>
			<td><?=$d[county]?></td>
			<td><?=id2name($d[mailerID])?></td>
		</tr>
<?	}
	echo "</table>";
 }
?>
<script>document.title='<?=$_SESSION[letters]?> Occupant Notices and Affidavits';</script>