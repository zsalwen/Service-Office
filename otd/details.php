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

function article($packet,$add){
	$var=$packet."-".strtoupper($add)."X";
	$q="select article from usps where packet = '$var' LIMIT 0,1";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	if ($d["article"] != ''){
		return $d["article"];
	}else{
		return 0;
	}
}

hardLog('Presale Details for '.$_GET[packet],'user');

function serverName($id){
	$resource=@mysql_query("select name from ps_users where id = '$id'");
	$data=mysql_fetch_array($resource, MYSQL_ASSOC);
return $data[name];
}
mysql_connect();
mysql_select_db('service');
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
					$i=0;
					while ($i < 6){$i++;
						if ($d["name$i"]){
							if ($d[address1]){
								$art=article($d[packet_id],$i);
								if ($art != 0){
									echo "<li>$art</li>";
								}
							}
							foreach (range('a','e') as $letter){
								$var=$i.$letter;
								if ($d["address$var"]){
									$art=article($d[packet_id],$var);
									if ($art != 0){
										echo "<li>$art</li>";
									}
								}
							}
							$var=$i."PO";
							if ($d["address$var"]){
								$art=article($d[packet_id],$i."PO");
								if ($art != 0){
									echo "<li>$art</li>";
								}
							}
							$var=$i."PO2";
							if ($d["address$var"]){
								$art=article($d[packet_id],$i."PO2");
								if ($art != 0){
									echo "<li>$art</li>";
								}
							}
						}
					}
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