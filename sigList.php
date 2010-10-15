<?
mysql_connect();
mysql_select_db('core');
function id2company($id){
	$q="SELECT company FROM ps_users WHERE id = '$id'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	return $d[company];
}
$q="SELECT DISTINCT name FROM `ps_signatory` ORDER BY packetID DESC";
$r=@mysql_query($q);
echo "<table border='1' align='center' style='border-collapse:collapse; border-style:solid 1px;><tr><td colspan='3' align='center'>Foreclosures</td></tr>";
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$r2=@mysql_query("SELECT serverID, packetID from ps_signatory WHERE name='$d[name]'");
	$d2=mysql_fetch_array($r2,MYSQL_ASSOC);
	$r3=@mysql_query("SELECT packetID from ps_signatory WHERE name='$d[name]' AND packetID <> '$d2[packetID]'");
	$d3=mysql_fetch_array($r2,MYSQL_ASSOC);
	echo "<tr><td>$d[name]</td><td>$d2[packetID],$d3[packetID]</td><td>".id2company($d2[serverID])."</td></tr>";
}
echo "<tr><td colspan='3' align='center'>Evictions</td></tr>";
$r=@mysql_query("SELECT DISTINCT name FROM `evictionSignatory` ORDER BY evictionID DESC");
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$r2=@mysql_query("SELECT serverID, evictionID from evictionSignatory WHERE name='$d[name]'");
	$d2=mysql_fetch_array($r2,MYSQL_ASSOC);
	echo "<tr><td>$d[name]</td><td>$d2[evictionID]</td><td>".id2company($d2[serverID])."</td></tr>";
}
echo "</table>";
?>