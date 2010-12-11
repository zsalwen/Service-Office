<?
mysql_connect();
mysql_select_db('core');
include 'common.php';
function id2state($id){
	$q="SELECT state FROM ps_users WHERE id='$id' LIMIT 0,1";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	return strtoupper($d[state]);
}
function oosList($packet){
	$list='';
	$q="SELECT server_id, server_ida, server_idb, server_idc, server_idd, server_ide, state1, state1a, state1b, state1c, state1d, state1e FROM ps_packets WHERE packet_id='$packet' LIMIT 0,1";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	if ($d[state1] != '' && strtoupper($d[state1]) != 'MD' && $d[server_id] != '' && id2state($d[server_id]) != 'MD'){
		$list .= "<li>".id2name($d[server_id]).' - '.strtoupper($d[state1])."</li>";
	}
	foreach(range('a','e') as $letter){
		if ($d["state1$letter"] != '' && strtoupper($d["state1$letter"]) != 'MD' && $d["server_id$letter"] != '' && id2state($d["server_id$letter"]) != 'MD'){
			$list .= "<li>".id2name($d["server_id$letter"]).' - '.strtoupper($d["state1$letter"])."</li>";
		}
	}
	if ($list != ''){
		$list = "<ol>$list</ol>";
	}
	return $list;
}
$today=date('m/d/y');
$limbo=time()-86400;
$limbo=date('Y-m-d H:i:s',$limbo);
<<<<<<< HEAD
echo "<style>ol {display:inline;}</style>";
echo "<table align='center' border='1' style='border-collapse:collapse;'><tr><td align='center' colspan='3' style='font-size:16px;'>OUT OF STATE SERVER CALL LIST</td></tr><td>Packet #</td><td>Dispatch Date</td><td>Servers to Call</td></tr>";
$q="SELECT * from ps_packets WHERE (affidavit_status = 'SERVICE CONFIRMED' OR affidavit_status='ASSIGNED') and  filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND service_status <> 'MAIL ONLY' AND (state1a <> '' OR state1a <> 'md' OR state1a <> 'MD') AND (state1b <> '' OR state1b <> 'md' OR state1b <> 'MD') AND (state1c <> '' OR state1c <> 'md' OR state1c <> 'MD') AND (state1d <> '' OR state1d <> 'md' OR state1d <> 'MD') AND (state1e <> '' OR state1e <> 'md' OR state1e <> 'MD') AND processor_notes NOT LIKE '%$today%' AND dispatchDate <= '$limbo' ORDER BY packet_id ASC";
$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
$i=0;
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){$i++;
	echo "<tr bgcolor='".row_color($i,'#FFFFFF','#DDDDDD')."'><td>$d[packet_id]</td><td>$d[dispatchDate]</td><td>".oosList($d[packet_id])."</td></tr>";
=======
echo "<table align='center' border='1' style='border-collapse:collapse;'><tr><td align='center' colspan='3' style='font-size:16px;'>OUT OF STATE SERVER CALL LIST</td></tr><td>Packet #</td><td>Dispatch Date</td><td>Servers to Call</td></tr>";
$q="SELECT * from ps_packets WHERE (affidavit_status = 'SERVICE CONFIRMED' OR affidavit_status='ASSIGNED') and  filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND service_status <> 'MAIL ONLY' AND (state1a <> '' OR state1a <> 'md' OR state1a <> 'MD') AND (state1b <> '' OR state1b <> 'md' OR state1b <> 'MD') AND (state1c <> '' OR state1c <> 'md' OR state1c <> 'MD') AND (state1d <> '' OR state1d <> 'md' OR state1d <> 'MD') AND (state1e <> '' OR state1e <> 'md' OR state1e <> 'MD') AND processor_notes NOT LIKE '%$today%' AND dispatchDate <= '$limbo' ORDER BY packet_id ASC";
$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	echo "<tr bgcolor='".row_color(2,'#FFFFFF','#DDDDDD')."'><td>$d[packet_id]</td><td>$d[dispatchDate]</td><td>".oosList($d[packet_id])."</td></tr>";
>>>>>>> d68e3676759550e5598c3215d39448e3574cc8af
}
echo "</table>";
?>