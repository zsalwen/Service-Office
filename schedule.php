<?
session_start();
include 'security.php';
include 'functions-calendar.php';
mysql_connect();
mysql_select_db('core');
$_SESSION[inc] = 0;
?>
<script>
function hideshow(which){
if (!document.getElementById)
return
if (which.style.display=="block")
which.style.display="none"
else
which.style.display="block"
}
function prompter(otd,ev,s,newDate,courier){
	var reply = prompt("Please enter your reason for updating the Est. Close Date", "")
	if (reply == null){
		alert("That is not a valid reason")
		window.location="http://staff.mdwestserve.com/schedule.php";
	}
	else{
		window.location="http://staff.mdwestserve.com/multEntry.php?otd="+otd+"&ev="+ev+"&s="+s+"&entry="+reply+"&newDate="+newDate+"&courier="+courier;
	}
}
</script>
<?
if ($_POST[courier]){
	if ($_POST[newEst] > 0){
	//use prompter
	$list='';
	if ($_POST[otd]){
		foreach( $_POST[otd] as $key => $value){
			$list .= "$key|";
		}
		$list=substr($list,0,-1);
	}
	$list2='';
	if ($_POST[ev]){
		foreach( $_POST[ev] as $key => $value){
			$list2 .= "$key|";
		}
		$list2=substr($list2,0,-1);
	}
	$list3='';
	if ($_POST[s]){
		foreach( $_POST[s] as $key => $value){
			$list3 .= "$key|";
		}
		$list3=substr($list3,0,-1);
	}
	//echo "<script>alert('OTD: $_POST[otd], EV: $_POST[ev], newEst: $_POST[newEst]');</script>";
	echo "<script>prompter('$list','$list2','$list3','$_POST[newEst]','$_POST[courier]');</script>";
	}else{
		echo "<div style='background-color:#00FF00;'>Courier Set<br />";
		if ($_POST[ev]){
			foreach( $_POST[ev] as $key => $value){
				@mysql_query("update evictionPackets set courierID = '$_POST[courier]' where eviction_id = '$key'");
@mysql_query("update sync set status='automated rescan requested' where product = 'EV' and from_id = '$key' ");
			}
		}
		if ($_POST[otd]){
			foreach( $_POST[otd] as $key => $value){
				@mysql_query("update ps_packets set courierID = '$_POST[courier]' where packet_id = '$key'");
@mysql_query("update sync set status='automated rescan requested' where product = 'OTD' and from_id = '$key' ");
			}
		}
		if ($_POST[s]){
			foreach( $_POST[s] as $key => $value){
				@mysql_query("update standard_packets set courierID = '$_POST[courier]' where packet_id = '$key'");
@mysql_query("update sync set status='automated rescan requested' where product = 'STANDARD' and from_id = '$key' ");
			}
		}
		echo "</div>";
	}
}


function clientFile($client){
	if ($client == '56'){ return 1; }
}
function serverFile($server){
	if ($server == '229'){ return 1; }
}






function isActive($status){
	if ($status == "IN PROGRESS" || $status == "ASSIGNED"){ return "<b style='color:#FF0000;'>IN SERVICE</b>"; }
	if ($status == "READY TO MAIL"){ return "<b style='color:#990000;'>Mail in progress.</b>"; }
}

function getCourier($cid){
$r=@mysql_query("SELECT courier.name, ps_packets.courierID FROM courier, ps_packets WHERE courier.courierID = ps_packets.courierID AND packet_id = '$cid' LIMIT 0,1 ");
$d=mysql_fetch_array($r,MYSQL_ASSOC);
if ($d[name]){ return strtoupper($d[name]); }else{ return " !!!MISSING!!! ";}
}
function getServer($cid){
$r=@mysql_query("select ps_packets.attorneys_id, attorneys.display_name from ps_packets, attorneys where ps_packets.packet_id = '$cid' AND attorneys.attorneys_id=ps_packets.attorneys_id LIMIT 0,1");
$d=mysql_fetch_array($r,MYSQL_ASSOC);
return strtoupper($d[display_name]);
}
function getEVServer($cid){
$r=@mysql_query("select evictionPackets.attorneys_id, attorneys.display_name from evictionPackets, attorneys where evictionPackets.eviction_id = '$cid' AND attorneys.attorneys_id=evictionPackets.attorneys_id LIMIT 0,1");
$d=mysql_fetch_array($r,MYSQL_ASSOC);
return strtoupper($d[display_name]);
}

