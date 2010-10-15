<?
mysql_connect();
mysql_select_db('core');
// loop through watchDog table to find closed cases
$r = @mysql_query("select details from marylandCaseData where status = 'Active' order by RAND() limit 0,3");
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	echo "<div style='border:solid 10px #ff0;'>$d[details]</div>";
	echo "<div style='border:solid 10px #0ff;'><pre>".htmlspecialchars($d[details])."</pre></div>";
}


?>