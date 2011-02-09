<?
mysql_connect();
mysql_select_db('service');
include 'common.php';
function id2state($id){
	$q="SELECT state FROM ps_users WHERE id='$id' LIMIT 0,1";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	return strtoupper($d[state]);
}
function oosList($packet){
	$list='';
	$i=0;
	$q="SELECT server_id, server_ida, server_idb, server_idc, server_idd, server_ide, state1, state1a, state1b, state1c, state1d, state1e, address1, address1a, address1b, address1c, address1d, address1e, city1, city1a, city1b, city1c, city1d, city1e, zip1, zip1a, zip1b, zip1c, zip1d, zip1e FROM ps_packets WHERE packet_id='$packet' LIMIT 0,1";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	if ($d[state1] != '' && strtoupper($d[state1]) != 'MD' && $d[server_id] != '' && id2state($d[server_id]) != 'MD' && $d[server_id] != 218){$i++;
		$list .= $i.". <a href='http://staff.mdwestserve.com/contractor_profile.php?admin=$d[server_id]' target='_blank'>".id2name($d[server_id])."</a>: <div style='font-size:10px; text-align:right !important; line-height:10px; border:solid 1px black;'>".strtoupper($d[address1])."<br>".strtoupper($d[city1]).", ".strtoupper($d[state1])." ".$d[zip1]."</div>";
	}
	foreach(range('a','e') as $letter){
		if ($d["state1$letter"] != '' && strtoupper($d["state1$letter"]) != 'MD' && $d["server_id$letter"] != '' && id2state($d["server_id$letter"]) != 'MD' && $d["server_id$letter"] != 218){$i++;
			$list .= $i.". <a href='http://staff.mdwestserve.com/contractor_profile.php?admin=".$d["server_id$letter"]."' target='_blank'>".id2name($d["server_id$letter"])."</a>: <div style='font-size:10px; text-align:right !important; line-height:10px; border:solid 1px black;'>".strtoupper($d["address1$letter"])."<br>".strtoupper($d["city1$letter"]).", ".strtoupper($d["state1$letter"])." ".$d["zip1$letter"]."</div>";
		}
	}
	return $list;
}
function justDate($dt){
	$date=explode(' ',$dt);
	return $date[0];
}
$today=date('m/d/y');
$limbo=time()-86400;
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
if ($_GET[all]){
	$needCall=" style='background-color:red;'";
	$all=" style='background-color:green; font-weight:bold;'";
}else{
	$needCall=" style='background-color:green; font-weight:bold;'";
	$all=" style='background-color:red;'";
}
echo "<table align='center' border='1' style='border-collapse:collapse;'><tr><td align='center' colspan='5' style='font-size:16px;'>OUT OF STATE SERVER CALL LIST</td><td align='center'><span$all><a href='?all=1'>ALL</a></span><span$needCall><a href='?needCall=1'>NEED CALL</a></span></td></tr>
<tr><td align='center'>Packet #</td><td align='center'>Dispatch Date</td><td align='center'>Service Status</td><td align='center'>Est. File Date</td><td align='center' style='padding-left:20px;'>Servers to Call</td><td align='center'>Notes</td></tr>";
if ($_GET[all]){
	$q="SELECT * from ps_packets WHERE (process_status = 'SERVICE CONFIRMED' OR process_status='ASSIGNED') and  filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND service_status <> 'MAIL ONLY' AND ((state1 <> '' AND state1 <> 'md' AND state1 <> 'MD') OR (state1a <> '' AND state1a <> 'md' AND state1a <> 'MD') OR (state1b <> '' AND state1b <> 'md' AND state1b <> 'MD') OR (state1c <> '' AND state1c <> 'md' AND state1c <> 'MD') OR (state1d <> '' AND state1d <> 'md' AND state1d <> 'MD') OR (state1e <> '' AND state1e <> 'md' AND state1e <> 'MD')) ORDER BY packet_id ASC";
}else{
	$q="SELECT * from ps_packets WHERE (process_status = 'SERVICE CONFIRMED' OR process_status='ASSIGNED') and  filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' AND service_status <> 'MAIL ONLY' AND ((state1 <> '' AND state1 <> 'md' AND state1 <> 'MD') OR (state1a <> '' AND state1a <> 'md' AND state1a <> 'MD') OR (state1b <> '' AND state1b <> 'md' AND state1b <> 'MD') OR (state1c <> '' AND state1c <> 'md' AND state1c <> 'MD') OR (state1d <> '' AND state1d <> 'md' AND state1d <> 'MD') OR (state1e <> '' AND state1e <> 'md' AND state1e <> 'MD')) AND processor_notes NOT LIKE '%$today%' AND dispatchDate <= '$limbo' ORDER BY packet_id ASC";
}
$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
$i=0;
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$oosList=oosList($d[packet_id]);
	if ($oosList != ''){$i++;
		if ($d[reopenDate] >= justDate($d[dispatchDate])){
			$dispatchDate=$d[reopenDate]."-REOPENED";
		}else{
			$dispatchDate=$d[dispatchDate];
		}
		if ($d[process_status] == 'ASSIGNED'){
			$status='ACTIVE';
			$color='green';
		}else{
			$status='BLACKHOLE';
			$color='yellow';
		}
		echo "
		<tr bgcolor='".row_color($i,'#FFFFFF','#DDDDDD')."'><td valign='top'><a href='order.php?packet=$d[packet_id]' target='_blank'>$d[packet_id]</a></td><td valign='top'>$dispatchDate</td><td valign='top' style='background-color:$color'>$status</td><td valign='top'>$d[estFileDate]</td><td style='padding-left:20px;' valign='top'>".$oosList."</td><td width='600px' valign='top'><span style='background-color:orange; display:inline;' onClick=\"hideshow(document.getElementById('notes-$d[packet_id]')); ChangeText('plus-$d[packet_id]');\">[<a id='plus-$d[packet_id]'>+</a>]</span><div style='display:none;' id='notes-$d[packet_id]'><iframe height='300px' width='600px' frameborder='0' src='http://staff.mdwestserve.com/notes.php?packet=$d[packet_id]'></iframe></div></td></tr>";
	}
}
echo "</table>";
?>