function getEVCourier($cid){
$r=@mysql_query("SELECT courier.name, evictionPackets.courierID FROM courier, evictionPackets WHERE courier.courierID = evictionPackets.courierID AND eviction_id = '$cid' LIMIT 0,1 ");
$d=mysql_fetch_array($r,MYSQL_ASSOC);
if ($d[name]){ return strtoupper($d[name]); }else{ return "!!!MISSING!!!";}
}
function getSServer($cid){
$r=@mysql_query("select standard_packets.attorneys_id, attorneys.display_name from standard_packets, attorneys where standard_packets.packet_id = '$cid' AND attorneys.attorneys_id=standard_packets.attorneys_id LIMIT 0,1");
$d=mysql_fetch_array($r,MYSQL_ASSOC);
return strtoupper($d[display_name]);
}

function getSCourier($cid){
$r=@mysql_query("SELECT courier.name, standard_packets.courierID FROM courier, standard_packets WHERE courier.courierID = standard_packets.courierID AND packet_id = '$cid' LIMIT 0,1 ");
$d=mysql_fetch_array($r,MYSQL_ASSOC);
if ($d[name]){ return strtoupper($d[name]); }else{ return "!!!MISSING!!!";}
}
function checkTrack($packet,$doc){
	$q="SELECT * from docuTrack WHERE packet='$packet' and document like '%$doc%' LIMIT 0,1";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	if ($d){
		if ($doc == "OUT WITH COURIER"){
				$document="<b style='color:#0022ff;'>Out with Courier</b>";
			return $document;
		}
		if ($doc == "FILED AFFIDAVIT"){
				$document="<b style='color:#007700;'>Returns in office</b> ".gotScans($packet);
			return $document;
		}
	}
}
function withCourier($packet){
	$q="SELECT * from docuTrack WHERE packet='$packet' and document='OUT WITH COURIER' LIMIT 0,1";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	if ($d && !checkTrack($packet,'FILED AFFIDAVIT')){
		$document="<b style='color:#0022ff;'>Out with Courier</b>";
		return $document;
	}else{
		return checkTrack($packet,'FILED AFFIDAVIT');
	}
}


function gotScans($packet){
	$q1="SELECT method FROM ps_affidavits WHERE packetID='$packet' AND method LIKE '%Return from court%' LIMIT 0,1";
	$r1=@mysql_query($q1) or die ("Query: $q1<br>".mysql_error());
	$d1=mysql_fetch_array($r1, MYSQL_ASSOC);
	if ($d1){ 
		return "<b style='color:#cc2277;'>Awaiting scan confirmation</b>"; 
	}else{
		return "<b style='color:#cc0044; text-decoration:blink'>Ready to scan</b>"; 
	}
}

function startPrep($packet){
	$q="SELECT * from docuTrack WHERE packet='$packet' and document like '%SIGNED AFFIDAVIT%' LIMIT 0,1";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	if ($d){
		$document="<b style='color:#993344;'>Affidavits in blackhole (partial at best)</b>";
		return $document;
	}
}

function EVwithCourier($packet){
	$q="SELECT * from docuTrack WHERE packet='EV$packet' and document='OUT WITH COURIER' LIMIT 0,1";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	if ($d && !checkTrack('EV'.$packet,'FILED AFFIDAVIT')){
		$document="<b style='color:#0022ff;'>Out with Courier</b>";
		return $document;
	}else{
		return checkTrack('EV'.$packet,'FILED AFFIDAVIT');
	}
}

