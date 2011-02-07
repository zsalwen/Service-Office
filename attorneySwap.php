<?
if($_COOKIE[psdata][level]=='Operations'){
mysql_connect();
mysql_select_db('core');
if($_GET[id]){
if ($_GET[core] == 'evictionPackets'){
$field = "eviction_id";
}else{
$field = "packet_id";
}
$q="update $_GET[core] set attorneys_id = '$_GET[attid]' where $field = '$_GET[id]' ";
echo $q.'<br>';
//@mysql_query($q) or die(mysql_error());
}
?>
<form method="GET">
<table border="1">
 <tr>
  <td>Product</td>
  <td></td>
  <td>Attorney ID</td>
  <td>Packet ID</td>
 </tr>
 <tr>
  <td>Presale</td>
  <td><input type="radio" name="core" value="ps_packets"></td>
  <td><input name="attid"></td>
  <td><input name="id"></td>
 </tr>
 <tr>
  <td>Eviction</td>
  <td><input type="radio" name="core" value="evictionPackets"></td>
  <td></td>
  <td></td>
 </tr>
 <tr>
  <td>Standard</td>
  <td><input type="radio" name="core" value="standard_packets"></td>
  <td></td>
  <td></td>
 </tr>
</table>
<input type="submit">
</form>
<hr>
<table border="1">
<tr>
<td>Attorney ID</td>
<td>Attorney Name</td>
</tr>
<?
$r=@mysql_query("select attorneys_id, display_name from attorneys order by attorneys_id");
while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
?>
<tr>
<td><?=$d[attorneys_id];?></td>
<td><?=$d[display_name];?></td>
</tr>
<? } ?>
</table>
<?
}else{
header('Location: http://mdwestserve.com');
}
?>