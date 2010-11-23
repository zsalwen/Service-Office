<?
include 'common.php';
hardLog('Intake Report','user');
$real=0;

function webservice($clientFile){
$select_query = "Select create_id From defendants  Where filenumber = '$clientFile'";
$result = mysql_query($select_query);
$data = mysql_fetch_array($result,MYSQL_ASSOC);
if ($data[create_id]) {
	return ' Webservice Data Present';
}
}

	
function isExport($id){
	$r = mysql_query("SELECT packetID FROM exportRequests WHERE packetID = '$id'");
	return mysql_num_rows($r);
}
function vol($court){
	$r = mysql_query("SELECT packet_id FROM ps_packets WHERE circuit_court = '$court' AND process_status='READY' and server_id < '1'");
	return mysql_num_rows($r);
}

function psStatus($status){
	$r = mysql_query("SELECT packet_id FROM ps_packets WHERE status = '$status' and process_status <> 'CANCELLED' AND process_status <> 'DUPLICATE' AND  process_status <> 'DAMAGED PDF'");
	return mysql_num_rows($r);
}

function def($court){
	$r = mysql_query("SELECT name1, name2, name3, name4, name5, name6 FROM ps_packets WHERE circuit_court = '$court' AND process_status='READY' and server_id < '1'");
	$total=0;
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	if ($d['name1']){	$total++; }
	if ($d['name2']){	$total++; }
	if ($d['name3']){	$total++; }
	if ($d['name4']){	$total++; }
	if ($d['name5']){	$total++; }
	if ($d['name6']){	$total++; }
	}
	return $total;
}

function add($court){
	$r = mysql_query("SELECT address1, address1a, address1b, address1c, address1d, address1e FROM ps_packets WHERE circuit_court = '$court' AND process_status='READY' and server_id < '1'");
	$total=0;
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	if ($d['address1']){	$total++; }
	if ($d['address1a']){	$total++; }
	if ($d['address1b']){	$total++; }
	if ($d['address1c']){	$total++; }
	if ($d['address1d']){	$total++; }
	if ($d['address1e']){	$total++; }
	}
	return $total;
}

function vol2($court){
	$r = mysql_query("SELECT packet_id FROM ps_packets WHERE circuit_court = '$court' AND process_status='READY' AND caseLookupFlag <> '' AND case_no='' and server_id < '1'");
	return mysql_num_rows($r);
}

function def2($court){
	$r = mysql_query("SELECT name1, name2, name3, name4, name5, name6 FROM ps_packets WHERE circuit_court = '$court' AND process_status='READY' AND caseLookupFlag <> '' AND case_no='' and server_id < '1'");
	$total=0;
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	if ($d['name1']){	$total++; }
	if ($d['name2']){	$total++; }
	if ($d['name3']){	$total++; }
	if ($d['name4']){	$total++; }
	if ($d['name5']){	$total++; }
	if ($d['name6']){	$total++; }
	}
	return $total;
}

function add2($court){
	$r = mysql_query("SELECT address1, address1a, address1b, address1c, address1d, address1e FROM ps_packets WHERE circuit_court = '$court' AND process_status='READY' AND caseLookupFlag <> '1' AND case_no='' and server_id < '1'");
	$total=0;
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	if ($d['address1']){	$total++; }
	if ($d['address1a']){	$total++; }
	if ($d['address1b']){	$total++; }
	if ($d['address1c']){	$total++; }
	if ($d['address1d']){	$total++; }
	if ($d['address1e']){	$total++; }
	}
	return $total;
}

function vol3($court){
	$r = mysql_query("SELECT packet_id FROM ps_packets WHERE circuit_court = '$court' AND process_status='READY' AND caseLookupFlag = '1' AND case_no='' and server_id < '1'");
	return mysql_num_rows($r);
}

