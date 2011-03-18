<?
include 'lock.php';
mysql_connect();
mysql_select_db('apache');
if($_GET[done]){ @mysql_query("delete from apacheErrors where id = '$_GET[done]' "); }
if($_GET[all]){ @mysql_query("delete from apacheErrors"); }
if($_GET[status]){ @mysql_query("update apacheErrors set status = '$_GET[status]'  where id = '$_GET[done]' "); }
$r=@mysql_query("select id, counter, lastTime, message from apacheErrors order by counter DESC, lastTime DESC");
?>
<meta http-equiv="refresh" content="60;url=http://staff.mdwestserve.com/dailyITtodo.php"> 
<h1>The first job in IT is error detection and correction.</h1>
<h3><?=date('r');?></h3>
 <a href="?all=1">clear all</a>
<table border="1">
<? while($d=mysql_fetch_array($r,MYSQL_ASSOC)){ ?>
<tr>
<td><?=$d[counter];?></td>
<td><?=$d[server]?></td>
<td><?=$d[lastTime]?></td>
<td><?=$d[message]?>, <a href="?done=<?=$d[id]?>">clear</a></td>
</tr>
<? } ?>
</table>
<? mysql_close(); ?>