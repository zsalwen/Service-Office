<?
include 'common.php';
?>
<link rel="stylesheet" type="text/css" href="fire.css" />
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
	</tr>	
<?
mysql_connect();
mysql_select_db('service');
$r=@mysql_query("select * from rescanRequests where rescanID = '' order by packetID DESC");
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
	</tr>
<?
}
?><div><?=$i;?> requests.</div></table></center>