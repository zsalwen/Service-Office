<?
session_start();
date_default_timezone_set('America/New_York');
include 'security.php';
include 'functions.php';
function talk($to,$message){
	include_once '/thirdParty/xmpphp/XMPPHP/XMPP.php';
	$conn = new XMPPHP_XMPP('talk.google.com', 5222, 'talkabout.files@gmail.com', '', 'xmpphp', 'gmail.com', $printlog=false, $loglevel=XMPPHP_Log::LEVEL_INFO);
	try {
		$conn->useEncryption(true);
		$conn->connect();
		$conn->processUntil('session_start');
		//$conn->presence("Ya, I'm online","available","talk.google.com");
		$conn->message($to, $message);
		$conn->disconnect();
	} catch(XMPPHP_Exception $e) {
		die($e->getMessage());
	}
}

mysql_connect();
mysql_select_db('service');
$q="UPDATE ps_users SET location='".$_SERVER['PHP_SELF']."', online_now='".time()."' WHERE id = '".$_COOKIE[psdata][user_id]."'";
@mysql_query($q);
 function timeline($id,$note){
 		@mysql_query("insert into explorer (date,date_time,user,packet,uri) values (NOW(),NOW(),'".$_COOKIE[psdata][name]."','OTD$id','$note')") or die(mysql_error());

	error_log("[".date('h:iA n/j/y')."] [".$_COOKIE[psdata][name]."] [".trim($id)."] [".trim($note)."] \n", 3, '/logs/timeline.log');
	mysql_select_db ('core');
	hardLog("$note for packet $id",'user');
	//talk('insidenothing@gmail.com',"$note for presale packet $id");
	$q1 = "SELECT timeline FROM ps_packets WHERE packet_id = '$id'";		
	$r1 = @mysql_query ($q1) or die(mysql_error());
	$d1 = mysql_fetch_array($r1, MYSQL_ASSOC);
	$access=date('m/d/y g:i A');
	if ($d1[timeline] != ''){
		$notes = $d1[timeline]."<br>$access: ".$note;
	}else{
		$notes = $access.': '.$note;
	}
	$notes = addslashes($notes);
	$q1 = "UPDATE ps_packets set timeline='$notes' WHERE packet_id = '$id'";		
	$r1 = @mysql_query ($q1) or die(mysql_error());
	//@mysql_query("insert into syslog (logTime, event) values (NOW(), 'Packet $id: $note')");
}
 function ev_timeline($id,$note){
	error_log("[".date('h:iA n/j/y')."] [".$_COOKIE[psdata][name]."] [".trim($id)."] [".trim($note)."] \n", 3, '/logs/timeline.log'); 

 mysql_select_db ('core');
	hardLog("$note for eviction packet $id",'user');
	//talk('insidenothing@gmail.com',"$note for eviction packet $id");

	$q1 = "SELECT timeline FROM evictionPackets WHERE eviction_id = '$id'";		
	$r1 = @mysql_query ($q1) or die(mysql_error());
	$d1 = mysql_fetch_array($r1, MYSQL_ASSOC);
	$access=date('m/d/y g:i A');
	if ($d1[timeline] != ''){
		$notes = $d1[timeline]."<br>$access: ".$note;
	}else{
		$notes = $access.': '.$note;
	}
	$notes = addslashes($notes);
	$q1 = "UPDATE evictionPackets set timeline='$notes' WHERE eviction_id = '$id'";		
	$r1 = @mysql_query ($q1) or die(mysql_error());
	//@mysql_query("insert into syslog (logTime, event) values (NOW(), 'Packet $id: $note')");
}
function opLog($event){
	//@mysql_query("insert into syslog (logTime, event) values (NOW(), '$event')");
}
function washURI($uri){
	$return=str_replace('portal//var/www/dataFiles/service/orders/','PS_PACKETS/',$uri);
	$return=str_replace('data/service/orders/','PS_PACKETS/',$uri);
	$return=str_replace('portal/','',$return);
	$return=str_replace('http://mdwestserve.com','http://alpha.mdwestserve.com',$return);
	return $return;
}
?>
