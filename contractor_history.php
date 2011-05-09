<? include 'common.php';
function zip2county($zip){
	$zip=trim($zip);
	//make sure zip is only 5 digits
	if (strpos($zip,'-') !== false){
		$zip=explode('-',$zip);
		$zip=$zip[0];
	}
	$q= "select county from zip_code where zip_code = '$zip' LIMIT 0,1";
	$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r, MYSQL_ASSOC); 
	if ($d[county]){
		return strtoupper($d[county]);
	}else{
		//curl getzips.com for appropriate info
		$url="http://www.getzips.com/CGI-BIN/ziplook.exe?What=1&Zip=$zip&Submit=Look+It+Up";
		$html=getPage($url,"Zip Code Lookup",'5','');
		$explode=explode("<P><B>AREA</B></TD></TR>",$html);
		$explode=explode(" VALIGN=TOP><P>",$explode[1]);
		$return=explode("</TD>",$explode[3]);
		return strtoupper($return[0]);
	}
}

if ($_GET[city]){
	$search=strtoupper($_GET[city]);
	$field="city1";
	$county=zip2county($_GET[zip]);
	if (strpos($search,'AKA')){
		$explode='AKA';
	}elseif(strpos($search,'A/K/A')){
		$explode='A/K/A';
	}elseif(strpos($search,'ARTA')){
		$explode='ARTA';
	}elseif(strpos($search,'A/R/T/A')){
		$explode='A/R/T/A';
	}
	if ($explode){
		$city=explode($explode,$search);
		$city1=$city[0];
		$city2=$city[1];
		$q="SELECT * FROM ps_packets, ps_pay WHERE (city1 LIKE '%$city1%' OR city1a LIKE '%$city1%' OR city1b LIKE '%$city1%' OR city1c LIKE '%$city1%' OR city1d LIKE '%$city1%' OR city1e LIKE '%$city1%' OR city1 LIKE '%$city2%' OR city1a LIKE '%$city2%' OR city1b LIKE '%$city2%' OR city1c LIKE '%$city2%' OR city1d LIKE '%$city2%' OR city1e LIKE '%$city2%') AND ps_packets.packet_id=ps_pay.packetID AND ps_pay.product='OTD'";
	}else{
		$q="SELECT * FROM ps_packets, ps_pay WHERE (city1 LIKE '%$search%' OR city1a LIKE '%$search%' OR city1b LIKE '%$search%' OR city1c LIKE '%$search%' OR city1d LIKE '%$search%' OR city1e LIKE '%$search%') AND ps_packets.packet_id=ps_pay.packetID AND ps_pay.product='OTD' ORDER BY packet_id DESC";
	}
	echo "<table align='center' border='1' style='border-collapse:collapse;'><tr><td align='center' colspan='3'>PREVIOUS SERVES IN $search, $county COUNTY</td></tr>";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		if ((strpos($d["$field"],$search) !== false) || (strpos($search,$d["$field"]) !== false)){
			if($d[server_id] != '' && $d[contractor_rate] != ''  && $d[contractor_rate] != '0'){
				$zip=$d[zip1];
				if (isset($serverList[$zip])){
					$serverList[$zip] .= "<tr><td><a href='/otd/order.php?packet=$d[packet_id]' target='_blank'>$d[packet_id]</a></td><td>".id2name($d[server_id])."</td><td><b>$$d[contractor_rate]</b></td></tr>";
				}else{
					$serverList[$zip] = "<tr><td><fieldset><legend>$zip</legend><table><tr><td><a href='/otd/order.php?packet=$d[packet_id]' target='_blank'>$d[packet_id]</a></td><td>".id2name($d[server_id])."</td><td><b>$$d[contractor_rate]</b></td></tr>";
				}
			}
		}
		foreach(range('a','e') as $letter){$i++;
			$var=$field.$letter;
			if ((strpos($d["$var"],$search) !== false) || (strpos($search,$d["$var"]) !== false)){
				if($d["server_id$letter"] != '' && $d["contractor_rate$letter"] != '' && $d["contractor_rate$letter"] != '0'){
					$zip=$d["zip1$letter"];
					if (isset($serverList[$zip])){
						$serverList[$zip] .= "<tr><td><a href='/otd/order.php?packet=$d[packet_id]' target='_blank'>$d[packet_id]</a></td><td>".id2name($d["server_id$letter"])."</td><td><b>$".$d["contractor_rate$letter"]."</b></td></tr>";
					}else{
						$serverList[$zip] = "<tr><td><fieldset><legend>$zip</legend><table><tr><td><a href='/otd/order.php?packet=$d[packet_id]' target='_blank'>$d[packet_id]</a></td><td>".id2name($d["server_id$letter"])."</td><td><b>$".$d["contractor_rate$letter"]."</td></tr>";
					}
				}
			}
		}
	}
	if (isset($serverList)){
		ksort($serverList);
		foreach($serverList as $value){
			echo $value."</table></fieldset></td></tr>";
		}
	}
	echo "</table>";
}elseif($_GET[zip]){
	$search=$_GET[zip];
	$county=zip2county($_GET[zip]);
	//make sure zip is only 5 digits
	if (strpos($zip,'-') !== false){
		$zip=explode('-',$zip);
		$zip=$zip[0];
	}
	$field="zip1";
	$q="SELECT * FROM ps_packets, ps_pay WHERE (zip1='$search' OR zip1a='$search' OR zip1b='$search' OR zip1c='$search' OR zip1d='$search' OR zip1e='$search') AND ps_packets.packet_id=ps_pay.packetID AND ps_pay.product='OTD' ORDER BY packet_id DESC";
	echo "<table align='center' border='1' style='border-collapse:collapse;'><tr><td align='center' colspan='3'>PREVIOUS SERVES IN $search, $county COUNTY</td></tr>";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		$server=$d[server_id];
		if ((strpos($d["$field"],$search) !== false) || (strpos($search,$d["$field"]) !== false)){
			if($d[server_id] != '' && $d[contractor_rate] != ''  && $d[contractor_rate] != '0'){
				if (isset($serverList[$server])){
					$serverList[$server] .= "<tr bgcolor='".row_color($i,'#333333','#FFFFFF')."'><td><a href='/otd/order.php?packet=$d[packet_id]' target='_blank'>$d[packet_id]</a></td><td><b>$$d[contractor_rate]</b></td></tr>";
				}else{
					$serverList[$server] = "<table style='border: 1px solid black; border-collapse:collapse;' border='1'><tr bgcolor='".row_color($i,'#333333','#FFFFFF')."'><td><a href='/otd/order.php?packet=$d[packet_id]' target='_blank'>$d[packet_id]</a></td><td><b>$$d[contractor_rate]</b></td></tr>";
				}
			}
		}
		foreach(range('a','e') as $letter){$i++;
			$var=$field.$letter;
			$server=$d["server_id$letter"];
			if ((strpos($d["$var"],$search) !== false) || (strpos($search,$d["$var"]) !== false)){
				if($d["server_id$letter"] != '' && $d["contractor_rate$letter"] != '' && $d["contractor_rate$letter"] != '0'){
					$zip=$d["zip1$letter"];
					if (isset($serverList[$server])){
						$serverList[$zip] .= "<tr bgcolor='".row_color($i,'#333333','#FFFFFF')."'><td><a href='/otd/order.php?packet=$d[packet_id]' target='_blank'>$d[packet_id]</a></td><td><b>$".$d["contractor_rate$letter"]."</b></td></tr>";
					}else{
						$serverList[$server] = "<table style='border: 1px solid black; border-collapse:collapse;' border='1'><tr bgcolor='".row_color($i,'#333333','#FFFFFF')."'><td><fieldset><legend>$zip</legend><table><tr><td><a href='/otd/order.php?packet=$d[packet_id]' target='_blank'>$d[packet_id]</a></td><td><b>$".$d["contractor_rate$letter"]."</td></tr>";
					}
				}
			}
		}
	}
	echo "<tr><td colspan='3' align='center'>$_GET[$zip]</td></tr>";
	if (isset($serverList)){
		ksort($serverList);
		foreach($serverList as $key => $value){
			echo "<tr><td><fieldset><legend>".id2name($key)."</legend>$value</table></fieldset></td></tr>";
		}
	}
	echo "</table>";
}else{
	echo "<h1>YOU MUST SUPPLY A CITY OR ZIP CODE</h1>";
}
?>