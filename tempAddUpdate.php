<?php
mysql_connect();
mysql_select_db('core');
function checkVerify($address){
	$r=@mysql_query("SELECT * FROM addressVerify where address like '%".$address."%' LIMIT 0,1 ");
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	if ($d[address]){
		return true;
	}else{
		return false;
	}
}
$q="SELECT address1, city1, state1, zip1, uspsVerify FROM ps_packets WHERE uspsVerify <> '' ORDER BY packet_id ASC";
$r=@mysql_query($q) or die (mysql_error());
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$add=strtoupper($d[address1].', '.$d[city1].', '.$d[state1].' '.$d[zip1]);
	if (checkVerify($add) !== true){
		$q1="INSERT into addressVerify (address, by) VALUES ('".addslashes($add)."', '$d[uspsVerify]')";
		@mysql_query($q1) or die ("Query: $q1<br>".mysql_error());
		echo "<li>$q1</li>";
	}
}
foreach(range('a','e') as $letter){
	$q="SELECT address1$letter, city1$letter, state1$letter, zip1$letter, uspsVerify$letter FROM ps_packets WHERE uspsVerify$letter <> '' ORDER BY packet_id ASC";
	$r=@mysql_query($q) or die (mysql_error());
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		$add=strtoupper($d["address1$letter"].', '.$d["city1$letter"].', '.$d["state1$letter"].' '.$d["zip1$letter"]);
		if (checkVerify($add) !== true){
			$q1="INSERT into addressVerify (address, by) VALUES ('".addslashes($add)."', '".$d["uspsVerify$letter"]."')";
			@mysql_query($q1) or die ("Query: $q1<br>".mysql_error());
			echo "<li>$q1</li>";
		}
	}
}
?>