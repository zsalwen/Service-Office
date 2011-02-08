<?
mysql_connect();
mysql_select_db('service');
ini_set('error_reporting',E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors','Off');
if ($_POST[name]){
@mysql_query("insert into attribute (name,description) values ( '$_POST[name]','$_POST[description]')");
}
if ($_GET[undeleteAttribute]){
  @mysql_query("update attribute set status='active' where id = '$_GET[undeleteAttribute]' ");
}
if ($_GET[deleteAttribute]){
  @mysql_query("update attribute set status='inactive' where id = '$_GET[deleteAttribute]' ");
}
?>
<style>
small { color:#FF0000; }
</style>
<form method="post">
This is the form to add new attributes<br>
<table>
<tr>
 <td>Name</td>
  <td>Description</td>
</td>
<tr>
  <td><input name="name"></td>
  <td><input name="description"></td>
</td>
</table>
<input type="submit">
</form>
<h3>Current Attributes:</h3>
<table border="1">
 <tr>
  <td>ID</td>
  <td>Name</td>
  <td>Description</td>
 </tr>
<? 
$r=@mysql_query("select * from attribute where status='active' order by name");
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){ ?>
 <tr>
  <td><?=$d[id];?></td>
  <td><?=$d[name];?></td>
  <td><?=$d[description];?></td>
 </tr>
<? } ?>
</table>