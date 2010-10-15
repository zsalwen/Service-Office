<? 
session_start();
ob_start();
@mysql_connect () or die(mysql_error());
include 'security.php';
include 'functions.php';
hardLog('Auction Match Report','user');

$i=0;
function auctionMatch($client_file){
$today = date('Y-m-d');
$q="select sale_date from schedule_items where file='$client_file' and sale_date > '$today' and item_status = 'ON SCHEDULE'";
$r=@mysql_query($q);
$d=mysql_fetch_array($r,MYSQL_ASSOC);
return $d[sale_date];
}
mysql_select_db ('core');

if ($_GET[all]){
$q="SELECT client_file, filing_status, server_id, server_ida, server_idb, server_idc, server_idd, server_ide, process_status, affidavit_status, filing_status, circuit_court, packet_id from ps_packets where 
									client_file <> '' and 
									process_status <> 'DUPLICATE' and 
									process_status <> 'DUPLICATE/DIFF-PDF' and 
									process_status <> 'DAMAGED PDF' and 
									process_status <> 'FILE COPY' and
									service_status <> 'CANCELLED' and
									filing_status <> 'CANCELLED' and
									filing_status <> 'DO NOT FILE' 
										order by packet_id DESC";
$q2="SELECT client_file, filing_status, server_id, server_ida, server_idb, server_idc, server_idd, server_ide, process_status, affidavit_status, filing_status, circuit_court, eviction_id from evictionPackets where 
									client_file <> '' and 
									process_status <> 'DUPLICATE' and 
									process_status <> 'DUPLICATE/DIFF-PDF' and 
									process_status <> 'DAMAGED PDF' and 
									process_status <> 'FILE COPY' and
									service_status <> 'CANCELLED' and
									filing_status <> 'CANCELLED' and
									filing_status <> 'DO NOT FILE' 
										order by eviction_id DESC";
}else{
$q="SELECT client_file, filing_status, server_id, server_ida, server_idb, server_idc, server_idd, server_ide, process_status, affidavit_status, filing_status, circuit_court, packet_id from ps_packets where 
									client_file <> '' and 
									process_status <> 'DUPLICATE' and 
									process_status <> 'DUPLICATE/DIFF-PDF' and 
									process_status <> 'DAMAGED PDF' and 
									process_status <> 'FILE COPY' and
									service_status <> 'CANCELLED' and
									filing_status <> 'CANCELLED' and
									filing_status <> 'FILED WITH COURT' and
									filing_status <> 'FILED BY CLIENT' and
 									filing_status <> 'FILED WITH COURT - FBS' and
									filing_status <> 'DO NOT FILE' 
										order by packet_id DESC";
$q2="SELECT client_file, filing_status, server_id, server_ida, server_idb, server_idc, server_idd, server_ide, process_status, affidavit_status, filing_status, circuit_court, eviction_id from evictionPackets where 
									client_file <> '' and 
									process_status <> 'DUPLICATE' and 
									process_status <> 'DUPLICATE/DIFF-PDF' and 
									process_status <> 'DAMAGED PDF' and 
									process_status <> 'FILE COPY' and
									service_status <> 'CANCELLED' and
									filing_status <> 'CANCELLED' and
									filing_status <> 'FILED WITH COURT' and
									filing_status <> 'FILED BY CLIENT' and
 									filing_status <> 'FILED WITH COURT - FBS' and
									filing_status <> 'DO NOT FILE' 
										order by eviction_id DESC";
}
$r=@mysql_query($q) or die(mysql_error());
$r2=@mysql_query($q2) or die(mysql_error());
echo "<style>table { border-collapse:collapse; font-size:12px; }</style><table border='1' width='100%'>
			<tr>
				<td>Service Status</td>
				<td>Affidavit Status</td>
				<td>Filing Status</td>
				<td>Court</td>
				<td align='center'><strong>Auction Date</strong></td>
				<td>Server</td>
				<td>Client</td>
				<td>Service</td>
			</tr>	
				";
