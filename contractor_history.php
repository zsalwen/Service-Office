<? include 'common.php';
function zip2county($zip){
	$zip=justZip($zip);
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
		$q2="SELECT * FROM evictionPackets, ps_pay WHERE (city1 LIKE '%$city1%' OR city1 LIKE '%$city2%') AND evictionPackets.eviction_id=ps_pay.packetID AND ps_pay.product='EV'";
		$q3="SELECT * FROM standard_packets, ps_pay WHERE (city1 LIKE '%$city1%' OR city1a LIKE '%$city1%' OR city1b LIKE '%$city1%' OR city1c LIKE '%$city1%' OR city1d LIKE '%$city1%' OR city1e LIKE '%$city1%' OR city1 LIKE '%$city2%' OR city1a LIKE '%$city2%' OR city1b LIKE '%$city2%' OR city1c LIKE '%$city2%' OR city1d LIKE '%$city2%' OR city1e LIKE '%$city2%') AND standard_packets.packet_id=ps_pay.packetID AND ps_pay.product='S'";
	}else{
		$q="SELECT * FROM ps_packets, ps_pay WHERE (city1 LIKE '%$search%' OR city1a LIKE '%$search%' OR city1b LIKE '%$search%' OR city1c LIKE '%$search%' OR city1d LIKE '%$search%' OR city1e LIKE '%$search%') AND ps_packets.packet_id=ps_pay.packetID AND ps_pay.product='OTD' ORDER BY packet_id DESC";
		$q2="SELECT * FROM evictionPackets, ps_pay WHERE city1 LIKE '%$search%' AND evictionPackets.eviction_id=ps_pay.packetID AND ps_pay.product='EV' ORDER BY eviction_id DESC";
		$q3="SELECT * FROM standard_packets, ps_pay WHERE (city1 LIKE '%$search%' OR city1a LIKE '%$search%' OR city1b LIKE '%$search%' OR city1c LIKE '%$search%' OR city1d LIKE '%$search%' OR city1e LIKE '%$search%') AND standard_packets.packet_id=ps_pay.packetID AND ps_pay.product='S' ORDER BY packet_id DESC";
	}
	$i=0;
	echo "<table align='center' border='1' style='border-collapse:collapse;'><tr><td align='center' colspan='3'>PREVIOUS SERVES IN $search, IN THE COUNTY OF $county</td></tr>";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		if ($d["$field"] != ''){
			if ((strpos($d["$field"],$search) !== false) || (strpos($search,$d["$field"]) !== false)){
				if($d[server_id] != '' && $d[contractor_rate] != ''  && $d[contractor_rate] != '0' && $d[server_id] != '218'){
					$server=$d[server_id];
					$rate=$d[contractor_rate];
					$zip=justZip($d[zip1]);
					$serverList[$zip][$server][$rate] = $serverList[$zip][$server][$rate]."<tr bgcolor='[color]'><td><a href='/otd/order.php?packet=$d[packet_id]' target='_blank'>OTD$d[packet_id]</a></td><td><b>$$d[contractor_rate]</b></td></tr>";
				}
			}
		}
		foreach(range('a','e') as $letter){$i++;
			$var=$field.$letter;
			if ($d["$var"] != ''){
				if ((strpos($d["$var"],$search) !== false) || (strpos($search,$d["$var"]) !== false)){
					if($d["server_id$letter"] != '' && $d["contractor_rate$letter"] != '' && $d["contractor_rate$letter"] != '0' && $d["server_id$letter"] != '218'){
						$zip=justZip($d["zip1$letter"]);
						$server=$d["server_id$letter"];
						$rate=$d["contractor_rate$letter"];
						$serverList[$zip][$server][$rate] = $serverList[$zip][$server][$rate]."<tr bgcolor='[color]'><td><a href='/otd/order.php?packet=$d[packet_id]' target='_blank'>OTD$d[packet_id]</a></td><td><b>$".$d["contractor_rate$letter"]."</td></tr>";
					}
				}
			}
		}
	}
	$r2=@mysql_query($q2) or die ("Query: $q2<br>".mysql_error());
	while($d2=mysql_fetch_array($r2,MYSQL_ASSOC)){
		if ($d2["$field"] != ''){
			if ((strpos($d2["$field"],$search) !== false) || (strpos($search,$d2["$field"]) !== false)){
				if($d2[server_id] != '' && $d2[contractor_rate] != ''  && $d2[contractor_rate] != '0' && $d[server_id] != '218'){
					$server=$d2[server_id];
					$rate=$d2[contractor_rate];
					$zip=justZip($d2[zip1]);
					$serverList[$zip][$server][$rate] = $serverList[$zip][$server][$rate]."<tr bgcolor='[color]'><td><a href='/ev/order.php?packet=$d2[eviction_id]' target='_blank'>EV$d2[eviction_id]</a></td><td><b>$$d2[contractor_rate]</b></td></tr>";
				}
			}
		}
	}
	$r3=@mysql_query($q3) or die ("Query: $q3<br>".mysql_error());
	while($d3=mysql_fetch_array($r3,MYSQL_ASSOC)){
		if ($d3["$field"] != ''){
			if ((strpos($d3["$field"],$search) !== false) || (strpos($search,$d3["$field"]) !== false)){
				if($d3[server_id] != '' && $d3[contractor_rate] != ''  && $d3[contractor_rate] != '0' && $d[server_id] != '218'){
					$server=$d3[server_id];
					$rate=$d3[contractor_rate];
					$zip=justZip($d3[zip1]);
					$serverList[$zip][$server][$rate] = $serverList[$zip][$server][$rate]."<tr bgcolor='[color]'><td><a href='/s/order.php?packet=$d3[packet_id]' target='_blank'>S$d3[packet_id]</a></td><td><b>$$d3[contractor_rate]</b></td></tr>";
				}
			}
		}
		foreach(range('a','e') as $letter){$i++;
			$var=$field.$letter;
			if ($d3["$var"] != ''){
				if ((strpos($d3["$var"],$search) !== false) || (strpos($search,$d3["$var"]) !== false)){
					if($d3["server_id$letter"] != '' && $d3["contractor_rate$letter"] != '' && $d3["contractor_rate$letter"] != '0' && $d["server_id$letter"] != '218'){
						$zip=justZip($d3["zip1$letter"]);
						$server=$d3["server_id$letter"];
						$rate=$d3["contractor_rate$letter"];
						$serverList[$zip][$server][$rate] = $serverList[$zip][$server][$rate]."<tr bgcolor='[color]'><td><a href='/s/order.php?packet=$d3[packet_id]' target='_blank'>S$d3[packet_id]</a></td><td><b>$".$d3["contractor_rate$letter"]."</td></tr>";
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
	$i=0;
	$field="zip1";
	$q="SELECT * FROM ps_packets, ps_pay WHERE (TRIM(zip1)='$search' OR TRIM(zip1a)='$search' OR TRIM(zip1b)='$search' OR TRIM(zip1c)='$search' OR TRIM(zip1d)='$search' OR TRIM(zip1e)='$search') AND ps_packets.packet_id=ps_pay.packetID AND ps_pay.product='OTD' ORDER BY packet_id DESC";
	$q2="SELECT * FROM evictionPackets, ps_pay WHERE TRIM(zip1)='$search' AND evictionPackets.eviction_id=ps_pay.packetID AND ps_pay.product='EV' ORDER BY eviction_id DESC";
	$q3="SELECT * FROM standard_packets, ps_pay WHERE (TRIM(zip1)='$search' OR TRIM(zip1a)='$search' OR TRIM(zip1b)='$search' OR TRIM(zip1c)='$search' OR TRIM(zip1d)='$search' OR TRIM(zip1e)='$search') AND standard_packets.packet_id=ps_pay.packetID AND ps_pay.product='S' ORDER BY packet_id DESC";
	echo "<table align='center' border='1' style='border-collapse:collapse;'><tr><td align='center' colspan='3'>PREVIOUS SERVES IN<br><b>$search</b>, <b>$county</b> COUNTY</td></tr>";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		$server=$d[server_id];
		$rate=trim($d[contractor_rate]);
		if ((strpos($d["$field"],$search) !== false) || (strpos($search,$d["$field"]) !== false)){
			if($d[server_id] != '' && $d[contractor_rate] != ''  && $d[contractor_rate] != '0' && $d[server_id] != '218'){$i++;
				$serverList[$server][$rate] = $serverList[$server][$rate]."<tr bgcolor='[color]'><td><a href='/otd/order.php?packet=$d[packet_id]' target='_blank'>OTD$d[packet_id]</a></td><td><b>$$d[contractor_rate]</b></td></tr>";
			}
		}
		foreach(range('a','e') as $letter){
			$var=$field.$letter;
			$server=$d["server_id$letter"];
			$rate=trim($d["contractor_rate$letter"]);
			if ((strpos($d["$var"],$search) !== false) || (strpos($search,$d["$var"]) !== false)){
				if($d["server_id$letter"] != '' && $d["contractor_rate$letter"] != '' && $d["contractor_rate$letter"] != '0' && $d["server_id$letter"] != '218'){$i++;
					$serverList[$server][$rate] = $serverList[$server][$rate]."<tr bgcolor='[color]'><td><a href='/otd/order.php?packet=$d[packet_id]' target='_blank'>OTD$d[packet_id]</a></td><td><b>$".$d["contractor_rate$letter"]."</td></tr>";
				}
			}
		}
	}
	$r2=@mysql_query($q2) or die ("Query: $q2<br>".mysql_error());
	while($d2=mysql_fetch_array($r2,MYSQL_ASSOC)){
		$server=$d2[server_id];
		$rate=trim($d2[contractor_rate]);
		if ((strpos($d2["$field"],$search) !== false) || (strpos($search,$d2["$field"]) !== false)){
			if($d2[server_id] != '' && $d2[contractor_rate] != ''  && $d2[contractor_rate] != '0' && $d[server_id] != '218'){$i++;
				$serverList[$server][$rate] = $serverList[$server][$rate]."<tr bgcolor='[color]'><td><a href='/ev/order.php?packet=$d2[eviction_id]' target='_blank'>EV$d2[eviction_id]</a></td><td><b>$$d2[contractor_rate]</b></td></tr>";
			}
		}
	}
	$r3=@mysql_query($q3) or die ("Query: $q3<br>".mysql_error());
	while($d3=mysql_fetch_array($r3,MYSQL_ASSOC)){
		$server=$d3[server_id];
		$rate=trim($d3[contractor_rate]);
		if ((strpos($d3["$field"],$search) !== false) || (strpos($search,$d3["$field"]) !== false)){
			if($d3[server_id] != '' && $d3[contractor_rate] != ''  && $d3[contractor_rate] != '0' && $d[server_id] != '218'){$i++;
				$serverList[$server][$rate] = $serverList[$server][$rate]."<tr bgcolor='[color]'><td><a href='/s/order.php?packet=$d3[packet_id]' target='_blank'>S$d3[packet_id]</a></td><td><b>$$d3[contractor_rate]</b></td></tr>";
			}
		}
		foreach(range('a','e') as $letter){
			$var=$field.$letter;
			$server=$d3["server_id$letter"];
			$rate=trim($d3["contractor_rate$letter"]);
			if ((strpos($d3["$var"],$search) !== false) || (strpos($search,$d3["$var"]) !== false)){
				if($d3["server_id$letter"] != '' && $d3["contractor_rate$letter"] != '' && $d3["contractor_rate$letter"] != '0' && $d["server_id$letter"] != '218'){$i++;
					$serverList[$server][$rate] = $serverList[$server][$rate]."<tr bgcolor='[color]'><td><a href='/s/order.php?packet=$d3[packet_id]' target='_blank'>S$d3[packet_id]</a></td><td><b>$".$d3["contractor_rate$letter"]."</td></tr>";
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
				echo "<td valign='top' style='padding-left:0px; padding-right:0px;'><table style='border: 1px solid black; border-collapse:collapse;' border='1'>".row_color2($v2,"#FFFFFF","#CCCCCC")."</table></td>";
			}
			echo "</tr></table></td></tr>";
		}
	}
	echo "</table>";
}else{
	echo "<h1>YOU MUST SUPPLY A CITY OR ZIP CODE</h1>";
}
?>