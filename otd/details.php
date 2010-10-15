<?
function hardLog($str,$type){
	if ($type == "user"){
		$log = "/logs/user.log";
	}
	if ($type == "contractor"){
		$log = "/logs/contractor.log";
	}
	if ($type == "debug"){
		$log = "/logs/debug.log";
	}
	if ($log){
		error_log(date('h:iA n/j/y')." ".$_COOKIE[psdata][name]." ".$_SERVER["REMOTE_ADDR"]." ".trim($str)."\n", 3, $log);
	}
}

hardLog('Presale Details for '.$_GET[packet],'user');

function serverName($id){
	$resource=@mysql_query("select name from ps_users where id = '$id'");
	$data=mysql_fetch_array($resource, MYSQL_ASSOC);
return $data[name];
}
mysql_connect();
mysql_select_db('core');
$r=@mysql_query("select *,
						date_format(date_received, '%W, %M %D %Y') as date_received_f,
						date_format(estFileDate, '%W, %M %D %Y') as estFileDate,
						date_format(fileDate, '%W, %M %D %Y') as fileDate
							from ps_packets where packet_id = '$_GET[packet]'");
$d=mysql_fetch_array($r,MYSQL_ASSOC);
// some logic questions for building links
//// qc checklist
if ($d[packet_id] > 3620){
	$checkLink="serviceSheet.php?packet=$d[packet_id]&autoPrint=1";
}else{
	$checkLink="oldServiceSheet.php?packet=$d[packet_id]&autoPrint=1";
}
//// otd link
$otdStr=str_replace('portal//var/www/dataFiles/service/orders/','PS_PACKETS/',$d[otd]);
$otdStr=str_replace('data/service/orders/','PS_PACKETS/',$otdStr);
$otdStr=str_replace('portal/','',$otdStr);
//$otdStr=str_replace('mdwestserve.com','alpha.mdwestserve.com',$otdStr);
/*if (!$otdStr){
	$otdStr=$d[otd];
}*/
if (!strpos($otdStr,'mdwestserve.com')){
	$otdStr="http://mdwestserve.com/".$otdStr;
}
// end link logic
$unknown = "<a style='color:FF0000;'>Unknown</a>";
?>
<style>
a { text-decoration:none; }
.title { background-color:#cccccc; border-bottom:dashed 1px #000000; }
.title2 { background-color:#ffffcc; border-bottom:dashed 1px #FFFF00; }
.title3 { background-color:#ccffff; border-bottom:dashed 1px #00FFFF; }

</style>
<?
$received=strtotime($d[date_received]);
$deadline=$received+432000;
$deadline=date('l, F jS Y',$deadline);
?>
<div style="">Service Book for <a href="?packet=<?=$d[packet_id]?>">Packet <?=$d[packet_id]?></a> <a href="order.php?packet=<?=$d[packet_id];?>" target="_blank">Edit Order Details</a></div>
<!-- show status -->
<table width="100%" style="border-collapse:collapse;" cellspacing="0" cellpadding="0" border="1">
	<tr>
		<td width="150px" class="title">Case Data</td>
		<td> <?=$d[case_no];?> in <?=$d[circuit_court];?>. Received on <?=$d[date_received_f];?>, deadline to file <?=$deadline;?></li></td>
	</tr>
	<tr>
		<td class="title">Service Data</td>
		<td>
			<table cellspacing="0" cellpadding="2" width="100%">
				<tr>
					<td valign="top">
					<? if ($d[name1]){ if (!$d[onAffidavit1]){ $def1 = "-Not Defendant"; } echo "<li>$d[name1] $def1</li>"; } ?>
					<? if ($d[name2]){ if (!$d[onAffidavit2]){ $def2 = "-Not Defendant"; } echo "<li>$d[name2] $def2</li>"; } ?>
					<? if ($d[name3]){ if (!$d[onAffidavit3]){ $def3 = "-Not Defendant"; } echo "<li>$d[name3] $def3</li>"; } ?>
					<? if ($d[name4]){ if (!$d[onAffidavit4]){ $def4 = "-Not Defendant"; } echo "<li>$d[name4] $def4</li>"; } ?>
					<? if ($d[name5]){ if (!$d[onAffidavit5]){ $def5 = "-Not Defendant"; } echo "<li>$d[name5] $def5</li>"; } ?>
					<? if ($d[name6]){ if (!$d[onAffidavit6]){ $def6 = "-Not Defendant"; } echo "<li>$d[name6] $def6</li>"; } ?>
					</td>
					<td valign="top">
					</td>
					<td valign="top">
					<? if ($d[address1]){ echo "<li>$d[address1], $d[city1], $d[state1] $d[zip1] </li>"; } ?>
					<? if ($d[address1a]){ echo "<li>$d[address1a], $d[city1a], $d[state1a] $d[zip1a] </li>"; } ?>
					<? if ($d[address1b]){ echo "<li>$d[address1b], $d[city1b], $d[state1b] $d[zip1b] </li>"; } ?>
					<? if ($d[address1c]){ echo "<li>$d[address1c], $d[city1c], $d[state1c] $d[zip1c] </li>"; } ?>
					<? if ($d[address1d]){ echo "<li>$d[address1d], $d[city1d], $d[state1d] $d[zip1d] </li>"; } ?>
					<? if ($d[address1e]){ echo "<li>$d[address1e], $d[city1e], $d[state1e] $d[zip1e] </li>"; } ?>
					</td>
					<td valign="top">
					</td>
					<td valign="top">
					<? if ($d[server_id]){ echo "<li>".serverName($d[server_id])."</li>"; } ?>
					<? if ($d[server_ida]){ echo "<li>".serverName($d[server_ida])."</li>"; } ?>
					<? if ($d[server_idb]){ echo "<li>".serverName($d[server_idb])."</li>"; } ?>
					<? if ($d[server_idc]){ echo "<li>".serverName($d[server_idc])."</li>"; } ?>
					<? if ($d[server_idd]){ echo "<li>".serverName($d[server_idd])."</li>"; } ?>
					<? if ($d[server_ide]){ echo "<li>".serverName($d[server_ide])."</li>"; } ?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class="title">Printables</td>
		<td>
			<table width="100%" style="border-collapse:collapse;" cellspacing="0" cellpadding="0">
				<tr>
					<td><li><a href="<?=$checkLink?>" target="_blank">Quality Control checklist</a></li></td>
					<td><li><a href="<?=$otdStr?>" target="_blank">Papers to be served</a></li></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class="title">Courier Info</td>
<?
$rXX=@mysql_query("select name from courier where courierID = '$d[courierID]'");
$dXX=mysql_fetch_array($rXX,MYSQL_ASSOC);
if ($dXX[name]){
$courier = $dXX[name];
}else{
$courier = $unknown;
}
?>
		<td><?=$courier;?> on <?=$d[estFileDate];?></td>
	</tr>
	<tr>
		<td class="title">Status Matrix</td>
		<td>
			<table cellspacing="0" cellpadding="2">
				<tr>
					<td class="title3">Client</td>
					<td><?=$d[status];?></td>
				</tr>
				<tr>
					<td class="title3">Process</td>
					<td><?=$d[process_status];?></td>
				</tr>
				<tr>
					<td class="title3">Service</td>
					<td><?=$d[service_status];?></td>
				</tr>
				<tr>
					<td class="title3">Affidavit</td>
					<td><?=$d[affidavit_status];?></td>
				</tr>
<? if ($d[affidavit2_status]){ ?>				
				<tr>
					<td class="title3">Watch</td>
					<td><?=$d[affidavit2_status];?></td>
				</tr>
<? }?>
<?
if ($d[filing_status]){
$filing_status = $d[filing_status];
}else{
$filing_status = $unknown;
}
?>
				<tr>
					<td class="title3">Filing</td>
					<td>
						<table cellspacing="0" cellpadding="2" width="100%" border="1">
							<tr>
								<td class="title2">Status</td>
								<td class="title2">Scheduled Filing Date</td>
								<td class="title2">Actual Filing Date</td>
							</tr>
							<tr>
								<td><?=$filing_status;?></td>
								<td><?=$d[estFileDate];?></td>
								<td><?=$d[fileDate];?></td>
							</tr>
						</table>
					</td>
				</tr>
<?
if ($d[mail_status]){
$mail_status = $d[mail_status];
}else{
$mail_status = $unknown;
}
?>
				<tr>
					<td class="title3">Mail</td>
					<td><?=$mail_status;?>
					<?
					if ($d[article1]){ echo "<li>".$d[article1]."</li>"; }
					if ($d[article1a]){ echo "<li>".$d[article1a]."</li>"; }
					if ($d[article1b]){ echo "<li>".$d[article1b]."</li>"; }
					if ($d[article1c]){ echo "<li>".$d[article1c]."</li>"; }
					if ($d[article1d]){ echo "<li>".$d[article1d]."</li>"; }
					if ($d[article1e]){ echo "<li>".$d[article1e]."</li>"; }
					
					if ($d[article2]){ echo "<li>".$d[article2]."</li>"; }
					if ($d[article2a]){ echo "<li>".$d[article2a]."</li>"; }
					if ($d[article2b]){ echo "<li>".$d[article2b]."</li>"; }
					if ($d[article2c]){ echo "<li>".$d[article2c]."</li>"; }
					if ($d[article2d]){ echo "<li>".$d[article2d]."</li>"; }
					if ($d[article2e]){ echo "<li>".$d[article2e]."</li>"; }
					
					if ($d[article3]){ echo "<li>".$d[article3]."</li>"; }
					if ($d[article3a]){ echo "<li>".$d[article3a]."</li>"; }
					if ($d[article3b]){ echo "<li>".$d[article3b]."</li>"; }
					if ($d[article3c]){ echo "<li>".$d[article3c]."</li>"; }
					if ($d[article3d]){ echo "<li>".$d[article3d]."</li>"; }
					if ($d[article3e]){ echo "<li>".$d[article3e]."</li>"; }
					
					if ($d[article4]){ echo "<li>".$d[article4]."</li>"; }
					if ($d[article4a]){ echo "<li>".$d[article4a]."</li>"; }
					if ($d[article4b]){ echo "<li>".$d[article4b]."</li>"; }
					if ($d[article4c]){ echo "<li>".$d[article4c]."</li>"; }
					if ($d[article4d]){ echo "<li>".$d[article4d]."</li>"; }
					if ($d[article4e]){ echo "<li>".$d[article4e]."</li>"; }
					
					if ($d[article5]){ echo "<li>".$d[article5]."</li>"; }
					if ($d[article5a]){ echo "<li>".$d[article5a]."</li>"; }
					if ($d[article5b]){ echo "<li>".$d[article5b]."</li>"; }
					if ($d[article5c]){ echo "<li>".$d[article5c]."</li>"; }
					if ($d[article5d]){ echo "<li>".$d[article5d]."</li>"; }
					if ($d[article5e]){ echo "<li>".$d[article5e]."</li>"; }
					
					if ($d[article6]){ echo "<li>".$d[article6]."</li>"; }
					if ($d[article6a]){ echo "<li>".$d[article6a]."</li>"; }
					if ($d[article6b]){ echo "<li>".$d[article6b]."</li>"; }
					if ($d[article6c]){ echo "<li>".$d[article6c]."</li>"; }
					if ($d[article6d]){ echo "<li>".$d[article6d]."</li>"; }
					if ($d[article6e]){ echo "<li>".$d[article6e]."</li>"; }
					
					
					if ($d[article1PO]){ echo "<li>".$d[article1PO]."</li>"; }
					if ($d[article2PO]){ echo "<li>".$d[article2PO]."</li>"; }
					if ($d[article3PO]){ echo "<li>".$d[article3PO]."</li>"; }
					if ($d[article4PO]){ echo "<li>".$d[article4PO]."</li>"; }
					if ($d[article5PO]){ echo "<li>".$d[article5PO]."</li>"; }
					if ($d[article6PO]){ echo "<li>".$d[article6PO]."</li>"; }
					
					if ($d[article1PO2]){ echo "<li>".$d[article1PO2]."</li>"; }
					if ($d[article2PO2]){ echo "<li>".$d[article2PO2]."</li>"; }
					if ($d[article3PO2]){ echo "<li>".$d[article3PO2]."</li>"; }
					if ($d[article4PO2]){ echo "<li>".$d[article4PO2]."</li>"; }
					if ($d[article5PO2]){ echo "<li>".$d[article5PO2]."</li>"; }
					if ($d[article6PO2]){ echo "<li>".$d[article6PO2]."</li>"; }

					
					?>
					</td>
				</tr>
<? if ($d[photo_status]){ ?>				
				<tr>
					<td class="title3">Photo</td>
					<td><?=$d[photo_status];?></td>
				</tr>
<? }?>
			</table>
		</td>
	</tr>
</table>
<table>
	<tr>
		<td>
			<iframe height="110px" width="600px"  frameborder="0" src="http://staff.mdwestserve.com/notes.php?packet=<?=$d[packet_id]?>"></iframe>
		</td>
		<td>
			uploads
		</td>
	</tr>
</table>