function def3($court){
	$r = mysql_query("SELECT name1, name2, name3, name4, name5, name6 FROM ps_packets WHERE circuit_court = '$court' AND process_status='READY' AND caseLookupFlag = '1' AND  case_no='' and server_id < '1'");
	$total=0;
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	if ($d['name1']){	$total++; }
	if ($d['name2']){	$total++; }
	if ($d['name3']){	$total++; }
	if ($d['name4']){	$total++; }
	if ($d['name5']){	$total++; }
	if ($d['name6']){	$total++; }
	}
	return $total;
}

function add3($court){
	$r = mysql_query("SELECT address1, address1a, address1b, address1c, address1d, address1e FROM ps_packets WHERE circuit_court = '$court' AND process_status='READY' AND caseLookupFlag = '' AND case_no='' and server_id < '1'");
	$total=0;
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	if ($d['address1']){	$total++; }
	if ($d['address1a']){	$total++; }
	if ($d['address1b']){	$total++; }
	if ($d['address1c']){	$total++; }
	if ($d['address1d']){	$total++; }
	if ($d['address1e']){	$total++; }
	}
	return $total;
}


function evStatus($status){
	$r = mysql_query("SELECT eviction_id FROM evictionPackets WHERE status = '$status' and process_status <> 'CANCELLED' AND process_status <> 'DUPLICATE' AND  process_status <> 'DAMAGED PDF'");
	return mysql_num_rows($r);
}
//eviction functions begin here
function evVol($court){
	$r = mysql_query("SELECT eviction_id FROM evictionPackets WHERE circuit_court = '$court' AND process_status='READY' and server_id < '1'");
	return mysql_num_rows($r);
}

function evDef($court){
	$r = mysql_query("SELECT name1, name2, name3, name4, name5, name6 FROM evictionPackets WHERE circuit_court = '$court' AND process_status='READY' and server_id < '1'");
	$total=0;
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	if ($d['name1']){	$total++; }
	if ($d['name2']){	$total++; }
	if ($d['name3']){	$total++; }
	if ($d['name4']){	$total++; }
	if ($d['name5']){	$total++; }
	if ($d['name6']){	$total++; }
	}
	return $total;
}

function evAdd($court){
	$r = mysql_query("SELECT address1, address1a, address1b, address1c, address1d, address1e FROM evictionPackets WHERE circuit_court = '$court' AND process_status='READY' and server_id < '1'");
	$total=0;
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	if ($d['address1']){	$total++; }
	if ($d['address1a']){	$total++; }
	if ($d['address1b']){	$total++; }
	if ($d['address1c']){	$total++; }
	if ($d['address1d']){	$total++; }
	if ($d['address1e']){	$total++; }
	}
	return $total;
}

function evVol2($court){
	$r = mysql_query("SELECT eviction_id FROM evictionPackets WHERE circuit_court = '$court' AND process_status='READY' AND caseLookupFlag <> '' AND case_no='' and server_id < '1'");
	return mysql_num_rows($r);
}

function evDef2($court){
	$r = mysql_query("SELECT name1, name2, name3, name4, name5, name6 FROM evictionPackets WHERE circuit_court = '$court' AND process_status='READY' AND caseLookupFlag <> '' AND case_no='' and server_id < '1'");
	$total=0;
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	if ($d['name1']){	$total++; }
	if ($d['name2']){	$total++; }
	if ($d['name3']){	$total++; }
	if ($d['name4']){	$total++; }
	if ($d['name5']){	$total++; }
	if ($d['name6']){	$total++; }
	}
	return $total;
}

function evAdd2($court){
	$r = mysql_query("SELECT address1, address1a, address1b, address1c, address1d, address1e FROM evictionPackets WHERE circuit_court = '$court' AND process_status='READY' AND caseLookupFlag <> '1' AND case_no='' and server_id < '1'");
	$total=0;
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	if ($d['address1']){	$total++; }
	if ($d['address1a']){	$total++; }
	if ($d['address1b']){	$total++; }
	if ($d['address1c']){	$total++; }
	if ($d['address1d']){	$total++; }
	if ($d['address1e']){	$total++; }
	}
	return $total;
}

function evVol3($court){
	$r = mysql_query("SELECT eviction_id FROM evictionPackets WHERE circuit_court = '$court' AND process_status='READY' AND caseLookupFlag = '1' AND case_no='' and server_id < '1'");
	return mysql_num_rows($r);
}

