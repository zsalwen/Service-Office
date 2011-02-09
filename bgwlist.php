<?
include 'common.php';
mysql_connect();
mysql_select_db('service');
$q="SELECT closeOut, client_file, packet_id, service_status FROM ps_packets WHERE attorneys_id='70' AND closeOut <> '0000-00-00'";
$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
echo "<table align='center' style='border-collapse: collapse;' border='1'><tr><td align='center'>BGW #</td><td align='center'>MDWS #</td><td align='center'>Completion Date</td><td align='center'>Status</td></tr>";
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	echo "<tr><td align='center'>$d[client_file]</td><td align='center'>$d[packet_id]</td><td align='center'>$d[closeOut]</td><td align='center'>$d[service_status]</td></tr>";
}
?>