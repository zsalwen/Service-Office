<?
include 'lock.php';
mysql_connect();
mysql_select_db('apache');
if($_GET[done]){ @mysql_query("delete from apacheErrors where id = '$_GET[done]' "); }
if($_GET[clear]){ @mysql_query("delete from apacheErrors where counter = '1' "); }
if($_GET[macro]=='1'){ 
@mysql_query("delete from apacheErrors where message LIKE  '%patrick%' ");
@mysql_query("delete from apacheErrors where message LIKE  '%hosting%' ");
}
if($_GET[macro]=='2'){ 
@mysql_query("delete from apacheErrors where message LIKE  '%thirdParty%' ");
}
if($_GET[macro]=='3'){ 
@mysql_query("delete from apacheErrors where message LIKE  '%[notice]%' ");
}
if($_GET[zach]){ @mysql_query("update apacheErrors set status = 'Talk to Zach'  where id = '$_GET[zach]' "); }
if($_GET[runner]){ @mysql_query("update apacheErrors set status = 'Talk to Runner'  where id = '$_GET[runner]' "); }
if($_GET[patrick]){ @mysql_query("update apacheErrors set status = 'Talk to Patrick'  where id = '$_GET[patrick]' "); }
?>
<style>
td{font-size:11px;white-space:nowrap;}
</style>
<meta http-equiv="refresh" content="300;url=http://staff.mdwestserve.com/dailyITtodo.php?server=<?=$_GET[server]?>&message=<?=$_GET[message]?>"> 
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
<div>Most Occurring Errors (Limited by Search) 
<a href="?clear=1">Clear All 1 Count Errors</a> 
<a href="?macro=1">Clear Macro #1 [patrick]</a>
<a href="?macro=2">Clear Macro #2 [thirdParty]</a>
<a href="?macro=3">Clear Macro #3 [notice]</a>
</div>
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
<td><?=$d[message]?>, <a href="?done=<?=$d[id]?>&server=<?=$_GET[server];?>&message=<?=$_GET[message];?>">Remove</a>, <a href="?zach=<?=$d[id]?>&server=<?=$_GET[server];?>&message=<?=$_GET[message];?>">Zach</a>, <a href="?runner=<?=$d[id]?>&server=<?=$_GET[server];?>&message=<?=$_GET[message];?>">Runner</a>, <a href="?patrick=<?=$d[id]?>&server=<?=$_GET[server];?>&message=<?=$_GET[message];?>">Patrick</a> </td>
</tr>
<? } ?>
</table>
<? mysql_close(); ?>