function SwithCourier($packet){
	$q="SELECT * from docuTrack WHERE packet='S$packet' and document='OUT WITH COURIER' LIMIT 0,1";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	if ($d && !checkTrack('S'.$packet,'FILED AFFIDAVIT')){
		$document="<b style='color:#0022ff;'>Out with Courier</b>";
		return $document;
	}else{
		return checkTrack('S'.$packet,'FILED AFFIDAVIT');
	}
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
function standardCourt($str){
	if ($str == ''){
		return "NO COURT SET";
	}else{
		return strtoupper($str);
	}
}
function getCounties($today){
	$i=0;
	$r=@mysql_query("select DISTINCT circuit_court from ps_packets where estFileDate = '$today' AND fileDate='0000-00-00' and status <> 'CANCELLED' and service_status <> 'MAIL ONLY' order by circuit_court ");
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		$list["$i"] = $d[circuit_court];
		$exclude .= " AND circuit_court <> '$d[circuit_court]'";
		$i++;
	}
	$r=@mysql_query("select DISTINCT circuit_court from evictionPackets where estFileDate = '$today' AND fileDate='0000-00-00' and status <> 'CANCELLED' and case_no <> ''$exclude order by circuit_court");
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		$list["$i"] = $d[circuit_court];
		$exclude .= " AND circuit_court <> '$d[circuit_court]'";
		$i++;
	}
	$r=@mysql_query("select DISTINCT circuit_court from standard_packets where estFileDate = '$today' AND fileDate='0000-00-00' and status <> 'CANCELLED' and case_no <> ''$exclude order by circuit_court  ");
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		$list["$i"] = $d[circuit_court];
		$i++;
	}
	if ($list != ''){
		sort($list);
		return $list;
	}
}
function OTDFill($today,$court){
	$x=@mysql_query("select packet_id, date_received, case_no, fileDate, service_status, process_status, filing_status, attorneys_id, server_id, rush from ps_packets where estFileDate = '$today' AND circuit_court = '$court' and status <> 'CANCELLED' and fileDate = '0000-00-00' and service_status <> 'MAIL ONLY' AND case_no <> ''");
	while ($dx=mysql_fetch_array($x,MYSQL_ASSOC)){
		echo "<div ";
		$getCourier='';
		$getCourier=getCourier($dx[packet_id]);
		if ($getCourier == 'DO NOT FILE!'){
			echo "style='background-color:#FFcccc; ";
		}else{
			echo "style='background-color:#ccFFcc; ";
		}

		if ($getCourier == ' !!!MISSING!!! '){
			echo "border: 5px double yellow;'";
			$courier=" font-size:15px;";
		}else{
			echo "'";
		}
		echo ">";
	?>
	<input type="checkbox" name="otd[<?=$dx[packet_id]?>]">
		<a class="otd" href="/otd/order.php?packet=<?=$dx[packet_id]?>" target="_Blank">OTD<?=$dx[packet_id]?></a>
		<? if ($dx[rush] != ''){ echo "<b style='background-color:#FFBB00; color:000000; font-weight:bold; font-size:16px; text-decoration:blink;'>RUSH</b>";} ?>
		<b style='color:#990000;'><?=strtoupper($dx[case_no]);?></b> <b style='color:#003377;'><?=getServer($dx[packet_id]);?></b>
		<b style='color:#330077;<?=$courier?>'><?=$getCourier;?></b> <?=isActive($dx[service_status])?> 
		<? 	if ($dx[fileDate] != "0000-00-00"){ echo "<b style='color:#009900;'>FILE CLOSED ON ".$dx[fileDate]."</b>"; }
			elseif($dx[filing_status] == "PREP TO FILE" && !withCourier($dx[packet_id]) && !clientFile($dx[attorneys_id]) && !serverFile($dx[server_id]) ){ echo "<b style='color:#00cc00;'>Ready for courier.</b>"; }
			elseif($dx[filing_status] == "PREP TO FILE" && !withCourier($dx[packet_id]) && !clientFile($dx[attorneys_id]) && serverFile($dx[server_id]) ){ echo "<b style='color:#00cc00;'>Ready to send to server to file.</b>"; }
			elseif($dx[filing_status] == "PREP TO FILE" && !withCourier($dx[packet_id]) && clientFile($dx[attorneys_id]) && !serverFile($dx[server_id]) ){ echo "<b style='color:#00cc00;'>Ready to send to client to file.</b>"; }
			elseif(withCourier($dx[packet_id])){ echo withCourier($dx[packet_id]); }
			else{ echo startPrep($dx[packet_id]);}?></div><?
	}
}
function OTDmissing($today,$court){
	$x=@mysql_query("select packet_id, date_received, case_no, fileDate, service_status, process_status, filing_status, attorneys_id, server_id, rush from ps_packets where estFileDate = '$today' AND circuit_court = '$court' and status <> 'CANCELLED' and fileDate = '0000-00-00' and service_status <> 'MAIL ONLY' AND case_no = ''");
	while ($dx=mysql_fetch_array($x,MYSQL_ASSOC)){
		$missingCases++;
		$listNum++;
		$_SESSION[inc] = $_SESSION[inc]+1;
		$missingList[0] .= '<div ';
		$getCourier='';
		$getCourier=getCourier($dx[packet_id]);
		if ($getCourier == 'DO NOT FILE!'){
			$missingList[0] .= "style='background-color:#FFcccc; ";
		}else{
			$missingList[0] .= "style='background-color:#ccFFcc; ";
		}

		if ($getCourier == ' !!!MISSING!!! '){
			$missingList[0] .=  "border: 5px double yellow;'";
			$courier=" font-size:15px;";
		}else{
			$missingList[0] .= "'";
		}
		$missingList[0] .= "><input type='checkbox' name='otd[".$dx[packet_id]."]'>";
		if ($dx[rush] != ''){
			$missingList[0] .=  "<b style='background-color:#FFBB00; color:000000; font-weight:bold; font-size:16px; text-decoration:blink;'>RUSH</b>";
		}
		$missingList[0] .= "<a class='otd' href='/otd/order.php?packet=".$dx[packet_id]."' target='_Blank'>OTD".$dx[packet_id]."</a>
		<b style='color:#003377;'>".getServer($dx[packet_id])."</b>
		<b style='color:#330077;$courier'>".getCourier($dx[packet_id])."</b> ".isActive($dx[service_status])." ".isActive($dx[process_status]);
		if ($dx[fileDate] != "0000-00-00"){
			$missingList[0] .= "<b style='color:#009900;'>FILE CLOSED ON ".$dx[fileDate]."</b>";
		}
		elseif($dx[filing_status] == "PREP TO FILE" && !withCourier($dx[packet_id]) && !clientFile($dx[attorneys_id]) && !serverFile($dx[server_id]) ){
			$missingList[0] .= "<b style='color:#00cc00;'>Ready for courier.</b>";
		}
		elseif($dx[filing_status] == "PREP TO FILE" && !withCourier($dx[packet_id]) && !clientFile($dx[attorneys_id]) && serverFile($dx[server_id]) ){
			$missingList[0] .= "<b style='color:#00cc00;'>Ready to send to server to file.</b>";
		}
		elseif($dx[filing_status] == "PREP TO FILE" && !withCourier($dx[packet_id]) && clientFile($dx[attorneys_id]) && !serverFile($dx[server_id]) ){
			$missingList[0] .= "<b style='color:#00cc00;'>Ready to send to client to file.</b>";
		}
		elseif(withCourier($dx[packet_id])){
			$missingList[0] .= withCourier($dx[packet_id]);
		}else{
			$missingList[0] .= startPrep($dx[packet_id]);
		}
		$missingList[0] .= "</div>";
	}
	$missingList[1]=$missingCases++;
	return $missingList;
}
function EVfill ($today,$court){
	$x=@mysql_query("select eviction_id, date_received, case_no, fileDate, service_status, process_status, filing_status, attorneys_id, server_id, rush from evictionPackets where estFileDate = '$today' AND circuit_court = '$court' and status <> 'CANCELLED' and case_no <> '' and fileDate = '0000-00-00'");
	//$count=mysql_num_rows($x);
	while ($dx=mysql_fetch_array($x,MYSQL_ASSOC)){
	?><div    
	<?
	$missingList='';
	$getEVCourier='';
	$getEVCourier=getEVCourier($dx[eviction_id]);
	if ($getEVCourier == 'DO NOT FILE!'){
		$missingList .=  "background-color:#FFcccc;";
	}else{
		$missingList .=  "background-color:#ccFFcc;";
	}

	if (trim($getEVCourier) == '!!!MISSING!!!'){
		$missingList .= "border: 5px double yellow;";
		$courier=" font-size:15px;";
	}
	if ($missingList != ''){
		echo "style='$missingList'";
	}
?>
	><input type="checkbox" name="ev[<?=$dx[eviction_id]?>]">
		<a class="ev" href="/ev/order.php?packet=<?=$dx[eviction_id]?>" target="_Blank">EV<?=$dx[eviction_id]?></a> 
		<? if ($dx[rush] != ''){ echo "<b style='background-color:#FFBB00; color:000000; font-weight:bold; font-size:16px; text-decoration:blink;'>RUSH</b>";} ?>
		<b style='color:#990000;'><?=strtoupper($dx[case_no]);?></b> 
		<b style='color:#003377;'><?=getEVServer($dx[eviction_id]);?></b> 
		<b style='color:#330077;<?=$courier?>'><?=$getEVCourier;?></b> 
		<?=isActive($dx[service_status])?> 
		<? 	if ($dx[fileDate] != "0000-00-00"){ echo "<b style='color:#009900;'>FILE CLOSED ON ".$dx[fileDate]."</b>"; }
		elseif($dx[filing_status] == "PREP TO FILE" && !EVwithCourier($dx[eviction_id]) && !clientFile($dx[attorneys_id]) && !serverFile($dx[server_id]) ){ echo "<b style='color:#00cc00;'>Ready for courier.</b>"; }
		elseif($dx[filing_status] == "PREP TO FILE" && !EVwithCourier($dx[eviction_id]) && !clientFile($dx[attorneys_id]) && serverFile($dx[server_id]) ){ echo "<b style='color:#00cc00;'>Ready to send to server to file.</b>"; }
		elseif($dx[filing_status] == "PREP TO FILE" && !EVwithCourier($dx[eviction_id]) && clientFile($dx[attorneys_id]) && !serverFile($dx[server_id]) ){ echo "<b style='color:#00cc00;'>Ready to send to client to file.</b>"; }
		elseif(EVwithCourier($dx[eviction_id])){ echo EVwithCourier($dx[eviction_id]);}
		else{ echo startPrep('EV'.$dx[eviction_id]);}
		echo "</div>";
	}
}
function Sfill($today,$court){
	$x=@mysql_query("select packet_id, date_received, case_no, fileDate, service_status, process_status, filing_status, attorneys_id, server_id, rush from standard_packets where estFileDate = '$today' AND circuit_court = '$court' and status <> 'CANCELLED' and fileDate = '0000-00-00'");
	//$count=mysql_num_rows($x);
	while ($dx=mysql_fetch_array($x,MYSQL_ASSOC)){
	?><div    
	<?
	$missingList='';
	$getSCourier='';
	$getSCourier=getSCourier($dx[packet_id]);
	if (trim($getSCourier) == 'DO NOT FILE!'){
		$missingList .=  "background-color:#FFcccc;";
	}else{
		$missingList .=  "background-color:#ccFFcc;";
	}
	if (trim($getSCourier) == '!!!MISSING!!!'){
		$missingList .= "border: 5px double yellow;";
		$courier=" font-size:15px;";
	}
	if ($missingList != ''){
		echo "style='$missingList'";
	}
	?>
	><input type="checkbox" name="s[<?=$dx[packet_id]?>]">
		<a class="s" href="/standard/order.php?packet=<?=$dx[packet_id]?>" target="_Blank">S<?=$dx[packet_id]?></a>
		<? if ($dx[rush] != ''){ echo "<b style='background-color:#FFBB00; color:000000; font-weight:bold; font-size:16px; text-decoration:blink;'>RUSH</b>";} ?>		
		<b style='color:#990000;'><?=strtoupper($dx[case_no]);?></b> 
		<b style='color:#003377;'><?=getSServer($dx[packet_id]);?></b> 
		<b style='color:#330077;<?=$courier?>'><?=$getSCourier;?></b> 
		<?=isActive($dx[service_status])?> 
	<? 	if ($dx[fileDate] != "0000-00-00"){ echo "<b style='color:#009900;'>FILE CLOSED ON ".$dx[fileDate]."</b>"; }
		elseif($dx[filing_status] == "PREP TO FILE" && !SwithCourier($dx[packet_id]) && !clientFile($dx[attorneys_id]) && !serverFile($dx[server_id]) ){ echo "<b style='color:#00cc00;'>Ready for courier.</b>"; }
		elseif($dx[filing_status] == "PREP TO FILE" && !SwithCourier($dx[packet_id]) && !clientFile($dx[attorneys_id]) && serverFile($dx[server_id]) ){ echo "<b style='color:#00cc00;'>Ready to send to server to file.</b>"; }
		elseif($dx[filing_status] == "PREP TO FILE" && !SwithCourier($dx[packet_id]) && clientFile($dx[attorneys_id]) && !serverFile($dx[server_id]) ){ echo "<b style='color:#00cc00;'>Ready to send to client to file.</b>"; }
		elseif(SwithCourier($dx[packet_id])){ echo SwithCourier($dx[packet_id]);}
		else{ echo startPrep('S'.$dx[packet_id]);}
		echo "</div>";
	}
}
function next_weekday($timestamp = NULL) {
	if($timestamp === NULL) $timestamp = time();
	$next = strtotime("midnight +1 day", $timestamp);
	$d = getdate($next);
	if($d['wday'] == 0 || $d['wday'] == 6) $next = strtotime("midnight next monday", $timestamp);
	return $next;
} 
function dailyContainer($today){
	$i=0;
	$court=getCounties($today);
	if ($today == date('Y-m-d')){
		$bgcolor="#FF0000;";
	}elseif($today == date('Y-m-d',next_weekday())){
		$bgcolor="#FFFF00";
	}else{
		$bgcolor="#00FF00";
	}
	echo "<div style='background-color:$bgcolor;' align='center'><b>$today</b></div>";
	while ($i < count($court)){
		$missingCases=0;
		$missingList='';
		?>
		<fieldset>
			<legend onClick="hideshow(document.getElementById('OTD<?=str_replace(' ','',$court["$i"])?><?=str_replace('-','',$today);?>'));" ><?=$court["$i"]?></legend>
			<div id="<?=str_replace(' ','',$court["$i"])?><?=str_replace('-','',$today);?>" name="<?=str_replace(' ','',$court["$i"])?><?=str_replace('-','',$today);?>" >
		<?
		//run OTD, EV, and S lists
		OTDfill($today,$court["$i"]);
		EVfill($today,$court["$i"]);
		Sfill($today,$court["$i"]);
		//run missing case # lists
		$missing=OTDmissing($today,$court["$i"]);
		$missingList=$missing[0];
		$missingCases=$missing[1];
		if ($missingCases > 0){
			echo "<div style='display:none;' id='List".$_SESSION[inc]."'>$missingList</div>";
			echo "<div style='background-color:#ffffff; color:red;' onclick=\"hideshow(document.getElementById('List".$_SESSION[inc]."'))\">$missingCases OTD Files Missing Case Numbers</div>";
		}
		echo "</div></fieldset>";
		$i++;
	}
}
function getEstFile($yesterday){
	$i=0;
	$r=@mysql_query("select distinct estFileDate from ps_packets where estFileDate > '$yesterday' order by estFileDate");
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		$list["$i"] = $d[estFileDate];
		$exclude .= " AND estFileDate <> '$d[estFileDate]'";
		$i++;
	}
	$r=@mysql_query("select distinct estFileDate from evictionPackets where estFileDate > '$yesterday'$exclude order by estFileDate");
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		$list["$i"] = $d[estFileDate];
		$exclude .= " AND estFileDate <> '$d[estFileDate]'";
		$i++;
	}
	$r=@mysql_query("select distinct estFileDate from standard_packets where estFileDate > '$yesterday'$exclude order by estFileDate");
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		$list["$i"] = $d[estFileDate];
		$i++;
	}
	if ($list != ''){
		sort($list);
		return $list;
	}
}

