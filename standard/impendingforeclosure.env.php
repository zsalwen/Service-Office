<?
mysql_connect();
mysql_select_db('core');

$r=@mysql_query("select * from standard_packets where packet_id = '$_GET[packet]'") or die(mysql_error());
$d=mysql_fetch_array($r,MYSQL_ASSOC);
$name = "OCCUPANT";
$line1 = $d["address1"];
$csz = $d["city1"].', '.$d["state1"].' '.$d["zip1"];
$cord = "S".$_GET[packet];

	?>
	<table style='page-break-after:always' align='center'><tr><td>
	<IMG SRC="http://staff.mdwestserve.com/barcode.php?barcode=<?=$cord?>&width=400&height=40"><br>
	<img  src="http://staff.mdwestserve.com/standard/impendingforeclosure.jpg.php?name=<?=strtoupper($name)?>&line1=<?=strtoupper(str_replace('#','no. ',$line1))?>&csz=<?=strtoupper($csz)?>">
	</td></tr></table>



