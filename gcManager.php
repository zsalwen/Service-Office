<?
include 'common.php';
//include 'menu.php';
if ($_GET[update] && $_GET[id] && $_GET[svc]){
	psActivity('mailGreenCard');
	if ($_GET[svc] == 'otd'){
		timeline($_GET[id],$_COOKIE[psdata][name]." Received Green Card");
		@mysql_query("update ps_packets set gcStatus = '$_GET[update]' where packet_id = '$_GET[id]'");
	}elseif($_GET[svc] == 'ev'){
		ev_timeline($_GET[id],$_COOKIE[psdata][name]." Received Green Card");
		@mysql_query("update evictionPackets set gcStatus = '$_GET[update]' where eviction_id = '$_GET[id]'");
	}
	echo "<script>window.location='gcManager.php'</script>";
}
function article($packet,$add){
	$var=$packet."-".strtoupper($add)."X";
	$q="select article from usps where packet = '$var' LIMIT 0,1";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC) or die ("Query: $q<br>".mysql_error());
	if ($d["article"] != ''){
		return $d["article"];
	}else{
		return 0;
	}
}
?>
<style>
.R{ background-color:#33CCFF;}
.F{ background-color:#00FF00;}
.P{ background-color:#FF9966;}
a { color:#000000; text-decoration:none;}
</style>
<br>
<br>
<br>
<table style="border-collapse:collapse;" border="1">
<tr class="noprint">
	<td nowrap="nowrap">Articles</td>
	<td nowrap>Green Card Status</td>
	<td nowrap>Filing Status</td>
    <td nowrap colspan="2"> Update Green Card Status</td>
    <td nowrap>Packet</td>
</tr>

<?
$q= "select gcStatus, filing_status, eviction_id from evictionPackets where service_status = 'MAILING AND POSTING' OR service_status = 'CANCELLED' ORDER BY eviction_id DESC";
$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
$i=0;
while ($d=mysql_fetch_array($r, MYSQL_ASSOC)) {$i++;
?>
<tr class="<?=substr($d[gcStatus],0,1)?>">
	<td nowrap="nowrap">
    <?
	$ii=0;
	while ($ii < 6){$ii++;
		if ($d["name$ii"]){
			if ($d[address1]){
				$art=article("EV".$d[eviction_id],$ii);
				if ($art != 0){
					if ($ii > 1){
						echo "<br>";
					}
					echo "<a target='_Blank' href=../'usps.php?track=$art'>Art $ii: $art</a>";
				}
			}
		}
	}
	?>
    </td>
	<td nowrap><?=$d[gcStatus]?></td>
	<td nowrap><?=$d[filing_status]?></td>
    <td nowrap class="noprint" style="background-color:#33CCFF;"><a href="?update=RETURNED&id=<?=$d[eviction_id]?>&svc=ev"> RETURNED </a></td>
    <td nowrap class="noprint" style="background-color:#00FF00;"><a href="?update=FILED&id=<?=$d[eviction_id]?>&svc=ev"> FILED </a></td    ><td nowrap align="center"><a href="/ev/evUpload.php?eviction=<?=$d[eviction_id]?>" target="_blank">EV<?=$d[eviction_id]?></a></td>
</tr>
<? }?>
<?
$q= "select gcStatus, article1, article1a, article1b, article1c, article1d, article1e, article2, article2a, article2b, article2c, article2d, article2e, article3, article3a, article3b, article3c, article3d, article3e, article4, article4a, article4b, article4c, article4d, article4e, article5, article5a, article5b, article5c, article5d, article5e, article6, article6a, article6b, article6c, article6d, article6e filing_status, packet_id from ps_packets where service_status = 'MAILING AND POSTING' OR service_status = 'CANCELLED' ORDER BY packet_id DESC";
$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
$i=0;
while ($d=mysql_fetch_array($r, MYSQL_ASSOC)) {$i++;
?>
<tr class="<?=substr($d[gcStatus],0,1)?>">
	<td nowrap="nowrap">
    <?
	$ii=0;
	while ($ii < 6){$ii++;
		if ($d["name$ii"]){
			if ($d[address1]){
				$art=article($d[packet_id],$ii);
				if ($art != 0){
					if ($ii > 1){
						echo "<br>";
					}
					echo "<a target='_Blank' href=../'usps.php?track=$art'>Art $ii: $art</a>";
				}
			}
			foreach (range('a','e') as $letter){
				$var=$ii.$letter;
				if ($d["address$var"]){
					$art=article($d[packet_id],$var);
					if ($art != 0){
						if ($ii > 1){
							echo "<br>";
						}
						echo "<a target='_Blank' href=../'usps.php?track=$art'>Art ".strtoupper($var).": $art</a>";
					}
				}
			}
			$var=$ii."PO";
			if ($d["address$var"]){
				$art=article($d[packet_id],$var);
				if ($art != 0){
					if ($ii > 1){
						echo "<br>";
					}
					echo "<a target='_Blank' href=../'usps.php?track=$art'>Art ".strtoupper($var).": $art</a>";
				}
			}
			$var=$ii."PO2";
			if ($d["address$var"]){
				$art=article($d[packet_id],$var);
				if ($art != 0){
					if ($ii > 1){
						echo "<br>";
					}
					echo "<a target='_Blank' href=../'usps.php?track=$art'>Art ".strtoupper($var).": $art</a>";
				}
			}
		}
	}
	?>
    </td>
	<td nowrap><?=$d[gcStatus]?></td>
	<td nowrap><?=$d[filing_status]?></td>
    <td nowrap class="noprint" style="background-color:#33CCFF;"><a href="?update=RETURNED&id=<?=$d[packet_id]?>&svc=otd"> RETURNED </a></td>
    <td nowrap class="noprint" style="background-color:#00FF00;"><a href="?update=FILED&id=<?=$d[packet_id]?>&svc=otd"> FILED </a></td    ><td nowrap align="center"><a href="/otd/affidavitUpload.php?packet=<?=$d[packet_id]?>" target="_blank">OTD<?=$d[packet_id]?></a></td>
</tr>
<? }?>
</table>
<?// include '/footer.php'; 
mysql_close();
?>