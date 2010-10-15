<?
include 'common.php';
function wash($str){
	$str=trim($str);
	$str=strtoupper($str);
	$str=str_replace('#','NO.',$str);
	return $str;
}
function getLnL2($address){
$address = str_replace(' ','+',$address);
$key = "ABQIAAAA2ArF_EF7s8gt5SlN-66dGRSfmlIekNqjlVCJp0F7JMAdTRULxxROmJgRMz28hDdQwD38VWhIIr_ypA";
   $curl = curl_init();
   curl_setopt ($curl, CURLOPT_URL, "http://maps.google.com/maps/geo?q=$address&output=csv&key=$key");
   curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
   $result = curl_exec ($curl);
   curl_close ($curl);
   $data = explode(',',$result);
   return $data;
}
function isLnL($packet,$def){
	$q="SELECT lat".$def.", lng".$def." FROM ps_geocode WHERE packet_id='$packet'";
	$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
	if ($d["lat$var"] != 0 && is_numeric($d["lat$var"])){
		$data = 1;
	}else{
		$data = $d["lat$var"].", ".$d["lng$var"];
	}
	return $data;
}
$packet = $_GET[packet];
if ($_POST[uspsVerify]){
	@mysql_query("UPDATE ps_packets set uspsVerify = '".$_COOKIE[psdata][name]."' where packet_id = '$_GET[packet]'");
	timeline($_GET[packet],$_COOKIE[psdata][name]." verfied addresses via USPS");
	hardLog('verfied addresses via USPS for packet '.$_GET[packet],'user');
	if ($_GET[close]){
		echo "<script>self.close()</script>";
	}else{
		echo "<script>window.parent.location.href='order.php?packet=$_GET[packet]';</script>";
	}
}

$r=@mysql_query("SELECT * FROM ps_packets where packet_id = '$packet' ");
$d=mysql_fetch_array($r, MYSQL_ASSOC);

if(!$d[uspsVerify]){

?>
<? if($d[address1]){ ?>
<iframe src="http://mdwestserve.com/ps/usps.php?address=<?=$d[address1]?>&city=<?=$d[city1]?>&state=<?=$d[state1]?>" width="300" height="100"></iframe>
<? }?>
<? if($d[address1a]){ ?>
<iframe src="http://mdwestserve.com/ps/usps.php?address=<?=$d[address1a]?>&city=<?=$d[city1a]?>&state=<?=$d[state1a]?>" width="300" height="100"></iframe>
<? }?>
<? if($d[address1b]){ @mysql_query("update ps_packets set alertMax='3' where packet_id = '$_GET[packet]'"); ?>
<iframe src="http://mdwestserve.com/ps/usps.php?address=<?=$d[address1b]?>&city=<?=$d[city1b]?>&state=<?=$d[state1b]?>" width="300" height="100"></iframe>
<? }?>
<? if($d[address1c]){ @mysql_query("update ps_packets set alertMax='4' where packet_id = '$_GET[packet]'"); ?>
<iframe src="http://mdwestserve.com/ps/usps.php?address=<?=$d[address1c]?>&city=<?=$d[city1c]?>&state=<?=$d[state1c]?>" width="300" height="100"></iframe>
<? }?>
<? if($d[address1d]){ @mysql_query("update ps_packets set alertMax='5' where packet_id = '$_GET[packet]'"); ?>
<iframe src="http://mdwestserve.com/ps/usps.php?address=<?=$d[address1d]?>&city=<?=$d[city1d]?>&state=<?=$d[state1d]?>" width="300" height="100"></iframe>
<? }?>
<? if($d[address1e]){ @mysql_query("update ps_packets set alertMax='6' where packet_id = '$_GET[packet]'"); ?>
<iframe src="http://mdwestserve.com/ps/usps.php?address=<?=$d[address1e]?>&city=<?=$d[city1e]?>&state=<?=$d[state1e]?>" width="300" height="100"></iframe>
<? }

echo "<fieldset><legend>GEOCODING $d[packet_id]</legend><table border='1'>";
$i=0;
while ($i < 6){$i++;
	if ($d["address$i"]){
		$makeLnL = wash($d["address$i"]).', '.wash($d["city$i"]).', '.wash($d["state$i"]).' '.wash($d["zip$i"]);
		echo "<tr><td>$makeLnL</td><td>";
		if (isLnL($d[packet_id],$i) != 1){
			$lnl = getLnL2($makeLnL);
			$j = 0;
			while ($j < count($lnl)){
				echo $lnl[$j].', ';
				$j++;
			}
			echo "</td></tr>";
			$r2=@mysql_query("SELECT packet_id FROM ps_geocode WHERE packet_id='$d[packet_id]'");
			$d2=mysql_fetch_array($r2,MYSQL_ASSOC);
			if ($d2 != ''){
				$q3 = "UPDATE ps_geocode set lat$i='$lnl[2]', lng$i='$lnl[3]' where packet_id ='$d[packet_id]'";
			}else{
				$q3 = "INSERT INTO ps_geocode (packet_id,lat$i,lng$i) VALUES ('$d[packet_id]','$lnl[2]','$lnl[3]')";
			}
			$r3 = @mysql_query ($q3) or die(mysql_error());
		}else{
			echo "This address has already been assigned a geocode value: ".isLnL($d[packet_id],$i).".</td></tr>";
		}
		foreach(range('a','e') as $letter){
			if ($d["address$i$letter"]){
				$var=$i.$letter;
				$makeLnL = wash($d["address$i$letter"]).', '.wash($d["city$i$letter"]).', '.wash($d["state$i$letter"]).' '.wash($d["zip$i$letter"]);
				echo "<tr><td>$makeLnL</td><td>";
				if (isLnL($d[packet_id],$var) != 1){
					$lnl = getLnL2($makeLnL);
					$j = 0;
					while ($j <= count($lnl)){
						echo $lnl[$j].', ';
						$j++;
					}
					echo "</td></tr>";
					$r2=@mysql_query("SELECT packet_id FROM ps_geocode WHERE packet_id='$d[packet_id]'");
					$d2=mysql_fetch_array($r2,MYSQL_ASSOC);
					if ($d2 != ''){
						$q3 = "UPDATE ps_geocode set lat".$i.$letter."='$lnl[2]', lng".$i.$letter."='$lnl[3]' where packet_id ='$d[packet_id]'";
					}else{
						$q3 = "INSERT INTO ps_geocode (packet_id,lat".$i.$letter.",lng".$i.$letter.") VALUES ('$d[packet_id]','$lnl[2]','$lnl[3]')";
					}
					$r3 = @mysql_query ($q3) or die(mysql_error());
				}else{
					echo "This address has already been assigned a geocode value: ".isLnL($d[packet_id],$var).".</td></tr>";
				}
			}
		}
	}
}
echo "</table></fieldset>";
?>
<form method="post"><input name="uspsVerify" type="submit" value="I, <?=$_COOKIE[psdata][name]?>, Confirm Valid USPS Addresses<? if ($_GET[matrix]){ echo " (and proceed with MAIL ONLY)"; } ?>" /></form><a href="?packet=<?=$_GET[packet]?>">Reload Supernova for Packet <?=$_GET[packet]?></a>
<? }?>