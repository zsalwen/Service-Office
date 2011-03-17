<?
include 'lock.php';
mysql_connect();
mysql_select_db('apache');
if($_GET[done]){ @mysql_query("delete from apacheErrors where id = '$_GET[done]' "); 
$r=@mysql_query("select id, counter, lastTime, message from apacheErrors order by counter DESC, lastTime DESC");
?>
<table>
<? while($d=mysql_fetch_array($r,MYSQL_ASSOC)){ ?>
<tr>
<td><?=$d[counter];?></td>
<td><?=$d[lastTime]?></td>
<td><?=$d[message]?></td>
<td><a href="?done=<?=$d[id]?>">mark fixed</a></td>
</tr>
<? } ?>
</table>
<? mysql_close(); ?>