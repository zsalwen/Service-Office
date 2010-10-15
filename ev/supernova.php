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
<?
echo "<fieldset><legend>GEOCODING $d[eviction_id]</legend><table border='1'>";
$i=0;
while ($i < 6){$i++;
	if ($d["address$i"]){
		$makeLnL = wash($d["address$i"]).', '.wash($d["city$i"]).', '.wash($d["state$i"]).' '.wash($d["zip$i"]);
		echo "<tr><td>$makeLnL</td><td>";
		if (isLnL($d[eviction_id],$i) != 1){
			$lnl = getLnL2($makeLnL);
			$j = 0;
			while ($j < count($lnl)){
				echo $lnl[$j].', ';
				$j++;
			}
			echo "</td></tr>";
			$r2=@mysql_query("SELECT eviction_id FROM ps_geocode WHERE eviction_id='$d[eviction_id]'");
			$d2=mysql_fetch_array($r2,MYSQL_ASSOC);
			if ($d2 != ''){
				$q3 = "UPDATE ps_geocode set lat$i='$lnl[2]', lng$i='$lnl[3]' where eviction_id ='$d[eviction_id]'";
			}else{
				$q3 = "INSERT INTO ps_geocode (eviction_id,lat$i,lng$i) VALUES ('$d[eviction_id]','$lnl[2]','$lnl[3]')";
			}
			$r3 = @mysql_query ($q3) or die(mysql_error());
		}else{
			echo "This address has already been assigned a geocode value: ".isLnL($d[eviction_id],$i).".</td></tr>";
		}
	}
}
echo "</table></fieldset>";
?>
<form method="post"><input name="uspsVerify" type="submit" value="I, <?=$_COOKIE[psdata][name]?>, Confirm Valid USPS Addresses" /></form><a href="?packet=<?=$_GET[packet]?>">Reload Supernova for Packet <?=$_GET[packet]?></a>
<? }?>
<? include 'footer.php'; ?>