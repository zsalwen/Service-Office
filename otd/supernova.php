<?
include 'common.php';
function wash($str){
	$str=trim($str);
	$str=strtoupper($str);
	$str=str_replace('#','NO.',$str);
	return $str;
}
function isVerified($packet){
	$r=@mysql_query("SELECT address1, address1a, address1b, address1c, address1d, address1e, uspsVerify, uspsVerifya, uspsVerifyb, uspsVerifyc, uspsVerifyd, uspsVerifye FROM ps_packets where packet_id = '$packet' LIMIT 0,1 ");
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	$i=0;
	//if address is not verified, increment counter
	if ($d[address1] != '' && $d[uspsVerify] == ''){$i++;}
	foreach(range('a','e') as $letter){
		if ($d["address1$letter"] != '' && $d["uspsVerify$letter"] == ''){$i++;}
	}
	if ($i > 0){
		return false;
	}else{
		return true;
	}
}
$packet = $_GET[packet];
if ($_POST[uspsVerify] || $_POST[uspsVerifya] || $_POST[uspsVerifyb] || $_POST[uspsVerifyc] || $_POST[uspsVerifyd] || $_POST[uspsVerifye]){
	$query='';
	if ($_POST[uspsVerify]){
		$query .= "uspsVerify = '".$_COOKIE[psdata][name]."'";
		timeline($_GET[packet],$_COOKIE[psdata][name]." verified address1 via USPS");
		hardLog('verified address1 via USPS for packet '.$_GET[packet],'user');
	}
	foreach(range('a','e') as $letter){
		if ($_POST["uspsVerify$letter"]){
			if ($query == ''){
				$query .= "uspsVerify$letter = '".$_COOKIE[psdata][name]."'";
			}else{
				$query .= ", uspsVerify$letter = '".$_COOKIE[psdata][name]."'";
			}
			timeline($_GET[packet],$_COOKIE[psdata][name]." verified address1$letter via USPS");
			hardLog("verified address1$letter via USPS for packet $_GET[packet]",'user');
		}
	}
	if ($query != ''){
		@mysql_query("UPDATE ps_packets set $query where packet_id = '$_GET[packet]'");
	}
	
	$isVerified=isVerified($packet);
	if ($_GET[close] && $isVerified == true){
		echo "<script>self.close()</script>";
	}elseif($isVerified == true){
		echo "<script>window.parent.location.href='order.php?packet=$_GET[packet]';</script>";
	}
}
$isVerified=isVerified($packet);
if($isVerified != true){
$r=@mysql_query("SELECT * FROM ps_packets where packet_id = '$packet' ");
$d=mysql_fetch_array($r, MYSQL_ASSOC);
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
$i=0;
while ($i < 6){$i++;
	if ($d["address$i"]){
		$makeLnL = wash($d["address$i"]).', '.wash($d["city$i"]).', '.wash($d["state$i"]).' '.wash($d["zip$i"]);
		echo "<tr><td>$makeLnL</td>";
		if ($d[uspsVerify] == ''){
			echo "<td><input name='uspsVerify' type='submit' value='I, $_COOKIE[psdata][name], Confirm Valid USPS Address$matrix'  /></td></tr>";
		}else{
			echo "<td>Address Confirmed by $d[uspsVerify]</tr></tr>";
		}
		foreach(range('a','e') as $letter){
			if ($d["address$i$letter"]){
				$var=$i.$letter;
				$makeLnL = wash($d["address$i$letter"]).', '.wash($d["city$i$letter"]).', '.wash($d["state$i$letter"]).' '.wash($d["zip$i$letter"]);
				echo "<tr><td>$makeLnL</td>";
				if ($d["uspsVerify$letter"] == ''){
					"<td><input name='uspsVerify$letter' type='submit' value='I, $_COOKIE[psdata][name], Confirm Valid USPS Address$matrix'  /></td></tr>";
				}else{
					echo "<td>Address Confirmed by $d[uspsVerify$letter]</tr></tr>";
				}
			}
		}
	}
}
echo "</table></fieldset>";
?>
</form><a href="?packet=<?=$_GET[packet]?>">Reload Supernova for Packet <?=$_GET[packet]?></a>
<? }?>