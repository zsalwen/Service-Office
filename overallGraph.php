<?
mysql_connect();
mysql_select_db('core');
$r=@mysql_query("select * from overallGraph order by id desc limit 0,30");
?>
<style>
body { padding:0px; margin:0px; }
td { text-align:center; padding:2px; }
table { border-collapse: collapse; }
</style>
<table border="1" width="100%">
<tr>
<td bgcolor="AFEEEE"><b>Date</b></td>
<td bgcolor="AFEEEE"><b>Received to Dispatch</b></td>
<td bgcolor="AFEEEE"><b>Dispatch to Close</b></td>
<td bgcolor="5DFC0A"><b>Webservice Queue</b></td>
<td bgcolor="FF8000"><b>New OTD</b></td>
<td bgcolor="FF8000"><b>Active OTD</b></td>
<td bgcolor="FF8000"><b>Quality Control OTD</b></td>
<td bgcolor="FF8000"><b>Mailroom OTD</b></td>
<td bgcolor="FF8000"><b>Blackhole OTD</b></td>
<td bgcolor="FBEC5D"><b>New EV</b></td>
<td bgcolor="FBEC5D"><b>Active EV</b></td>
<td bgcolor="FBEC5D"><b>Blankhole EV</b></td>
<td bgcolor="EE8262"><b>New S</b></td>
<td bgcolor="EE8262"><b>Active S</b></td>
<td bgcolor="EE8262"><b>In Progress S</b></td>
<td bgcolor="8EE5EE"><b>Watchdog Active</b></td>
<td bgcolor="8EE5EE"><b>Watchdog Blackhole</b></td>
<td bgcolor="5DFC0A"><b>30 Day Volume</b></td>
</tr>
<?
while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
?>
<tr>
<td bgcolor="AFEEEE"><?=$d[date]?></td>
<td bgcolor="AFEEEE"><?=$d[dispatch]?></td>
<td bgcolor="AFEEEE"><?=$d[closed]?></td>
<td bgcolor="5DFC0A"><?=$d[pre]?></td>
<td bgcolor="FF8000"><?=$d[otdN]?></td>
<td bgcolor="FF8000"><?=$d[otdA]?></td>
<td bgcolor="FF8000"><?=$d[otdQ]?></td>
<td bgcolor="FF8000"><?=$d[otdM]?></td>
<td bgcolor="FF8000"><?=$d[otdB]?></td>
<td bgcolor="FBEC5D"><?=$d[evN]?></td>
<td bgcolor="FBEC5D"><?=$d[evA]?></td>
<td bgcolor="FBEC5D"><?=$d[evB]?></td>
<td bgcolor="EE8262"><?=$d[sN]?></td>
<td bgcolor="EE8262"><?=$d[sA]?></td>
<td bgcolor="EE8262"><?=$d[sI]?></td>
<td bgcolor="8EE5EE"><?=$d[wa]?></td>
<td bgcolor="8EE5EE"><?=$d[wb]?></td>
<td bgcolor="5DFC0A"><?=$d[vol]?></td>
</tr>
<?
}
mysql_close();
?>
