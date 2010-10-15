<?
include 'common.php';
include 'lock.php';

echo "Service Review for Packet $_GET[packet]<br>";



$r=@mysql_query("SELECT timeline FROM ps_packets where packet_id='$_GET[packet]'");
$d=mysql_fetch_array($r, MYSQL_ASSOC);

echo "<div>".$d[timeline]."</div>";


?> 
<style>
div { font-size:12px; }
body { background-color:FFFFFF; }</style>