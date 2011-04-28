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
<td bgcolor="AFEEEE">Date</td>
<td bgcolor="AFEEEE">Received to Dispatch</td>
<td bgcolor="AFEEEE">Dispatch to Close</td>
<td bgcolor="5DFC0A">Webservice Queue</td>
<td bgcolor="FF8000">New OTD</td>
<td bgcolor="FF8000">Active OTD</td>
<td bgcolor="FF8000">Quality Control OTD</td>
<td bgcolor="FF8000">Mailroom OTD</td>
<td bgcolor="FF8000">Blackhole OTD</td>
<td bgcolor="FBEC5D">New EV</td>
<td bgcolor="FBEC5D">Active EV</td>
<td bgcolor="FBEC5D">Blankhole EV</td>
<td bgcolor="EE8262">New S</td>
<td bgcolor="EE8262">Active S</td>
<td bgcolor="EE8262">In Progress S</td>
<td bgcolor="8EE5EE">Watchdog Active</td>
<td bgcolor="8EE5EE">Watchdog Blackhole</td>
<td bgcolor="5DFC0A">30 Day Volume</td>
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
