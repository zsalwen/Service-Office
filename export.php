<?
include 'common.php';
?>
<center>

<table border="1" cellspacing="0" cellpadding="2">
	<tr>
		<td>Request Number</td>
		<td>Packet ID</td>
		<td>Request By</td>
		<td>Approved By</td>
		<td>'Date Received'</td>
		<td>'Service Status'</td>
		<td>'Client Status'</td>
		<td>'Process Status'</td>
		<td>'Affidavit Status'</td>
		<td>'Name 1'</td>
		<td>'Timeline'</td>
	</tr>	
<?
mysql_connect();
mysql_select_db('core');
$r=@mysql_query("select * from exportRequests where exportDate = '0000-00-00 00:00:00' and byID <> '".$_COOKIE[psdata][user_id]."' order by requestID DESC");
$i=0;
while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
$i++;
$r2=@mysql_query("select * from ps_packets where packet_id='$d[packetID]'");
$d2=mysql_fetch_array($r2,MYSQL_ASSOC);?>
	<tr>
		<td><?=$d[requestID];?></td>
		<td><a target="_Blank" href="/otd/order.php?packet=<?=$d[packetID];?>"><?=$d[packetID];?></a></td>
		<td><?=id2name($d[byID]);?></td>
		<td><?=id2name($d[confirmID]);?></td>
		<td><?=$d2[date_received];?></td>
		<td><?=$d2[service_status];?></td>
		<td><?=$d2[status];?></td>
		<td><?=$d2[process_status];?></td>
		<td><?=$d2[affidavit_status];?></td>
		<td><?=$d2[name1];?></td>
		<td><?=$d2[timeline];?></td>
	</tr>
<?
}
$r=@mysql_query("select * from EVexportRequests where exportDate = '0000-00-00 00:00:00' and byID <> '".$_COOKIE[psdata][user_id]."' order by requestID DESC");
while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
$i++;
$r2=@mysql_query("select * from evictionPackets where eviction_id='$d[evictionID]'");
$d2=mysql_fetch_array($r2,MYSQL_ASSOC);?>
	<tr>
		<td><?=$d[requestID];?></td>
		<td><a target="_Blank" href="/ev/order.php?packet=<?=$d[evictionID];?>"><?=$d[evictionID];?></a></td>
		<td><?=id2name($d[byID]);?></td>
		<td><?=id2name($d[confirmID]);?></td>
		<td><?=$d2[date_received];?></td>
		<td><?=$d2[service_status];?></td>
		<td><?=$d2[status];?></td>
		<td><?=$d2[process_status];?></td>
		<td><?=$d2[affidavit_status];?></td>
		<td><?=$d2[name1];?></td>
		<td><?=$d2[timeline];?></td>
	</tr>
<?
}	
?><div>You can approve <?=$i;?> requests.</div></table><div><a href="http://data.mdwestserve.com/cron/checkExport.php" target="_BLANK">Push exports / Clear approved</a></div></center>