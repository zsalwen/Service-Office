<?
include 'common.php';
// page to send BGW a confirmation email either for all files marked 'SEND TO CLIENT' on the given date ($_GET[sendDate]), or for a specified OTD or EV.
function monthConvert($month){
	if ($month == '01'){ return 'January'; }
	if ($month == '02'){ return 'February'; }
	if ($month == '03'){ return 'March'; }
	if ($month == '04'){ return 'April'; }
	if ($month == '05'){ return 'May'; }
	if ($month == '06'){ return 'June'; }
	if ($month == '07'){ return 'July'; }
	if ($month == '08'){ return 'August'; }
	if ($month == '09'){ return 'September'; }
	if ($month == '10'){ return 'October'; }
	if ($month == '11'){ return 'November'; }
	if ($month == '12'){ return 'December'; }
}
function mailInfo($packet){
	$r=@mysql_query("SELECT name1, name2, name3, name4, name5, name6, address1, address1a, address1b, address1c, address1d, address1e, city1, city1a, city1b, city1c, city1d, city1e, state1, state1a, state1b, state1c, state1d, state1e, zip1, zip1a, zip1b, zip1c, zip1d, zip1e, pobox, pobox2, pocity, pocity2, postate, postate2, pozip, pozip2 FROM ps_packets WHERE packet_id='$packet'");
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	$r1=@mysql_query("SELECT * FROM mailMatrix WHERE packetID='$packet'");
	$d1=mysql_fetch_array($r1,MYSQL_ASSOC);
	$list .= "<table border='1' style='border-collapse:collapse; border: 1px solid black;'><tr><td align='center'><b>Address</b></td><td align='center'><b>Parties Mailed</td></tr><tr>";
	
	if ($d[address1]){
		$list .= "<tr><td>".$d["address1"]."<br>".$d["city1"].", ".$d["state1"]." ".$d["zip1"]."</td>";
		$i=0;
		$list2='';
		while ($i < 6){$i++;
			if ($d["name$i"] && $d1["add$i"]){
				$list2 .= "<li>".$d["name$i"]."</li>";
			}
		}
		$list .= "<td><ol>$list2</ol></td></tr>";
	}
	
	foreach(range('a','e') as $letter){
		if ($d["address1$letter"]){
			$list .= "<tr><td>".$d["address1$letter"]."<br>".$d["city1$letter"].", ".$d["state1$letter"]." ".$d["zip1$letter"]."</td>";
			$i=0;
			$list2='';
			while ($i < 6){$i++;
				$var=$i.$letter;
				if ($d["name$i"] && $d1["add$var"]){
					$list2 .= "<li>".$d["name$i"]."</li>";
				}
			}
			$list .= "<td><ol>$list2</ol></td></tr>";
		}
	}
	if ($d[pobox]){
		$list .= "<tr><td>".$d["pobox"]."<br>".$d["pocity"].", ".$d["postate"]." ".$d["pozip"]."</td>";
		$i=0;
		$list2='';
		while ($i < 6){$i++;
			$var=$i."PO";
			if ($d["name$i"] && $d1["add$var"]){
				$list2 .= "<li>".$d["name$i"]."</li>";
			}
		}
		$list .= "<td><ol>$list2</ol></td></tr>";
	}
	if ($d[pobox2]){
		$list .= "<tr><td>".$d["pobox2"]."<br>".$d["pocity2"].", ".$d["postate2"]." ".$d["pozip2"]."</td>";
		$i=0;
		$list2='';
		while ($i < 6){$i++;
			$var=$i."PO2";
			if ($d["name$i"] && $d1["add$var"]){
				$list2 .= "<li>".$d["name$i"]."</li>";
			}
		}
		$list .= "<td><ol>$list2</ol></td></tr>";
	}
	$list .= "</ol></td></tr></table>";
	return $list;
}
//sends "Service Confirmed" email to client for specific EV or OTD
function confirmService($packet,$sendName,$sendEmail,$type){
	if ($type='Packet'){
		$qdr="SELECT closeOut, client_file, attorneys_id, service_status FROM ps_packets WHERE packet_id='$packet'";
	}else{
		$qdr="SELECT closeOut, client_file, attorneys_id, service_status FROM evictionPackets WHERE eviction_id='$packet'";
	}
	$rdr=@mysql_query($qdr) or die("Query: $qdr<br>".mysql_error());
	$ddr=mysql_fetch_array($rdr,MYSQL_ASSOC);

	$to = "MDWestServe Archive <mdwestserve@gmail.com>";
	$subject = "Service Completed for $type $packet ($ddr[client_file])";
	$headers  = "MIME-Version: 1.0 \n";
	$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
	$headers .= "From: ".$sendName." <".$sendEmail."> \n";

	$attR = @mysql_query("select send_to_client from attorneys where attorneys_id = '$ddr[attorneys_id]'");
	$attD = mysql_fetch_array($attR, MYSQL_BOTH);
	$c=0;
	$cc = explode(',',$attD[send_to_client]);
	$ccC = count($cc);
	while ($c < $ccC){
	$headers .= "Cc: ".$cc[$c]."\n";
	$c++;
	}
	$fname = id2attorney($ddr["attorneys_id"]).'/'.$filename;
	//get most recent closeOut
	if ($ddr[closeOut] != '0000-00-00'){
		$co=explode('-',$ddr[closeOut]);
		$month=monthConvert($co[1]);
		$closeOut=$month.' '.$co[2].', '.$co[0];
		$body ="<strong>Thank you for selecting MDWestServe as Your Process Service Provider.</strong><br>
	Service for $type $packet (<strong>$ddr[client_file]</strong>) was completed on $closeOut, via $ddr[service_status].";
	}else{
		if ($type='Packet'){
			$q10a="SELECT action_str, action_type from ps_history WHERE packet_id='$packet' AND (wizard='BORROWER' OR wizard='NOT BORROWER')";
			//also Invalid Address entries
			$q10b="SELECT action_str, action_type from ps_history WHERE packet_id='$packet' AND (wizard='INVALID')";
		}else{
			$q10a="SELECT action_str, action_type from evictionHistory WHERE eviction_id='$packet' AND (wizard='BORROWER' OR wizard='NOT BORROWER')";
			//also Invalid Address entries
			$q10b="SELECT action_str, action_type from evictionHistory WHERE eviction_id='$packet' AND (wizard='INVALID')";
		}
		$r10a=@mysql_query($q10a) or die(mysql_error());
		$r10b=@mysql_query($q10b) or die(mysql_error());
		
		$serviceDate='';
		$serviceDates='';
		while ($d10a=mysql_fetch_array($r10a, MYSQL_ASSOC)){
			$serviceDate=explode('DATE OF SERVICE',$d10a[action_str]);
			$serviceDates .= $d10a[action_type].' - '.$serviceDate[1];
		}
		while ($d10b=mysql_fetch_array($r10b, MYSQL_ASSOC)){
			$dateStr=explode('WITH NO RESULTS, ON ',$d10b[action_str]);
			$serviceDate=str_replace('.</LI>','',$dateStr[1]);
			if ($serviceDates == ''){
				$serviceDates = $d10b[action_type].' - '.$serviceDate;
			}else{
				$serviceDates .= '<br>'.$d10b[action_type].' - '.$serviceDate;
			}
		}
		if ($serviceDates != ''){
			$body ="<strong>Thank you for selecting MDWestServe as Your Process Service Provider.</strong><br>
	Service for $type $packet (<strong>$ddr[client_file]</strong>) is complete, via $ddr[service_status].  
	As this document predates our latest system of affidavit entry, there is no standardized method of telling on which date service was completed.  
	To better facilitate the coordinating of auctions and post-service processing, we have included a list of all service actions and the dates on which they occurred:<br><br>$serviceDates";
		}else{
			$body ="<strong>Thank you for selecting MDWestServe as Your Process Service Provider.</strong><br>
	Service for $type $packet (<strong>$ddr[client_file]</strong>) is complete, via $ddr[service_status].";
		}
	}
	$body .= "<br><br><br><br>".$sendName."<br>MDWestServe, Inc.<br>(410) 828-4568<br>service@mdwestserve.com<br>".time()."<br>".md5(time());
	$headers .= "Cc: $sendName <$sendEmail> \n";
	mail($to,$subject,$body,$headers);
	echo "<div>".strtoupper($type)." $packet MAILED</div>";
}
//sends "Mailing Confirmed" email to client for specific EV or OTD
function confirmMail($packet,$sendName,$sendEmail,$type){
	if ($type='Packet'){
		$qdr="SELECT closeOut, client_file, attorneys_id, service_status FROM ps_packets WHERE packet_id='$packet'";
	}else{
		$qdr="SELECT closeOut, client_file, attorneys_id, service_status FROM evictionPackets WHERE eviction_id='$packet'";
	}
	$rdr=@mysql_query($qdr) or die("Query: $qdr<br>".mysql_error());
	$ddr=mysql_fetch_array($rdr,MYSQL_ASSOC);

	$to = "MDWestServe Archive <mdwestserve@gmail.com>";
	$subject = "Mailing Completed for $type $packet ($ddr[client_file])";
	$headers  = "MIME-Version: 1.0 \n";
	$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
	$headers .= "From: ".$sendName." <".$sendEmail."> \n";

	$attR = @mysql_query("select send_to_client from attorneys where attorneys_id = '$ddr[attorneys_id]'");
	$attD = mysql_fetch_array($attR, MYSQL_BOTH);
	$c=0;
	$cc = explode(',',$attD[send_to_client]);
	$ccC = count($cc);
	while ($c < $ccC){
	$headers .= "Cc: ".$cc[$c]."\n";
	$c++;
	}
	$fname = id2attorney($ddr["attorneys_id"]).'/'.$filename;
	//get most recent closeOut
	$body ="<strong>Thank you for selecting MDWestServe as Your Mailing Service Provider.</strong><br>
	Mailing for $type $packet (<strong>$ddr[client_file]</strong>)";
	if ($ddr[closeOut] != '0000-00-00'){
		$co=explode('-',$ddr[closeOut]);
		$month=monthConvert($co[1]);
		$closeOut=$month.' '.$co[2].', '.$co[0];
		$body .= " was completed on $closeOut.";
	}else{
		$body .= " is complete, please contact our office for further details.";
	}
	$body .= mailInfo($packet);
	$body .= "<br><br><br><br>".$sendName."<br>MDWestServe, Inc.<br>(410) 828-4568<br>service@mdwestserve.com<br>".time()."<br>".md5(time());
	$headers .= "Cc: $sendName <$sendEmail> \n";
	mail($to,$subject,$body,$headers);
	echo "<div>".strtoupper($type)." $packet MAILED</div>";
}
?>
<style>
li {padding-bottom:0px;}
ol {display:inline; padding-bottom:0px}
</style>
<?
$i=0;
if ($_GET[sendDate]){
	//don't send "MAIL ONLY" confirmation emails as those will be sent upon confirming mail in mailman.php
	$r=@mysql_query("select packet_id from ps_packets where filing_status = 'SEND TO CLIENT' AND fileDate='".$_GET[sendDate]."' AND service_status <> 'MAIL ONLY' order by packet_id ASC");
	while($d=mysql_fetch_array($r,MYSQL_ASSOC)){$i++;
		confirmService($d[packet_id],$_COOKIE[psdata][name],$_COOKIE[psdata][email],'Packet');
	}
	$r=@mysql_query("select eviction_id from evictionPackets where filing_status = 'SEND TO CLIENT' AND fileDate='".$_GET[sendDate]."' order by eviction_id ASC");
	while($d=mysql_fetch_array($r,MYSQL_ASSOC)){$i++;
		confirmService($d[eviction_id],$_COOKIE[psdata][name],$_COOKIE[psdata][email],'Eviction');
	}
}elseif($_GET[OTD]){$i++;
	confirmMail($_GET[OTD],$_COOKIE[psdata][name],$_COOKIE[psdata][email],'Packet');
}elseif($_GET[EV]){$i++;
	confirmMail($_GET[EV],$_COOKIE[psdata][name],$_COOKIE[psdata][email],'Eviction');
}
echo "<table align='center' style='font-size:20px;'><tr><td>$i EMAILS SENT</td></tr></table>";
?>