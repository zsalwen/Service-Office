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
	hardLog("$note for standard packet $id",'user');

	$q1 = "SELECT timeline FROM standard_packets WHERE packet_id = '$id'";		
	$r1 = @mysql_query ($q1) or die("Query: $q1<br>".mysql_error());
	$d1 = mysql_fetch_array($r1, MYSQL_ASSOC);
	$access=date('m/d/y g:i A');
	if ($d1[timeline] != ''){
		$notes = stripslashes($d1[timeline])."<br>$access: ".$note;
	}else{
		$notes = $access.': '.$note;
	}
	$notes = addslashes($notes);
	$q1 = "UPDATE standard_packets set timeline='".addslashes($notes)."' WHERE packet_id = '$id'";		
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
function id2email($id){
	$q=@mysql_query("SELECT email from ps_users where id='$id' LIMIT 0,1") or die(mysql_error());
	$d=mysql_fetch_array($q, MYSQL_ASSOC);
	return $d[email];
}
function id2company($id){
	$q=@mysql_query("SELECT company from ps_users where id='$id' LIMIT 0,1") or die(mysql_error());
	$d=mysql_fetch_array($q, MYSQL_ASSOC);
	return strtoupper($d[company]);
}
function id2name($id){
	$q="SELECT name FROM ps_users WHERE id = '$id'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
return $d[name];
}
//looking for $_GET[packet], $_GET[entry], $_GET[newDate], and $_GET[oldDate]
$q="SELECT * FROM standard_packets WHERE packet_id='".$_GET[packet]."' LIMIT 0,1";
$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
$d=mysql_fetch_array($r,MYSQL_ASSOC);
if ($_GET[oldDate]){
	//update packet
	@mysql_query("UPDATE standard_packets SET estFileDate='".$_GET[newDate]."' WHERE packet_id='".$_GET[packet]."'");
	//generate email
	$entry=strtoupper($_GET[entry]);
	$to = "Service Updates <mdwestserve@gmail.com>";
	$subject = "EstFileDate Updated for S$d[packet_id] ($d[client_file]), From $_GET[oldDate] To $_GET[newDate]: $entry";
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
}else{
	//set estFileDate, timeline "dispatched", set servers
	@mysql_query("UPDATE standard_packets SET estFileDate='".$_GET[newDate]."' WHERE packet_id='".$_GET[packet]."'");
	$timeline='';
	$dispDate=date('F jS, Y');
	$to = "Service Updates <mdwestserve@gmail.com>";
	$subject = "Dispatched Service for S$d[packet_id] ($d[client_file])";
	$headers  = "MIME-Version: 1.0 \n";
	$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
	$headers .= "From: ".$_COOKIE[psdata][name]." <".$_COOKIE[psdata][email]."> \n";
	$body="Service for Packet $d[packet_id] (<strong>$d[client_file]</strong>) has been dispatched by ".$_COOKIE[psdata][name].", today $dispDate.<br><b>Please understand that this email is sent as confirmation of a process service file sent from our office today.  If you do not reply to the contrary--stating files have not been received--within 24 hours, you will be held responsible for any delays not made known to our office.</b><br>".$_COOKIE[psdata][name]."<br>MDWestServe<br>service@mdwestserve.com<br>(410) 828-4568<br>".time()."<br>".md5(time());
	if ($_GET[server_id] != ''){
		@mysql_query("UPDATE standard_packets SET server_id='$_GET[server_id]' WHERE packet_id='$_GET[packet_id]'") or die(mysql_error());
		echo "<script>alert('UPDATED SERVER')</script>";
		if ($_GET[server_id] != $d[server_id]){
			$serverID=$_GET[server_id];
			$id2name=id2name($serverID);
			if ($id2name == ''){
				$id2name="[BLANK]";
			}
			$id2company=id2company($serverID);
			if (trim($id2company) == ''){
				$id2company=$id2name;
			}
			$timeline = $_COOKIE[psdata][name]." Dispatched Order, Set $id2name as Server";
			if (($_GET[process_status] == 'ASSIGNED') && ($serverID != '')){
				//if file is currently assigned, send email to server(s) updating them about dispatch.
				$sdCount[$serverID]++;
				$subject2 = $subject." To [$id2company]";
				$headers2 = $headers."Cc: Service Updates <".id2email($d[server_id])."> \n";
				$headers3 .= $headers2;
				mail($to,$subject2,$body,$headers2);
			}
		}
	}
	foreach(range('a','e') as $letter){
		if ($_GET["server_id$letter"] != ''){
			@mysql_query("UPDATE standard_packets SET server_id$letter='".$_GET["server_id$letter"]."' WHERE packet_id='$_GET[packet_id]'") or die(mysql_error());
			echo "<script>alert('UPDATED SERVER $letter')</script>";
			if ($_GET["server_id$letter"] != $d["server_id$letter"]){
				$serverID='';
				$serverID=$_GET["server_id$letter"];
				$id2name='';
				$id2name=id2name($serverID);
				if ($id2name == ''){
					$id2name="[BLANK]";
				}
				$id2company='';
				$id2company=id2company($serverID);
				if (trim($id2company) == ''){
					$id2company=$id2name;
				}
				if (($_GET[process_status] == 'ASSIGNED') && ($serverID != '') && ($sdCount[$serverID] < 1)){
					//if file is currently assigned, send email to server(s) updating them about dispatch.
					$sdCount[$serverID]++;
					$subject2 = $subject." To [$id2company]";
					$headers2 = $headers."Cc: Service Updates <".id2email($serverID)."> \n";
					$headers3 .= $headers2;
					mail($to,$subject2,$body,$headers2);
				}
				if (trim($timeline) != ''){
					$timeline .= ", Set $id2name as Server ".strtoupper($letter);
				}else{
					$timeline = $_COOKIE[psdata][name]." Dispatched Order, Set $id2name as Server ".strtoupper($letter);
				}
			}
		}
	}
	timeline($_GET[packet],$timeline.", Deadline: $_GET[newDate]");
}
echo "<script>window.location='order.php?packet=$_GET[packet]';</script>";
?>