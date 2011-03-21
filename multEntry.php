<?
mysql_connect();
mysql_select_db('core');
function timeline($id,$note){
  	@mysql_query("insert into explorer (date,date_time,user,packet,uri) values (NOW(),NOW(),'".$_COOKIE[psdata][name]."','C$id','$note')") or die(mysql_error());

 	error_log("[".date('h:iA n/j/y')."] [".$_COOKIE[psdata][name]."] [".trim($id)."] [".trim($note)."] \n", 3, '/logs/timeline.log');
	mysql_select_db ('core');
	hardLog("$note for packet $id",'user');
	$q1 = "SELECT timeline FROM ps_packets WHERE packet_id = '$id'";		
	$r1 = @mysql_query ($q1) or die(mysql_error());
	$d1 = mysql_fetch_array($r1, MYSQL_ASSOC);
	$access=date('m/d/y g:i A');
	if ($d1[timeline] != ''){
		$notes = stripslashes($d1[timeline])."<br>$access: ".$note;
	}else{
		$notes = $access.': '.$note;
	}
	$q1 = "UPDATE ps_packets set timeline='".addslashes($notes)."' WHERE packet_id = '$id'";		
	$r1 = @mysql_query ($q1) or die("Query: $q1<br>".mysql_error());
	//@mysql_query("insert into syslog (logTime, event) values (NOW(), 'Packet $id: $note')");
}
function ev_timeline($id,$note){
	error_log("[".date('h:iA n/j/y')."] [".$_COOKIE[psdata][name]."] [EV".trim($id)."] [".trim($note)."] \n", 3, '/logs/timeline.log');
	mysql_select_db ('core');
	hardLog("$note for eviction packet $id",'user');
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
}
function s_timeline($id,$note){
	error_log("[".date('h:iA n/j/y')."] [".$_COOKIE[psdata][name]."] [EV".trim($id)."] [".trim($note)."] \n", 3, '/logs/timeline.log');
	mysql_select_db ('core');
	hardLog("$note for standard packet $id",'user');
	$q1 = "SELECT timeline FROM standard_packets WHERE packet_id = '$id'";		
	$r1 = @mysql_query ($q1) or die(mysql_error());
	$d1 = mysql_fetch_array($r1, MYSQL_ASSOC);
	$access=date('m/d/y g:i A');
	if ($d1[timeline] != ''){
		$notes = $d1[timeline]."<br>$access: ".$note;
	}else{
		$notes = $access.': '.$note;
	}
	$notes = addslashes($notes);
	$q1 = "UPDATE standard_packets set timeline='$notes' WHERE packet_id = '$id'";		
	$r1 = @mysql_query ($q1) or die(mysql_error());
}
function hardLog($str,$type){
	if ($type == "user"){
		$log = "/logs/user.log";
	}
	if ($type == "contractor"){
		$log = "/logs/contractor.log";
	}
	if ($type == "debug"){
		$log = "/logs/debug.log";
	}
	if ($log){
		error_log(date('h:iA n/j/y')." ".$_COOKIE[psdata][name]." ".$_SERVER["REMOTE_ADDR"]." ".trim($str)."\n", 3, $log);
	}
}


/*echo "OTD: [$_GET[otd]]<br>";
echo "EV: [$_GET[ev]]<br>";
echo "newDate: [$_GET[newDate]]<br>";
echo "entry: [$_GET[entry]]<br>";
echo "courier: [$_GET[courier]]<br>";*/