hardLog('Post-Service Schedule','user');



?>
<link rel="stylesheet" type="text/css" href="fire.css" />
<style>
fieldset {
margin:0px;
padding:0px;
border:ridge 5px #ff0000;
background-color:#cccccc;
		}
legend	{
margin:0px;
//padding:0px;
border:ridge 4px  #006666;
background-color:#ffffFF;
font-size:13px;
		} 
h2	{
margin:0px;
//padding:5px;
border:ridge 4px  #006666;
background-color:#66ccdd;
		} 
ol	{
margin:0px;
padding:0px;
		}
li	{
margin:0px;
padding:0px;
padding-left:10px;
		}
a
		{
		width:200px;
		border:solid 1px #999999;
		padding:2px;
		font-weight:bold;
		}
a.otd
		{
		width:200px;
		border:solid 1px #999999;
		padding:2px;
		background-color:#FFFF00;
		font-weight:bold;
		}
a.ev
		{
		width:200px;
		border:solid 1px #999999;
		padding:2px;
		background-color:#00FFFF;
		font-weight:bold;
		}
a.s
		{
		width:200px;
		border:solid 1px #999999;
		padding:2px;
		background-color:#FF0000;
		font-weight:bold;
		}
b
		{
		width:200px;
		}
div 
		{
		font-size:11px;
		border:solid 0px #000;
		border-bottom:0px;
		}
