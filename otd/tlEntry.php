<?
mysql_connect();
mysql_select_db('core');
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

function timeline($id,$note){
  		@mysql_query("insert into explorer (date,date_time,user,packet,uri) values (NOW(),NOW(),'".$_COOKIE[psdata][name]."','C$id','".addslashes($note)."')") or die(mysql_error());

 	error_log("[".date('h:iA n/j/y')."] [".$_COOKIE[psdata][name]."] [".trim($id)."] [".trim($note)."] \n", 3, '/logs/timeline.log');
	//talk('insidenothing@gmail.com',"$note for presale packet $id");

	mysql_select_db ('core');
	hardLog("$note for packet $id",'user');

	$q1 = "SELECT timeline FROM ps_packets WHERE packet_id = '$id'";		
	$r1 = @mysql_query ($q1) or die("Query: $q1<br>".mysql_error());
	$d1 = mysql_fetch_array($r1, MYSQL_ASSOC);
	$access=date('m/d/y g:i A');
	if ($d1[timeline] != ''){
		$notes = stripslashes($d1[timeline])."<br>$access: ".$note;
	}else{
		$notes = $access.': '.$note;
	}
	$notes = addslashes($notes);
	$q1 = "UPDATE ps_packets set timeline='".addslashes($notes)."' WHERE packet_id = '$id'";		
	$r1 = @mysql_query ($q1) or die("Query: $q1<br>".mysql_error());
	//@mysql_query("insert into syslog (logTime, event) values (NOW(), 'Packet $id: $note')");
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
echo "1!";
if ($_GET[reopen]){
	echo "2!";
	$r13=@mysql_query("select processor_notes, fileDate from ps_packets where packet_id = '$_GET[packet]' LIMIT 0,1") or die (mysql_error());
	$d13=mysql_fetch_array($r13,MYSQL_ASSOC);
	echo "3a!";
	$oldNote = $d13[processor_notes];
	echo "3b!";
	$note="<b>REOPEN:</b> file originally closed out on ".$d13[fileDate];
	echo "3c!";
	$newNote = "<li>From ".$_COOKIE[psdata][name]." on ".date('m/d/y g:ia').": \"".$note."\"</li>".$oldNote;
	echo "3d!";
	$today=date('Y-m-d');
	echo "3e!";
	$q="UPDATE ps_packets SET processor_notes='".dbIN($newNote)."', filing_status='REOPENED', affidavit_status='IN PROGRESS', affidavit_status2='REOPENED', process_status='ASSIGNED', reopenDate='$today', fileDate='0000-00-00', estFileDate='$_GET[deadline]', request_close='', request_closea='', request_closeb='', request_closec='', request_closed='', request_closee='' WHERE packet_id='".$_GET[packet]."'";
	echo "3f!";
	/*@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	timeline($_GET[packet],$_COOKIE[psdata][name]." Reopened for Additional Service, Deadline: $_GET[deadline]");
	echo "<script>window.location='order.php?packet=$_GET[packet]';</script>";*/
	echo "4!";
	echo $q."<br>";
	echo $_COOKIE[psdata][name]." Reopened for Additional Service, Deadline: $_GET[deadline]";
	echo "5!";
}else{
	//looking for $_GET[packet], $_GET[entry], $_GET[newDate], and $_GET[oldDate]
	$q="SELECT packet_id, client_file FROM ps_packets WHERE packet_id='".$_GET[packet]."'";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	//update packet
	@mysql_query("UPDATE ps_packets SET estFileDate='".$_GET[newDate]."' WHERE packet_id='".$_GET[packet]."'");
	//generate email
	$entry=strtoupper($_GET[entry]);
	$to = "Service Updates <mdwestserve@gmail.com>";
	$subject = "EstFileDate Updated for OTD$d[packet_id] ($d[client_file]), From $_GET[oldDate] To $_GET[newDate]: $entry";
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
}
?>