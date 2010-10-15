<?
include 'common.php';




$q1="select packet_id, mail_status, closeOut from ps_packets where process_status = 'READY TO MAIL' order by closeOut, mail_status, packet_id";

$q2="select packet_id, mail_status, process_status, closeOut from ps_packets where mail_status='Printed Awaiting Postage' order by closeOut, mail_status, packet_id";

$q3="select packet_id, mail_status, process_status, closeOut from ps_packets where gcStatus='MAILED' AND mail_status <> 'Mailed First Class and Certified Return Receipt' order by closeOut, mail_status, packet_id";


?>
<table>
	<tr>
		<td valign="top"><b><?=$q1;?></b><ol><?
$r=@mysql_query($q1);
while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	echo "<li>$d[closeOut] $d[packet_id]) Mail Status: $d[mail_status]</li>";
}
		?></ol></td>
		<td valign="top"><b><?=$q2;?></b><ol><?
$r=@mysql_query($q2);
while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	echo "<li>$d[closeOut] $d[packet_id]) Process Status: $d[process_status]</li>";
}
		?></ol></td>
		<td valign="top"><b><?=$q3;?></b><ol><?
$r=@mysql_query($q3);
while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	echo "<li>$d[closeOut] $d[packet_id]) Process Status: $d[process_status] Mail Status: $d[mail_status]</li>";
}
		?></ol></td>
	</tr>
</table>
