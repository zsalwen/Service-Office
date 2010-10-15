<?
include 'common.php';
function explodeDate($date){
	$date=explode(' ',$date);
	return $date[0];
}
if (!$_GET[mail]){ ?>
<table align="center">
	<tr>
		<td valign='top'><a href="?mail=1">Organize List For<br>Mailing to Client</a></td>
		<td>
<? } ?>
		<table align="center" style="border-collapse:collapse;" border="1">
		<tr>
				<td>packetID</td>
				<td>Client #</td>
				<td>County</td>
				<td>Date Received</td>
				<td>Status</td>
			</tr>
		<?
		$q="SELECT * FROM ps_packets WHERE filing_status='AWAITING CASE NUMBER'";
		$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
		while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
			$date=explodeDate($d[date_received]);
			echo "<tr>
				<td>";
			if (!$_GET[mail]){
				echo "<a href='http://staff.mdwestserve.com/otd/order.php?packet=$d[packet_id]' target='_blank'>";
			}
			echo "OTD$d[packet_id]";
			if (!$_GET[mail]){
				echo "</a>";
			}
			echo "</td>
				<td>$d[client_file]</td>
				<td>$d[circuit_court]</td>
				<td>$date</td>
				<td>$d[service_status]</td>
			</tr>";
		}
if (!$_GET[mail]){
echo "</td></tr></table>";
} ?>
</table>