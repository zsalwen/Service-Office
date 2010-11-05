<?
include 'common.php';
function wash($str){
	$str=trim($str);
	$str=strtoupper($str);
	$str=str_replace('#','NO.',$str);
	return $str;
}
$packet = $_GET[packet];
if ($_POST[uspsVerify]){
	@mysql_query("UPDATE evictionPackets set uspsVerify = '".$_COOKIE[psdata][name]."' where eviction_id = '$_GET[packet]'");
	timeline($_GET[packet],$_COOKIE[psdata][name]." verfied addresses via USPS");
	hardLog('verfied addresses via USPS for packet '.$_GET[packet],'user');
	if ($_GET[close]){?><script>self.close()</script><? }else{
	?><script>window.parent.location.href='order.php?packet=<?=$_GET[packet]?>';</script><? }
}
$r=@mysql_query("SELECT * FROM evictionPackets where eviction_id = '$packet' ");
$d=mysql_fetch_array($r, MYSQL_ASSOC);
if(!$d[uspsVerify]){
?>
<iframe src="http://staff.mdwestserve.com/ev/usps.php?address=<?=$d[address1]?>&city=<?=$d[city1]?>&state=<?=$d[state1]?>" width="300" height="100"></iframe>
<form method="post"><input name="uspsVerify" type="submit" value="I, <?=$_COOKIE[psdata][name]?>, Confirm Valid USPS Addresses" /></form><a href="?packet=<?=$_GET[packet]?>">Reload Supernova for Packet <?=$_GET[packet]?></a>
<? }?>
<? include 'footer.php'; ?>