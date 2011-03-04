<?
if ($_GET[cancel] == 1){
	//if MDWS employee confirms file cancellation, send separate emails to all servers (if service is is active), and another email to clients/service.
	if ($d[process_status] == 'ASSIGNED'){
		//if file is currently assigned, send email to server(s).
		$to = "Service Updates <mdwestserve@gmail.com>";
		$subject = "Cancelled Service for Packet $d[packet_id] ($d[client_file])";
		$headers  = "MIME-Version: 1.0 \n";
		$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
		$headers .= "From: ".$_COOKIE[psdata][name]." <".$_COOKIE[psdata][email]."> \n";
		$body="Service for Packet $d[packet_id] (<strong>$d[client_file]</strong>) has been cancelled by ".$_COOKIE[psdata][name].", if service is still in progress, please contact MDWestServe for instructions on how to proceed.";
		$body .= "<br><br>(410) 828-4568<br>service@mdwestserve.com<br>MDWestServe, Inc.";
		//send separate emails so that servers do not see each other, but so that mdwestserve@gmail.com does
		if ($d[server_id]){
			$serverID=$d[server_id];
			$sCount[$serverID]++;
			$headers2 = $headers."Cc: Service Updates <".id2email($d[server_id])."> \n";
			$headers3 .= $headers2;
			mail($to,$subject,$body,$headers2);
		}
		foreach(range('a','e') as $letter){
			$serverID='';
			$serverID=$d["server_id$letter"];
			if ($serverID != '' && $sCount[$serverID] < 1){
				$sCount[$serverID]++;
				$headers2 = $headers."Cc: Service Updates <".id2email($serverID)."> \n";
				$headers3 .= $headers2;
				mail($to,$subject,$body,$headers2);
			}
		}
		echo "<div style='background-color:#00FF00; font-size:11px;'>$headers3<hr>$body</div>";
	}
	@mysql_query("UPDATE ps_packets SET process_status = 'CANCELLED', service_status='CANCELLED', status='CANCELLED', affidavit_status='CANCELLED', payAuth='1' where packet_id='$d[packet_id]'");
	timeline($d[packet_id],$_COOKIE[psdata][name]." Cancelled Order  PER ".$_GET[cancelRef]);
	error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Cancelled Process Service PER ".$_GET[cancelRef]." for OTD$d[packet_id] ($d[client_file]) [RECEIVED: $d[date_received]]",3,"/logs/user.log");
	// email client
	$to = "Service Updates <mdwestserve@gmail.com>";
	$subject = "Cancelled Service for Packet $d[packet_id] ($d[client_file])";
	$headers  = "MIME-Version: 1.0 \n";
	$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
	$headers .= "From: ".$_COOKIE[psdata][name]." <service.cancelled@mdwestserve.com> \n";
	$attR = @mysql_query("select ps_to from attorneys where attorneys_id = '$d[attorneys_id]'");
	$attD = mysql_fetch_array($attR, MYSQL_BOTH);
	$c=-1;
	$cc = explode(',',$attD[ps_to]);
	$ccC = count($cc)-1;
	while ($c++ < $ccC){
		$headers .= "Cc: Service Updates <".trim($cc[$c])."> \n";
	}
	if (stripos($headers,$_GET[cancelRef]) == false){
		$headers .= "Cc: Service Updates <".$_GET[cancelRef]."> \n";
	}
	$headers .= "Cc: Service Updates <service@mdwestserve.com> \n";
	$history=historyList($d[packet_id],$d[attorneys_id]);
	if (strpos($history,'"')){
		$history=str_replace('"','\"',$history);
	}
	$attachmentList=attachmentList($d[packet_id],'OTD');
	$body ="<strong>Thank you for selecting MDWestServe as Your Process Service Provider.</strong><br>
	Service for Packet $d[packet_id] (<strong>$d[client_file]</strong>) is cancelled by ".$_COOKIE[psdata][name].", per ".$_GET[cancelRef]." closeout documents as follows:
	$attachmentList
	<div style='border:solid 1px;'>Service in $d[circuit_court] COUNTY was $d[service_status]. Filing status was $d[filing_status].<br>
	<center><h2>HISTORY ITEMS:</h2>
	$history
	</center></div><br>";
	$body .= "<br><br>(410) 828-4568<br>service@mdwestserve.com<br>MDWestServe, Inc.";
	mail($to,$subject,$body,$headers);
	echo "<div style='background-color:#00FF00; font-size:11px;'>$headers<hr>$body</div>";
	$r=@mysql_query("select * from ps_packets where packet_id='$d[packet_id]'");
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
}

?>