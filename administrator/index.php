<?
if (!$_COOKIE[admin][name]){
header('Location: http://staff.mdwestserve.com/adminstrator/login.php');
}
?>
<table width="100%"><tr>
foreach (glob("modules/*.php") as $filename)
{
     echo "<td><a href='$filename' target='box'>".strtoupper(str_replace('.php','',$filename))."</a></td>";
}
?>
</tr></table>
<center><iframe name="box" id ="box" style="width:99%; height:99%;"></iframe></center>