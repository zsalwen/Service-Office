<?
function wash($str){
	$str=trim($str);
	$str=strtoupper($str);
	$str=str_replace('#','NO.',$str);
	return $str;
}
function getLnL($address){
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
function isLnL($eviction,$def){
	$q="SELECT lat".$def.", lng".$def." FROM ps_geocode WHERE eviction_id='$eviction'";
	$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
	if ($d["lat$var"] != 0 && is_numeric($d["lat$var"])){
		$data = 1;
	}else{
		$data = $d["lat$var"].", ".$d["lng$var"];
	}
	return $data;
}
mysql_connect(); 
mysql_select_db('core');
if ($_GET[all]){
	$q = "SELECT * FROM evictionPackets order by eviction_id ASC";
	$r = @mysql_query ($q) or die(mysql_error());
	while ($d=mysql_fetch_array($r, MYSQL_ASSOC)){
		echo "<fieldset><legend>$d[eviction_id]</legend><table border='1'>";
		$i=0;
		while ($i < 6){$i++;
			if ($d["address$i"]){
				$makeLnL = wash($d["address$i"]).', '.wash($d["city$i"]).', '.wash($d["state$i"]).' '.wash($d["zip$i"]);
				echo "<tr><td>$makeLnL</td><td>";
				if (isLnL($d[eviction_id],$i) != 1){
					$lnl = getLnL($makeLnL);
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
	}
}elseif($_GET[start]){
	$start=$_GET[start];
	$q = "SELECT * FROM evictionPackets WHERE eviction_id >= '$start'";
	$r = @mysql_query ($q) or die(mysql_error());
	while ($d=mysql_fetch_array($r, MYSQL_ASSOC)){
		echo "<fieldset><legend>$d[eviction_id]</legend><table border='1'>";
		$i=0;
		while ($i < 6){$i++;
			if ($d["address$i"]){
				$makeLnL = wash($d["address$i"]).', '.wash($d["city$i"]).', '.wash($d["state$i"]).' '.wash($d["zip$i"]);
				echo "<tr><td>$makeLnL</td><td>";
				if (isLnL($d[eviction_id],$i) != 1){
					$lnl = getLnL($makeLnL);
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
	}
}elseif($_GET[id]){
	$id=$_GET[id];
	$q = "SELECT * FROM evictionPackets WHERE eviction_id = '$id'";
	$r = @mysql_query ($q) or die(mysql_error());
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	echo "<fieldset><legend>$d[eviction_id]</legend><table border='1'>";
	$i=0;
	while ($i < 6){$i++;
		if ($d["address$i"]){
			$makeLnL = wash($d["address$i"]).', '.wash($d["city$i"]).', '.wash($d["state$i"]).' '.wash($d["zip$i"]);
			echo "<tr><td>$makeLnL</td><td>";
			if (isLnL($d[eviction_id],$i) != 1){
				$lnl = getLnL($makeLnL);
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
}else{
	echo "<h2>This page will not run anything without a 'GET' variable.</h2>";
}
?>