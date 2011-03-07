<?
// new update queries
if($_POST){

$queryBuilder = '';

foreach ($_POST as $field => $value) {
    $queryBuilder .=  " $field =  '$value', ";
}

$queryBuilder =substr($queryBuilder, 0, -1);

$built = "update packet set $queryBuilder where id = '$_GET[packet]' ";

echo $built;

 @mysql_query("update packet set case_no = '$_POST[case_no]' where id = '$_GET[packet]' ");
}

/*
if ($_POST[reopen]){
	$r13=@mysql_query("select processor_notes, fileDate from packet where id = '$_GET[packet]'");
	$d13=mysql_fetch_array($r13,MYSQL_ASSOC);
	$oldNote = $d13[processor_notes];
	$note="file originally closed out on ".$d13[fileDate];
	$newNote = "<li>From ".$_COOKIE[psdata][name]." on ".date('m/d/y g:ia').": \"".$note."\"</li>".$oldNote;
	$today=date('Y-m-d');
	$deadline=time()+432000;
	$deadline=date('Y-m-d',$deadline);
	$q="UPDATE packet SET processor_notes='".dbIN($newNote)."', filing_status='REOPENED', affidavit_status='IN PROGRESS', affidavit_status2='REOPENED', process_status='ASSIGNED', reopenDate='$today', fileDate='0000-00-00', estFileDate='$deadline', request_close='', request_closea='', request_closeb='', request_closec='', request_closed='', request_closee='' WHERE id='".$_GET[packet]."'";
	@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	timeline($_GET[packet],$_COOKIE[psdata][name]." Reopened File for Additional Service");
}

if ($_POST[sendToClient]){
	$today=date('Y-m-d');
	@mysql_query("UPDATE packet SET fileDate='$today', estFileDate='$today', filing_status='SEND TO CLIENT' WHERE id='$_GET[packet]'");
	timeline($_GET[packet],$_COOKIE[psdata][name]." Marked File Send to Client");
}

if ($_POST[submit]){
	if ($_GET[packet]){
		$q=@mysql_query("SELECT * from packet WHERE id='$_POST[id]'") or die (mysql_error());
		$d=mysql_fetch_array($q, MYSQL_ASSOC);
		if ($_POST[estFileDate] != $d[estFileDate]){
			//if estFileDate has been changed, set flag to open prompter
			$newClose=1;
			$oldFileDate=$d[estFileDate];
		}
		if (($d[address1] != $_POST[address]) || ($d[city1] != $_POST[city]) || ($d[state1] != $_POST[state]) || ($d[zip1] != $_POST[zip])){
			$newAddress=1;
		}
		foreach(range('a','e') as $letter){
			if (($d["address1$letter"] != $_POST["address$letter"]) || ($d["city1$letter"] != $_POST["city$letter"]) || ($d["state1$letter"] != $_POST["state$letter"]) || ($d["zip1$letter"] != $_POST["zip$letter"])){
				$newAddress=1;
			}
		}
		//reset uspsVerify if the address has been modified and no confirmed entry already exists within the addressVerify table
		if ($newAddress != '' && isVerified($d[id]) !== true){
			@mysql_query("UPDATE packet SET uspsVerify='' WHERE id='$d[id]'");
		}
		$case_no=trim($_POST[case_no]);
		// un dbCleaner on all items

		if ($_POST[addressType] != $d[addressType]){
			$searchAdd=$d[address1].", ".$d[city1].", ".$d[state1]." ".$d[zip1];
			$searchAdd=strtoupper($searchAdd);
			$reviseList=addressRevise($_POST[id],$searchAd,$d[addressType],$_POST[addressType]);
			$TYPE .= "<table><tr><td>History ID</td><td>Old Type</td><td>New Type</td></tr>".$reviseList;
			//$TYPE .= "<h1>POST addressType: ".$_POST[addressType]."</h1><br><h1>DB addressType: ".$d[addressType]."</h1>";
		}
		foreach(range('a','e') as $letter){
			if ($_POST["addressType$letter"] != $d["addressType$letter"]){
				$searchAdd=$d["address1$letter"].", ".$d["city1$letter"].", ".$d["state1$letter"]." ".$d["zip1$letter"];
				$searchAdd=strtoupper($searchAdd);
				$reviseList=addressRevise($_POST[id],$searchAdd,$d["addressType$letter"],$_POST["addressType$letter"]);
				$TYPE .= "<table><tr><td>History ID</td><td>Old Type</td><td>New Type</td></tr>".$reviseList;
				//$TYPE .= "<h1>POST addressType".$letter.": ".$_POST["addressType$letter"]."</h1><br><h1>DB addressType".$letter.": ".$d["addressType$letter"]."</h1>";
			}
		}
		if ($newClose != 1){
			$estQ="estFileDate='$_POST[estFileDate]',";
		}
			@mysql_query("UPDATE packet SET process_status='$_POST[process_status]',
			filing_status='$_POST[filing_status]',
			service_status='$_POST[service_status]',
			fileDate='$_POST[fileDate]',
			courierID='$_POST[courierID]',
			addlDocs='$_POST[addlDocs]',
			lossMit='$_POST[lossMit]',
			avoidDOT='$_POST[avoidDOT]', ".$estQ."
			reopenDate='$_POST[reopenDate]',
			status='$_POST[status]',
			affidavit_status='$_POST[affidavit_status]',
			affidavit_status2='$_POST[affidavit_status2]',
			photoStatus='$_POST[photoStatus]',
			pobox='$_POST[pobox]',
			pocity='$_POST[pocity]',
			postate='$_POST[postate]',
			pozip='$_POST[pozip]',
			pobox2='$_POST[pobox2]',
			pocity2='$_POST[pocity2]',
			postate2='$_POST[postate2]',
			pozip2='$_POST[pozip2]',
			mail_status='$_POST[mail_status]',
			affidavitType='$_POST[affidavitType]',
			onAffidavit1='$_POST[onAffidavit1]',
			onAffidavit2='$_POST[onAffidavit2]',
			onAffidavit3='$_POST[onAffidavit3]',
			onAffidavit4='$_POST[onAffidavit4]',
			onAffidavit5='$_POST[onAffidavit5]',
			onAffidavit6='$_POST[onAffidavit6]',
			refile='$_POST[refile]',
			amendedAff='$_POST[amendedAff]',
			mailWeight='$_POST[mailWeight]',
			altPlaintiff='$_POST[altPlaintiff]',
			pages='$_POST[pages]',
			rush='$_POST[rush]',
			priority='$_POST[priority]',
			request_close='$_POST[request_close]',
			request_closea='$_POST[request_closea]',
			request_closeb='$_POST[request_closeb]',
			request_closec='$_POST[request_closec]',
			request_closed='$_POST[request_closed]',
			request_closee='$_POST[request_closee]',
			serveComplete='$_POST[serveComplete]',
			serveCompletea='$_POST[serveCompletea]',
			serveCompleteb='$_POST[serveCompleteb]',
			serveCompletec='$_POST[serveCompletec]',
			serveCompleted='$_POST[serveCompleted]',
			serveCompletee='$_POST[serveCompletee]',
			addressType='$_POST[addressType]',
			addressTypea='$_POST[addressTypea]',
			addressTypeb='$_POST[addressTypeb]',
			addressTypec='$_POST[addressTypec]',
			addressTyped='$_POST[addressTyped]',
			addressTypee='$_POST[addressTypee]',
			client_file='".strtoupper($_POST[client_file])."',
			case_no='".str_replace('?',0,$case_no)."',
			reopenNotes='".addslashes(strtoupper($_POST[reopenNotes]))."',
			auctionNote='".strtoupper($_POST[auctionNote])."',
			circuit_court='".strtoupper($_POST[circuit_court])."'
			WHERE id='$_POST[id]'") or die(mysql_error());
	}else{
		$case_no=trim($_POST[case_no]);
		@mysql_query("UPDATE packet SET process_status='$_POST[process_status]',
		filing_status='$_POST[filing_status]',
		service_status='$_POST[service_status]',
		pobox='$_POST[pobox]',
		pocity='$_POST[pocity]',
		postate='$_POST[postate]',
		pozip='$_POST[pozip]',
		pobox2='$_POST[pobox2]',
		pocity2='$_POST[pocity2]',
		postate2='$_POST[postate2]',
		pozip2='$_POST[pozip2]',
		entry_id='$id',
		courierID='$_POST[courierID]',
		addlDocs='$_POST[addlDocs]',
		lossMit='$_POST[lossMit]',
		avoidDOT='$_POST[avoidDOT]',
		fileDate='$_POST[fileDate]',
		estFileDate='$_POST[estFileDate]',
		reopenDate='$_POST[reopenDate]',
		affidavit_status='$_POST[affidavit_status]',
		affidavit_status2='$_POST[affidavit_status2]',
		photoStatus='$_POST[photoStatus]',
		onAffidavit1='$_POST[onAffidavit1]',
		onAffidavit2='$_POST[onAffidavit2]',
		onAffidavit3='$_POST[onAffidavit3]',
		onAffidavit4='$_POST[onAffidavit4]',
		onAffidavit5='$_POST[onAffidavit5]',
		onAffidavit6='$_POST[onAffidavit6]',
		refile='$_POST[refile]',
		amendedAff='$_POST[amendedAff]',
		mailWeight='$_POST[mailWeight]',
		altPlaintiff='$_POST[altPlaintiff]',
		rush='$_POST[rush]',
		priority='$_POST[priority]',
		pages='$_POST[pages]',
		request_close='$_POST[request_close]',
		request_closea='$_POST[request_closea]',
		request_closeb='$_POST[request_closeb]',
		request_closec='$_POST[request_closec]',
		request_closed='$_POST[request_closed]',
		request_closee='$_POST[request_closee]',
		serveComplete='$_POST[serveComplete]',
		serveCompletea='$_POST[serveCompletea]',
		serveCompleteb='$_POST[serveCompleteb]',
		serveCompletec='$_POST[serveCompletec]',
		serveCompleted='$_POST[serveCompleted]',
		serveCompletee='$_POST[serveCompletee]',
		addressType='$_POST[addressType]',
		addressTypea='$_POST[addressTypea]',
		addressTypeb='$_POST[addressTypeb]',
		addressTypec='$_POST[addressTypec]',
		addressTyped='$_POST[addressTyped]',
		addressTypee='$_POST[addressTypee]',
		mail_status='$_POST[mail_status]',
		affidavitType='$_POST[affidavitType]',
		client_file='".strtoupper($_POST[client_file])."',
		case_no='".str_replace('?',0,$case_no)."',
		process_status='READY',
		status='RECIEVED',
		circuit_court='".strtoupper($_POST[circuit_court])."'
		WHERE id='$_POST[id]'") or die(mysql_error());
		timeline($_POST[id],$_COOKIE[psdata][name]." Performed Data Entry");
		//if file is mail only, then open mailMatrix, minips_pay (upon submission of minips_pay, have that open quality control checklist)
		if ($_POST[service_status] == "MAIL ONLY"){
			@mysql_query("UPDATE packet SET process_status='AWAITING CONFIRMATION' WHERE id='".$_POST[id]."'");
		}
		//here is where we will automate the address check and other popups
		echo "<script>window.open('supernova.php?packet=".$_POST[id]."&close=1',   'supernova',   'width=600, height=800'); </script>";
	}
	//set servers and make timeline entries (if necessary);
	$timeline='';
	$dispDate=date('F jS, Y');
	$to = "Service Updates <mdwestserve@gmail.com>";
	$subject = "Dispatched Service for Packet $d[id] ($d[client_file])";
	$headers  = "MIME-Version: 1.0 \n";
	$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
	$headers .= "From: ".$_COOKIE[psdata][name]." <".$_COOKIE[psdata][email]."> \n";
	$body="Service for Packet $d[id] (<strong>$d[client_file]</strong>) has been dispatched by ".$_COOKIE[psdata][name].", today $dispDate.<br><b>Please understand that this email is sent as confirmation of a process service file sent from our office today.  If you do not reply to the contrary--stating files have not been received--within 24 hours, you will be held responsible for any delays not made known to our office.</b><br>".$_COOKIE[psdata][name]."<br>MDWestServe<br>service@mdwestserve.com<br>(410) 828-4568<br>".time()."<br>".md5(time());
	if (isset($_POST[server1])){
		@mysql_query("UPDATE packet SET server_id='$_POST[server1]' WHERE id='$_POST[id]'") or die(mysql_error());
		if ($_POST[server1] != $d[server_id]){
			$serverID=$_POST[server1];
			$id2name=id2name($serverID);
			if ($id2name == ''){
				$id2name="[BLANK]";
			}
			$id2company=id2company($serverID);
			if (trim($id2company) == ''){
				$id2company=$id2name;
			}
			$timeline = $_COOKIE[psdata][name]." Updated Order, Set $id2name as Server";
			if (($_POST[process_status] == 'ASSIGNED') && ($serverID != '')){
				//if file is currently assigned, send email to server(s) updating them about dispatch.
				$sdCount[$serverID]++;
				$subject2 = $subject." To [$id2company]";
				$headers2 = $headers."Cc: Service Updates <".id2email($d[server_id])."> \n";
				$headers3 .= $headers2;
				mail($to,$subject2,$body,$headers2);
			}
		}
	}
	foreach (range('a','e') as $letter){
		if (isset($_POST["server1$letter"])){
			@mysql_query("UPDATE packet SET server_id$letter='".$_POST["server1$letter"]."' WHERE id='$_POST[id]'") or die(mysql_error());
			if ($_POST["server1$letter"] != $d["server_id$letter"]){
				$serverID='';
				$serverID=$_POST["server1$letter"];
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
				if (($_POST[process_status] == 'ASSIGNED') && ($serverID != '') && ($sdCount[$serverID] < 1)){
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
					$timeline = $_COOKIE[psdata][name]." Updated Order, Set $id2name as Server ".strtoupper($letter);
				}
			}
		}
	}
	if ($noEntry != 1){
		if ($_GET[packet] && $timeline == ''){
			timeline($_GET[packet],$_COOKIE[psdata][name]." Updated Order");
		}elseif (trim($timeline) != ''){
			timeline($_POST[id],$timeline);
		}
	}
	$r=mysql_query("SELECT name1, name2, name3, name4, name5, name6, address1, address1a, address1b, address1c, address1d, address1e, city1, city1a, city1b, city1c, city1d, city1e, state1, state1a, state1b, state1c, state1d, state1e, zip1, zip1a, zip1b, zip1c, zip1d, zip1e, address2, address2a, address2b, address2c, address2d, address2e, city2, city2a, city2b, city2c, city2d, city2e, state2, state2a, state2b, state2c, state2d, state2e, zip2, zip2a, zip2b, zip2c, zip2d, zip2e, address3, address3a, address3b, address3c, address3d, address3e, city3, city3a, city3b, city3c, city3d, city3e, state3, state3a, state3b, state3c, state3d, state3e, zip3, zip3a, zip3b, zip3c, zip3d, zip3e, address4, address4a, address4b, address4c, address4d, address4e, city4, city4a, city4b, city4c, city4d, city4e, state4, state4a, state4b, state4c, state4d, state4e, zip4, zip4a, zip4b, zip4c, zip4d, zip4e, address5, address5a, address5b, address5c, address5d, address5e, city5, city5a, city5b, city5c, city5d, city5e, state5, state5a, state5b, state5c, state5d, state5e, zip5, zip5a, zip5b, zip5c, zip5d, zip5e, address6, address6a, address6b, address6c, address6d, address6e, city6, city6a, city6b, city6c, city6d, city6e, state6, state6a, state6b, state6c, state6d, state6e, zip6, zip6a, zip6b, zip6c, zip6d, zip6e, pobox, pobox2, pocity, pocity2, postate, postate2, pozip, pozip2 from packet WHERE id='$_POST[id]'");
	$d=mysql_fetch_array($r, MYSQL_ASSOC) or die(mysql_error());
	$i2=0;
	$qList="";
	while ($i2 < 6){$i2++;
		if (isset($_POST["name$i2"]) && ($_POST["name$i2"] != $d["name$i2"])){
			$qList .="name$i2='".dbCleaner($_POST["name$i2"])."', ";
		}
		if (isset($_POST[address]) && $_POST[address] != $d["address$i2"]){
			$qList .="address$i2='".dbCleaner($_POST[address])."', ";
		}
		if (isset($_POST[city]) && $_POST[city] != $d["city$i2"]){
			$qList .="city$i2='".dbCleaner($_POST[city])."', ";
		}
		if (isset($_POST[state]) && $_POST[state] != $d["state$i2"]){
			$qList .="state$i2='".dbCleaner($_POST[state])."', ";
		}
		if (isset($_POST[zip]) && $_POST[zip] != $d["zip$i2"]){
			$qList .="zip$i2='".dbCleaner($_POST[zip])."', ";
		}
		foreach(range('a','e') as $letter){
			$var=$i2.$letter;
			if (isset($_POST["address$letter"]) && $_POST["address$letter"] != $d["address$var"]){
				$qList .="address$var='".dbCleaner($_POST["address$letter"])."', ";
			}
			if (isset($_POST["city$letter"]) && $_POST["city$letter"] != $d["city$var"]){
				$qList .="city$var='".dbCleaner($_POST["city$letter"])."', ";
			}
			if (isset($_POST["state$letter"]) && $_POST["state$letter"] != $d["state$var"]){
				$qList .="state$var='".dbCleaner($_POST["state$letter"])."', ";
			}
			if (isset($_POST["zip$letter"]) && $_POST["zip$letter"] != $d["zip$var"]){
				$qList .="zip$var='".dbCleaner($_POST["zip$letter"])."', ";
			}
		}
	}
	if ($qList != ""){
		$q="UPDATE packet SET ".substr($qList,0,-2)." WHERE id='$_POST[id]'";
		@mysql_query($q) or die("Query: $q<br>".mysql_error());
	}
	if ($_GET[packet] && $newClose == 1){
		echo "<script>prompter('$_POST[id]','$_POST[estFileDate]','$oldFileDate');</script>";
	}elseif ($_GET[packet]){
		header ('Location: edit.php?packet='.$_GET[packet].'&type='.addslashes($TYPE));
	}else{
		if ($_GET[start]){
			header ('Location: edit.php?start='.$_GET[start].'&type='.addslashes($TYPE));
		}else{
			echo "<script>window.location.href='edit.php';</script>";
		}
	}
}
*/
?>