<?
include 'lock.php';
mysql_connect();
mysql_select_db('apache');
if($_GET[done]){ @mysql_query("delete from apacheErrors where id = '$_GET[done]' "); }
if($_GET[clear]){ @mysql_query("delete from apacheErrors where counter = '1' "); }
if($_GET[zach]){ @mysql_query("update apacheErrors set status = 'Assigned to Zach'  where id = '$_GET[zach]' "); }
if($_GET[runner]){ @mysql_query("update apacheErrors set status = 'Assigned to Runner'  where id = '$_GET[runner]' "); }
if($_GET[patrick]){ @mysql_query("update apacheErrors set status = 'Assigned to Patrick'  where id = '$_GET[patrick]' "); }
?>
<style>
td{font-size:11px;}
</style>
<meta http-equiv="refresh" content="60;url=http://staff.mdwestserve.com/dailyITtodo.php?server=<?=$_GET[server]?>&message=<?=$_GET[message]?>"> 
<div>The apache error log is managed by Runner, runner@hwestauctions.com</div>
<div><b>Last 10 Errors</b></div>
<table border="1">
<? 
$r=@mysql_query("select id, counter, lastTime, message,server,status from apacheErrors order by lastTime DESC limit 0,10");
while($d=mysql_fetch_array($r,MYSQL_ASSOC)){ ?>
<? if(!$d[status]){ ?> <tr bgcolor="#FF6666"> <? }else{ ?> <tr>  <? }?>
<td><?=$d[counter];?></td>
<td><?=$d[server]?></td>
<td><?=$d[status]?></td>
<td><?=$d[lastTime]?></td>
<td><?=$d[message]?></td>
</tr>
<? } ?>
</table>
<div>Most Occurring Errors (Limited by Search) <a href="?clear=1">Clear All 1 Count Errors</div>
<table border="1">
<form>
<tr>
<td></td>
<td>Server</td>
<td></td><td></td>
<td>Message</td>
</tr>
<tr>
<td></td>
<td><select name="server">
<option value=''>ALL</option>
<option value='lb'>MDWS-LB</option>
<option value='mdws1'>MDWS-1</option>
<option value='mdws2'>MDWS-2</option>
<option value='hwa1'>HWA-1</option>
</select></td>
<td></td><td></td>
<td><input name="message"> <input type='submit' value='Set Filter'></td>
</tr>
</form>

<? 
$r=@mysql_query("select id, counter, lastTime, message,server,status from apacheErrors where server LIKE '%$_GET[server]%' and message LIKE '%$_GET[message]%' order by counter DESC, lastTime DESC");
while($d=mysql_fetch_array($r,MYSQL_ASSOC)){ ?>
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