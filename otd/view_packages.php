<? include 'common.php';
include 'menu.php'; 

function inPackage($id){
	$r=@mysql_query("SELECT name1, name2, name3, name4, name5, name6 from ps_packets where package_id = '$id' AND process_status <> 'CANCELLED'");
	$files=0;
	$total=0;
	while ($d=mysql_fetch_array($r, MYSQL_ASSOC)){
		$files++;
		
		if ($d[name1]){ $total++; }
		if ($d[name2]){ $total++; }
		if ($d[name3]){ $total++; }
		if ($d[name4]){ $total++; }
		if ($d[name5]){ $total++; }
		if ($d[name6]){ $total++; }
	}
	return $files.'F / '.$total.'D';
}

function packageContents($id){
	$table = "<table border='1' width='100%'>";
	$q="select packet_id, address1, address1a, address1b, address1c, address1d, address1e, client_file, case_no, server_id, server_ida, server_idb, process_status, circuit_court from ps_packets where package_id = '$id'";
	$r=@mysql_query($q);
	while ($d=mysql_fetch_array($r, MYSQL_ASSOC)){
		$packet=$d[packet_id];
		$table .= "<tr class='pdd' onclick='window.location=\"order.php?packet=$packet\"'><td><strong>$packet</strong> - $d[address1]";
		if ($d[address1a]){
			$table .= ", $d[address1a]";
		}
		if ($d[address1b]){
			$table .= ", $d[address1b]";
		}
		if ($d[address1c]){
			$table .= ", $d[address1c]";
		}
		if ($d[address1d]){
			$table .= ", $d[address1d]";
		}
		if ($d[address1e]){
			$table .= ", $d[address1e]";
		}
		$table .="</td><td>$d[client_file]</td><td>$d[case_no]</td><td>".id2name($d[server_id])."</td><td>".id2name($d[server_ida])."</td><td>".id2name($d[server_idb])."</td><td>$d[process_status]</td><td>$d[circuit_court]</td></tr>";
	}
	$table .= "</table>";
	return $table;
}?>
<style>
tr.pdd:hover{ background-color:#CCFFCC; color:#0000FF; cursor:pointer;}
</style>
<table bgcolor="#FFFFFF" width="100%" border="1">
	<tr>
    	<td colspan="8" bgcolor="#000066" align="center"><font color="#CCCCCC"><b>Existing Packages</b></font></td>
    </tr>
    <tr bgcolor="#ccccff">
    	<td align="center">ID</td>
        <td align="center">Name</td>
        <td align="center">Volume</td>
        <td align="center">Contractor Rate</td>
        <td align="center">Client Rate</td>
        <td align="center">Package Set Date</td>
    </tr>
<?
$q="SELECT id, name, set_date from ps_packages where id != '' order by set_date DESC";
$r=mysql_query($q) or die("Query: $q".mysql_error());
$i=0;
while ($d=mysql_fetch_array($r, MYSQL_ASSOC)){$i++;
$q1="SELECT ps_pay.contractor_rate, ps_pay.client_rate from ps_packets, ps_pay where ps_packets.package_id = '$d[id]' AND ps_packets.packet_id=ps_pay.packetID AND ps_pay.product='OTD'";
$r1=mysql_query($q1) or die("Query: $q1".mysql_error());
$d1=mysql_fetch_array($r1, MYSQL_ASSOC);
?>
	<tr title="click to expand package <?=$d[name]?>" onClick="hideshow(document.getElementById('note<?=$d[id]?>'))" bgcolor="<?=row_color($i,'#ccffff','#ccccff')?>">
    	<td align="center"><?=$d[id]?></td>
        <td align="center"><?=$d[name]?></td>
        <td align="center"><?=inPackage($d[id])?></td>
        <? if ($d1[contractor_rate]){ ?>
        <td align="center"><?=$d1[contractor_rate]?></td>
        <? } else { ?>
        <td align="center"><i>Contractor rate not set</i></td>
        <? } ?>
        <? if ($d1[client_rate]){ ?>
        <td align="center"><?=$d1[client_rate]?></td>
        <? } else { ?>
        <td align="center"><i>Client rate not set</i></td>
        <? } ?>
        <td align="center"><?=$d[set_date]?></td>
    <tr>
    	<td colspan="8"><div style="display:none;" id="note<?=$d[id]?>"> <?=packageContents($d[id])?> </div></td>
    </tr>
<?}
if ($i == 0) {?>
	<tr>
		<td align="center" colspan="8" style="font-size:20px"><font color="#FF0000"><b>No packages to display</b></font></td>
    </tr>
<? }?>      
</table>
<? include 'footer.php'; ?>