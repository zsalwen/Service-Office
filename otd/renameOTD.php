<?
function washURI2SVR($uri){
	$uri = str_replace('http://mdwestserve.com/portal/','',$uri);
	return $uri;
}
function washURI2NEW($uri){
	$uri = str_replace('#','',$uri);
	return $uri;
}
function washURI2SAVE($uri){
	$uri = str_replace('/data/service/orders/','/PS_PACKETS/',$uri);
	return $uri;
}

function hardLog($str,$type){
	if ($type == "user"){
		$log = "/logs/user.log";
	}
	// this is important code 
	// this is important code 
	// this is important code 
	if ($log){
		error_log(date('h:iA j/n/y')." ".$_COOKIE[psdata][name]." ".trim($str)."\n", 3, $log);
	}
	// this is important code 
}

if ($_GET[packet]){
mysql_connect();
mysql_select_db('service');
$r=@mysql_query("select otd from ps_packets where packet_id = '$_GET[packet]'");
$d=mysql_fetch_array($r,MYSQL_ASSOC);
$old = washURI2SVR($d[otd]);
$new = washURI2NEW($old);

echo "Moving<br> <b>$old</b><br> to <br><b>$new</b><br>";
	hardLog('formatted OTD link for '.$_GET[packet],'user');


system("cp '$old' '$new'");

$save = "http://mdwestserve.com".washURI2SAVE($new);
$save=str_replace("http://mdwestserve.comhttp://mdwestserve.com","http://mdwestserve.com",$save);
$save=str_replace("http://mdwestserve.comPS_PACKETS","http://mdwestserve.com/PS_PACKETS",$save);
echo "Updating OTD to <br><b>$save</b><br>";
@mysql_query("update ps_packets set otd='$save' where packet_id = '$_GET[packet]'");
if (!$_GET[test]){
?>
<script>alert('OTD LINK FIXED'); window.location.href='order.php?packet=<?=$_GET[packet]?>'</script>
<?
}else{
?>
<script>alert('OTD LINK FIXED'); window.location.href='order.php';</script>
<?
}
}
?>