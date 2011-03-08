<?
mysql_connect();
mysql_select_db('core');
// manage attachment functions

// database settings

// disk / file system settings


$r=@mysql_query("select * from attachment where id = '$_GET[id]' ");
$d=mysql_fetch_array($r,MYSQL_ASSOC);
?>
<table>
<tr>
<td><b>id</b></td>
<td><?=$d[id];?></td>
</tr>
<tr>
<td><b>instruction_id</b></td>
<td><?=$d[instruction_id];?></td>
</tr>
<tr>
<td><b>instruction_id</b></td>
<td><?=$d[instruction_id];?></td>
</tr>
<tr>
<td><b>user_id</b></td>
<td><?=$d[user_id];?></td>
</tr>
<tr>
<td><b>server_id</b></td>
<td><?=$d[server_id];?></td>
</tr>
<tr>
<td><b>processed</b></td>
<td><?=$d[processed];?></td>
</tr>
<tr>
<td><b>url</b></td>
<td><?=$d[url];?></td>
</tr>
<tr>
<td><b>path</b></td>
<td><?=$d[path];?></td>
</tr>
<tr>
<td><b>description</b></td>
<td><?=$d[description];?></td>
</tr>
<tr>
<td><b>type</b></td>
<td><?=$d[type];?></td>
</tr>
<tr>
<td><b>pages</b></td>
<td><?=$d[pages];?></td>
</tr>
<tr>
<td><b>absolute_url</b></td>
<td><?=$d[absolute_url];?></td>
</tr>
<tr>
<td><b>status</b></td>
<td><?=$d[status];?></td>
</tr>
</table>
<iframe src="<?=$d[absolute_url];?>" width="100%" height="300px">