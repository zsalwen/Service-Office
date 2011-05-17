<? include 'common.php';
function zip2county($zip){
	$zip=trim(justZip($zip));
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

function row_color2($str,$bg1,$bg2){
	$i=0;
	$explode=explode("bgcolor='[color]",$str);
	$count=count($explode)-1;
	$return=$explode[0];
	while ($i < $count){$i++;
		if ( $i%2 ) {
			$return .= "bgcolor='$bg1".$explode[$i];
		} else {
			 $return .= "bgcolor='$bg2".$explode[$i];
		}
	}
	return $return;
}

function justZip($zip){
	$zip=trim($zip);
	if (strpos($zip,'-') !== false){
		$zip=explode('-',$zip);
		$zip=$zip[0];
	}
	return $zip;
}
?>
<style>
table,tr,td,fieldset{padding:0px;}
</style>
<?
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
	$i=0;
	echo "<table align='center' border='1' style='border-collapse:collapse;'><tr><td align='center' colspan='3'>PREVIOUS SERVES IN $search, IN THE COUNTY OF $county</td></tr>";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		if ($d["$field"] != ''){
			if ((strpos($d["$field"],$search) !== false) || (strpos($search,$d["$field"]) !== false)){
				if($d[server_id] != '' && $d[contractor_rate] != ''  && $d[contractor_rate] != '0'){
					$server=$d[server_id];
					$rate=$d[contractor_rate];
					$zip=justZip($d[zip1]);
					if (isset($serverList[$zip][$server][$rate])){
						//continue rate list
						$serverList[$zip][$server][$rate] = $serverList[$zip][$server][$rate]."
						<tr bgcolor='[color]'><td><a href='/otd/order.php?packet=$d[packet_id]' target='_blank'>$d[packet_id]</a></td><td></td></tr>";
					}else{
						//start rate list
						$serverList[$zip][$server][$rate] = "<tr bgcolor='[color]'><td><a href='/otd/order.php?packet=$d[packet_id]' target='_blank'>$d[packet_id]</a></td><td><b>$$d[contractor_rate]</b></td></tr>";
					}
				}
			}
		}
		foreach(range('a','e') as $letter){$i++;
			$var=$field.$letter;
			if ($d["$var"] != ''){
				if ((strpos($d["$var"],$search) !== false) || (strpos($search,$d["$var"]) !== false)){
					if($d["server_id$letter"] != '' && $d["contractor_rate$letter"] != '' && $d["contractor_rate$letter"] != '0'){
						$zip=justZip($d["zip1$letter"]);
						$server=$d["server_id$letter"];
						$rate=$d["contractor_rate$letter"];
						if (isset($serverList[$zip][$server][$rate])){
							//continue rate list
							$serverList[$zip][$server][$rate] = $serverList[$zip][$server][$rate]."
							<tr bgcolor='[color]'><td><a href='/otd/order.php?packet=$d[packet_id]' target='_blank'>$d[packet_id]</a></td><td></td></tr>";
						}else{
							//start rate list
							$serverList[$zip][$server][$rate] = "<tr bgcolor='[color]'><td><a href='/otd/order.php?packet=$d[packet_id]' target='_blank'>$d[packet_id]</a></td><td><b>$".$d["contractor_rate$letter"]."</td></tr>";
						}
					}
				}
			}
		}
	}
	if (isset($serverList)){
		ksort($serverList);
		foreach($serverList as $k1 => $v1){
			//zips
			echo "
			<tr><td align='center'><fieldset style='padding:0px;'>
			<legend>$k1</legend><table><tr>";
			ksort($v1);
			foreach ($v1 as $k2 => $v2){
				//servers
				krsort($v2);
				$count=count($v2);
				echo "
				<td valign='top'><table align='center'>
				<tr bgcolor='#FFFF00'><td align='center' colspan='$count' style='font-weight:bold;'>".id2name($k2)."</td></tr><tr bgcolor='#FF0000'>
				";
				foreach ($v2 as $k3 => $v3){
					//rates
					//krsort($v3);
					echo "<td valign='top' style='padding-left:0px; padding-right:0px;' align='center'>
					<table style='border: 1px solid black; border-collapse:collapse;' border='1' align='center'>
					".row_color2($v3,"#FFFFFF","#CCCCCC")."
					</table></td>";
				}
				echo "
				</tr></table></td>";
			}
			echo "
			</td></tr></table></fieldset></td></tr>";
		}
	}
	echo "</table>";
}elseif($_GET[zip]){
	$search=justZip($_GET[zip]);
	$county=zip2county($_GET[zip]);
	//make sure zip is only 5 digits

	$i=0;
	$field="zip1";
	$q="SELECT * FROM ps_packets, ps_pay WHERE (zip1='$search' OR zip1a='$search' OR zip1b='$search' OR zip1c='$search' OR zip1d='$search' OR zip1e='$search') AND ps_packets.packet_id=ps_pay.packetID AND ps_pay.product='OTD' ORDER BY packet_id DESC";
	echo "<table align='center' border='1' style='border-collapse:collapse;'><tr><td align='center' colspan='3'>PREVIOUS SERVES IN<br><b>$search</b>, <b>$county</b> COUNTY</td></tr>";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		$server=$d[server_id];
		$rate=trim($d[contractor_rate]);
		if ((strpos($d["$field"],$search) !== false) || (strpos($search,$d["$field"]) !== false)){
			if($d[server_id] != '' && $d[contractor_rate] != ''  && $d[contractor_rate] != '0'){$i++;
				if (isset($serverList[$server][$rate])){
					$serverList[$server][$rate] = $serverList[$server][$rate]."<tr bgcolor='[color]'><td><a href='/otd/order.php?packet=$d[packet_id]' target='_blank'>$d[packet_id]</a></td><td></td></tr>";
				}else{
					$serverList[$server][$rate] = "<table style='border: 1px solid black; border-collapse:collapse;' border='1'><tr bgcolor='[color]'><td><a href='/otd/order.php?packet=$d[packet_id]' target='_blank'>$d[packet_id]</a></td><td><b>$$d[contractor_rate]</b></td></tr>";
				}
			}
		}
		foreach(range('a','e') as $letter){
			$var=$field.$letter;
			$server=$d["server_id$letter"];
			$rate=trim($d["contractor_rate$letter"]);
			if ((strpos($d["$var"],$search) !== false) || (strpos($search,$d["$var"]) !== false)){
				if($d["server_id$letter"] != '' && $d["contractor_rate$letter"] != '' && $d["contractor_rate$letter"] != '0'){$i++;
					if (isset($serverList[$server][$rate])){
						$serverList[$server][$rate] = $serverList[$server][$rate]."<tr bgcolor='[color]'><td><a href='/otd/order.php?packet=$d[packet_id]' target='_blank'>$d[packet_id]</a></td><td></td></tr>";
					}else{
						$serverList[$server][$rate] = "<table style='border: 1px solid black; border-collapse:collapse;' border='1'><tr bgcolor='[color]'><td><a href='/otd/order.php?packet=$d[packet_id]' target='_blank'>$d[packet_id]</a></td><td><b>$".$d["contractor_rate$letter"]."</td></tr>";
					}
				}
			}
		}
	}
	if (isset($serverList)){
		ksort($serverList);
		foreach($serverList as $k1 => $v1){
			echo "<tr bgcolor='#FFFF00'><td align='center' style='font-weight:bold;'>".id2name($k1)."</td></tr><tr bgcolor='#FF0000'><td align='center'><table><tr>";
			krsort($v1);
			foreach($v1 as $k2 => $v2){
				echo "<td valign='top' style='padding-left:0px; padding-right:0px;'>".row_color2($v2,"#FFFFFF","#CCCCCC")."</table></td>";
			}
			echo "</tr></table></td></tr>";
		}
	}
	echo "</table>";
}else{
	echo "<h1>YOU MUST SUPPLY A CITY OR ZIP CODE</h1>";
}
?>