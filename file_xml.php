<?php
session_start();
include 'functions.php';
echo db_connect('delta.mdwestserve.com','intranet','','');
header("Content-type: text/xml");
$open = "&lt;font size=4&gt;";
$close = "&lt;/font&gt;";
$break = "&lt;br&gt;";
$stronga = "&lt;strong&gt;";
$strongb = "&lt;/strong&gt;";
$pubtoday = date('m/d/Y');
$today = date('Y-m-d');

$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";

$xml .= "<markers>";


function markerSet($set){
	$q1 = "SELECT * FROM ps_geocode where (packet_id <> '' OR eviction_id <> '')";
	$r1 = @mysql_query ($q1) or die($q1);
	while($data = mysql_fetch_array($r1, MYSQL_ASSOC)){
// internal map only
//$link = "&lt;a href=index.php?page=details&amp;id=$data[schedule_id]&gt;Click For Details&lt;/a&gt;";
//$xml .= "<marker lat='$data[lat]' lng='$data[lng]' html='$stronga $data[sale_date] $strongb $break $data[address1] $break $data[city], $data[state] $break $link' />";

// public map
$xml .= '<marker lat=\''.$data["lat$set"].'\' lng=\''.$data["lng$set"].'\' html=\''.$open.' '.$stronga.' '.$strongb.' '.htmlentities($data["address$set"]).' '.$break.' '.$data["city$set"].', '.$data["state$set"].' '.$break.' '.$data["name$set"].' '.$close.'\' />';

	}
return $xml;
}// end function
$i=0;
while ($i < 6){$i++;

	$xml .= markerSet($i);
	foreach(range('a','e') as $letter){
		$var=$i.$letter;
		$xml .= markerSet($var);
	}
}
$xml .= "</markers>";

echo $xml;



/*
	$q1 = "SELECT * FROM ps_packets where (lat1 > '0') and package_id = ''";
	$r1 = @mysql_query ($q1) or die($q1);
	while($data = mysql_fetch_array($r1, MYSQL_ASSOC)){
// internal map only
//$link = "&lt;a href=index.php?page=details&amp;id=$data[schedule_id]&gt;Click For Details&lt;/a&gt;";
//$xml .= "<marker lat='$data[lat]' lng='$data[lng]' html='$stronga $data[sale_date] $strongb $break $data[address1] $break $data[city], $data[state] $break $link' />";

// public map
$xml .= "<marker lat='$data[lat]' lng='$data[lng]' html='$open $stronga $strongb ".htmlentities($data[serve_add])." $break ".$data[city].", $data[state] $data[zip] $break $data[party] $close' />";

	}

$xml .= "</markers>";

echo $xml;

*/
?>


