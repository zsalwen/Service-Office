<?
include 'common.php';
hardLog('Quality Control Report','user');
opLog($_COOKIE[psdata][name]." Loaded Quality Control");
function photoCount($packet){
	$count=trim(getPage("http://data.mdwestserve.com/countPhotos.php?packet=$packet", 'MDWS Count Photos', '5', ''));
	if ($count==''){
		$count=0;
	}
	$count .= " PHOTO";
	if($photoCount != 1){
		$count .= "S";
	}
	return $count;
}
if ($_GET[mailDate]){
	$mailDate=$_GET[mailDate];
}else{
	$mailDate=date('Y-m-d');
}
$i=0;
?>
<style>
a { color:#000000; text-decoration:none }
td { color:#000000 }
.M2 { background-color:#FF6633; color:#666666}
.I { background-color:#FF66CC; color:#FFFFFF}
.P { background-color:#00CC00; color:#FFFFFF}
.M { background-color:#CCCC00; color:#FFFFFF}
.M23 { background-color:#FF4477; color:#666666}
.I3 { background-color:#6688CC; color:#CCCCCC}
.P3 { background-color:#99FF00; color:#CCCCCC}
.M3 { background-color:#CCCC00; color:#CCCCCC}
.R { background-color: #FF0000; }
td { border-bottom:solid 1px; border-right:dotted 1px; font-size:12px;}
</style>
<table><tr><td>
<form><div align="center">Update Affidavits for the date: <? if ($_GET[mailDate]){ echo $_GET[mailDate]; }else{ echo date('Y-m-d');}?><br /><input name="mailDate" size=10 value="<? if ($_GET[mailDate]){ echo $_GET[mailDate]; }else{ echo date('Y-m-d');}?>" /> <input type="submit" value="Set" /></div></form>
</td></tr><tr><td valign="top">
<table width="100%" style="border-collapse:collapse;" border="1">
    <tr bgcolor="#FFCC66">
    	<td>Received</td>
    	<td>Wizard Link</td>
        <td>Order Link</td>
        <td>Primary Server</td>
		<td>Client</td>
        <td>Service Status</td>
        <td>Affidavit Status</td>
		<td>Photo Count</td>
        <td>Op. Notes</td>
        <td>Ext. Notes</td>
        </tr>
<?
$qc="SELECT DISTINCT ps_packets.packet_id, ps_packets.attorneys_id, ps_packets.service_status, ps_packets.affidavit_status, ps_packets.filing_status, ps_packets.server_id, ps_packets.server_ida, ps_packets.server_idb, ps_packets.server_idc, ps_packets.server_idd, ps_packets.server_ide, ps_packets.processor_notes, ps_packets.extended_notes, ps_packets.date_received, ps_packets.client_file, ps_packets.rush, ps_packets.avoidDOT, ps_packets.priority, ps_pay.bill410, ps_packets.circuit_court FROM ps_packets, ps_pay WHERE ps_packets.process_status = 'ASSIGNED' AND (ps_packets.request_close = 'YES' OR ps_packets.request_closea = 'YES' OR ps_packets.request_closeb = 'YES' OR ps_packets.request_closec = 'YES' OR ps_packets.request_closed = 'YES' OR ps_packets.request_closee = 'YES') AND ps_packets.packet_id=ps_pay.packetID AND ps_pay.product='OTD' order by date_received";
$rc=@mysql_query($qc) or die(mysql_error());
while ($dc=mysql_fetch_array($rc, MYSQL_ASSOC)){ $i++;?>
	
    <tr <? if (($dc[attorneys_id] == '1') && ($dc[circuit_court] == "ANNE ARUNDEL") && ($dc[service_status] == "IN PROGRESS" || $dc[service_status] == "MAILING AND POSTING")){ echo "class=R"; }else{ echo "class='".substr($dc[service_status],0,1)."'";} ?>>
    	<td nowrap="nowrap"><?=$dc[date_received]?><br><?=$dc[client_file]?></td>
		<? if ($dc[bill410] != ''){ ?>
		<td><a class="x<?=$dc[attorneys_id]?>" href="http://service.mdwestserve.com/wizard.php?jump=<?=$dc[packet_id]?>-1&mailDate=<?=$mailDate?>" target="_blank">Load OTD<?=$dc[packet_id]?></a></td>
		<? }else{ ?>
        <td><a class="x<?=$dc[attorneys_id]?>" href="http://staff.mdwestserve.com/otd/minips_pay.php?id=<?=$dc[packet_id]?>&qc=<?=$mailDate?>" target="_blank">Load OTD<?=$dc[packet_id]?></a></td>
		<? } ?>
        <td><a href="http://staff.mdwestserve.com/otd/order.php?packet=<?=$dc[packet_id]?>" target="_blank">Load Order</a></td>
        <td><?=id2name($dc[server_id])?> <?=id2name($dc[server_ida])?> <?=id2name($dc[server_idb])?> <?=id2name($dc[server_idc])?> <?=id2name($dc[server_idd])?> <?=id2name($dc[server_ide])?></td>
		<td <? if ($dc[rush] == 'checked'){echo "bgcolor='#FF0000'";}elseif($dc[priority] == 'checked'){echo "bgcolor='#00FFFF'";} ?>><?=id2attorney($dc[attorneys_id])?><? if ($dc[rush] == 'checked'){echo "<br><b style='background-color:red;'>RUSH</b>";} ?><? if ($dc[avoidDOT] == 'checked'){ echo "<br><b style='background-color:red;'>!AvoidDOT!</b>";} ?><? if ($dc[priority] == 'checked'){echo "<br><b style='background-color:red;'>PRIORITY</b>";} ?></td>
        <td><?=$dc[service_status]?></td>
        <td><?=$dc[affidavit_status]?></td>
		<td align="center"><?=photoCount($dc[packet_id])?></td>
        <td><?=stripslashes($dc[processor_notes])?></td>
        <td><?=stripslashes($dc[extended_notes])?></td>
</tr>
<? } 
//pull evictions
$qc="SELECT DISTINCT evictionPackets.eviction_id, evictionPackets.attorneys_id, evictionPackets.service_status, evictionPackets.affidavit_status, evictionPackets.filing_status, evictionPackets.server_id, evictionPackets.processor_notes, evictionPackets.extended_notes, evictionPackets.date_received, evictionPackets.client_file, evictionPackets.rush, evictionPackets.priority, ps_pay.bill410 FROM evictionPackets, ps_pay WHERE evictionPackets.process_status = 'ASSIGNED' AND evictionPackets.request_close = 'YES' AND evictionPackets.eviction_id=ps_pay.packetID AND ps_pay.product='EV' order by date_received";
$rc=@mysql_query($qc) or die(mysql_error());

while ($dc=mysql_fetch_array($rc, MYSQL_ASSOC)){ $i++;?>
	
    <tr class="<?=substr($dc[service_status],0,1)?>3">
    	<td nowrap="nowrap"><?=$dc[date_received]?><br><?=$dc[client_file]?></td>
		<? if ($dc[bill410] != ''){ ?>
		<td><a class="x<?=$dc[attorneys_id]?>" href="http://service.mdwestserve.com/ev_wizard.php?jump=<?=$dc[eviction_id]?>-1&mailDate=<?=$mailDate?>" target="_blank">Load EV<?=$dc[eviction_id]?></a></td>
		<? }else{ ?>
        <td><a class="x<?=$dc[attorneys_id]?>" href="http://staff.mdwestserve.com/ev/accounting.php?id=<?=$dc[eviction_id]?>&qc=<?=$mailDate?>" target="_blank">Load EV<?=$dc[eviction_id]?></a></td>
		<? } ?>
        <td><a href="http://staff.mdwestserve.com/ev/order.php?packet=<?=$dc[eviction_id]?>" target="_blank">Load Order</a></td>
        <td><?=id2name($dc[server_id])?> <?=id2name($dc[server_ida])?> <?=id2name($dc[server_idb])?> <?=id2name($dc[server_idc])?> <?=id2name($dc[server_idd])?> <?=id2name($dc[server_ide])?></td>
		<td <? if ($dc[rush] == 'checked'){echo "bgcolor='#FF0000'";}elseif($dc[priority] == 'checked'){echo "bgcolor='#00FFFF'";} ?>><?=id2attorney($dc[attorneys_id])?><? if ($dc[rush] == 'checked'){echo "<br><b>RUSH</b>";} ?><? if ($dc[priority] == 'checked'){echo "<br><b>PRIORITY</b>";} ?></td>
        <td><?=$dc[service_status]?></td>
        <td><?=$dc[affidavit_status]?></td>
		<td><?=photoCount("EV".$dc[eviction_id])?></td>
        <td><?=stripslashes($dc[processor_notes])?></td>
        <td><?=stripslashes($dc[extended_notes])?></td>
</tr>
<? } ?>
<h1 align="center">There are <?=$i?> Cases to Review for Close - <a href="http://staff.mdwestserve.com/multChecklist.php?autoPrint=1" target="_blank">Print All Checklists</a></h1>
<? $final1 = $i;?>
</table>

</td></tr>
</td></tr></table>
<script>document.title='Quality Control: <?=$final1?> Close';</script>
<?
//include 'footer.php';
?>
<meta http-equiv="refresh" content="60" />
<? 
//include 'footer.php';
mysql_close();
?>