<?
mysql_connect();
mysql_select_db('intranet');

$r=@mysql_query("select distinct batchID from schedule_items"):
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){

echo "<li>$d[batchID]</li>";


}

?>