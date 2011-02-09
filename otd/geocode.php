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
mysql_connect(); 
mysql_select_db('service');
if ($_GET[all]){
	$q = "SELECT * FROM ps_packets order by packet_id ASC";
	$r = @mysql_query ($q) or die(mysql_error());
	while ($d=mysql_fetch_array($r, MYSQL_ASSOC)){
		echo "<fieldset><legend>$d[packet_id]</legend><table border='1'>";
		$i=0;
		while ($i < 6){$i++;
			if ($d["address$i"]){
				$makeLnL = wash($d["address$i"]).', '.wash($d["city$i"]).', '.wash($d["state$i"]).' '.wash($d["zip$i"]);
				echo "<tr><td>$makeLnL</td><td>";
				if (isLnL($d[packet_id],$i) != 1){
					$lnl = getLnL($makeLnL);
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
							$lnl = getLnL($makeLnL);
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
	}
}elseif($_GET[start]){
	$start=$_GET[start];
	$q = "SELECT * FROM ps_packets WHERE packet_id >= '$start'";
	$r = @mysql_query ($q) or die(mysql_error());
	while ($d=mysql_fetch_array($r, MYSQL_ASSOC)){
		echo "<fieldset><legend>$d[packet_id]</legend><table border='1'>";
		$i=0;
		while ($i < 6){$i++;
			if ($d["address$i"]){
				$makeLnL = wash($d["address$i"]).', '.wash($d["city$i"]).', '.wash($d["state$i"]).' '.wash($d["zip$i"]);
				echo "<tr><td>$makeLnL</td><td>";
				if (isLnL($d[packet_id],$i) != 1){
					$lnl = getLnL($makeLnL);
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
							$lnl = getLnL($makeLnL);
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
	}
}elseif($_GET[id]){
	$id=$_GET[id];
	$q = "SELECT * FROM ps_packets WHERE packet_id = '$id'";
	$r = @mysql_query ($q) or die(mysql_error());
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	echo "<fieldset><legend>$d[packet_id]</legend><table border='1'>";
	$i=0;
	while ($i < 6){$i++;
		if ($d["address$i"]){
			$makeLnL = wash($d["address$i"]).', '.wash($d["city$i"]).', '.wash($d["state$i"]).' '.wash($d["zip$i"]);
			echo "<tr><td>$makeLnL</td><td>";
			if (isLnL($d[packet_id],$i) != 1){
				$lnl = getLnL($makeLnL);
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
						$lnl = getLnL($makeLnL);
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
}else{
	echo "<h2>This page will not run anything without a 'GET' variable.</h2>";
}
?>