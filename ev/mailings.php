<?
include 'common.php';
$EV=$_GET[EV];
$r=@mysql_query("SELECT uspsVerify, qualityControl FROM evictionPackets WHERE eviction_id='$EV' LIMIT 0,1");
$d=mysql_fetch_array($r,MYSQL_ASSOC);
if ($d[uspsVerify] == ''){
	$msg="ADDRESSES ";
}
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
		<td align='center' style='font-size:16px;'>PRINT MAILINGS FOR EV<?=$EV?></td>
	</tr>
	<? if ($msg != ''){ ?>
	<tr>
		<td align='center' ><?=$msg?></td>
	</tr>
	<? }else{ ?>
	<tr>
		<td><a href='http://staff.mdwestserve.com/envelopePrint.php?EV=<?=$EV?>' target='_blank'>Print Envelopes</a></td>
	</tr>
	<tr>
		<td><a href='http://staff.mdwestserve.com/greenMaster.php?EV=<?=$EV?>' target='_blank'>Print Greencards</a></td>
	</tr>
	<tr>
		<td><a href='http://staff.mdwestserve.com/whiteMaster.php?EV=<?=$EV?>&space=185' target='_blank'>Print Whitecards</a></td>
	</tr>
	<tr>
		<td><a href='http://staff.mdwestserve.com/mailDateSwap.php?eviction=<?=$EV?>' target='_blank'>Change Mailing Date(s)</a></td>
	</tr>
	<tr>
		<td><a href='http://service.mdwestserve.com/mailMatrix.php?packet=<?=$EV?>&product=EV'>Mail Matrix</a></td>
	</tr>
	<? } ?>
	<tr>
		<td><a href='tracking.php?packet=<?=$EV?>'>USPS Tracking</a></td>
	</tr>
</table>