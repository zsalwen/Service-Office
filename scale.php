<?
include 'database.class.php';
include 'mail.class.php';
$file = "http://mdwestserve.com//PS_PACKETS/September 18 2009 11:04:33-09-156656P.pdf/09-156656P.pdf";
$db = new database;
$db -> database = "core";
$db -> connect();
$r=@mysql_query("select packet_id, otd, attorneys_id from ps_packets where otd <> '' order by packet_id DESC limit 0,15");
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	// postage calculation
	$mail = new postage;
	$mail -> pdf = $d[otd];
	$weight = $mail -> weight();
	$mail -> weight = $weight;
	$cost = $mail -> cost();
	// end
	echo "<li>$d[packet_id] - $d[attorneys_id] - [[ $weight ]] $cost</li>";
}
?>