$i=0;
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){$i++;
	if($d[client_file]){
		if ($d[filing_status] == "FILED WITH COURT" || $d[filing_status] == "FILED WITH COURT - FBS"){
			$color='#99ff99';
		}elseif($d[filing_status] == "READY TO SIGN"){
			$color='#ffff99';
		}elseif($d[filing_status] == "PREP TO FILE"){
			$color='#FFFF99';
		}else{
			$color='#FF9999';
		}
		$servers='';
		if ($d[server_ida]){
			$servers=', '.id2name($d[server_ida]);
		}
		if ($d[server_idb]){
			$servers.=', '.id2name($d[server_idb]);
		}
		if ($d[server_idc]){
			$servers.=', '.id2name($d[server_idc]);
		}
		if ($d[server_idd]){
			$servers.=', '.id2name($d[server_idd]);
		}
		if ($d[server_ide]){
			$servers.=', '.id2name($d[server_ide]);
		}
		$ac["$i"] = "<tr bgcolor='$color'>
			<td>$d[process_status]</td>
			<td>$d[affidavit_status]</td>
			<td>$d[filing_status]</td>
			<td>$d[circuit_court]</td>
			<td bgcolor='FFFFFF' align='center'>[X]</td>
			<td>".id2name($d[server_id]).$servers."</td>
			<td>$d[client_file]</td>
			<td><a href='http://staff.mdwestserve.com/otd/order.php?packet=$d[packet_id]' target='_Blank'>OTD$d[packet_id]</a></td>
			</tr>";
		$ad["$i"] = $d[client_file];
	}
}
while ($d2=mysql_fetch_array($r2,MYSQL_ASSOC)){$i++;
	if($d2[client_file]){
		if ($d2[filing_status] == "FILED WITH COURT" || $d2[filing_status] == "FILED WITH COURT - FBS"){
			$color='#99ff99';
		}elseif($d2[filing_status] == "READY TO SIGN"){
			$color='#ffff99';
		}elseif($d2[filing_status] == "PREP TO FILE"){
			$color='#FFFF99';
		}else{
			$color='#FF9999';
		}
		$servers='';
		if ($d2[server_ida]){
			$servers=', '.id2name($d2[server_ida]);
		}
		if ($d2[server_idb]){
			$servers.=', '.id2name($d2[server_idb]);
		}
		if ($d2[server_idc]){
			$servers.=', '.id2name($d2[server_idc]);
		}
		if ($d2[server_idd]){
			$servers.=', '.id2name($d2[server_idd]);
		}
		if ($d2[server_ide]){
			$servers.=', '.id2name($d2[server_ide]);
		}
		$ac["$i"] = "<tr bgcolor='$color'>
			<td>$d2[process_status]</td>
			<td>$d2[affidavit_status]</td>
			<td>$d2[filing_status]</td>
			<td>$d2[circuit_court]</td>
			<td bgcolor='FFFFFF' align='center'>[X]</td>
			<td>".id2name($d2[server_id]).$servers."</td>
			<td>$d2[client_file]</td>
			<td><a href='http://staff.mdwestserve.com/ev/order.php?packet=$d2[eviction_id]' target='_Blank'>EV$d2[eviction_id]</a></td>
			</tr>";
		$ad["$i"] = $d2[client_file];
	}
}
$contents=ob_get_clean();
$i=0;
$i2=0;
@mysql_connect ('hwa1.hwestauctions.com','','') or die(mysql_error());
mysql_select_db ('intranet');
$count=count($ac);
while ($i2 < $count){$i2++;
	$client_file=auctionMatch($ad["$i2"]);
	if ($client_file != ''){$i++;
		$contents .= str_replace('[X]',$client_file,$ac["$i2"]);
	}
}
mysql_select_db ('core');
echo $contents;
?>
</table>
<script>document.title='<?=$i?> Auctions';</script>
<meta http-equiv="refresh" content="120" />

