<?
include 'lock.php';
mysql_connect();
mysql_select_db('apache');
$r=@mysql_query("select counter, lastTime, message from apacheErrors order by counter DESC, lastTime DESC");
?>
<table>
<? while($d=mysql_fetch_array($r,MYSQL_ASSOC)){ ?>
<tr>
<td><?=$d[counter];?></td>
<td><?=$d[lastTime]?></td>
<td><?=$d[message]?></td>
</tr>
<? } ?>
</table>
<? mysql_close(); ?>