<center>
<?
mysql_connect();
mysql_select_db('service');
function dbOUT($str){
$str = stripslashes($str);
return $str;
}
?>
<h1>Prior Reports</h1>
<? 
$r=@mysql_query("select * from finalReport");
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
?>
<div style="border:solid 2px #00ff00;">
<div><?=$d[completed]?></div>
<?=dbOUT($d[report]);?>
</div>
<br>
<?
}
?>
</center>