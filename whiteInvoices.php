<?
include 'common.php';
echo "<table align='center' border='1' style='border=collapse:collapse;'><tr><td align='center' style='font-size:16px;' colspan='12'>WHITE FILES MISSING INVOICES</td></tr><tr>";
$q="SELECT packet_id FROM ps_packets WHERE attorneys_id='3' ORDER BY packet_id ASC";
$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
$i=0;
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$q1="SELECT affidavitID, method from ps_affidavits WHERE packetID='$d[packet_id]' AND method LIKE '%aff%'";
	$r1=@mysql_query($q1) or die ("Query: $q1<br>".mysql_error());
	$d1=mysql_fetch_array($r1,MYSQL_ASSOC);
	if ($d1[method] == ''){$i++;
		echo "<td align='center'>$d[packet_id]</td>";
		if ($i == 12){
			echo "</tr><tr>";
			$i=0;
		}
	}
}
$q="SELECT eviction_id FROM evictionPackets WHERE attorneys_id='3' ORDER BY eviction_id ASC";
$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$q1="SELECT affidavitID, method from ps_affidavits WHERE packetID='EV$d[eviction_id]' AND method LIKE '%aff%'";
	$r1=@mysql_query($q1) or die ("Query: $q1<br>".mysql_error());
	$d1=mysql_fetch_array($r1,MYSQL_ASSOC);
	if ($d1[method] == ''){$i++;
		echo "<td align='center'>EV$d[eviction_id]</td>";
		if ($i == 12){
			echo "</tr><tr>";
			$i=0;
		}
	}
}
echo "</table>";
?>