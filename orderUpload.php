<?
mysql_connect();
mysql_select_db('core');
$print .= date("F d Y H:i:s.")." : Portal upload started\n";
function email2name($email){
	$q="SELECT name FROM ps_users WHERE email LIKE '%$email%'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
return $d[name];
}

function email2id($email){
	$q="SELECT contact_id FROM contacts WHERE email LIKE '%$email%'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
return $d[contact_id];
}

// code service type
$svcType = $_POST[svcType];
if ($svcType == "OTD"){
	$svcType2 = "Packet";
	$svcType3 = "PRESALE";
	$table = "ps_packets";
	$svcStatus = "";
}elseif($svcType == "MAIL ONLY"){
	$svcType2 = "Packet";
	$svcType3 = "MAIL ONLY";
	$table = "ps_packets";
	$svcStatus = "MAIL ONLY";
}elseif($svcType == "EV"){
	$svcType2 = "Eviction";
	$svcType3 = "EVICTION";
	$table = "evictionPackets";
	$svcStatus = "";
}elseif($svcType == "S"){
	$svcType2 = "Standard";
	$svcType3 = "STANDARD";
	$table = "standard_packets";
	$svcStatus = "";
}

// code attorneys ID
$attid = $_POST[attorneysID];

// open this directory 
$myDirectory = opendir("/home/autostart/");

// get each entry
while($entryName = readdir($myDirectory)) {
	$dirArray[] = $entryName;
}

// close directory
closedir($myDirectory);

//	count elements in array
$indexCount	= count($dirArray);
//Print ("$indexCount  files<br>\n");

// sort 'em
sort($dirArray);

// set timeline
$userName=email2name($_POST[uploadEmail]);
$timeline=date("m/d/y H:i:s A")." File Sent Through Staff Portal By ".$userName;

// set contact
$contact=email2id($_POST[uploadEmail]);

//notes
$notes = $_POST[attorneyNotes];
$attorneyNotes = addslashes($notes);
// print 'em
print("User: ".$_COOKIE[psdata][name]);
print("<TABLE border=1 cellpadding=5 cellspacing=0 class=whitelinks>\n");
print("<TR><td>from</td><td>to</td><td>status</td><td>packet</td><td>link</td></TR>\n");
// loop through the array of files and print them all
for($index=0; $index < $indexCount; $index++) {
        if (substr("$dirArray[$index]", 0, 1) != "."){ // don't list hidden files
		$from = "/home/autostart/$dirArray[$index]";
		$clientFile =	explode('_',$dirArray[$index]);
		$clientFile = $clientFile[0];
		$print .= date("F d Y H:i:s.")." : File Number $clientFile\n";
		$path = "/data/service/orders/";
		if (!file_exists($path)){//c5
			mkdir ($path,0777);
		}//c5
		$file_path = $clientFile."-".date('r');
		if (!file_exists($path.$file_path)){//c6
			mkdir ($path.$file_path,0777);
		}//c6
		$to=$path.$file_path."/".$dirArray[$index];
		$link1="http://mdwestserve.com/PS_PACKETS/".$file_path."/".$dirArray[$index];
		print("<TR><td>$from</td>");
		print("<td>$to</td>");
		
		//print $packet #
		
		print("<td>");
		if (!copy($from, $to)) {
			print("Failed Copy");
		}else{
			unlink($from);
			print("Passed Copy, Link Recorded");
			if($attid == '70'){
				$query = "INSERT INTO $table (date_received, otd, attorneys_id, status, client_file, timeline, contact, attorney_notes, filing_status, courierID, service_status) values (NOW(), '$link1', '$attid', 'NEW', '$clientFile', '$timeline', '$contact', '$attorneyNotes', 'DO NOT FILE', '16', '$svcStatus')";
			}else{
				$query = "INSERT INTO $table (date_received, otd, attorneys_id, status, client_file, timeline, contact, attorney_notes, service_status) values (NOW(), '$link1', '$attid', 'NEW', '$clientFile', '$timeline', '$contact', '$attorneyNotes', '$svcStatus')";
			}
			@mysql_query($query) or die(mysql_error());
			print("</td><td>".mysql_insert_id()."</td>");
			print("<td><a href='".$link1."'>OTD</a>");
			//generate email
			$print .= date("F d Y H:i:s.")." : Uploaded by ".$userName." \n";
			$print .= date("F d Y H:i:s.")." : New $svcType2 ID ".mysql_insert_id()." \n";
			if ($_POST[attorneyNotes]){
				$print .= date("F d Y H:i:s.").": Client Note: $attorneyNotes \n";
			}
			$print .= date("F d Y H:i:s.")." : Portal upload complete\n";
			mail('mdwestserve@gmail.com',$userName.': NEW '.$svcType3.' ORDER FOR '.$clientFile,addslashes($print));
		}
		print("</td>");
		print("</TR>\n");
	
	

	
	
	
	
	}
}
print("</TABLE>		<a href='http://staff.mdwestserve.com/transporter/orderTransport.php'>Drag and drop uploads over [HERE].</a>
\n");



?>