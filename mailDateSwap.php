<?
function replaceDate($str,$newDate){
	$action=explode('BY FIRST CLASS MAIL ON ',$str);
	$searchDate=explode('.</LI>',$action[1]);
	$searchDate=$searchDate[0];
	$newStr=addslashes(str_replace($searchDate,$newDate,$str));
	return $newStr;
}
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
include "common.php";
if ($_GET[packet]){
	if ($_GET[replace]){
		$replace=strtoupper($_GET[replace]);
		echo "<h1>Mailing entries updated for packet $_GET[packet], replaced with $replace.</h1><br>";
		$q1="SELECT * FROM ps_history WHERE packet_id='".$_GET[packet]."' AND wizard='MAILING DETAILS'";
		$r1=@mysql_query($q1) or die ("Query: $q1<br>".mysql_error());
		echo "<table align='center' width='50%'>";
		echo "<tr><td>PACKET ID</td><td width='80%'>UPDATED ACTION</td></tr>";
		while($d1=mysql_fetch_array($r1, MYSQL_ASSOC)){
			$id=$d1[history_id];
			$newHistory=replaceDate($d1[action_str],$replace);
			echo '<tr><td>'.$id.'</td><td>'.$newHistory."</tr></td>";
			$q2="UPDATE ps_history SET action_str='$newHistory' WHERE history_id='$id'";
			$r2=@mysql_query($q2) or die("Query: $q2<br>".mysql_error());
		}
		echo "</table>";
		//convert new date to datestamp, check new date against closeOut date, update closeOut if necessary.
		$dt=strtotime($_GET[replace]);
		$dt=date('Y-m-d', $dt);
		$q3="SELECT * FROM ps_packets WHERE packet_id='".$_GET[packet]."'";
		$r3=@mysql_query($q3) or die("Query: $q3<br>".mysql_error());
		$d3=mysql_fetch_array($r3,MYSQL_ASSOC);
		echo "PACKET: $_GET[packet]<br>CLOSEOUT: $d3[closeOut]<br>NEW DATE: $dt";
		if ($dt > $d3[closeOut]){
			$q4="UPDATE ps_packets SET closeOut='$dt' WHERE packet_id='".$_GET[packet]."'";
			$r4=@mysql_query($q4) or die("Query: $q4<br>".mysql_error());
			echo "<br>UPDATED CLOSEOUT to $dt";
			//if file complete email has already been sent, send another with new closeOut date.
			if ($d3[affidavit_status]='SERVICE CONFIRMED'){
				$to = "MDWestServe Archive <mdwestserve@gmail.com>";
				$subject = "Service Completed UPDATE for Packet $_GET[packet] ($d3[client_file])";
				$headers  = "MIME-Version: 1.0 \n";
				$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
				$headers .= "From: ".$_COOKIE[psdata][name]." <".$_COOKIE[psdata][email]."> \n";
				$attR = @mysql_query("select ps_to_alt from attorneys where attorneys_id = '$d3[attorneys_id]'");
				$attD = mysql_fetch_array($attR, MYSQL_BOTH);
				$c=0;
				$cc = explode(',',$attD[ps_to_alt]);
				$ccC = count($cc);
				while ($c < $ccC){
				$headers .= "Cc: ".$cc[$c]."\n";
				$c++;
				}
				$co=explode('-',$dt);
				$month=monthConvert($co[1]);
				$closeOut=$month.' '.$co[2].', '.$co[0];
				$body ="<strong>Thank you for selecting MDWestServe as Your Process Service Provider.</strong><br>Service for packet $_GET[packet] (<strong>$d3[client_file]</strong>) was completed on <b>$closeOut</b>, via $d3[service_status].<br>An earlier email was sent listing a completion date of $d3[closeOut], which has been updated to a current date of <b>$closeOut</b>.<br><br><br><br>".$_COOKIE[psdata][name]."<br>MDWestServe<br>service@mdwestserve.com<br>".time()."<br>".md5(time());
				$headers .= "Cc: ".$_COOKIE[psdata][email]."\n";
				$headers .= "Cc: MDWestServe Archive <mdwestserve@gmail.com> \n";
				mail($to,$subject,$body,$headers);
			}
		}
		psActivity("serviceREConfirmed");
		timeline($_GET[packet],$_COOKIE[psdata][name]." RESET Mailing Date To $dt");
		error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." RESET Mailing Date for OTD$_GET[packet] to $dt \n",3,"/logs/user.log");
		echo "<script>window.location='http://service.mdwestserve.com/obAffidavit.php?mail=1&autoPrint=1&packet=$_GET[packet]';</script>";
	}else{
		$q1="SELECT * FROM ps_history WHERE packet_id='".$_GET[packet]."' AND wizard='MAILING DETAILS'";
		$r1=@mysql_query($q1) or die ("Query: $q1<br>".mysql_error());
		while($d1=mysql_fetch_array($r1, MYSQL_ASSOC)){
			$action=$d1[action_str];
			$action=explode('BY FIRST CLASS MAIL ON ',$action);
			$searchDate=explode('.</LI>',$action[1]);
			$searchDate=$searchDate[0];
			if (!strpos($dateList,$searchDate)){
				$dateList .= $searchDate."<br>";
			}
		}
		echo "<form><table><tr><td>Packet $_GET[packet], Mailings Were Sent:</td></tr><tr><td>$dateList</td></tr>";
		echo "<tr><td>Enter Replacement Date: <input name='replace'></td></tr><tr><td><input type='submit' value='SUBMIT'><input type='hidden' name='packet' value='".$_GET[packet]."'></td></tr></form></table>";
	}
}elseif($_GET[eviction]){
	if ($_GET[replace]){
		$replace=strtoupper($_GET[replace]);
		echo "<h1>Mailing entries updated for eviction $_GET[eviction], replaced with $replace.</h1><br>";
		$q1="SELECT * FROM evictionHistory WHERE eviction_id='".$_GET[eviction]."' AND wizard='MAILING DETAILS'";
		$r1=@mysql_query($q1) or die ("Query: $q1<br>".mysql_error());
		echo "<table align='center' width='50%'>";
		echo "<tr><td>PACKET ID</td><td width='80%'>UPDATED ACTION</td></tr>";
		while($d1=mysql_fetch_array($r1, MYSQL_ASSOC)){
			$id=$d1[history_id];
			$newHistory=replaceDate($d1[action_str],$replace);
			echo '<tr><td>'.$id.'</td><td>'.$newHistory."</tr></td>";
			$q2="UPDATE evictionHistory SET action_str='$newHistory' WHERE history_id='$id'";
			$r2=@mysql_query($q2) or die("Query: $q2<br>".mysql_error());
		}
		echo "</table>";
		//convert new date to datestamp, check new date against closeOut date, update closeOut if necessary.
		$dt=strtotime($_GET[replace]);
		$dt=date('Y-m-d', $dt);
		$q3="SELECT * FROM evictionPackets WHERE eviction_id='".$_GET[eviction]."'";
		$r3=@mysql_query($q3) or die("Query: $q3<br>".mysql_error());
		$d3=mysql_fetch_array($r3,MYSQL_ASSOC);
		echo "EVICTION: $_GET[eviction]<br>CLOSEOUT: $d3[closeOut]<br>NEW DATE: $dt";
		if ($dt > $d3[closeOut]){
			$q4="UPDATE evictionPackets SET closeOut='$dt' WHERE eviction_id='".$_GET[eviction]."'";
			$r4=@mysql_query($q4) or die("Query: $q4<br>".mysql_error());
			echo "<br>UPDATED CLOSEOUT to $dt";
			//if file complete email has already been sent, send another with new closeOut date.
			if ($d3[affidavit_status]='SERVICE CONFIRMED'){
				$to = "MDWestServe Archive <mdwestserve@gmail.com>";
				$subject = "Service Completed UPDATE for Eviction $_GET[eviction] ($d3[client_file])";
				$headers  = "MIME-Version: 1.0 \n";
				$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
				$headers .= "From: ".$_COOKIE[psdata][name]." <".$_COOKIE[psdata][email]."> \n";
				$attR = @mysql_query("select ps_to_alt from attorneys where attorneys_id = '$d3[attorneys_id]'");
				$attD = mysql_fetch_array($attR, MYSQL_BOTH);
				$c=0;
				$cc = explode(',',$attD[ps_to_alt]);
				$ccC = count($cc);
				while ($c < $ccC){
				$headers .= "Cc: ".$cc[$c]."\n";
				$c++;
				}
				$co=explode('-',$dt);
				$month=monthConvert($co[1]);
				$closeOut=$month.' '.$co[2].', '.$co[0];
				$body ="<strong>Thank you for selecting MDWestServe as Your Process Service Provider.</strong><br>Service for eviction $_GET[eviction] (<strong>$d3[client_file]</strong>) was completed on <b>$closeOut</b>, via $d3[service_status].<br>An earlier email was sent listing a completion date of $d3[closeOut], which has been updated to a current date of <b>$closeOut</b>.<br><br><br><br>".$_COOKIE[psdata][name]."<br>MDWestServe<br>service@mdwestserve.com<br>".time()."<br>".md5(time());
				$headers .= "Cc: ".$_COOKIE[psdata][email]."\n";
				$headers .= "Cc: MDWestServe Archive <mdwestserve@gmail.com> \n";
				mail($to,$subject,$body,$headers);
			}
		}
		ev_timeline($_GET[eviction],$_COOKIE[psdata][name]." RESET Mailing Date To $dt");
		error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." RESET Mailing Date for EV$_GET[eviction] to $dt \n",3,"/logs/user.log");
		echo "<script>window.location='http://service.mdwestserve.com/evictionAff.php?mail=1&autoPrint=1&id=$_GET[eviction]';</script>";
	}else{
		$q1="SELECT * FROM evictionHistory WHERE eviction_id='".$_GET[eviction]."' AND wizard='MAILING DETAILS'";
		$r1=@mysql_query($q1) or die ("Query: $q1<br>".mysql_error());
		while($d1=mysql_fetch_array($r1, MYSQL_ASSOC)){
			$action=$d1[action_str];
			$action=explode('AND BY FIRST CLASS MAIL ON ',$action);
			$searchDate=explode('.</LI>',$action[1]);
			$searchDate=$searchDate[0];
			if (!strpos($dateList,$searchDate)){
				$dateList .= $searchDate."<br>";
			}
		}
		echo "<form><table><tr><td>eviction $_GET[eviction], Mailings Were Sent:</td></tr><tr><td>$dateList</td></tr>";
		echo "<tr><td>Enter Replacement Date: <input name='replace'></td></tr><tr><td><input type='submit' value='SUBMIT'><input type='hidden' name='eviction' value='".$_GET[eviction]."'></td></tr></form></table>";
	}
}else{
	if ($_GET[replace]){
		$search=$_GET[search];
		$replace=strtoupper($_GET[replace]);
		$q1="SELECT * FROM ps_history WHERE action_str LIKE '%$search%' AND wizard='MAILING DETAILS'";
		$r1=@mysql_query($q1) or die ("Query: $q1<br>".mysql_error());
		echo "<table align='center' width='50%'>";
		echo "<tr><td>PACKET ID</td><td>HISTORY ID</td><td width='80%'>UPDATED ACTION</td></tr>";
		$i=0;
		while($d1=mysql_fetch_array($r1, MYSQL_ASSOC)){$i++;
			$id=$d1[history_id];
			$history=explode($search,$d1[action_str]);
			$newHistory = implode($replace, $history);
			$newHistory = addslashes($newHistory);
			echo "<tr><td><a href='http://service.mdwestserve.com/obAffidavit.php?packet=$d1[packet_id]&mail=1&autoPrint=1' target='_blank'>$d1[packet_id]</a></td><td>$id</td><td>$newHistory</tr></td>";
			$q2="UPDATE ps_history SET action_str='$newHistory' WHERE history_id='$id'";
			$r2=@mysql_query($q2) or die("Query: $q2<br>".mysql_error());
			$OTDs["$i"]=$d1[packet_id];
		}
		echo "</table>";
		$q1="SELECT * FROM evictionHistory WHERE action_str LIKE '%$search%' AND wizard='MAILING DETAILS'";
		$r1=@mysql_query($q1) or die ("Query: $q1<br>".mysql_error());
		$i=0;
		while($d1=mysql_fetch_array($r1, MYSQL_ASSOC)){$i++;
			$id=$d1[history_id];
			$history=explode($search,$d1[action_str]);
			$newHistory = implode($replace, $history);
			$newHistory = addslashes($newHistory);
			echo "<tr><td><a href='http://service.mdwestserve.com/evictionAff.php?id=$d1[eviction_id]&mail=1&autoPrint=1' target='_blank'>$d1[eviction_id]</a></td><td>$id</td><td>$newHistory</tr></td>";
			$q2="UPDATE evictionHistory SET action_str='$newHistory' WHERE history_id='$id'";
			$r2=@mysql_query($q2) or die("Query: $q2<br>".mysql_error());
			$EVs["$i"]=$d1[eviction_id];
		}
		echo "</table>";
		$i=0;
		while($i < count($OTDs)){$i++;
			if ($OTDs["$i"] != ''){
				//convert new date to datestamp, check new date against closeOut date, update closeOut if necessary.
				$dt=strtotime($replace);
				$dt=date('Y-m-d', $dt);
				$q3="SELECT * FROM ps_packets WHERE packet_id='".$OTDs["$i"]."'";
				$r3=@mysql_query($q3) or die("Query: $q3<br>".mysql_error());
				$d3=mysql_fetch_array($r3,MYSQL_ASSOC);
				echo "PACKET: ".$OTDs["$i"]."<br>CLOSEOUT: $d3[closeOut]<br>NEW DATE: $dt";
				if ($dt > $d3[closeOut]){
					$q4="UPDATE ps_packets SET closeOut='$dt' WHERE packet_id='".$OTDs["$i"]."'";
					$r4=@mysql_query($q4) or die("Query: $q4<br>".mysql_error());
					echo "<br>UPDATED CLOSEOUT to $dt";
					//if file complete email has already been sent, send another with new closeOut date.
					if ($d3[affidavit_status]='SERVICE CONFIRMED'){
						$to = "MDWestServe Archive <mdwestserve@gmail.com>";
						$subject = "Service Completed UPDATE for Packet ".$OTDs["$i"]." ($d3[client_file])";
						$headers  = "MIME-Version: 1.0 \n";
						$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
						$headers .= "From: ".$_COOKIE[psdata][name]." <".$_COOKIE[psdata][email]."> \n";
						$attR = @mysql_query("select ps_to_alt from attorneys where attorneys_id = '$d3[attorneys_id]'");
						$attD = mysql_fetch_array($attR, MYSQL_BOTH);
						$c=0;
						$cc = explode(',',$attD[ps_to_alt]);
						$ccC = count($cc);
						while ($c < $ccC){
						$headers .= "Cc: ".$cc[$c]."\n";
						$c++;
						}
						$co=explode('-',$dt);
						$month=monthConvert($co[1]);
						$closeOut=$month.' '.$co[2].', '.$co[0];
						$body ="<strong>Thank you for selecting MDWestServe as Your Process Service Provider.</strong><br>Service for packet ".$OTDs["$i"]." (<strong>$d3[client_file]</strong>) was completed on <b>$closeOut</b>, via $d3[service_status].<br>An earlier email was sent listing a completion date of $d3[closeOut], which has been updated to a current date of <b>$closeOut</b>.<br><br><br><br>".$_COOKIE[psdata][name]."<br>MDWestServe<br>service@mdwestserve.com<br>".time()."<br>".md5(time());
						$headers .= "Cc: ".$_COOKIE[psdata][email]."\n";
						$headers .= "Cc: MDWestServe Archive <mdwestserve@gmail.com> \n";
						mail($to,$subject,$body,$headers);
					}
				}
				timeline($OTDs["$i"],$_COOKIE[psdata][name]." RESET Mailing Date To $dt");
				error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." RESET Mailing Date for OTD".$OTDs["$i"]." to $dt \n",3,"/logs/user.log");
			}
		}
		$i=0;
		while($i < count($EVs)){$i++;
			if ($EVs["$i"] != ''){
				//convert new date to datestamp, check new date against closeOut date, update closeOut if necessary.
				$dt=strtotime($_GET[replace]);
				$dt=date('Y-m-d', $dt);
				$q3="SELECT * FROM evictionPackets WHERE eviction_id='".$EVs["$i"]."'";
				$r3=@mysql_query($q3) or die("Query: $q3<br>".mysql_error());
				$d3=mysql_fetch_array($r3,MYSQL_ASSOC);
				echo "EVICTION: ".$EVs["$i"]."<br>CLOSEOUT: $d3[closeOut]<br>NEW DATE: $dt";
				if ($dt > $d3[closeOut]){
					$q4="UPDATE evictionPackets SET closeOut='$dt' WHERE eviction_id='".$EVs["$i"]."'";
					$r4=@mysql_query($q4) or die("Query: $q4<br>".mysql_error());
					echo "<br>UPDATED CLOSEOUT to $dt";
					//if file complete email has already been sent, send another with new closeOut date.
					if ($d3[affidavit_status]='SERVICE CONFIRMED'){
						$to = "MDWestServe Archive <mdwestserve@gmail.com>";
						$subject = "Service Completed UPDATE for Eviction ".$EVs["$i"]." ($d3[client_file])";
						$headers  = "MIME-Version: 1.0 \n";
						$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
						$headers .= "From: ".$_COOKIE[psdata][name]." <".$_COOKIE[psdata][email]."> \n";
						$attR = @mysql_query("select ps_to_alt from attorneys where attorneys_id = '$d3[attorneys_id]'");
						$attD = mysql_fetch_array($attR, MYSQL_BOTH);
						$c=0;
						$cc = explode(',',$attD[ps_to_alt]);
						$ccC = count($cc);
						while ($c < $ccC){
						$headers .= "Cc: ".$cc[$c]."\n";
						$c++;
						}
						$co=explode('-',$dt);
						$month=monthConvert($co[1]);
						$closeOut=$month.' '.$co[2].', '.$co[0];
						$body ="<strong>Thank you for selecting MDWestServe as Your Process Service Provider.</strong><br>Service for eviction ".$EVs["$i"]." (<strong>$d3[client_file]</strong>) was completed on <b>$closeOut</b>, via $d3[service_status].<br>An earlier email was sent listing a completion date of $d3[closeOut], which has been updated to a current date of <b>$closeOut</b>.<br><br><br><br>".$_COOKIE[psdata][name]."<br>MDWestServe<br>service@mdwestserve.com<br>".time()."<br>".md5(time());
						$headers .= "Cc: ".$_COOKIE[psdata][email]."\n";
						$headers .= "Cc: MDWestServe Archive <mdwestserve@gmail.com> \n";
						mail($to,$subject,$body,$headers);
					}
				}
				ev_timeline($EVs["$i"],$_COOKIE[psdata][name]." RESET Mailing Date To $dt");
				error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." RESET Mailing Date for EV".$EVs["$i"]." to $dt \n",3,"/logs/user.log");
			}
		}
	}
	if($_GET[search] && $_GET[replace]){ ?>
	<table align="center"><tr><td>
	<h1>Mailing entries updated for search term <?=$_GET[search]?>, replaced with <?=$_GET[replace]?>.</h1><br>
	</td></tr></table>
	<? }elseif($_GET[search]){ ?>
		<table align="center"><tr><td>
		<h1>Enter Replacement Term:</h1><br>
		<form><input name="replace"><input type="submit" value="SUBMIT">
		<input type="hidden" name="search" value="<?=$_GET[search]?>">
		</form>
		</tr></td></table>
	<?	$search=$_GET[search];
		$q1="SELECT * FROM ps_history WHERE action_str LIKE '%$search%' AND wizard='MAILING DETAILS'";
		$r1=@mysql_query($q1) or die ("Query: $q1<br>".mysql_error());
		echo "<table align='center' width='50%'><tr><td align='2' colspan='2'>SEARCHING FOR: <i>$search</i></td></tr>";
		echo "<tr><td>PACKET/EVICTION ID</td><td width='80%'>ACTION</td></tr>";
		while($d1=mysql_fetch_array($r1, MYSQL_ASSOC)){
			echo "<tr><td><a href='http://service.mdwestserve.com/obAffidavit.php?packet=$d1[packet_id]&mail=1&autoPrint=1' target='_blank'>OTD$d1[packet_id]</a></td><td>$d1[action_str]</td></tr>";
		}
		$q1="SELECT * FROM evictionHistory WHERE action_str LIKE '%$search%' AND wizard='MAILING DETAILS'";
		$r1=@mysql_query($q1) or die ("Query: $q1<br>".mysql_error());
		while($d1=mysql_fetch_array($r1, MYSQL_ASSOC)){
			echo "<tr><td><a href='http://service.mdwestserve.com/evictionAff.php?id=$d1[eviction_id]&mail=1&autoPrint=1' target='_blank'>EV$d1[eviction_id]</a></td><td>$d1[action_str]</td></tr>";
		}
		echo "</table>";
	}else{ ?>
	<table align="center"><tr><td>
	<h1>Enter History Search Term (<small>Keep in mind that this will update all OTDs as well as EVs, and notify client of date changes</small>):</h1><br>
	<form><input name="search"><input type="submit" value="SUBMIT"></form>
	</tr></td></table>
<? } 
}
?>