</style>

<?
if ($_GET[back]){
	$back=$_GET[back];
}else{
	$back=1;
}
$yesterday = date('Y-m-d', mktime(0, 0, 0, date("m") , date("d") - $back, date("Y")));
$today = date('Y-m-d');

?>
<form method="post">
<div style='background-color:#FF0000;' align="center">
Courier: <select name="courier">
<?
$CCr=@mysql_query("select * from courier WHERE isActive='1' order by name ASC");
while($CCd=mysql_fetch_array($CCr,MYSQL_ASSOC)){
if ($CCd[phone]){
	$phone="-".$CCd[phone];
}else{
	$phone='';
}
?>
<option value="<?=$CCd[courierID]?>"><?=$CCd[name]?><?=$phone?></option>
<? }?></select>
<input type="submit" value="Set as courier">
!!! ONLY SET IF CHANGING DATE: <input name="newEst"> !!! YYYY-MM-DD !!!</div>
<table>


<tr style="background-color:transparent;">
<?

$i=0;
$est=getEstFile($yesterday);
while($i < count($est)){
	//echo "<script>alert('Getting info for ".$est["$i"]."')</script>";
	echo "<td valign='top'>";
	dailyContainer($est["$i"]);
	echo "</td>";
	$i++;
}
?>
</tr>






</table>
</form>
<?
include 'footer.php';
?><script>document.title='Post-Service Schedule'</script>