function evDef3($court){
	$r = mysql_query("SELECT name1, name2, name3, name4, name5, name6 FROM evictionPackets WHERE circuit_court = '$court' AND process_status='READY' AND caseLookupFlag = '1' AND  case_no='' and server_id < '1'");
	$total=0;
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	if ($d['name1']){	$total++; }
	if ($d['name2']){	$total++; }
	if ($d['name3']){	$total++; }
	if ($d['name4']){	$total++; }
	if ($d['name5']){	$total++; }
	if ($d['name6']){	$total++; }
	}
	return $total;
}

function evAdd3($court){
	$r = mysql_query("SELECT address1, address1a, address1b, address1c, address1d, address1e FROM evictionPackets WHERE circuit_court = '$court' AND process_status='READY' AND caseLookupFlag = '' AND case_no='' and server_id < '1'");
	$total=0;
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	if ($d['address1']){	$total++; }
	if ($d['address1a']){	$total++; }
	if ($d['address1b']){	$total++; }
	if ($d['address1c']){	$total++; }
	if ($d['address1d']){	$total++; }
	if ($d['address1e']){	$total++; }
	}
	return $total;
}

function dupCheck($string){
	$r=@mysql_query("select * from ps_packets where client_file LIKE '%$string%'");
	$c=mysql_num_rows($r);
	if ($c == 1){
		$return="class='single'";
		//$return[1]=$c;
	}else{
		$return="class='duplicate'";
		//$return[1]=$c;
	}
	return $return;
}
function dupList($string,$packet){
	if ($string){
		$r=@mysql_query("select * from ps_packets where client_file LIKE '%$string%' and packet_id <> '$packet'");
		$data="<span style='font-size:12px; background-color:#FF0000; border:solid 1px #ffff00;'>Possible Duplicates:";
		while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
			$data .= " <a href='/otd/order.php?packet=$d[packet_id]' target='_blank'>[$d[packet_id]]</a>";
		}
		$data .= "</span>";
	}else{
		$data="<span style='font-size:12px; background-color:#FF0000; border:solid 1px #ffff00;'>Unable to Determine Possible Duplicates</span>";
	}
	return $data;
}
function EVdupCheck($string){
	$r=@mysql_query("select * from evictionPackets where client_file LIKE '%$string%'");
	$c=mysql_num_rows($r);
	if ($c == 1){
		$return="class='single'";
		//$return[1]=$c;
	}else{
		$return="class='duplicate'";
		//$return[1]=$c;
	}
	return $return;
}
function EVdupList($string,$packet){
	if ($string){
		$r=@mysql_query("select * from evictionPackets where client_file LIKE '%$string%' and eviction_id <> '$packet'");
		$data="<div style='font-size:12px; background-color:#FF0000; border:solid 1px #ffff00;'>Possible Duplicates:";
		while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
			$data .= " <a href='/ev/order.php?packet=$d[eviction_id]' target='_blank'>[$d[eviction_id]]</a>";
		}
		$data .= "</div>";
	}else{
		$data="<div style='font-size:12px; background-color:#FF0000; border:solid 1px #ffff00;'>Unable to Determine Possible Duplicates</div>";
	}
	return $data;
}
$total =0;
$totala =0;
$totald =0;
//include 'menu.php';
?>
<meta http-equiv="refresh" content="30" />

<?
$newx=psStatus('NEW');
?>

<table width=100%><tr><td colspan='3' align='center'>
<div style="border-style:solid 1px; border-collapse:collapse; font-weight:bold; letter-spacing: 5px;background-color:00BBAA; width:600px;">FORECLOSURES</div>
</td></td><tr><td width="34%" valign='top'>
<div>Data Entry</div>

