<?
mysql_connect();
mysql_select_db('core');
$packet=$_GET[packet];
$q="SELECT otd, client_file FROM ps_packets WHERE packet_id='$packet'";
$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
$d=mysql_fetch_array($r,MYSQL_ASSOC);
$otdStr2=str_replace('http://mdwestserve.com','',$d[otd]);
$otdStr2=str_replace('PS_PACKETS/','data/service/orders/',$otdStr2);
if (!strpos($otdStr,'mdwestserve.com')){
	$otdStr="http://mdwestserve.com/".$otdStr;
}
if (file_exists($otdStr2)) {
	// We'll be outputting a PDF
	header('Content-type: application/pdf');
	// It will be called '(Packet $packet) $client_file.pdf'
	header('Content-Disposition: attachment; filename="(Packet '.$packet.') '.$d[client_file].'.pdf"');
	// The PDF source is in original.pdf
	readfile($otdStr2);
}else{
	echo "<center>".$otdStr2." does not exist.</center><br>";
}
?>