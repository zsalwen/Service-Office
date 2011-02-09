<?
include 'common.php';
$OTD=$_GET[OTD];
$r=@mysql_query("SELECT attorneys_id, uspsVerify, qualityControl FROM ps_packets WHERE packet_id='$OTD'");
$d=mysql_fetch_array($r,MYSQL_ASSOC);
$r2=@mysql_query("SELECT * FROM occNotices WHERE packet_id='$OTD'");
$d2=mysql_fetch_array($r2,MYSQL_ASSOC);
if ($d[qualityControl] == ''){
	if ($d[msg] == ''){
		$msg="QUALITY CONTROL ";
	}else{
		$msg .= "AND QUALITY CONTROL ";
	}
}
if ($msg != ''){
	$msg .= "MUST BE VERIFIED BEFORE ANY PRINTING WILL BE ALLOWED.";
}
?>
<table align='center'>
	<tr>
		<td align='center' style='font-size:16px;'>PRINT MAILINGS FOR OTD<?=$OTD?></td>
	</tr>
	<? if ($msg != ''){ ?>
	<tr>
		<td align='center' ><?=$msg?></td>
	</tr>
	<? }else{ ?>
	<tr>
	<? if ($d[attorneys_id] == '70'){ ?>
		<td align='center'><a href='http://staff.mdwestserve.com/otd/stuffPacket.bgw.php?packet=<?=$OTD?>' target='_blank'>Server #10 Envelopes</a> | <a href='http://staff.mdwestserve.com/otd/stuffPacket.bgw.php?packet=<?=$OTD?>&mail=1' target='_blank'>Our #10 Envelopes</a></td>
	<? }elseif ($d[attorneys_id] == '1'){ ?>
		<td align='center'><a href='http://staff.mdwestserve.com/otd/stuffPacket.2.php?packet=<?=$OTD?>&sb=1' target='_blank'>Server #10 Envelopes</a> | <a href='http://staff.mdwestserve.com/otd/stuffPacket.2.php?packet=<?=$OTD?>&mail=1&sb=1' target='_blank'>Our #10 Envelopes</a></td>
	<? }else{ ?>
		<td align='center'><a href='http://staff.mdwestserve.com/otd/stuffPacket.2.php?packet=<?=$OTD?>' target='_blank'>Server #10 Envelopes</a> | <a href='http://staff.mdwestserve.com/otd/stuffPacket.2.php?packet=<?=$OTD?>&mail=1' target='_blank'>Our #10 Envelopes</a></td>
	<? } ?>
	</tr>
	<? if ($d2[packet_id]){ ?>
	<tr>
		<td align='center'><a href='http://staff.mdwestserve.com/otd/multOccupant.php?packet=<?=$_GET[OTD]?>'>Re-Print Occupant Notice and Affidavits</a> | <a href='http://staff.mdwestserve.com/otd/envelopeMaster.php?OTD=<?=$_GET[OTD]?>'>Re-Print Occupant Notice Envelope</a></td>
	</tr>
	<? } ?>
	<tr>
		<td><a href='http://staff.mdwestserve.com/envelopePrint.php?OTD=<?=$OTD?>' target='_blank'>Print Envelopes</a></td>
	</tr>
	<tr>
		<td><a href='http://staff.mdwestserve.com/greenMaster.php?OTD=<?=$OTD?>' target='_blank'>Print Greencards</a></td>
	</tr>
	<tr>
		<td><a href='http://staff.mdwestserve.com/whiteMaster.php?OTD=<?=$OTD?>&space=185' target='_blank'>Print Whitecards</a></td>
	</tr>
	<tr>
		<td><a href='http://staff.mdwestserve.com/mailDateSwap.php?packet=<?=$OTD?>' target='_blank'>Change Mailing Date(s)</a></td>
	</tr>
	<tr>
		<td><a href='http://service.mdwestserve.com/mailMatrix.php?packet=<?=$OTD?>&product=OTD'>Mail Matrix</a></td>
	</tr>
	<tr>
		<td><a href='http://staff.mdwestserve.com/mailRange.php'>Mail Print Range</a></td>
	</tr>
	<? } ?>
	<tr>
		<td><a href='tracking.php?packet=<?=$OTD?>'>USPS Tracking</a></td>
	</tr>
</table>