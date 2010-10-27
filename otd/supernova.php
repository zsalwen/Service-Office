<?
include 'common.php';
function wash($str){
	$str=trim($str);
	$str=strtoupper($str);
	$str=str_replace('#','NO.',$str);
	return $str;
}
function checkVerify($address){
	$r=@mysql_query("SELECT * FROM addressVerify where address like '%".addslashes($address)."%' LIMIT 0,1 ");
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	if ($d[by] != ''){
		return true;
	}else{
		return false;
	}
}
function isVerified($packet){
	$r=@mysql_query("SELECT address1, address1a, address1b, address1c, address1d, address1e, city1, city1a, city1b, city1c, city1d, city1e, state1, state1a, state1b, state1c, state1d, state1e, zip1, zip1a, zip1b, zip1c, zip1d, zip1e FROM ps_packets where packet_id = '$packet' LIMIT 0,1 ");
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	$i=0;
	//if address is not verified, increment counter
	$add=strtoupper($d[address1].', '.$d[city1].', '.$d[state1].' '.$d[zip1]);
	if (($d[address1] != '') && (checkVerify($add) !== true)){$i++;}
	foreach(range('a','e') as $letter){
		$add=strtoupper($d["address1$letter"].', '.$d["city1$letter"].', '.$d["state1$letter"].' '.$d["zip1$letter"]);
		if (($d["address1$letter"] != '') && (checkVerify($add) !== true)){$i++;}
	}
	return $i;
}
$packet = $_GET[packet];
$query='';
if ($_POST[uspsVerify]){
	@mysql_query("INSERT into addressVerify (address, by, insertDate) VALUES ('".addslashes($_POST[add])."', '".$_COOKIE[psdata][name]."', NOW())");
	//@mysql_query("UPDATE ps_packets set uspsVerify = '".$_COOKIE[psdata][name]."' where packet_id = '$_GET[packet]'")
	timeline($_GET[packet],$_COOKIE[psdata][name]." verified address1 via USPS");
	hardLog('verified address1 via USPS for packet '.$_GET[packet],'user');
}
foreach(range('a','e') as $letter){
	if ($_POST["uspsVerify$letter"]){
		@mysql_query("INSERT into addressVerify (address, by, insertDate) VALUES ('".addslashes($_POST["add$letter"])."', '".$_COOKIE[psdata][name]."', NOW())");
		//@mysql_query("UPDATE ps_packets set uspsVerify$letter = '".$_COOKIE[psdata][name]."' where packet_id = '$_GET[packet]'")
		timeline($_GET[packet],$_COOKIE[psdata][name]." verified address1$letter via USPS");
		hardLog("verified address1$letter via USPS for packet $_GET[packet]",'user');
	}
}
$isVerified=isVerified($packet);
echo "<script>alert('$isVerified')</script>";
$r=@mysql_query("SELECT * FROM ps_packets where packet_id = '$packet' ");
$d=mysql_fetch_array($r, MYSQL_ASSOC);
if ($_GET[close] && ($isVerified == 0)){
	if ($d[uspsVerify] == ''){
		@mysql_query("UPDATE ps_packets set uspsVerify = '".$_COOKIE[psdata][name]."' where packet_id = '$_GET[packet]'");
	}
	echo "<script>self.close()</script>";
}elseif($isVerified == 0){
	if ($d[uspsVerify] == ''){
		@mysql_query("UPDATE ps_packets set uspsVerify = '".$_COOKIE[psdata][name]."' where packet_id = '$_GET[packet]'");
	}
	echo "<script>window.parent.location.href='order.php?packet=$_GET[packet]';</script>";
}
if(($isVerified != 0) && ($d[uspsVerify] == '')){
?>
<form method="post">
<? if($d[address1]){ ?>
<iframe src="http://service.mdwestserve.com/usps.php?address=<?=$d[address1]?>&city=<?=$d[city1]?>&state=<?=$d[state1]?>" width="300" height="100"></iframe>
<? }?>
<? if($d[address1a]){ ?>
<iframe src="http://service.mdwestserve.com/usps.php?address=<?=$d[address1a]?>&city=<?=$d[city1a]?>&state=<?=$d[state1a]?>" width="300" height="100"></iframe>
<? }?>
<? if($d[address1b]){ ?>
<iframe src="http://service.mdwestserve.com/usps.php?address=<?=$d[address1b]?>&city=<?=$d[city1b]?>&state=<?=$d[state1b]?>" width="300" height="100"></iframe>
<? }?>
<? if($d[address1c]){ ?>
<iframe src="http://service.mdwestserve.com/usps.php?address=<?=$d[address1c]?>&city=<?=$d[city1c]?>&state=<?=$d[state1c]?>" width="300" height="100"></iframe>
<? }?>
<? if($d[address1d]){ ?>
<iframe src="http://service.mdwestserve.com/usps.php?address=<?=$d[address1d]?>&city=<?=$d[city1d]?>&state=<?=$d[state1d]?>" width="300" height="100"></iframe>
<? }?>
<? if($d[address1e]){ ?>
<iframe src="http://service.mdwestserve.com/usps.php?address=<?=$d[address1e]?>&city=<?=$d[city1e]?>&state=<?=$d[state1e]?>" width="300" height="100"></iframe>
<? }
if ($_GET[matrix]){
	$matrix = " (and proceed with MAIL ONLY)";
}else{
	$matrix="";
}
echo "<table border='1'>";
if ($d["address1"]){
	$makeLnL = strtoupper($d[address1].', '.$d[city1].', '.$d[state1].' '.$d[zip1]);
	echo "<tr><td>$makeLnL</td>";
	if (checkVerify($makeLnL) !== true){
		echo "<td><input type='hidden' name='add' value='$makeLnL'><input name='uspsVerify' type='submit' value='I, ".$_COOKIE[psdata][name].", Confirm Valid USPS Address$matrix'  /></td></tr>";
	}else{
		echo "<td>Address Confirmed by $d[uspsVerify]</tr></tr>";
	}
	foreach(range('a','e') as $letter){
		if ($d["address1$letter"]){
			$makeLnL = strtoupper($d["address1$letter"].', '.$d["city1$letter"].', '.$d["state1$letter"].' '.$d["zip1$letter"]);
			echo "<tr><td>$makeLnL</td>";
			if (checkVerify($makeLnL) !== true){
				echo "<td><input type='hidden' name='add$letter' value='$makeLnL'><input name='uspsVerify$letter' type='submit' value='I, ".$_COOKIE[psdata][name].", Confirm Valid USPS Address$matrix'  /></td></tr>";
			}else{
				echo "<td>Address Confirmed by ".$d["uspsVerify$letter"]."</tr></tr>";
			}
		}
	}
}
echo "</table>";
?>
</form><a href="?packet=<?=$_GET[packet]?>">Reload Supernova for Packet <?=$_GET[packet]?></a>
<? }?>