<?
include 'lock.php';
mysql_connect();
mysql_select_db('apache');
if($_GET[done]){ @mysql_query("delete from apacheErrors where id = '$_GET[done]' "); }
if($_GET[zach]){ @mysql_query("update apacheErrors set status = 'Assigned to Zach'  where id = '$_GET[zach]' "); }
if($_GET[runner]){ @mysql_query("update apacheErrors set status = 'Assigned to Runner'  where id = '$_GET[runner]' "); }
if($_GET[patrick]){ @mysql_query("update apacheErrors set status = 'Assigned to Patrick'  where id = '$_GET[patrick]' "); }
$r=@mysql_query("select id, counter, lastTime, message,server,status from apacheErrors order by counter DESC, lastTime DESC");
?>
<meta http-equiv="refresh" content="60;url=http://staff.mdwestserve.com/dailyITtodo.php"> 
<h1>The first job in IT is error detection and correction.</h1>
<h3><?=date('r');?></h3>
<table border="1">
<? while($d=mysql_fetch_array($r,MYSQL_ASSOC)){ ?>
<? if(!$d[status]){ ?> <tr bgcolor="#FF6666"> <? }else{ ?> <tr>  <? }?>
<td><?=$d[counter];?></td>
<td><?=$d[server]?></td>
<td><?=$d[status]?></td>
<td><?=$d[lastTime]?></td>
<td><?=$d[message]?>, <a href="?done=<?=$d[id]?>">Remove</a>, <a href="?zach=<?=$d[id]?>">Zach</a>, <a href="?runner=<?=$d[id]?>">Runner</a>, <a href="?patrick=<?=$d[id]?>">Patrick</a> </td>
</tr>
<? } ?>
</table>
<? mysql_close(); ?>