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
$limbo=time();//-86400;
$limbo=date('Y-m-d H:i:s',$limbo);
?>
<style>
ol {display:inline;}
</style>
<script>
function hideshow(which){
	if (!document.getElementById)
	return
	if (which.style.display=="block")
	which.style.display="none"
	else
	which.style.display="block"
}
function ChangeText(field){
	if (document.getElementById(field).innerHTML == '+'){
		document.getElementById(field).innerHTML = '-';
	}else{
		document.getElementById(field).innerHTML = '+';
	}
}
</script>
<?
echo "<table align='center' border='1' style='border-collapse:collapse;'><tr><td align='center' colspan='4' style='font-size:16px;'>OUT OF STATE SERVER CALL LIST</td></tr>
<tr><td align='center'>Packet #</td><td align='center'>Dispatch Date</td><td align='center'>Servers to Call</td><td align='center'>Notes</td></tr>";
$q="SELECT * from ps_packets WHERE (affidavit_status = 'SERVICE CONFIRMED' OR affidavit_status='ASSIGNED') and  filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND service_status <> 'MAIL ONLY' AND ((state1a <> '' AND state1a AND 'md' OR state1a <> 'MD') OR (state1b <> '' AND state1b <> 'md' AND state1b <> 'MD') OR (state1c <> '' AND state1c <> 'md' AND state1c <> 'MD') OR (state1d <> '' AND state1d <> 'md' AND state1d <> 'MD') OR (state1e <> '' AND state1e <> 'md' AND state1e <> 'MD')) AND processor_notes NOT LIKE '%$today%' AND dispatchDate <= '$limbo' ORDER BY packet_id ASC";
$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
$i=0;
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){$i++;
	$oosList=oosList($d[packet_id]);
	//if ($oosList != ''){
		echo "
		<tr bgcolor='".row_color($i,'#FFFFFF','#DDDDDD')."'><td>$d[packet_id]</td><td>$d[dispatchDate]</td><td style='padding:left:20px;'>".$oosList."</td><td><a id='plus-$d[packet_id]' onClick=\"hideshow(document.getElementById('notes-$d[packet_id]')); ChangeText('plus-$d[packet_id]');\">[+]</a><div style='display:none;' id='notes-$d[packet_id]'><iframe height='300px' width='600px' frameborder='0' src='http://staff.mdwestserve.com/notes.php?packet=$d[packet_id]'></iframe></div></td></tr>";
	//}
}
echo "</table>";
?>