<?
$r=@mysql_query("SELECT client_file, case_no, packet_id, date_received, service_status FROM ps_packets WHERE status = 'NEW' and process_status <> 'CANCELLED' AND process_status <> 'DUPLICATE' AND  process_status <> 'DAMAGED PDF'");
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
if (isExport($d[packet_id]) == 0){
$dupCheck='';
$dupCheck=dupCheck($d[client_file]);
$real++;
?>
	<div><?=$d[date_received]?> #<a href="http://staff.mdwestserve.com/otd/order.php?packet=<?=$d[packet_id]?>" target="_blank"><?=$d[packet_id]?></a> : <a href="http://staff.mdwestserve.com/search.php?q=<?=$d[client_file]?>&field=client_file" target="_Blank"><b><?=$d[client_file]?><?=webservice($d[client_file])?></b></a> :<?=$d[case_no]?><? if ($d[service_status] == "MAIL ONLY"){ echo " <b>MAIL ONLY</b>"; }?> <? if ($dupCheck == "class='duplicate'"){ echo dupList($d[client_file],$d[packet_id]);} ?></div>
<?
}
}
?>

</td><td width="33%" valign='top'>
<table width=100% border="1" align="center" style="font-variant:small-caps;">
	<tr>
    	<td>County</td>
        <td align="center">Files</td>
        <td align="center">Addresses</td>
        <td align="center">Defendants</td>
    </tr>
<?
$r=@mysql_query("select DISTINCT circuit_court from ps_packets where process_status='READY' and server_id < '1'");
while ($d=mysql_fetch_array($r, MYSQL_ASSOC)){
$new = vol($d[circuit_court]);
$def = def($d[circuit_court]);
$add = add($d[circuit_court]);
$total = $total + $new;
$totald = $totald + $def;
$totala = $totala + $add;
?>
<tr>
	<td><?=$d[circuit_court];?></td>
    <td align="center"><strong><?=$new;?></strong></td>
    <td align="center"><strong><?=$add;?></strong></td>
    <td align="center"><strong><?=$def;?></strong></td>
</tr>


<? } ?>
<tr>
	<td align="right">Dispatch</td>
 <td align="center"><strong><?=$total?></strong></td>
 <td align="center"><strong><?=$totala?></strong></td>
 <td align="center"><strong><?=$totald?></strong></td>
 </tr>
 <tr>
	<td align="right">Data Entry</td>
 <td align="center"><strong><?=$newx?>/<?=$real?></strong></td>
  <? $title="FORECLOSURES: $newx New $total Ready"; ?>
 <td></td>
 </tr>

</table>

</td><td width="33%" valign='top'>

<table width=100% border="1" align="center" style="font-variant:small-caps;">
	<tr>
    	<td>County</td>
        <td align="center">Files</td>
        <td align="center">Addresses</td>
        <td align="center">Defendants</td>
    </tr>
<?
$new=0;
$add=0;
$def=0;
$total=0;
$totala=0;
$totald=0;
$r=@mysql_query("select DISTINCT circuit_court from ps_packets where process_status='READY' and server_id < '1'");
while ($d=mysql_fetch_array($r, MYSQL_ASSOC)){
$new = vol2($d[circuit_court]);
$def = def2($d[circuit_court]);
$add = add2($d[circuit_court]);
$total = $total + $new;
$totald = $totald + $def;
$totala = $totala + $add;
?>
<tr>
	<td><?=$d[circuit_court];?></td>
    <td align="center"><strong><?=$new;?></strong></td>
    <td align="center"><strong><?=$add;?></strong></td>
    <td align="center"><strong><?=$def;?></strong></td>
</tr>


<? } ?>
<tr>
	<td align="right">Case Lookup</td>
 <td align="center"><strong><?=$total?></strong></td>
 <td align="center"><strong><?=$totala?></strong></td>
 <td align="center"><strong><?=$totald?></strong></td>
 </tr>

