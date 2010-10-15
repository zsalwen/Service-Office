<?
mysql_connect();
mysql_select_db('core');
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
$q="SELECT * from ps_packets ORDER BY packet_id ASC";
$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
echo "<table>";
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	if ($d[address1] != ''){$i++;
		//$q2="INSERT INTO ps_geocode (packet_id, lat1, lat1a, lat1b, lat2, lat2a, lat2b, lat3, lat3a, lat3b, lat4, lat4a, lat4b, lng1, lng1a, lng1b, lng2, lng2a, lng2b, lng3, lng3a, lng3b, lng4, lng4a, lng4b) VALUES ('".$d[packet_id]."', '".$d[lat1]."', '".$d[lat1a]."', '".$d[lat1b]."', '".$d[lat2]."', '".$d[lat2a]."', '".$d[lat2b]."', '".$d[lat3]."', '".$d[lat3a]."', '".$d[lat3b]."', '".$d[lat4]."', '".$d[lat4a]."', '".$d[lat4b]."', '".$d[lng1]."', '".$d[lng1a]."', '".$d[lng1b]."', '".$d[lng2]."', '".$d[lng2a]."', '".$d[lng2b]."', '".$d[lng3]."', '".$d[lng3a]."', '".$d[lng3b]."', '".$d[lng4]."', '".$d[lng4a]."', '".$d[lng4b]."')";
		//$r2=@mysql_query($q2) or die ("Query: $q2<br>".mysql_error());
		echo "<tr><td>$d[packet_id]</td><td>".getLnL($d[address1])."</td></tr>";
	}
}
echo "</table>";
echo "<script>document.title='$i Files copied to ps_geocode'</script>";
?>