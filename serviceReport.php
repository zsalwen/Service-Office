<?
if ($_COOKIE[psdata][level] != 'Operations'){
	header('Location: http://anarchy.org');
}
mysql_connect();
mysql_select_db('core');
function serverName($id){
	$q="SELECT name, company FROM ps_users WHERE id = '$id'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	return $d[name].', '.$d[company];
}
function w9Status($id){
	$q="SELECT w9 FROM ps_users WHERE id = '$id'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	return $d[w9];
}

function paySearch($server_id){
	$total = 0;
	$loop = @mysql_query("select ps_pay.contractor_paid from ps_packets, ps_pay where ps_packets.server_id = '$server_id' AND ps_packets.packet_id=ps_pay.packetID AND ps_pay.product='OTD'");
	while ($data = mysql_fetch_array($loop,MYSQL_ASSOC)){
		$total = $total + $data[contractor_paid];
	}
	foreach(range('a','e') as $letter){
		$loop = @mysql_query("select ps_pay.contractor_paid$letter from ps_packets, ps_pay where ps_packets.server_id$letter = '$server_id' AND ps_packets.packet_id=ps_pay.packetID AND ps_pay.product='OTD'");
		while ($data = mysql_fetch_array($loop,MYSQL_ASSOC)){
			$total = $total + $data["contractor_paid$letter"];
		}
	}
	$loop = @mysql_query("select ps_pay.contractor_paid from evictionPackets, ps_pay where evictionPackets.server_id = '$server_id' AND evictionPackets.eviction_id=ps_pay.packetID AND ps_pay.product='EV'");
	while ($data = mysql_fetch_array($loop,MYSQL_ASSOC)){
		$total = $total + $data[contractor_paid];
	}
	foreach(range('a','e') as $letter){
		$loop = @mysql_query("select ps_pay.contractor_paid$letter from evictionPackets, ps_pay where evictionPackets.server_id$letter = '$server_id' AND evictionPackets.eviction_id=ps_pay.packetID AND ps_pay.product='EV'");
		while ($data = mysql_fetch_array($loop,MYSQL_ASSOC)){
			$total = $total + $data["contractor_paid$letter"];
		}
	}
	return $total;
}
//echo "<br>Build unique array of all 2009 server_id contractors in the database.";
$mr = @mysql_query("select distinct server_id from evictionPackets where fileDate LIKE '2009-%' and server_id <> ''");
while ($dr = mysql_fetch_array($mr,MYSQL_ASSOC)){
	$master[$dr[server_id]] = 'active';
	//echo "<li>$dr[server_id]</li>";
}
//echo "<br>Build unique array of all 2009 server_ida contractors in the database.";
$mr = @mysql_query("select distinct server_ida from evictionPackets where fileDate LIKE '2009-%' and server_ida <> ''");
while ($dr = mysql_fetch_array($mr,MYSQL_ASSOC)){
	$master[$dr[server_ida]] = 'active';
	//echo "<li>$dr[server_ida]</li>";
}
//echo "<br>Build unique array of all 2009 server_idb contractors in the database.";
$mr = @mysql_query("select distinct server_idb from evictionPackets where fileDate LIKE '2009-%' and server_idb <> ''");
while ($dr = mysql_fetch_array($mr,MYSQL_ASSOC)){
	$master[$dr[server_idb]] = 'active';
	//echo "<li>$dr[server_idb]</li>";
}
//echo "<br>Build unique array of all 2009 server_idc contractors in the database.";
$mr = @mysql_query("select distinct server_idc from evictionPackets where fileDate LIKE '2009-%' and server_idc <> ''");
while ($dr = mysql_fetch_array($mr,MYSQL_ASSOC)){
	$master[$dr[server_idc]] = 'active';
	//echo "<li>$dr[server_idc]</li>";
}
//echo "<br>Build unique array of all 2009 server_idd contractors in the database.";
$mr = @mysql_query("select distinct server_idd from evictionPackets where fileDate LIKE '2009-%' and server_idd <> ''");
while ($dr = mysql_fetch_array($mr,MYSQL_ASSOC)){
	$master[$dr[server_idd]] = 'active';
	//echo "<li>$dr[server_idd]</li>";
}
//echo "<br>Build unique array of all 2009 server_ide contractors in the database.";
$mr = @mysql_query("select distinct server_ide from evictionPackets where fileDate LIKE '2009-%' and server_ide <> ''");
while ($dr = mysql_fetch_array($mr,MYSQL_ASSOC)){
	$master[$dr[server_ide]] = 'active';
	//echo "<li>$dr[server_ide]</li>";
}
//echo "<br>Build unique array of all 2009 server_id contractors in the database.";
$mr = @mysql_query("select distinct server_id from ps_packets where fileDate LIKE '2009-%' and server_id <> ''");
while ($dr = mysql_fetch_array($mr,MYSQL_ASSOC)){
	$master[$dr[server_id]] = 'active';
	//echo "<li>$dr[server_id]</li>";
}
//echo "<br>Build unique array of all 2009 server_ida contractors in the database.";
$mr = @mysql_query("select distinct server_ida from ps_packets where fileDate LIKE '2009-%' and server_ida <> ''");
while ($dr = mysql_fetch_array($mr,MYSQL_ASSOC)){
	$master[$dr[server_ida]] = 'active';
	//echo "<li>$dr[server_ida]</li>";
}
//echo "<br>Build unique array of all 2009 server_idb contractors in the database.";
$mr = @mysql_query("select distinct server_idb from ps_packets where fileDate LIKE '2009-%' and server_idb <> ''");
while ($dr = mysql_fetch_array($mr,MYSQL_ASSOC)){
	$master[$dr[server_idb]] = 'active';
	//echo "<li>$dr[server_idb]</li>";
}
//echo "<br>Build unique array of all 2009 server_idc contractors in the database.";
$mr = @mysql_query("select distinct server_idc from ps_packets where fileDate LIKE '2009-%' and server_idc <> ''");
while ($dr = mysql_fetch_array($mr,MYSQL_ASSOC)){
	$master[$dr[server_idc]] = 'active';
	//echo "<li>$dr[server_idc]</li>";
}
//echo "<br>Build unique array of all 2009 server_idd contractors in the database.";
$mr = @mysql_query("select distinct server_idd from ps_packets where fileDate LIKE '2009-%' and server_idd <> ''");
while ($dr = mysql_fetch_array($mr,MYSQL_ASSOC)){
	$master[$dr[server_idd]] = 'active';
	//echo "<li>$dr[server_idd]</li>";
}
//echo "<br>Build unique array of all 2009 server_ide contractors in the database.";
$mr = @mysql_query("select distinct server_ide from ps_packets where fileDate LIKE '2009-%' and server_ide <> ''");
while ($dr = mysql_fetch_array($mr,MYSQL_ASSOC)){
	$master[$dr[server_ide]] = 'active';
	//echo "<li>$dr[server_ide]</li>";
}
echo "2009 Server W-9 Report
<table border='1' cellpadding='0' cellspacing='0' style='font-size:12px;'>";
$i=0;
$need=0;
$miss=0;
foreach ($master as $value => $key){
	
	$pay = paySearch($value);
	if(w9Status($value) == 'YES'){ $color = '00FF66'; }else{ $color = 'FF9900'; }
	if($pay == 0){ $color = 'FF0000'; }
	if($pay > 600 || $pay == 0){$i++;
		echo "
		<tr bgcolor='#$color'>
			<td>$i</td>
			<td><a href='http://staff.mdwestserve.com/contractor_review.php?admin=$value'>$value</a></td>
			<td>".serverName($value)."</td>
			<td>$".number_format($pay,2)."</td>
			<td>&nbsp;";
			if($pay > 600 && w9Status($value) != 'YES' ){ echo "W-9 Still Needed"; $need++;}
			echo "</td><td>&nbsp;";
			if($pay == 0){ echo "Payment Information Missing !"; $miss++;}
			echo "</td></tr>";
	}
}
echo "</table>";
echo "$need W-9's still needed, $miss payment information missing.";
?>