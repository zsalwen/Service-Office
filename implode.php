<? include 'common.php'; ?>

<link rel="stylesheet" type="text/css" href="fire.css" />
<div>Internal Reporting Data Check</div>
<table width="100%" cellspacing="0"><tr>
<td valign="top">
<div>Need "File Closed" date entered.</div>
<ol>
<?
$r=@mysql_query("select * from ps_packets where fileDate = '0000-00-00' and packet_id > '1200' order by date_received DESC");
while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
$rTest=@mysql_query("select * from rescanRequests where packetID = '$d[packet_id]'");
if($dTest=mysql_fetch_array($rTest,MYSQL_ASSOC)){
$color="ff0000";
}elseif($d[affidavit_status] == 'IN PROGRESS'){
$color="ffff00";
}else{
$color="cccccc";
}
?>
<li style="background-color:#<?=$color;?>;"><a href="/otd/order.php?packet=<?=$d[packet_id]?>">Packet <?=$d[packet_id]?> <?=$d[filing_status]?></li>
<?
}
?>
</ol>
</td>
<td valign="top">
<div>Need "File Closed" date entered.</div>
<ol>
<?
$r=@mysql_query("select * from ps_packets where fileDate = '0000-00-00' and packet_id < '1201' order by date_received DESC");
while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
$rTest=@mysql_query("select * from rescanRequests where packetID = '$d[packet_id]'");
if($dTest=mysql_fetch_array($rTest,MYSQL_ASSOC)){
$color="ff0000";
}elseif($d[affidavit_status] == 'IN PROGRESS'){
$color="ffff00";
}else{
$color="cccccc";
}
?>
<li style="background-color:#<?=$color;?>;"><a href="/otd/order.php?packet=<?=$d[packet_id]?>">Packet <?=$d[packet_id]?> <?=$d[filing_status]?></li>
<?
}
?>
</ol>
</td>
<td valign="top">
<div>Need "Process Service: Bill" amount entered. (enter 0.00 for no cost, blank is not a valid amount.)</div>
<ol>
<?
$r=@mysql_query("select * from ps_packets, ps_pay where ps_pay.bill410 = '' AND ps_packets.packet_id=ps_pay.packetID AND ps_pay.product='OTD' order by date_received");
while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
?>
<li style="background-color:#CCCCCC;"><a href="/otd/order.php?packet=<?=$d[packet_id]?>">Packet <?=$d[packet_id]?> <?=$d[affidavit_status]?> <?=$d[filing_status]?></li>
<?
}
?>
</ol>
</td>
<td valign="top">
<div>Files missing data entry. Export file or Enter information! </div>
<ol>
<?
$r=@mysql_query("select * from ps_packets where address1 = '' order by date_received");
while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
?>
<li style="background-color:#CCCCCC;"><a href="/otd/order.php?packet=<?=$d[packet_id]?>">Packet <?=$d[packet_id]?> <?=$d[affidavit_status]?> <?=$d[filing_status]?></li>
<?
}
?>
</ol>
</td>
</tr></table>
<script>document.title='Internal Data Quality Control'</script>