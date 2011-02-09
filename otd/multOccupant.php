<?
mysql_connect();
mysql_select_db('service');
include 'functions.php';
function includeOcc($packet,$mailerID){
	include "http://staff.mdwestserve.com/otd/occupant.php?packet=$packet&mailerID=$mailerID";
	include "http://staff.mdwestserve.com/otd/occAffidavit.php?packet=$packet";
}
function includeOccBypass($packet,$mailerID){
	include "http://staff.mdwestserve.com/otd/occupant.php?packet=$packet&bypass=1&mailerID=$mailerID";
	include "http://staff.mdwestserve.com/otd/occAffidavit.php?packet=$packet";
}
function occNotice($cn){
	$r=@mysql_query("select packet_id from occNotices where caseNo = '$cn'");
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	if($d[packet_id]){
		return $d[packet_id];
	}else{
		return 0;
	}
}
function getMailerID($packet){
	$r=@mysql_query("select mailerID from occNotices where packet_id='$packet' LIMIT 0,1");
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	return $d[mailerID];
}
function occDropDown(){
	$r=@mysql_query("select DISTINCT sendDate from occNotices order by sendDate DESC");
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		$list .= "<option>$d[sendDate]</option>";
	}
	return $list;
}
if (!$_GET[current] && !$_GET[packet]){?>
<table align="center" style="border-collapse:collapse;">
	<tr>
		<td><a href="?current=1">Print Current Notices</a></td>
		<form id="form">
		<td>View Notices Sent On: <select onchange="form.submit()" name="sendDate"><option><? if ($_GET[sendDate]){ echo $_GET[sendDate];}else{ echo "Select Date";}?></option><?=occDropDown()?></select></td>
		</form>
	</tr>
</table>
<? }
if ($_GET[current]){
	$i2=0;
	$list='';
	$q="select uspsVerify, caseVerify, qualityControl from ps_packets where (process_status = 'ASSIGNED' OR process_status = 'READY' OR process_status='SERVICE COMPLETED' OR process_status='READY TO MAIL') AND filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND filing_status <> 'SEND TO CLIENT' AND affidavit_status <> 'CANCELLED' AND (attorneys_id='3' OR attorneys_id='68' OR attorneys_id='7') AND (qualityControl='' OR uspsVerify='' OR caseVerify='') order by packet_id ASC";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	 while($d=mysql_fetch_array($r, MYSQL_ASSOC)){$i2++;
		if (occNotice($d[case_no]) == 0){
			if ($list=''){ $list = $d[packet_id]; }else{ $list .= ', '.$d[packet_id]; }
		}
	 }
	 if ($list != ''){
		if ($i2 > 1){
			$msg="PACKETS $list HAVE NOT HAD ALL DATA ENTRY VERIFIED.  NO NOTICES MAY BE PRINTED UNTIL THIS IS REMEDIED.";
		}else{
			$msg="PACKET $list HAS NOT HAD ALL DATA ENTRY VERIFIED.  NO NOTICES MAY BE PRINTED UNTIL THIS IS REMEDIED.";
		}
		echo "<script>alert('$msg')</script>";
	 }else{
		$q="select case_no, packet_id from ps_packets where (process_status = 'ASSIGNED' OR process_status = 'READY' OR process_status='SERVICE COMPLETED' OR process_status='READY TO MAIL') AND filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND filing_status <> 'SEND TO CLIENT' AND affidavit_status <> 'CANCELLED' AND (attorneys_id='3' OR attorneys_id='68' OR attorneys_id='7') order by packet_id ASC";
		$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
		while($d=mysql_fetch_array($r, MYSQL_ASSOC)){ $i++;
			if (occNotice($d[case_no]) == 0){
				includeOcc($d[packet_id],$_COOKIE[psdata][user_id]);
			}
		}
	}
 }elseif($_GET[packet]){
	$q="select case_no from ps_packets WHERE packet_id='$_GET[packet]' LIMIT 0,1";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	$occNotice=occNotice($d[case_no]);
	if ($occNotice == $_GET[packet]){
		includeOccBypass($_GET[packet],getMailerID($_GET[packet]));
	}elseif($occNotice == 0){
		echo "<center>NO OCCUPANT NOTICES HAVE BEEN GENERATED FOR THIS CLIENT FILE.<br><br>GO <a href='http://sysop.mdwestserve.com'>HERE</a> AND USE THE '#1 Print All OTD Envelopes' OPTION UNDER THE 'Mail Service' MENU TO PRINT IT CORRECTLY.</center>";
	}else{
		echo "<center>NO OCCUPANT NOTICES HAVE BEEN GENERATED FOR THIS PACKET.<br><br>HOWEVER, NOTICES WERE SENT FOR OTD$occNotice, WHICH HAS THE SAME FILE NUMBER OF $d[client_file]</center>";
	}
 }
 if ($_GET[sendDate]){
	echo "<table align='center' style='border-collapse:collapse;'><tr><td align='center' colspan='5' style='font-size:16px;font-weight:bold;'>VIEWING NOTICES SENT $_GET[sendDate]</td></tr>";
	$r=@mysql_query("SELECT * from occNotices where sendDate='$_GET[sendDate]'");
	while($d=mysql_fetch_array($r,MYSQL_ASSOC)){?>
		<tr>
			<td><?=$d[packet_id]?></td>
			<td><?=$d[client_file]?></td>
			<td><?=$d[case_no]?></td>
			<td><?=id2attorney($d[attorneysID])?></td>
			<td><?=$d[county]?></td>
			<td><?=id2name($d[mailerID])?></td>
			<td><a href='http://staff.mdwestserve.com/otd/multOccupant.php?packet=<?=$d[packet_id]?>'>Notice & Affidavits</a></td>
			<td><a href='http://staff.mdwestserve.com/otd/envelopeMaster.php?OTD=<?=$d[packet_id]?>'>Envelope</a></td>
		</tr>
<?	}
	echo "</table>";
 }
?>
<script>document.title='<?=$_SESSION[letters]?> Occupant Notices and Affidavits';</script>