if ($_GET[ev] != ''){
	$ev=explode("|",$_GET[ev]);
	foreach( $ev as $key => $value){
		echo "<li>[EV$value]</li>";
		//looking for $value, $_GET[entry], $_GET[newDate]
		$q="SELECT eviction_id, client_file, estFileDate FROM evictionPackets WHERE eviction_id='".$value."' LIMIT 0,1";
		$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
		$d=mysql_fetch_array($r,MYSQL_ASSOC);
		//update packet
		@mysql_query("UPDATE evictionPackets SET estFileDate='".$_GET[newDate]."', courierID = '$_GET[courier]' WHERE eviction_id='$value'");
		//generate email
		$entry=strtoupper($_GET[entry]);
		$to = "Service Updates <mdwestserve@gmail.com>";
		$subject = "EstFileDate Updated for EV$d[eviction_id] ($d[client_file]), From $d[estFileDate] To $_GET[newDate]: $entry";
		$headers  = "MIME-Version: 1.0 \n";
		$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
		$headers .= "From: ".$_COOKIE[psdata][name]." <".$_COOKIE[psdata][email]."> \n";
		$body="Service for Eviction $d[eviction_id] (<strong>$d[client_file]</strong>) has been modified by ".$_COOKIE[psdata][name].", Estimated File Date was changed From $d[estFileDate] To $_GET[newDate].";
		$body .= "<br>REASON: $entry";
		$body .= "<br><br>(410) 828-4568<br>service@mdwestserve.com<br>MDWestServe, Inc.";
		$headers .= "Cc: Service Updates <service@mdwestserve.com> \n";
		mail($to,$subject,$body,$headers);
		//make timeline entry
		ev_timeline($value,$_COOKIE[psdata][name]." Updated Est. Close from $d[estFileDate] to $_GET[newDate]: $entry");
	}
}
if ($_GET[otd] != ''){
	$otd=explode("|",$_GET[otd]);
	foreach( $otd as $key => $value){
		echo "<li>[OTD$value]</li>";
		//looking for $value, $_GET[entry], $_GET[newDate]
		$q="SELECT packet_id, client_file, estFileDate FROM ps_packets WHERE packet_id='".$value."' LIMIT 0,1";
		$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
		$d=mysql_fetch_array($r,MYSQL_ASSOC);
		//update packet
		@mysql_query("UPDATE ps_packets SET estFileDate='".$_GET[newDate]."', courierID = '$_GET[courier]' WHERE packet_id='$value'");
		//generate email
		$entry=strtoupper($_GET[entry]);
		$to = "Service Updates <mdwestserve@gmail.com>";
		$subject = "EstFileDate Updated for OTD$d[packet_id] ($d[client_file]), From $d[estFileDate] To $_GET[newDate]: $entry";
		$headers  = "MIME-Version: 1.0 \n";
		$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
		$headers .= "From: ".$_COOKIE[psdata][name]." <".$_COOKIE[psdata][email]."> \n";
		$body="Service for Packet $d[packet_id] (<strong>$d[client_file]</strong>) has been modified by ".$_COOKIE[psdata][name].", Estimated File Date was changed From $d[estFileDate] To $_GET[newDate].";
		$body .= "<br>REASON: $entry";
		$body .= "<br><br>(410) 828-4568<br>service@mdwestserve.com<br>MDWestServe, Inc.";
		$headers .= "Cc: Service Updates <service@mdwestserve.com> \n";
		mail($to,$subject,$body,$headers);
		//make timeline entry
		timeline($value,$_COOKIE[psdata][name]." Updated Est. Close from $d[estFileDate] to $_GET[newDate]: $entry");
	}
}
if ($_GET[s] != ''){
	$s=explode("|",$_GET[s]);
	foreach( $s as $key => $value){
		echo "<li>[S$value]</li>";
		//looking for $value, $_GET[entry], $_GET[newDate]
		$q="SELECT packet_id, client_file, estFileDate FROM standard_packets WHERE packet_id='".$value."' LIMIT 0,1";
		$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
		$d=mysql_fetch_array($r,MYSQL_ASSOC);
		//update packet
		@mysql_query("UPDATE standard_packets SET estFileDate='".$_GET[newDate]."', courierID = '$_GET[courier]' WHERE packet_id='$value'");
		//generate email
		$entry=strtoupper($_GET[entry]);
		$to = "Service Updates <mdwestserve@gmail.com>";
		$subject = "EstFileDate Updated for S$d[packet_id] ($d[client_file]), From $d[estFileDate] To $_GET[newDate]: $entry";
		$headers  = "MIME-Version: 1.0 \n";
		$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
		$headers .= "From: ".$_COOKIE[psdata][name]." <".$_COOKIE[psdata][email]."> \n";
		$body="Service for Standard $d[packet_id] (<strong>$d[client_file]</strong>) has been modified by ".$_COOKIE[psdata][name].", Estimated File Date was changed From $d[estFileDate] To $_GET[newDate].";
		$body .= "<br>REASON: $entry";
		$body .= "<br><br>(410) 828-4568<br>service@mdwestserve.com<br>MDWestServe, Inc.";
		$headers .= "Cc: Service Updates <service@mdwestserve.com> \n";
		mail($to,$subject,$body,$headers);
		//make timeline entry
		s_timeline($value,$_COOKIE[psdata][name]." Updated Est. Close from $d[estFileDate] to $_GET[newDate]: $entry");
	}
}
echo "<script>window.location='schedule.php';</script>";
?>