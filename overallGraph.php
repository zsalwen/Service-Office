<?
mysql_connect();
mysql_select_db('core');
$r=@mysql_query("select * from overallGraph order by id desc limit 0,30");
?>
<table>
<tr>
<td>Date</td>
<td>Received to Dispatch</td>
<td>Dispatch to Close</td>
<td>Webservice Queue</td>
<td>New OTD</td>
<td>Active OTD</td>
<td>Quality Control OTD</td>
<td>Mailroom OTD</td>
<td>Blackhole OTD</td>
<td>New EV</td>
<td>Active EV</td>
<td>Blankhole EV</td>
<td>New S</td>
<td>Active S</td>
<td>In Progress S</td>
<td>Watchdog Active</td>
<td>Watchdog Blackhole</td>
<td>30 Day Volume</td>
</tr>
<?
while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
?>
<tr>
<td><?=$d[date]?></td>
<td><?=$d[dispatch]?></td>
<td><?=$d[closed]?></td>
<td><?=$d[pre]?></td>
<td><?=$d[otdN]?></td>
<td><?=$d[otdA]?></td>
<td><?=$d[otdQ]?></td>
<td><?=$d[otdM]?></td>
<td><?=$d[otdB]?></td>
<td><?=$d[evN]?></td>
<td><?=$d[evA]?></td>
<td><?=$d[evB]?></td>
<td><?=$d[sN]?></td>
<td><?=$d[sA]?></td>
<td><?=$d[sI]?></td>
<td><?=$d[wa]?></td>
<td><?=$d[wb]?></td>
<td><?=$d[vol]?></td>
</tr>
<?
}
mysql_close();
?>
