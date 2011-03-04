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




//OTDs



/*

//looking for $_GET[packet], $_GET[entry], $_GET[newDate], and $_GET[oldDate]
$q="SELECT packet_id, client_file FROM ps_packets WHERE packet_id='".$_GET[packet]."'";
$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
$d=mysql_fetch_array($r,MYSQL_ASSOC);
	//update packet
	@mysql_query("UPDATE ps_packets SET estFileDate='".$_GET[newDate]."' WHERE packet_id='".$_GET[packet]."'");
	//generate email
	$entry=strtoupper($_GET[entry]);
	$to = "Service Updates <mdwestserve@gmail.com>";
	$subject = "Estimated File Date Updated for Packet $d[packet_id] ($d[client_file]), From $_GET[oldDate] To $_GET[newDate]: $entry";
	$headers  = "MIME-Version: 1.0 \n";
	$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
	$headers .= "From: ".$_COOKIE[psdata][name]." <".$_COOKIE[psdata][email]."> \n";
	$body="Service for Packet $d[packet_id] (<strong>$d[client_file]</strong>) has been modified by ".$_COOKIE[psdata][name].", Estimated File Date was changed From $_GET[oldDate] To $_GET[newDate].";
	$body .= "<br>REASON: $entry";
	$body .= "<br><br>(410) 828-4568<br>service@mdwestserve.com<br>MDWestServe, Inc.";
	$headers .= "Cc: Service Updates <service@mdwestserve.com> \n";
	mail($to,$subject,$body,$headers);
	//make timeline entry
	timeline($_GET[packet],$_COOKIE[psdata][name]." Updated Est. Close from $_GET[oldDate] to $_GET[newDate]: $entry");
echo "<script>window.location='order.php?packet=$_GET[packet]';</script>";









//EVs




//looking for $_GET[packet], $_GET[entry], $_GET[newDate], and $_GET[oldDate]
$q="SELECT eviction_id, client_file FROM evictionPackets WHERE eviction_id='".$_GET[packet]."'";
$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
$d=mysql_fetch_array($r,MYSQL_ASSOC);
	//update packet
	@mysql_query("UPDATE evictionPackets SET estFileDate='".$_GET[newDate]."' WHERE eviction_id='".$_GET[packet]."'");
	//generate email
	$entry=strtoupper($_GET[entry]);
	$to = "Service Updates <mdwestserve@gmail.com>";
	$subject = "Estimated File Date Updated for Eviction $d[eviction_id] ($d[client_file]), From $_GET[oldDate] To $_GET[newDate]: $entry";
	$headers  = "MIME-Version: 1.0 \n";
	$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
	$headers .= "From: ".$_COOKIE[psdata][name]." <".$_COOKIE[psdata][email]."> \n";
	$body="Service for Eviction $d[eviction_id] (<strong>$d[client_file]</strong>) has been modified by ".$_COOKIE[psdata][name].", Estimated File Date was changed From $_GET[oldDate] To $_GET[newDate].";
	$body .= "<br>REASON: $entry";
	$body .= "<br><br>(410) 828-4568<br>service@mdwestserve.com<br>MDWestServe, Inc.";
	$headers .= "Cc: Service Updates <service@mdwestserve.com> \n";
	mail($to,$subject,$body,$headers);
	//make timeline entry
	ev_timeline($_GET[packet],$_COOKIE[psdata][name]." Updated Est. Close from $_GET[oldDate] to $_GET[newDate]: $entry");
echo "<script>window.location='order.php?packet=$_GET[packet]';</script>";*/








//schedule.php update code

echo "<div style='background-color:#00FF00;'>Courier Set<br />";
echo "OTD: [$_GET[otd]]<br>";
echo "EV: [$_GET[ev]]<br>";
echo "newEst: [$_GET[newEst]]<br>";
echo "entry: [$_GET[entry]]<br>";

foreach( $_GET[otd] as $key => $value){
	echo "<li>OTD$key</li>";
	//@mysql_query("update ps_packets set courierID = '$_POST[courier]', estFileDate='$_POST[newEst]' where packet_id = '$key'");
}
foreach( $_GET[ev] as $key => $value){
	echo "<li>EV$key</li>";
	//@mysql_query("update evictionPackets set courierID = '$_POST[courier]', estFileDate='$_POST[newEst]' where eviction_id = '$key'");
} 
$i=0;
while($i < count($_GET[otd])){$i++;
	echo "<li>OTD$key</li>";
}
$i=0;
while($i < count($_GET[ev])){$i++;
	echo "<li>EV$key</li>";
}
echo "</div>";
?>