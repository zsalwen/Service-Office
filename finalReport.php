<center>
<?
mysql_connect();
mysql_select_db('core');
function dbIN($str){
$str = trim($str);
$str = addslashes($str);
$str = strtolower($str);
$str = ucwords($str);
return $str;
}
function dbOUT($str){
$str = stripslashes($str);
return $str;
}
$userID = $_COOKIE[psdata][user_id];
$email = $_COOKIE[psdata][email];
if ($_POST[report]){
@mysql_query("insert into finalReport (userID, completed, report) values ('$userID', NOW(), '".dbIN($_POST[report])."')");
$headers = "From: $email \n";
mail('patrick@mdwestserve.com','Project Report',dbIN($_POST[report]),$headers);
mail('zach@mdwestserve.com','Project Report',dbIN($_POST[report]),$headers);
}

?>
<h1>Project Report</h1>
<form method="post">
<div style="border:solid 2px #ff0000;">
<div><?=date('r');?></div>
<textarea name="report" cols="80" rows="20"></textarea><br><input type="submit" value="Save Report">
</div>
</form>
<h1>Prior Reports</h1>
<? 
$r=@mysql_query("select * from finalReport where userID = '$userID'");
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