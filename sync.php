<?
mysql_connect();
mysql_select_db('core');

@mysql_query("update sync set status='automated rescan requested' where to_id = '$_GET[packet]' ");

echo "Rescan Requested";

?>
<a href="http://data.mdwestserve.com/syncPackets.php">Process Rescan</a>