</table>
</td></tr><tr><td colspan='3' align="center">
<!---- BEGIN EVICTIONS--------------->
<div style="border-style:solid 1px; border-collapse:collapse; font-weight:bold; letter-spacing: 5px; background-color:99AAEE; width:600px;">EVICTIONS</div>
</td></tr><tr><td><div>Data Entry</div>
<?
$total =0;
$totala =0;
$totald =0;
$newx=evStatus('NEW');
$r=@mysql_query("SELECT client_file, case_no, eviction_id, date_received, otd FROM evictionPackets WHERE status = 'NEW' and process_status <> 'CANCELLED' AND process_status <> 'DUPLICATE' AND  process_status <> 'DAMAGED PDF'");
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
$EVdupCheck='';
$EVdupCheck=EVdupCheck($d[client_file]);
$otdStr=str_replace('data/service/orders/','PS_PACKETS/',$d[otd]);

?>
	<div><?=$d[date_received]?><br> +<a href="/ev/order.php?packet=<?=$d[eviction_id]?>" target="_Blank"><?=$d[eviction_id]?></a> : <?=$d[client_file]?><?=webservice($d[client_file])?> : <?=$d[case_no]?><? if ($EVdupCheck == "class='duplicate'"){ echo EVdupList($d[client_file],$d[eviction_id]);} ?> <a href="<?=$otdStr?>" target="_Blank">PAPERS</a></div>
<?
}
?>

</td><td valign='top'>
<table width=100% border="1" align="center" style="font-variant:small-caps;">
	<tr>
    	<td>County</td>
        <td align="center">Files</td>
        <td align="center">Addresses</td>
        <td align="center">Defendants</td>
    </tr>
<?
$r=@mysql_query("select DISTINCT circuit_court from evictionPackets where process_status='READY' and server_id < '1'");
while ($d=mysql_fetch_array($r, MYSQL_ASSOC)){
$new = evVol($d[circuit_court]);
$def = evDef($d[circuit_court]);
$add = evAdd($d[circuit_court]);
$total = $total + $new;
$totald = $totald + $def;
$totala = $totala + $add;
?>
<tr>
	<td><?=$d[circuit_court];?></td>
    <td align="center"><strong><?=$new;?></strong></td>
    <td align="center"><strong><?=$add;?></strong></td>
    <td align="center"><strong><?=$def;?></strong></td>
</tr>


<? } ?>
<tr>
	<td align="right">Dispatch</td>
 <td align="center"><strong><?=$total?></strong></td>
 <td align="center"><strong><?=$totala?></strong></td>
 <td align="center"><strong><?=$totald?></strong></td>
 </tr>
 <tr>
	<td align="right">Data Entry</td>
 <td align="center"><strong><?=$newx?></strong></td>
 <script>document.title='<?=$title?> || EVICTIONS: <?=$newx;?> New <?=$total;?> Ready';</script>
 <td></td>
 </tr>

</table>

</td><td valign='top'>

<table width=100% border="1" align="center" style="font-variant:small-caps;">
	<tr>
    	<td>County</td>
        <td align="center">Files</td>
        <td align="center">Addresses</td>
        <td align="center">Defendants</td>
    </tr>
<?
$new=0;
$add=0;
$def=0;
$total=0;
$totala=0;
$totald=0;
$r=@mysql_query("select DISTINCT circuit_court from evictionPackets where process_status='READY' and server_id < '1'");
while ($d=mysql_fetch_array($r, MYSQL_ASSOC)){
$new = evVol2($d[circuit_court]);
$def = evDef2($d[circuit_court]);
$add = evAdd2($d[circuit_court]);
$total = $total + $new;
$totald = $totald + $def;
$totala = $totala + $add;
?>
<tr>
	<td><?=$d[circuit_court];?></td>
    <td align="center"><strong><?=$new;?></strong></td>
    <td align="center"><strong><?=$add;?></strong></td>
    <td align="center"><strong><?=$def;?></strong></td>
</tr>


<? } ?>
<tr>
	<td align="right">Case Lookup</td>
 <td align="center"><strong><?=$total?></strong></td>
 <td align="center"><strong><?=$totala?></strong></td>
 <td align="center"><strong><?=$totald?></strong></td>
 </tr>

</table>
</td></tr></table>



<style>

table { border-collapse: collapse}
div { border: solid 1px;}
body td { font-size : 14px; font-weight: normal }
</style>



<? //include 'footer.php';
mysql_close();
?>
