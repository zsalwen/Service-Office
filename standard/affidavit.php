<?
function debug($line,$str){
	//error_log(time()." Line $line: $str \n", 3, '/logs/debug.log');
}
session_start();
debug(6,'start');
include 'functions.php';
@mysql_connect ();
mysql_select_db ('core');
// start output buffering
	$subtract='0';

function defCount($packet_id){
	debug(14,"defCount($packet_id)");
	$c=0;
	$r=@mysql_query("SELECT name1, name2, name3, name4, name5, name6 from standard_packets WHERE packet_id='$packet_id'");
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	if ($d[name1]){$c++;}
	if ($d[name2]){$c++;}
	if ($d[name3]){$c++;}
	if ($d[name4]){$c++;}
	if ($d[name5]){$c++;}
	if ($d[name6]){$c++;}
	debug(24,"defCount($packet_id) return $c");
	return $c;
}
/*
?>
<style>
.dim {
   /* the filter attribute is recognized in
   Internet Explorer and should be a percentage * /
   filter: Alpha(opacity=40);
   /* the -moz-opacity attribute is recognized by 
   Gecko browsers and should be a decimal * /
   -moz-opacity: .40;
   /* opacity is the proposed CSS3 method, supported
   in recent Gecko browsers * /
   opacity: .40;
}
.dimmer {
   /* the filter attribute is recognized in
   Internet Explorer and should be a percentage * /
   filter: Alpha(opacity=25);
   /* the -moz-opacity attribute is recognized by 
   Gecko browsers and should be a decimal * /
   -moz-opacity: .25;
   /* opacity is the proposed CSS3 method, supported
   in recent Gecko browsers * /
   opacity: .25;
}
*/ ?>
<style>
td { font-variant:small-caps; font-size:12px;}
td.a {font-size:14px;}
td.b {font-size:24px;}
table { page-break-after:always;}
p {border-style:solid; border-width:thick; border-collapse:collapse;}
</style>
<?
/*
if ($_GET[server]){
	$serveID=$_GET[server];
	$def = 0;
}elseif ($_GET[packet]){
	$packet = $_GET[packet];
	$def = 0;
}*/
function makeAffidavit($p,$defendant){
	debug(70,"makeAffidavit($p,$defendant)");
	$packet = $p;
	$def = 0;
	if (strpos($defendant,"!")){
		$overRide=1;
		$explode=explode('!',$defendant);
		$defendant=$explode[0];
	}
	while ($def < defCount($packet)){$def++;
	$q1='';
	$r1='';
	$d1='';
	$q2='';
	$r2='';
	$d2='';
	$q3='';
	$r3='';
	$d3='';
	$q4='';
	$r4='';
	$d4='';
	$sign_by='';
	$attempts = '';
	$iID = '';
	$attemptsa = '';
	$iIDa = '';
	$attemptsb = '';
	$iIDb = '';
	$attemptsc = '';
	$iIDc = '';
	$attemptsd = '';
	$iIDd = '';
	$attemptse = '';
	$iIDe = '';
	$posting = '';
	$iiID = '';
	$delivery = '';
	$deliveryID = '';
	$resident = '';
	$residentDesc = '';
	$serveAddress = '';
	$nondef='';
	$mailing = '';
	$crr='';
	$iiiID = '';
	$first='';

	// get main information
	if ($overRide == '1'){
		$q1="SELECT * FROM standard_packets WHERE packet_id='$packet'";
		$dim="";
	}else{
		$q1="SELECT * FROM standard_packets WHERE packet_id='$packet' AND affidavit_status='SERVICE CONFIRMED'";
		$dim="class='dim'";
	}
	$r1=@mysql_query($q1) or die(mysql_error());
	$d1=mysql_fetch_array($r1, MYSQL_ASSOC);
	if ($d1[amendedAff] == "checked"){
		$amended="Amended ";
	}else{
		$amended="";
	}
	$court = $d1[circuit_court];
	$courtType = $d1[courtType];
	if (!preg_match("/CITY|D.C./", $court)){
		$court = str_replace('PRINCE GEORGES','PRINCE GEORGE\'S',$court);
		$court = str_replace('QUEEN ANNES','QUEEN ANNE\'S',$court);
		$court = str_replace('ST MARYS','ST MARY\'S',$court);
		$court = ucwords(strtolower($court))." County";
	} else {
		$court = ucwords(strtolower($court));
	}
	// get plaintiff information
	mysql_select_db ('core');
	$q2="SELECT * from attorneys where attorneys_id = '$d1[attorneys_id]'";
	$r2=@mysql_query($q2) or die(mysql_error());
	$d2=mysql_fetch_array($r2, MYSQL_ASSOC);
	if ($d1[altPlaintiff] != '' && $d1[attorneys_id] != '1'){
		$plaintiff = str_replace('-','<br>',$d1[altPlaintiff]);
	}elseif($d1[altPlaintiff] != ''){
		$plaintiff = str_replace('-','<br>',$d1[altPlaintiff]);
	}else{
		$plaintiff = str_replace('-','<br>',$d2[ps_plaintiff]);
	}
	if ($d1[addlDocs] != ''){
		$addlDocs=$d1[addlDocs].",";
	}
	mysql_select_db ('core');
	// get service history
	$q4="SELECT * from standard_history where packet_id = '$packet' AND defendant_id = '$def' and (wizard='FIRST EFFORT' or wizard='INVALID') and onAffidavit='checked' order by sort_value desc";
	$r4=@mysql_query($q4) or die(mysql_error());
	while ($d4=mysql_fetch_array($r4, MYSQL_ASSOC)){
		if ($d4[serverID] == $d1[server_id]){
			debug(162,"Service History: $d4[action_str] $d4[serverID]");
			$attempts .= $d4[action_str];
			$iID = $d4[serverID];
		}elseif($d1[server_ida] && $d4[serverID] == $d1[server_ida]){
			debug(166,"Service History: $d4[action_str] $d4[serverID]");
			$attemptsa .= $d4[action_str];
			$iIDa = $d4[serverID];
		}elseif($d1[server_idb] && $d4[serverID] == $d1[server_idb]){
			debug(170,"Service History: $d4[action_str] $d4[serverID]");
			$attemptsb .= $d4[action_str];
			$iIDb = $d4[serverID];
		}elseif($d1[server_idc] && $d4[serverID] == $d1[server_idc]){
			debug(174,"Service History: $d4[action_str] $d4[serverID]");
			$attemptsc .= $d4[action_str];
			$iIDc = $d4[serverID];
		}elseif($d1[server_idd] && $d4[serverID] == $d1[server_idd]){
			debug(178,"Service History: $d4[action_str] $d4[serverID]");
			$attemptsd .= $d4[action_str];
			$iIDd = $d4[serverID];
		}elseif($d1[server_ide] && $d4[serverID] == $d1[server_ide]){
			debug(182,"Service History: $d4[action_str] $d4[serverID]");
			$attemptse .= $d4[action_str];
			$iIDe = $d4[serverID];
		}
	}

	$q4="SELECT * from standard_history where packet_id = '$packet' AND defendant_id = '$def' and wizard='SECOND EFFORT' and onAffidavit='checked' order by sort_value";
	$r4=@mysql_query($q4) or die(mysql_error());
	while ($d4=mysql_fetch_array($r4, MYSQL_ASSOC)){
		if ($d4[serverID]==$d1[server_id]){
			$attempts .= $d4[action_str];
			$iID = $d4[serverID];
		}elseif($d1[server_ida] && $d4[serverID]==$d1[server_ida]){
			$attemptsa .= $d4[action_str];
			$iIDa = $d4[serverID];
		}elseif($d1[server_idb] && $d4[serverID]==$d1[server_idb]){
			$attemptsb .= $d4[action_str];
			$iIDb = $d4[serverID];
		}elseif($d1[server_idc] && $d4[serverID]==$d1[server_idc]){
			$attemptsc .= $d4[action_str];
			$iIDc = $d4[serverID];
		}elseif($d1[server_idd] && $d4[serverID]==$d1[server_idd]){
			$attemptsd .= $d4[action_str];
			$iIDd = $d4[serverID];
		}elseif($d1[server_ide] && $d4[serverID]==$d1[server_ide]){
			$attemptse .= $d4[action_str];
			$iIDe = $d4[serverID];
		}
	}

	$q4="SELECT * from standard_history where packet_id = '$packet' AND defendant_id = '$def' and action_type = 'Posted Papers' and onAffidavit='checked'";
	$r4=@mysql_query($q4) or die(mysql_error());
	$d4=mysql_fetch_array($r4, MYSQL_ASSOC);
	$posting = $d4[action_str];
	$iiID = $d4[serverID];

	$q4="SELECT * from standard_history where packet_id = '$packet' AND defendant_id = '$def' and action_type = 'First Class C.R.R. Mailing' and onAffidavit='checked'";
	$r4=@mysql_query($q4) or die(mysql_error());
	while ($d4=mysql_fetch_array($r4, MYSQL_ASSOC)){
		$mailing .= $d4[action_str];
		$crr=$d4[action_type];
		$iiiID = $d4[serverID];
	}
	if ($mailing == ''){
		$q4="SELECT * from standard_history where packet_id = '$packet' AND defendant_id = '$def' and action_type = 'First Class Mailing' and onAffidavit='checked'";
		$r4=@mysql_query($q4) or die(mysql_error());
		while ($d4=mysql_fetch_array($r4, MYSQL_ASSOC)){
			$mailing .= $d4[action_str];
			$iiiID = $d4[serverID];
			$first = $d4[action_type];
		}
	}

	$q4="SELECT * from standard_history where packet_id = '$packet' AND defendant_id = '$def' and action_type = 'Served Defendant' and onAffidavit='checked'";
	$r4=@mysql_query($q4) or die(mysql_error());
	$d4=mysql_fetch_array($r4, MYSQL_ASSOC);
	$delivery = $d4[action_str];
	$deliveryID = $d4[serverID];
	$serveAddress = $d4[address];
	if ($delivery == ''){
		$q4="SELECT * from standard_history where packet_id = '$packet' AND defendant_id = '$def' and action_type = 'Served Resident' and onAffidavit='checked'";
		$r4=@mysql_query($q4) or die(mysql_error());
		$d4=mysql_fetch_array($r4, MYSQL_ASSOC);
		$delivery = $d4[action_str];
		$deliveryID = $d4[serverID];
		$resident = $d4[resident];
		$residentDesc = $d4[residentDesc];
		$serveAddress = $d4[address];
		$nondef='1';
	}
	// new settings
	if ($delivery != ''){
		debug(254,"Set type = 'pd'");
		$type = 'pd';
	}else{
		debug(257,"Set type = 'non'");
		$type = 'non';
	}
	// hard code
	$header="<td colspan='2' align='center' style='font-size:24px; font-variant:small-caps;'>State of Maryland<br><br></td></tr>
		<tr><td colspan='2' align='center' style='font-size:20px;'>".$courtType." Court for ".$court."<br><br></td></tr>
		<tr></tr>
		<tr><td class='a' style='width:600px;'>".$plaintiff."<br><small>_____________________<br /><em>Plaintiff</em></small><br /><br />v.<br /><br />";
			if ($d1[onAffidavit1]=='checked'){$header .= strtoupper($d1['name1']).'<br>';}
			if ($d1['name2'] && $d1[onAffidavit2]=='checked'){$header .= strtoupper($d1['name2']).'<br>';}
			if ($d1['name3'] && $d1[onAffidavit3]=='checked'){$header .= strtoupper($d1['name3']).'<br>';}
			if ($d1['name4'] && $d1[onAffidavit4]=='checked'){$header .= strtoupper($d1['name4']).'<br>';}
			if ($d1['name5'] && $d1[onAffidavit5]=='checked'){$header .= strtoupper($d1['name5']).'<br>';}
			if ($d1['name6'] && $d1[onAffidavit6]=='checked'){$header .= strtoupper($d1['name6']).'<br>';}
			$header .=strtoupper($d1['address1']).'<br>';
			$header .=strtoupper($d1['city1']).', '.strtoupper($d1['state1']).' '.$d1['zip1'].'<br>';
			$header .= "<small>_____________________<br /><em>Defendant</em></small></td>
				<td align='right' valign='top' style='width:200px' nowrap='nowrap'><div style='font-size:24px; border:solid 1px #666666; text-align:center;'>Case Number<br />&nbsp;".str_replace(0,'&Oslash;',$d1[case_no])."</div>";

	if ($type == "non"){
		$article = "14-209(b)";
		//$result = "MAILING AND POSTING";
		if ($attempts != ''){
				$history = "<div style='font-weight:300'><u>Describe with particularity the good faith efforts to serve ".$d1["name$def"].":</u></div>
				".$attempts;
			}elseif($attemptsa != ''){
				$history = "<div style='font-weight:300'><u>Describe with particularity the good faith efforts to serve ".$d1["name$def"].":</u></div>
				".$attemptsa;
				$iID=$iIDa;
			}
			$history2 = "<div style='font-weight:300'><u>Include the date of the posting and a description of the location of the posting on the property:</u></div>".$posting;
		if ($mailing == ''){
			$history3 = "<div class='dim' style='font-weight:300'><u>State the date on which the required papers were mailed by first-class and certified mail, return receipt requested, and the name and address of the addressee:</u>
				<center><font size='36 px'>AWAITING MAILING<br>DO NOT FILE</font></center></div>";
			$noMail = 1;
		}else{
			if ($crr != ''){
				$history3 = "<div style='font-weight:300'><u>State the date on which the required papers were mailed by first-class and certified mail, return receipt requested, and the name and address of the addressee:</u></div>
				".$mailing;
			}elseif(($iiID == $d1[server_id]) || ($first != '' && $crr == '')){
				$history3 = "<div style='font-weight:300'><u>State the date on which the required papers were mailed by first-class and the name and address of the addressee:</u></div>
				".$mailing;
			}
		}
			$history4 = "<u>If available, the original certified mail return receipt shall be attached to the affidavit.</u><div style='height:50px; width:550px; border:double 4px; color:#666'>Affix original certified mail return receipt here.</div>";
	}
	if ($type == "pd"){
		$article = "14-209(a)";
		$result = "PERSONAL DELIVERY";
	}
	// ok let's really have some fun with this 
	$history = attorneyCustomLang($d1[attorneys_id],$history);
	$history1 = attorneyCustomLang($d1[attorneys_id],$history1);
	$history2 = attorneyCustomLang($d1[attorneys_id],$history2);
	$history3 = attorneyCustomLang($d1[attorneys_id],$history3);
	$history4 = attorneyCustomLang($d1[attorneys_id],$history4);
	$delivery = attorneyCustomLang($d1[attorneys_id],$delivery);
	if ($type == "non"){
	//begin output buffering
	ob_start();
	//------------------------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------------------------
	if ($iIDe){
		$hiID=$iIDe;
	}elseif($iIDd){
		$hiID=$iIDd;
	}elseif($iIDc){
		$hiID=$iIDc;
	}elseif($iIDb){
		$hiID=$iIDb;
	}elseif($iIDa){
		$hiID=$iIDa;
	}elseif($iID){
		$hiID=$iID;
	}
	//$topPage["$def"] = ob_get_clean();
	//ob_start();
	//Multiple servers' attemps begin here
	//6th server
	if ($iIDe){
		$historye = "<div style='font-weight:300'><u>Describe with particularity the good faith efforts to serve ".$d1["name$def"].":</u></div>
				".$attemptse;
	$r5=@mysql_query("SELECT * from ps_signatory where serverID='$iIDe' AND packetID='$packet'");
	$d5=mysql_fetch_array($r5, MYSQL_ASSOC);
	$serverName=$d5[name];
	$serverID=$hiID;
	$serverAdd=$d5[address];
	$serverCity=$d5[city];
	$serverState=$d5[state];
	$serverZip=$d5[zip];
	$serverPhone=$d5[phone];
	if (!$d5){
	$q3="SELECT * from ps_users where id = '$iIDe'";
	$r3=@mysql_query($q3) or die(mysql_error());
	$d3=mysql_fetch_array($r3, MYSQL_ASSOC);
	$serverName=$d3[name];
	$serverAdd=$d3[address];
	$serverCity=$d3[city];
	$serverState=$d3[state];
	$serverZip=$d3[zip];
	$serverPhone=$d3[phone];
	}
	$cord='S'.$d1[packet_id]."-".$def."-".$serverID."%";
	?>
		<table width="80%" align="center" bgcolor="#FFFFFF" <? if (strtoupper($d1[affidavit_status]) != "SERVICE CONFIRMED"){ echo $dim;}?>>
		<tr><?=$header?><IMG SRC='barcode.php?barcode=<?=$cord?>&width=300&height=40'><center>File Number: <?=$d1[client_file]?><br>[PAGE]</center></td></tr>
		<tr>
			<td colspan="2" align="center" style="font-weight:bold; text-decoration:underline" height="30px" valign="top"><?=$amended?><font size='+2'>Affidavit of Service</font><? if ($iID && !$iIDa && !$iIDb && !$iIDc && !$iIDd && !$iIDe){ echo " and Posting";}?></td>
		</tr>
		<tr>
			<td colspan="2" align="center" style="font-weight:bold; font-size:20px;" height="30px" valign="top"><?=$result?></td>
		</tr>
		<tr>
`		</tr>
		<tr>
			<td colspan="2" style="font-weight:bold; padding-left:20px;"><?=stripslashes($historye)?></td>
		</tr>   
		<tr>
			<td colspan="2">I solemnly affirm under the penalties of perjury that the contents of this <?=strtolower($amended)?>affidavit are true and correct, to the best of my knowledge, information and belief<? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?>, and that I did perform service as set forth above<? }?><? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?>, and that I served the Order to Docket and all other papers filed with it to [PERSON SERVED]<? }?>.<br></td>
		</tr>
		<tr>
			<td colspan="2">I, <?=$serverName?>, certify that I am over eighteen years old and not a party to this action<? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?> and that I served [PERSON SERVED], [RELATION TO DEFENDANT]<? }?><? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?> and that I did perform service as set forth above<? }?>.<br /></td>
		</tr>
		<tr>
			<td valign="top" width="50%"></td>
			<td valign="top" width="50%">________________________<u>DATE:</u>________<br /><?=$serverName?><br /><?=$serverAdd?><br /><?=$serverCity?>, <?=$serverState?> <?=$serverZip?><br /><?=$serverPhone?><br><?=$_SERVER[REMOTE_ADDR]?><br><?=$_SERVER[REMOTE_ADDR]?></td> 
		</tr>
	</table>
	<? }
	$pagee["$def"] = ob_get_clean();
	ob_start();
	//5th server
	if ($iIDd){
		$historyd = "<div style='font-weight:300'><u>Describe with particularity the good faith efforts to serve ".$d1["name$def"].":</u></div>
				".$attemptsd;
	 ?>
		<table width="80%" align="center" bgcolor="#FFFFFF" <? if (strtoupper($d1[affidavit_status]) != "SERVICE CONFIRMED"){echo $dim;}?>>
	<?
	$r5=@mysql_query("SELECT * from ps_signatory where serverID='$iIDd' AND packetID='$packet'");
	$d5=mysql_fetch_array($r5, MYSQL_ASSOC);
	$serverName=$d5[name];
	$serverID=$iIDd;
	$serverAdd=$d5[address];
	$serverCity=$d5[city];
	$serverState=$d5[state];
	$serverZip=$d5[zip];
	$serverPhone=$d5[phone];
	if (!$d5){
	$q3="SELECT * from ps_users where id = '$iIDd'";
	$r3=@mysql_query($q3) or die(mysql_error());
	$d3=mysql_fetch_array($r3, MYSQL_ASSOC);
	$serverName=$d3[name];
	$serverAdd=$d3[address];
	$serverCity=$d3[city];
	$serverState=$d3[state];
	$serverZip=$d3[zip];
	$serverPhone=$d3[phone];
	}
	$cord='S'.$d1[packet_id]."-".$def."-".$serverID."%";
	?>        
		<? echo "<tr>".$header."<IMG SRC='barcode.php?barcode=".$cord."&width=300&height=40'><center>File Number: ".$d1[client_file]."<br>[PAGE]</center></td></tr>"; ?>
		<tr>
			<td colspan="2" align="center" style="font-weight:bold; text-decoration:underline" height="30px" valign="top"><?=$amended?><font size='+2'>Affidavit of Service</font></td>
		</tr>
		<tr>
			<td colspan="2" align="center" style="font-weight:bold; font-size:20px;" height="30px" valign="top"><?=$result?></td>
		</tr>
		<tr>
			<td colspan="2" align="left">I, <?=$serverName?>, did serve the <?=$addlDocs?> in the above-captioned case by:<br></td>
		</tr>
		<tr>
			<td colspan="2" style="font-weight:bold; padding-left:20px;"><?=stripslashes($historyd)?></td>
		</tr>
		<tr>
			<td colspan="2">I solemnly affirm under the penalties of perjury that the contents of this <?=strtolower($amended)?>affidavit are true and correct, to the best of my knowledge, information and belief<? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?>, and that I did perform service as set forth above<? }?><? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?>, and that I served the Order to Docket and all other papers filed with it to [PERSON SERVED]<? }?>.<br></td>
		</tr>
		<tr>
			<td colspan="2">I, <?=$serverName?>, certify that I am over eighteen years old and not a party to this action<? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?> and that I served [PERSON SERVED], [RELATION TO DEFENDANT]<? }?><? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?> and that I did perform service as set forth above<? }?>.<br /></td>
		</tr>
		<tr>
			<td valign="top" style="font-size:14px" width="50%"></td>
			<td valign="top" style="font-size:14px" width="50%">________________________<u>DATE:</u>________<br /><?=$serverName?><br /><?=$serverAdd?><br /><?=$serverCity?>, <?=$serverState?> <?=$serverZip?><br /><?=$serverPhone?><br><?=$_SERVER[REMOTE_ADDR]?></td> 
		</tr>
	</table>
	<? } 
	$paged["$def"] = ob_get_clean();
	ob_start();
	//4th server
	if ($iIDc){
		$historyc = "<div style='font-weight:300'><u>Describe with particularity the good faith efforts to serve ".$d1["name$def"].":</u></div>
				".$attemptsc;
	?>
		<table width="80%" align="center" bgcolor="#FFFFFF" <? if (strtoupper($d1[affidavit_status]) != "SERVICE CONFIRMED"){echo $dim;}?>>
	<?
	$r5=@mysql_query("SELECT * from ps_signatory where serverID='$iIDc' AND packetID='$packet'");
	$d5=mysql_fetch_array($r5, MYSQL_ASSOC);
	$serverName=$d5[name];
	$serverID=$iIDc;
	$serverAdd=$d5[address];
	$serverCity=$d5[city];
	$serverState=$d5[state];
	$serverZip=$d5[zip];
	$serverPhone=$d5[phone];
	if (!$d5){
	$q3="SELECT * from ps_users where id = '$iIDc'";
	$r3=@mysql_query($q3) or die(mysql_error());
	$d3=mysql_fetch_array($r3, MYSQL_ASSOC);
	$serverName=$d3[name];
	$serverAdd=$d3[address];
	$serverCity=$d3[city];
	$serverState=$d3[state];
	$serverZip=$d3[zip];
	$serverPhone=$d3[phone];
	}
	$cord='S'.$d1[packet_id]."-".$def."-".$serverID."%";
	?>
		<? echo "<tr>".$header."<IMG SRC='barcode.php?barcode=".$cord."&width=300&height=40'><center>File Number: ".$d1[client_file]."<br>[PAGE]</center></td></tr>"; ?>
		<tr>
			<td colspan="2" align="center" style="font-weight:bold; text-decoration:underline" height="30px" valign="top"><?=$amended?><font size='+2'>Affidavit of Service</font></td>
		</tr>
		<tr>
			<td colspan="2" align="center" style="font-weight:bold; font-size:20px;" height="30px" valign="top"><?=$result?></td>
		</tr>
		<tr>
			<td colspan="2" align="left">I, <?=$serverName?>, did serve the <?=$addlDocs?> in the above-captioned case by:<br></td>
		</tr>
		<tr>
			<td colspan="2" style="font-weight:bold; padding-left:20px;"><?=stripslashes($historyc)?></td>
		</tr>
		<tr>
			<td colspan="2">I solemnly affirm under the penalties of perjury that the contents of this <?=strtolower($amended)?>affidavit are true and correct, to the best of my knowledge, information and belief<? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?>, and that I did perform service as set forth above<? }?><? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?>, and that I served the Order to Docket and all other papers filed with it to [PERSON SERVED]<? }?>.<br></td>
		</tr>
		<tr>
			<td colspan="2">I, <?=$serverName?>, certify that I am over eighteen years old and not a party to this action<? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?> and that I served [PERSON SERVED], [RELATION TO DEFENDANT]<? }?><? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?> and that I did perform service as set forth above<? }?>.<br /></td>
		</tr>
		<tr>
			<td valign="top" style="font-size:14px" width="50%"></td>
			<td valign="top" style="font-size:14px" width="50%">________________________<u>DATE:</u>________<br /><?=$serverName?><br /><?=$serverAdd?><br /><?=$serverCity?>, <?=$serverState?> <?=$serverZip?><br /><?=$serverPhone?><br><?=$_SERVER[REMOTE_ADDR]?></td> 
		</tr>
	</table>
	<? 
	}
	$pagec["$def"] = ob_get_clean();
	ob_start();
	//3rd server
	if ($iIDb){
		$historyb = "<div style='font-weight:300'><u>Describe with particularity the good faith efforts to serve ".$d1["name$def"].":</u></div>
				".$attemptsb;
	?>
		<table width="80%" align="center" bgcolor="#FFFFFF" <? if (strtoupper($d1[affidavit_status]) != "SERVICE CONFIRMED"){echo $dim;}?>>
	<?
	$r5=@mysql_query("SELECT * from ps_signatory where serverID='$iIDb' AND packetID='$packet'");
	$d5=mysql_fetch_array($r5, MYSQL_ASSOC);
	$serverName=$d5[name];
	$serverID=$iIDb;
	$serverAdd=$d5[address];
	$serverCity=$d5[city];
	$serverState=$d5[state];
	$serverZip=$d5[zip];
	$serverPhone=$d5[phone];
	if (!$d5){
	$q3="SELECT * from ps_users where id = '$iIDb'";
	$r3=@mysql_query($q3) or die(mysql_error());
	$d3=mysql_fetch_array($r3, MYSQL_ASSOC);
	$serverName=$d3[name];
	$serverAdd=$d3[address];
	$serverCity=$d3[city];
	$serverState=$d3[state];
	$serverZip=$d3[zip];
	$serverPhone=$d3[phone];
	}
	$cord='S'.$d1[packet_id]."-".$def."-".$serverID."%";
	?>   
		<? echo "<tr>".$header."<IMG SRC='barcode.php?barcode=".$cord."&width=300&height=40'><center>File Number: ".$d1[client_file]."<br>[PAGE]</center></td></tr>"; ?>
		<tr>
			<td colspan="2" align="center" style="font-weight:bold; text-decoration:underline" height="30px" valign="top"><?=$amended?><font size='+2'>Affidavit of Service</font></td>
		</tr>
		<tr>
			<td colspan="2" align="center" style="font-weight:bold; font-size:20px;" height="30px" valign="top"><?=$result?></td>
		</tr>
		<tr>
			<td colspan="2" align="left">I, <?=$serverName?>, did serve the <?=$addlDocs?> in the above-captioned case by:<br></td>
		</tr>
		<tr>
			<td colspan="2" style="font-weight:bold; padding-left:20px;"><?=stripslashes($historyb)?></td>
		</tr>     
		<tr>
			<td colspan="2">I solemnly affirm under the penalties of perjury that the contents of this <?=strtolower($amended)?>affidavit are true and correct, to the best of my knowledge, information and belief<? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?>, and that I did perform service as set forth above<? }?><? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?>, and that I served the Order to Docket and all other papers filed with it to [PERSON SERVED]<? }?>.<br></td>
		</tr>
		<tr>
			<td colspan="2">I, <?=$serverName?>, certify that I am over eighteen years old and not a party to this action<? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?> and that I served [PERSON SERVED], [RELATION TO DEFENDANT]<? }?><? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?> and that I did perform service as set forth above<? }?>.<br /></td>
		</tr>
		<tr>
			<td valign="top" style="font-size:14px" width="50%"></td>
			<td valign="top" style="font-size:14px" width="50%">________________________<u>DATE:</u>________<br /><?=$serverName?><br /><?=$serverAdd?><br /><?=$serverCity?>, <?=$serverState?> <?=$serverZip?><br /><?=$serverPhone?><br><?=$_SERVER[REMOTE_ADDR]?></td> 
		</tr>
	</table>
	<? 
	}
	$pageb["$def"] = ob_get_clean();
	ob_start();
	//2nd server
	if ($iIDa){
		$historya = "<div style='font-weight:300'><u>Describe with particularity the good faith efforts to serve ".$d1["name$def"].":</u></div>
				".$attemptsa;
	?>
		<table width="80%" align="center" bgcolor="#FFFFFF" <? if (strtoupper($d1[affidavit_status]) != "SERVICE CONFIRMED"){echo $dim;}?>>
	<?
	$r5=@mysql_query("SELECT * from ps_signatory where serverID='$iIDa' AND packetID='$packet'");
	$d5=mysql_fetch_array($r5, MYSQL_ASSOC);
	$serverName=$d5[name];
	$serverID=$iIDa;
	$serverAdd=$d5[address];
	$serverCity=$d5[city];
	$serverState=$d5[state];
	$serverZip=$d5[zip];
	$serverPhone=$d5[phone];
	if (!$d5){
	$q3="SELECT * from ps_users where id = '$iIDa'";
	$r3=@mysql_query($q3) or die(mysql_error());
	$d3=mysql_fetch_array($r3, MYSQL_ASSOC);
	$serverName=$d3[name];
	$serverAdd=$d3[address];
	$serverCity=$d3[city];
	$serverState=$d3[state];
	$serverZip=$d3[zip];
	$serverPhone=$d3[phone];
	}
	$cord='S'.$d1[packet_id]."-".$def."-".$serverID."%";
	?>  
		<? echo "<tr>".$header."<IMG SRC='barcode.php?barcode=".$cord."&width=300&height=40'><center>File Number: ".$d1[client_file]."<br>[PAGE]</center></td></tr>"; ?>
		<tr>
			<td colspan="2" align="center" style="font-weight:bold; text-decoration:underline" height="30px" valign="top"><?=$amended?><font size='+2'>Affidavit of Service</font></td>
		</tr>
		<tr>
			<td colspan="2" align="center" style="font-weight:bold; font-size:20px;" height="30px" valign="top"><?=$result?></td>
		</tr>
		<tr>
			<td colspan="2" align="left">I, <?=$serverName?>, did serve the <?=$addlDocs?> in the above-captioned case by:<br></td>
		</tr>
		<tr>
			<td colspan="2" style="font-weight:bold; padding-left:20px;"><?=stripslashes($historya)?></td>
		</tr>      
		<tr>
			<td colspan="2">I solemnly affirm under the penalties of perjury that the contents of this <?=strtolower($amended)?>affidavit are true and correct, to the best of my knowledge, information and belief<? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?>, and that I did perform service as set forth above<? }?><? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?>, and that I served the Order to Docket and all other papers filed with it to [PERSON SERVED]<? }?>.<br></td>
		</tr>
		<tr>
			<td colspan="2">I, <?=$serverName?>, certify that I am over eighteen years old and not a party to this action<? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?> and that I served [PERSON SERVED], [RELATION TO DEFENDANT]<? }?><? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?> and that I did perform service as set forth above<? }?>.<br /></td>
		</tr>
		<tr>
			<td valign="top" style="font-size:14px" width="50%"></td>
			<td valign="top" style="font-size:14px" width="50%">________________________<u>DATE:</u>________<br /><?=$serverName?><br /><?=$serverAdd?><br /><?=$serverCity?>, <?=$serverSstate?> <?=$serverZip?><br /><?=$serverPhone?><br><?=$_SERVER[REMOTE_ADDR]?></td> 
		</tr>
	</table>
	<? 
	} 
	$pagea["$def"] = ob_get_clean();
	ob_start();
	//1st server, or servera if non-Burson
	//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	if ($iID != $iIDa){
	?>
		<table width="80%" align="center" bgcolor="#FFFFFF" <? if (strtoupper($d1[affidavit_status]) != "SERVICE CONFIRMED"){echo $dim;}?>>
	 <? 
	$r5=@mysql_query("SELECT * from ps_signatory where serverID='$iID' AND packetID='$packet'");
	$d5=mysql_fetch_array($r5, MYSQL_ASSOC);
	$serverName=$d5[name];
	$serverID=$iID;
	$serverAdd=$d5[address];
	$serverCity=$d5[city];
	$serverState=$d5[state];
	$serverZip=$d5[zip];
	$serverPhone=$d5[phone];
	if (!$d5){
	$q3="SELECT * from ps_users where id = '$iID'";
	$r3=@mysql_query($q3) or die(mysql_error());
	$d3=mysql_fetch_array($r3, MYSQL_ASSOC);
	$serverName=$d3[name];
	$serverAdd=$d3[address];
	$serverCity=$d3[city];
	$serverState=$d3[state];
	$serverZip=$d3[zip];
	$serverPhone=$d3[phone];
	}
	$cord='S'.$d1[packet_id]."-".$def."-".$serverID."%";
	 echo "<tr>".$header."<IMG SRC='barcode.php?barcode=".$cord."&width=300&height=40'><center>File Number: ".$d1[client_file]."<br>[PAGE]</center></td></tr>"; ?>
		<tr>
			<td colspan="2" align="center" style="font-weight:bold; text-decoration:underline" height="30px" valign="top"><?=$amended?><font size='+2'>Affidavit of Service</font><? if ($iID==$iiID){ echo " and Posting";} ?></td>
		</tr>
		<tr>
			<td colspan="2" align="center" style="font-weight:bold; font-size:20px;" height="30px" valign="top"><?=$result?></td>
		</tr>
		<tr>
			<td colspan="2" align="left">I, <?=$serverName?>, did serve the <?=$addlDocs?> in the above-captioned case by:<br></td>
		</tr>
		<tr>
			<td colspan="2" style="font-weight:bold; padding-left:20px;"><?=stripslashes($history)?></td>
		</tr>
	<?
	if ($iID == $iiID){
	}else{
	?>        
		<tr>
			<td colspan="2">I solemnly affirm under the penalties of perjury that the contents of this <?=strtolower($amended)?>affidavit are true and correct to the best of my knowledge, information and belief<? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?>, and that I did perform service as set forth above<? }?><? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?>, and that I served the Order to Docket and all other papers filed with it to [PERSON SERVED]<? }?>.<br></td>
		</tr>
		<tr>
			<td colspan="2">I, <?=$serverName?>, certify that I am over eighteen years old and not a party to this action<? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?> and that I served [PERSON SERVED], [RELATION TO DEFENDANT]<? }?><? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?> and that I did perform service as set forth above<? }?>.<br /></td>
		</tr>
		<tr>
			<td valign="top" style="font-size:14px" width="50%"></td>
			<td valign="top" style="font-size:14px" width="50%">________________________<u>DATE:</u>________<br /><?=$serverName?><br /><?=$serverAdd?><br /><?=$serverCity?>, <?=$serverState?> <?=$serverZip?><br /><?=$serverPhone?><br><?=$_SERVER[REMOTE_ADDR]?></td> 
		</tr>
	</table>
	<? }
	 }
	$pageI["$def"] = ob_get_clean();
	ob_start();
	 //Multiple servers' attempts end here
	if($posting){
	if ($iID==$iiID){
	}else{
	?>
		<table width="80%" align="center" bgcolor="#FFFFFF" <? if (strtoupper($d1[affidavit_status]) != "SERVICE CONFIRMED"){echo $dim;}?>>
	<?
	$r5=@mysql_query("SELECT * from ps_signatory where serverID='$iiID' AND packetID='$packet'");
	$d5=mysql_fetch_array($r5, MYSQL_ASSOC);
	$serverName=$d5[name];
	$serverID=$iiID;
	$serverAdd=$d5[address];
	$serverCity=$d5[city];
	$serverState=$d5[state];
	$serverZip=$d5[zip];
	$serverPhone=$d5[phone];
	if (!$d5){
	$q3="SELECT * from ps_users where id = '$iiID'";
	$r3=@mysql_query($q3) or die(mysql_error());
	$d3=mysql_fetch_array($r3, MYSQL_ASSOC);
	$serverName=$d3[name];
	$serverAdd=$d3[address];
	$serverCity=$d3[city];
	$serverState=$d3[state];
	$serverZip=$d3[zip];
	$serverPhone=$d3[phone];
	}
	$cord='S'.$d1[packet_id]."-".$def."-".$serverID."%";
	?> 
		<? echo "<tr>".$header."<IMG SRC='barcode.php?barcode=".$cord."&width=300&height=40'><center>File Number: ".$d1[client_file]."<br>[PAGE]</center></td></tr>"; ?>
		<tr>
			<td colspan="2" align="center" style="font-weight:bold; text-decoration:underline" height="30px" valign="top"><?=$amended?>Affidavit of Posting</td>
		</tr>
		<tr>
			<td colspan="2" align="center" style="font-weight:bold; font-size:20px;" height="30px" valign="top"><?=$result?></td>
		</tr>
		<tr>
			<td colspan="2" align="left">I, <?=$serverName?>, did serve the <?=$addlDocs?> in the above-captioned case by:<br></td>
		</tr>
		<? } ?>
		<tr>
			<td colspan="2" style="font-weight:bold; padding-left:20px"><?=stripslashes($history2)?></td>
		</tr>       
		<tr>
			<td colspan="2">I solemnly affirm under the penalties of perjury that the contents of this <?=strtolower($amended)?>affidavit are true and correct to the best of my knowledge, information and belief.<br></td>
		</tr>
		<tr>
			<td colspan="2">I, <?=$serverName?>, certify that I am over eighteen years old and not a party to this action.<br /></td>
		</tr>
		<tr>
			<td valign="top" style="font-size:14px" width="50%"></td>
			<td valign="top" style="font-size:14px" width="50%">________________________<u>DATE:</u>________<br /><?=$serverName?><br /><?=$serverAdd?><br /><?=$serverCity?>, <?=$serverState?> <?=$serverZip?><br /><?=$serverPhone?><br><?=$_SERVER[REMOTE_ADDR]?></td> 
		</tr>
	</table>
	<? } 
	 $pageII["$def"] = ob_get_clean();
	 $postingID["$def"] = $iiID;
	 ob_start();
	  if($iiiID){ ?>
		<table width="80%" align="center" bgcolor="#FFFFFF" <? if (strtoupper($d1[affidavit_status]) != "SERVICE CONFIRMED"){echo $dim;}?>>
	<?
	$r5=@mysql_query("SELECT * from ps_signatory where serverID='$iiiID' AND packetID='$packet'");
	$d5=mysql_fetch_array($r5, MYSQL_ASSOC);
	$serverName=$d5[name];
	$serverID=$iiiID;
	$serverAdd=$d5[address];
	$serverCity=$d5[city];
	$serverState=$d5[state];
	$serverZip=$d5[zip];
	$serverPhone=$d5[phone];
	if (!$d5){
	$q3="SELECT * from ps_users where id = '$iiiID'";
	$r3=@mysql_query($q3) or die(mysql_error());
	$d3=mysql_fetch_array($r3, MYSQL_ASSOC);
	$serverName=$d3[name];
	$serverAdd=$d3[address];
	$serverCity=$d3[city];
	$serverState=$d3[state];
	$serverZip=$d3[zip];
	$serverPhone=$d3[phone];
	}
	$cord='S'.$d1[packet_id]."-".$def."-".$serverID."%";
	 echo "<tr>".$header."<IMG SRC='barcode.php?barcode=".$cord."&width=300&height=40'><center>File Number: ".$d1[client_file]."<br>[PAGE]</center></td></tr>"; ?>
		<tr>
			<td colspan="2" align="center" style="font-weight:bold; text-decoration:underline" height="30px" valign="top"><?=$amended?>Affidavit of Mailing</td>
		</tr>
		<tr>
			<td colspan="2" align="center" style="font-weight:bold; font-size:20px;" height="30px" valign="top"><?=$result?></td>
		</tr>
		<tr>
			<td colspan="2" align="left">I, <?=$serverName?>, did serve the <?=$addlDocs?> in the above-captioned case by:<br></td>
		</tr>
		<tr>
			<td colspan="2" style="font-weight:bold; padding-left:20px"><?=stripslashes($history3)?></td>
		</tr>      
		<tr <? if($noMail == 1){ echo 'class="dim"';}?>>
			<td colspan="2">I solemnly affirm under the penalties of perjury that the contents of this <?=strtolower($amended)?>affidavit are true and correct to the best of my knowledge, information and belief.  And that I mailed the above papers under section 14-209(b) to <?=strtoupper($d1["name$def"])?>.<br></td>
		</tr>
		<tr <? if($noMail == 1){ echo 'class="dim"';}?>>
			<td colspan="2">I, <?=$serverName?>, certify that I am over eighteen years old and not a party to this action.<br /></td>
		</tr>
		<tr <? if($noMail == 1){ echo 'class="dim"';}?>>
			<td valign="top" style="font-size:14px" width="50%"></td>
			<td valign="top" style="font-size:14px" width="50%">________________________<u>DATE:</u>________<br /><?=$serverName?><br /><?=$serverAdd?><br /><?=$serverCity?>, <?=$serverState?> <?=$serverZip?><br /><?=$serverPhone?><br><?=$_SERVER[REMOTE_ADDR]?></td> 
		</tr>
		<tr>
			<td colspan="2" style="padding-left:20px"><?=stripslashes($history4)?></td>
		</tr>
	</table>
	<? }
	 $pageIII["$def"] = ob_get_clean();
	//------------------------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------------------------
	}elseif($type == "pd"){ 
	ob_start();
	?>
	<table width="80%" align="center" bgcolor="#FFFFFF" <? if (strtoupper($d1[affidavit_status]) != "SERVICE CONFIRMED"){echo $dim;}?>>
	<?
	$r5=@mysql_query("SELECT * from ps_signatory where serverID='$deliveryID' AND packetID='$packet'");
	$d5=mysql_fetch_array($r5, MYSQL_ASSOC);
	$serverName=$d5[name];
	$serverID=$deliveryID;
	$serverAdd=$d5[address];
	$serverCity=$d5[city];
	$serverState=$d5[state];
	$serverZip=$d5[zip];
	$serverPhone=$d5[phone];
	if (!$d5){
	$q3="SELECT * from ps_users where id = '$deliveryID'";
	$r3=@mysql_query($q3) or die(mysql_error());
	$d3=mysql_fetch_array($r3, MYSQL_ASSOC);
	$serverName=$d3[name];
	$serverAdd=$d3[address];
	$serverCity=$d3[city];
	$serverState=$d3[state];
	$serverZip=$d3[zip];
	$serverPhone=$d3[phone];
	}
	$cord='S'.$d1[packet_id]."-".$def."-".$serverID."%";
	?> 
	<? echo "<tr>".$header."<IMG SRC='barcode.php?barcode=".$cord."&width=300&height=40'><center>File Number: ".$d1[client_file]."<br>[PAGE]</center></td></tr>"; ?>
		<tr>
			<td colspan="2" align="center" style="font-weight:bold; text-decoration:underline" height="30px" valign="top"><?=$amended?>Affidavit of Personal Delivery</td>
		</tr>
		<tr>
			<td colspan="2" align="center" style="font-weight:bold; font-size:20px;" height="30px" valign="top"><?=$result?></td>
		</tr>
		<tr>
			<td colspan="2" align="left">I, <?=$serverName?>, did serve the <?=$addlDocs?> in the above-captioned case by:<br></td>
		</tr>
	<? if ($residentDesc){
		$desc=strtoupper(str_replace('CO-A BORROWER IN THE ABOVE-REFERENCED CASE', 'A BORROWER IN THE ABOVE-REFERENCED CASE', str_replace('BORROWER','A BORROWER IN THE ABOVE-REFERENCED CASE', attorneyCustomLang($d1[attorneys_id],strtoupper($residentDesc)))));
	}?>
		<tr>
			<td colspan="2" style="font-weight:bold; font-size:14px; padding-left:20px; padding-top:20px; padding-bottom:20px; line-height:2;"><?=stripslashes($delivery)?></td>
		</tr>       
		<tr>
			<td colspan="2">I solemnly affirm under the penalties of perjury that the contents of <? if ($type == 'non'){ ?>section (i) of <? }?>this <?=strtolower($amended)?>affidavit are true and correct to the best of my knowledge, information and belief<? if ($type == 'pd' && $nondef == '1'){?>, and that I served the order to docket and other papers to <? if ($resident){ echo strtoupper($resident);}else{ echo '[PERSON SERVED]';}?>, <? if ($residentDesc){echo $desc;}else{ echo '[RELATION TO DEFENDANT]';}?><? if ($serveAddress){ echo ', at '.$serveAddress;}?><? }elseif($type == 'pd' && $nondef != '1'){?>, and that I served the order to docket and other papers to <?=strtoupper($d1["name$def"])?><? if ($serveAddress){ echo ', at '.$serveAddress;}?><? } ?>.<br><br /></td>
		</tr>
		<tr>
			<td colspan="2">I, <?=$serverName?>, certify that I am over eighteen years old and not a party to this action.<br /></td>
		</tr>
		<tr>
			<td valign="top" style="font-size:14px" width="50%"></td>
			<td valign="top" style="font-size:14px" width="50%">________________________<u>DATE:</u>________<br /><?=$serverName?><br /><?=$serverAdd?><br /><?=$serverCity?>, <?=$serverState?> <?=$serverZip?><br /><?=$serverPhone?><br><?=$_SERVER[REMOTE_ADDR]?></td> 
		</tr>
	</table>
	<? 
	$pagePD["$def"] = ob_get_clean();
	$PDID["$def"]=$deliveryID;
	$PDADD["$def"]=$serveAddress;
	} }

	//count pages and construct table of contents
	$count=0;
	$totalPages=0;
	$defs=defCount($packet);
	$checked='';
	while($count < $defs){$count++;
		if ($pagee["$count"] != ''){
			$totalPages++;
			if (($count==$defendant || $defendant=="ALL") && ($_COOKIE[psdata][level]=='Operations' || $iIDe==$_COOKIE[psdata][user_id]) && ($defendant != "MAIL")){
				$contents="<tr bgcolor='".row_color($totalPages,'#FFFFFF','#cccccc')."'><td class='a'><input type='checkbox' DISABLED checked='yes'></td><td class='a'>Page $totalPages of [PAGE]</td><td class='a'>".id2name($iIDe)."</td><td class='a'>ATTEMPTING TO SERVE <b>".strtoupper($d1["name$count"])."</b></td><td class='a'>".$d1[address1e].", ".$d1[city1e].", ".$d1[state1e]." ".$d1[zip1e]."</td></tr>";
				$checked=1;
			}else{
				$contents="<tr bgcolor='#FF0000'><td class='a'><input type='checkbox' DISABLED></td><td class='a'>Page $totalPages of [PAGE]</td><td class='a'>".id2name($iIDe)."</td><td class='a'>ATTEMPTING TO SERVE <b>".strtoupper($d1["name$count"])."</b></td><td class='a'>".$d1[address1e].", ".$d1[city1e].", ".$d1[state1e]." ".$d1[zip1e]."</td></tr>";
				if ($missing == ''){
					$missing = $totalPages;
				}else{
					$missing .= ', '.$totalPages;
				}
			}
		}
		if ($paged["$count"] != ''){
			$totalPages++;
			if (($count==$defendant || $defendant=="ALL") && ($_COOKIE[psdata][level]=='Operations' || $iIDd==$_COOKIE[psdata][user_id]) && ($defendant != "MAIL")){
				$contents .= "<tr bgcolor='".row_color($totalPages,'#FFFFFF','#cccccc')."'><td class='a'><input type='checkbox' DISABLED checked='yes'></td><td class='a'>Page $totalPages of [PAGE]</td><td class='a'>".id2name($iIDd)."</td><td class='a'>ATTEMPTING TO SERVE <b>".strtoupper($d1["name$count"])."</b></td><td class='a'>".$d1[address1d].", ".$d1[city1d].", ".$d1[state1d]." ".$d1[zip1d]."</td></tr>";
				$checked=1;
			}else{
				$contents .= "<tr bgcolor='#FF0000'><td class='a'><input type='checkbox' DISABLED></td><td class='a'>Page $totalPages of [PAGE]</td><td class='a'>".id2name($iIDd)."</td><td class='a'>ATTEMPTING TO SERVE <b>".strtoupper($d1["name$count"])."</b></td><td class='a'>".$d1[address1d].", ".$d1[city1d].", ".$d1[state1d]." ".$d1[zip1d]."</td></tr>";
				if ($missing == ''){
					$missing = $totalPages;
				}else{
					$missing .= ', '.$totalPages;
				}
			}
		}
		if ($pagec["$count"] != ''){
			$totalPages++;
			if (($count==$defendant || $defendant=="ALL") && ($_COOKIE[psdata][level]=='Operations' || $iIDc==$_COOKIE[psdata][user_id]) && ($defendant != "MAIL")){
				$contents .= "<tr bgcolor='".row_color($totalPages,'#FFFFFF','#cccccc')."'><td class='a'><input type='checkbox' DISABLED checked='yes'></td><td class='a'>Page $totalPages of [PAGE]</td><td class='a'>".id2name($iIDc)."</td><td class='a'>ATTEMPTING TO SERVE <b>".strtoupper($d1["name$count"])."</b></td><td class='a'>".$d1[address1c].", ".$d1[city1c].", ".$d1[state1c]." ".$d1[zip1c]."</td></tr>";
				$checked=1;
			}else{
				$contents .= "<tr bgcolor='#FF0000'><td class='a'><input type='checkbox' DISABLED></td><td class='a'>Page $totalPages of [PAGE]</td><td class='a'>".id2name($iIDc)."</td><td class='a'>ATTEMPTING TO SERVE <b>".strtoupper($d1["name$count"])."</b></td><td class='a'>".$d1[address1c].", ".$d1[city1c].", ".$d1[state1c]." ".$d1[zip1c]."</td></tr>";
				if ($missing == ''){
					$missing = $totalPages;
				}else{
					$missing .= ', '.$totalPages;
				}
			}
		}
		if ($pageb["$count"] != ''){
			$totalPages++;
			if (($count==$defendant || $defendant=="ALL") && ($_COOKIE[psdata][level]=='Operations' || $iIDb==$_COOKIE[psdata][user_id]) && ($defendant != "MAIL")){
				$contents .= "<tr bgcolor='".row_color($totalPages,'#FFFFFF','#cccccc')."'><td class='a'><input type='checkbox' DISABLED checked='yes'></td><td class='a'>Page $totalPages of [PAGE]</td><td class='a'>".id2name($iIDb)."</td><td class='a'>ATTEMPTING TO SERVE <b>".strtoupper($d1["name$count"])."</b></td><td class='a'>".$d1[address1b].", ".$d1[city1b].", ".$d1[state1b]." ".$d1[zip1b]."</td></tr>";
				$checked=1;
			}else{
				$contents .= "<tr bgcolor='#FF0000'><td class='a'><input type='checkbox' DISABLED></td><td class='a'>Page $totalPages of [PAGE]</td><td class='a'>".id2name($iIDb)."</td><td class='a'>ATTEMPTING TO SERVE <b>".strtoupper($d1["name$count"])."</b></td><td class='a'>".$d1[address1b].", ".$d1[city1b].", ".$d1[state1b]." ".$d1[zip1b]."</td></tr>";
				if ($missing == ''){
					$missing = $totalPages;
				}else{
					$missing .= ', '.$totalPages;
				}
			}
		}
		if ($pagea["$count"] != ''){
			$totalPages++;
			if (($count==$defendant || $defendant=="ALL") && ($_COOKIE[psdata][level]=='Operations' || $iIDa==$_COOKIE[psdata][user_id]) && ($defendant != "MAIL")){
				$contents .= "<tr bgcolor='".row_color($totalPages,'#FFFFFF','#cccccc')."'><td class='a'><input type='checkbox' DISABLED checked='yes'></td><td class='a'>Page $totalPages of [PAGE]</td><td class='a'>".id2name($iIDa)."</td><td class='a'>ATTEMPTING TO SERVE <b>".strtoupper($d1["name$count"])."</b></td><td class='a'>".$d1[address1a].", ".$d1[city1a].", ".$d1[state1a]." ".$d1[zip1a]."</td></tr>";
				$checked=1;
			}else{
				$contents .= "<tr bgcolor='#FF0000'><td class='a'><input type='checkbox' DISABLED></td><td class='a'>Page $totalPages of [PAGE]</td><td class='a'>".id2name($iIDa)."</td><td class='a'>ATTEMPTING TO SERVE <b>".strtoupper($d1["name$count"])."</b></td><td class='a'>".$d1[address1a].", ".$d1[city1a].", ".$d1[state1a]." ".$d1[zip1a]."</td></tr>";
				if ($missing == ''){
					$missing = $totalPages;
				}else{
					$missing .= ', '.$totalPages;
				}
			}
		}
		if ($pageI["$count"] != ''){
			$totalPages++;
			if ($iID==$iiID){
				if (($count==$defendant || $defendant=="ALL") && ($_COOKIE[psdata][level]=='Operations' || $postingID["$count"]==$_COOKIE[psdata][user_id]) && ($defendant != "MAIL")){
					$contents .= "<tr bgcolor='".row_color($totalPages,'#FFFFFF','#cccccc')."'><td class='a'><input type='checkbox' DISABLED checked='yes'></td><td class='a'>Page $totalPages of [PAGE]</td><td class='a'>".id2name($postingID["$count"])."</td><td class='a'>ATTEMPTS & POSTING FOR <b>".strtoupper($d1["name$count"])."</b></td><td class='a'>".$d1[address1].", ".$d1[city1].", ".$d1[state1]." ".$d1[zip1]."</td></tr>";
				$checked=1;
				}else{
					$contents .= "<tr bgcolor='#FF0000'><td class='a'><input type='checkbox' DISABLED></td><td class='a'>Page $totalPages of [PAGE]</td><td class='a'>".id2name($postingID["$count"])."</td><td class='a'>ATTEMPTS & POSTING FOR <b>".strtoupper($d1["name$count"])."</b></td><td class='a'>".$d1[address1].", ".$d1[city1].", ".$d1[state1]." ".$d1[zip1]."</td></tr>";
					if ($missing == ''){
					$missing = $totalPages;
				}else{
					$missing .= ', '.$totalPages;
				}
				}
			}else{
				if (($count==$defendant || $defendant=="ALL") && ($_COOKIE[psdata][level]=='Operations' || $iID==$_COOKIE[psdata][user_id]) && ($defendant != "MAIL")){
					$contents .= "<tr bgcolor='".row_color($totalPages,'#FFFFFF','#cccccc')."'><td class='a'><input type='checkbox' DISABLED checked='yes'></td><td class='a'>Page $totalPages of [PAGE]</td><td class='a'>".id2name($iID)."</td><td class='a'>ATTEMPTING TO SERVE <b>".strtoupper($d1["name$count"])."</b></td><td class='a'>".$d1[address1].", ".$d1[city1].", ".$d1[state1]." ".$d1[zip1]."</td></tr>";
				$checked=1;
				}else{
					$contents .= "<tr bgcolor='#FF0000'><td class='a'><input type='checkbox' DISABLED></td><td class='a'>Page $totalPages of [PAGE]</td><td class='a'>".id2name($iID)."</td><td class='a'>ATTEMPTING TO SERVE <b>".strtoupper($d1["name$count"])."</b></td><td class='a'>".$d1[address1].", ".$d1[city1].", ".$d1[state1]." ".$d1[zip1]."</td></tr>";
					if ($missing == ''){
					$missing = $totalPages;
				}else{
					$missing .= ', '.$totalPages;
				}
				}
			}
		}
		if ($pageII["$count"] != ''){
			//if posting server also made attempt(s), do nothing
			if ($iID==$iiID){
			}else{
			//otherwise increase counter
				$totalPages++;
				if (($count==$defendant || $defendant=="ALL") && ($_COOKIE[psdata][level]=='Operations' || $postingID["$count"]==$_COOKIE[psdata][user_id]) && ($defendant != "MAIL")){
					$contents .= "<tr bgcolor='".row_color($totalPages,'#FFFFFF','#cccccc')."'><td class='a'><input type='checkbox' DISABLED checked='yes'></td><td class='a'>Page $totalPages of [PAGE]</td><td class='a'>".id2name($postingID["$count"])."</td><td class='a'>POSTING FOR <b>".strtoupper($d1["name$count"])."</b></td><td class='a'>".$d1[address1].", ".$d1[city1].", ".$d1[state1]." ".$d1[zip1]."</td></tr>";
				$checked=1;
				}else{
					$contents .= "<tr bgcolor='#FF0000'><td class='a'><input type='checkbox' DISABLED></td><td class='a'>Page $totalPages of [PAGE]</td><td class='a'>".id2name($iiID)."</td><td class='a'>POSTING FOR <b>".strtoupper($d1["name$count"])."</b></td><td class='a'>".$d1[address1].", ".$d1[city1].", ".$d1[state1]." ".$d1[zip1]."</td></tr>";
					if ($missing == ''){
					$missing = $totalPages;
				}else{
					$missing .= ', '.$totalPages;
				}
				}
			}
		}
		if ($pageIII["$count"] != ''){
			$totalPages++;
			if ($d1[address1e]){
				$add1ex=$d1["address1e"].' '.$d1["city1e"].', '.$d1["state1e"].' '.$d1["zip1e"]."<br>";
			}
			if ($d1[address1d]){
				$add1dx=$d1["address1d"].' '.$d1["city1d"].', '.$d1["state1d"].' '.$d1["zip1d"]."<br>";
			}
			if ($d1[address1c]){
				$add1cx=$d1["address1c"].' '.$d1["city1c"].', '.$d1["state1c"].' '.$d1["zip1c"]."<br>";
			}
			if ($d1[address1b]){
				$add1bx=$d1["address1b"].' '.$d1["city1b"].', '.$d1["state1b"].' '.$d1["zip1b"]."<br>";
			}
			if ($d1[address1a]){
				$add1ax=$d1["address1a"].' '.$d1["city1a"].', '.$d1["state1a"].' '.$d1["zip1a"]."<br>";
			}
			$add1x = $d1["address1"].' '.$d1["city1"].', '.$d1["state1"].' '.$d1["zip1"];
			if (($count==$defendant || $defendant=="ALL" || $defendant=="MAIL") && ($_COOKIE[psdata][level]=='Operations' || $iiiID==$_COOKIE[psdata][user_id])){
				$contents .= "<tr bgcolor='".row_color($totalPages,'#FFFFFF','#cccccc')."'><td class='a'><input type='checkbox' DISABLED checked='yes'></td><td class='a'>Page $totalPages of [PAGE]</td><td class='a'>".id2name($iiiID)."</td><td class='a'>MAILING FOR <b>".strtoupper($d1["name$count"])."</b></td><td class='a'>$add1ex $add1dx $add1cx $add1bx $add1ax $add1x</td></tr>";
				$checked=1;
			}else{
				$contents .= "<tr bgcolor='#FF0000'><td class='a'><input type='checkbox' DISABLED></td><td class='a'>Page $totalPages of [PAGE]</td><td class='a'>".id2name($iiiID)."</td><td class='a'>MAILING FOR <b>".strtoupper($d1["name$count"])."</b></td><td class='a'>$add1ex $add1dx $add1cx $add1bx $add1ax $add1x</td></tr>";
				if ($missing == ''){
					$missing = $totalPages;
				}else{
					$missing .= ', '.$totalPages;
				}
			}
		}
		if ($pagePD["$count"] != ''){
			$totalPages++;
			$defName="name".$count;
			if (($count==$defendant || $defendant=="ALL") && ($_COOKIE[psdata][level]=='Operations' || $PDID["$count"]==$_COOKIE[psdata][user_id]) && ($defendant != "MAIL")){
				$contents .= "<tr bgcolor='".row_color($totalPages,'#FFFFFF','#cccccc')."'><td class='a'><input type='checkbox' DISABLED checked='yes'></td><td class='a'>Page $totalPages of [PAGE]</td><td class='a'>".id2name($PDID["$count"])."</td><td class='a'>DELIVERY TO <b>".strtoupper($d1["name$count"])."</b></td><td class='a'>".$PDADD["$count"]."</td></tr>";
				$checked=1;
			}else{
				$contents .= "<tr bgcolor='#FF0000'><td class='a'><input type='checkbox' DISABLED></td><td class='a'>Page $totalPages of [PAGE]</td><td class='a'>".id2name($PDID["$count"])."</td><td class='a'>DELIVERY TO <b>".strtoupper($d1["name$count"])."</b></td><td class='a'>".$PDADD["$count"]."</td></tr>";
				if ($missing == ''){
					$missing = $totalPages;
				}else{
					$missing .= ', '.$totalPages;
				}
			}
		}
	}
	//assemble table of contents
		$contents=str_replace("[PAGE]",$totalPages,$contents);
		if ($missing != ''){
			$missing = explode(', ',$missing);
			$last=count($missing)-1;
			$missing[$last]=" and ".$missing[$last];
			$missing=implode(", ",$missing);
		}
		if ($_COOKIE[psdata][user_id] == "229" || $_COOKIE[psdata][user_id] == "267" || $_COOKIE[psdata][user_id] == "192" || $_COOKIE[psdata][user_id] == "2"){
			$fileInstructions="<tr><td colspan='5' align='center' class='b' style='color:#FF0000;'>PLEASE PREPARE TWO SIGNED, NOTARIZED SETS OF THESE DOCUMENTS.  DO NOT FILE UNTIL <B><U>ALL</U> PORTIONS</B> ARE IN YOUR POSSESSION.<br><br>";
			if ($missing==''){
				$fileInstructions .= "PLEASE CONTACT THE MDWESTSERVE OFFICE TO VERIFY THAT THESE DOCUMENTS ARE READY TO BE FILED.</td></tr>";
			}else{
				$fileInstructions .= "PLEASE WAIT TO TAKE ANY FURTHER ACTION UNTIL PAGES $missing ARE DELIVERED TO YOU, OR YOU RECEIVE ADDITIONAL INSTRUCTIONS FROM THE MDWESTSERVE OFFICE.</td></tr>";
			}
		}else{
			$fileInstructions="<tr><td colspan='5' align='center' class='b' style='color:#FF0000;'>PLEASE DELIVER TWO SIGNED, NOTARIZED SETS OF THESE DOCUMENTS TO THE MDWESTSERVE OFFICE AS SOON AS POSSIBLE.</td></tr>";
		}
	if ($_COOKIE[psdata][level] == 'Operations'){
			$serverMatrix="<tr><td colspan='5'><table align='center' class='p' border='2' frame='border' rules='all'>";
			$serverMatrix .= "<tr><td align='center'><b>SERVER MATRIX</b></td><td>$d1[name1]</td>";
			$restOfRow="<td></td>";
			if ($d1[name2]){
				$serverMatrix .= "<td>$d1[name2]</td>";
				$restOfRow .= "<td></td>";
			}
			if ($d1[name3]){
				$serverMatrix .= "<td>$d1[name3]</td>";
				$restOfRow .= "<td></td>";
			}
			if ($d1[name4]){
				$serverMatrix .= "<td>$d1[name4]</td>";
				$restOfRow .= "<td></td>";
			}
			if ($d1[name5]){
				$serverMatrix .= "<td>$d1[name5]</td>";
				$restOfRow .= "<td></td>";
			}
			if ($d1[name6]){
				$serverMatrix .= "<td>$d1[name6]</td>";
				$restOfRow .= "<td></td>";
			}
			$serverMatrix .= "</tr>";
			$serverMatrix .= "<tr><td><small><b>".strtoupper(id2name($d1[server_id]))."</b> - $d1[address1], $d1[city1], $d1[state1] $d1[zip1]</small></td>".$restOfRow."</tr>";
			if ($d1[address1a]){
				$serverMatrix .= "<tr><td><small><b>".strtoupper(id2name($d1[server_ida]))."</b> - $d1[address1a], $d1[city1a], $d1[state1a] $d1[zip1a]</small></td>".$restOfRow."</tr>";
			}
			if ($d1[address1b]){
				$serverMatrix .= "<tr><td><small><b>".strtoupper(id2name($d1[server_idb]))."</b> - $d1[address1b], $d1[city1b], $d1[state1b] $d1[zip1b]</small></td>".$restOfRow."</tr>";
			}
			if ($d1[address1c]){
				$serverMatrix .= "<tr><td><small><b>".strtoupper(id2name($d1[server_idc]))."</b> - $d1[address1c], $d1[city1c], $d1[state1c] $d1[zip1c]</small></td>".$restOfRow."</tr>";
			}
			if ($d1[address1d]){
				$serverMatrix .= "<tr><td><small><b>".strtoupper(id2name($d1[server_idd]))."</b> - $d1[address1d], $d1[city1d], $d1[state1d] $d1[zip1d]</small></td>".$restOfRow."</tr>";
			}
			if ($d1[address1e]){
				$serverMatrix .= "<tr><td><small><b>".strtoupper(id2name($d1[server_ide]))."</b> - $d1[address1e], $d1[city1e], $d1[state1e] $d1[zip1e]</small></td>".$restOfRow."</tr>";
			}
			$serverMatrix .= "</table></td></tr>";
		}
		if (!$_GET[mail] && $checked == '1' && $overRide != '1'){
			echo "<table width='90%' align='center' bgcolor='#FFFFFF' style='border-style:solid 2px; border-collapse:collapse;' border='1' height='850px'><tr><td colspan='5' align='center' class='b'>TABLE OF CONTENTS FOR PACKET $packet</td></tr>".$fileInstructions."<tr align='center'><td>Present</td><td>Page #</td><td>Server</td><td>Action(s)</td><td>Addresses</td></tr>".$contents.$serverMatrix."</table>";
		}
	$count2=0;
	$currentCounter=0;
	while($count2 < $defs){$count2++;
		if ($pagee["$count2"] != ''){
			$currentCounter++;
			if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($_COOKIE[psdata][level]=='Operations' || $iIDe==$_COOKIE[psdata][user_id]) && ($defendant != "MAIL")){
				echo str_replace("[PAGE]","Set 1 (Affidavit $currentCounter of $totalPages)",$pagee["$count2"]);
			}
		}
		if ($paged["$count2"] != ''){
			$currentCounter++;
			if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($_COOKIE[psdata][level]=='Operations' || $iIDd==$_COOKIE[psdata][user_id]) && ($defendant != "MAIL")){
				echo str_replace("[PAGE]","Set 1 (Affidavit $currentCounter of $totalPages)",$paged["$count2"]);
			}
		}
		if ($pagec["$count2"] != ''){
			$currentCounter++;
			if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($_COOKIE[psdata][level]=='Operations' || $iIDc==$_COOKIE[psdata][user_id]) && ($defendant != "MAIL")){
				echo str_replace("[PAGE]","Set 1 (Affidavit $currentCounter of $totalPages)",$pagec["$count2"]);
			}
		}
		if ($pageb["$count2"] != ''){
			$currentCounter++;
			if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($_COOKIE[psdata][level]=='Operations' || $iIDb==$_COOKIE[psdata][user_id]) && ($defendant != "MAIL")){
				echo str_replace("[PAGE]","Set 1 (Affidavit $currentCounter of $totalPages)",$pageb["$count2"]);
			}
		}
		if ($pagea["$count2"] != ''){
			$currentCounter++;
			if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($_COOKIE[psdata][level]=='Operations' || $iIDa==$_COOKIE[psdata][user_id]) && ($defendant != "MAIL")){
				echo str_replace("[PAGE]","Set 1 (Affidavit $currentCounter of $totalPages)",$pagea["$count2"]);
			}
		}
		if ($pageI["$count2"] != ''){
			$currentCounter++;
			if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($_COOKIE[psdata][level]=='Operations' || $iID==$_COOKIE[psdata][user_id]) && ($defendant != "MAIL")){
				echo str_replace("[PAGE]","Set 1 (Affidavit $currentCounter of $totalPages)",$pageI["$count2"]);
			}
		}
		if ($pageII["$count2"] != ''){
			//if posting server also made attempt(s), do nothing
			if ($iID==$iiID){
			}else{
			//otherwise increase counter
				$currentCounter++;
			}
			if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($_COOKIE[psdata][level]=='Operations' || $iiID==$_COOKIE[psdata][user_id]) && ($defendant != "MAIL")){
				echo str_replace("[PAGE]","Set 1 (Affidavit $currentCounter of $totalPages)",$pageII["$count2"]);
			}
		}
		if ($pageIII["$count2"] != ''){
			$currentCounter++;
			if (($count2==$defendant || $defendant=="ALL" || $defendant=="MAIL") && ($_COOKIE[psdata][level]=='Operations') && ($defendant != "SERVER")){
				echo str_replace("[PAGE]","Set 1 (Affidavit $currentCounter of $totalPages)",$pageIII["$count2"]);
			}
		}
		if ($pagePD["$count2"] != ''){
			$currentCounter++;
			if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($_COOKIE[psdata][level]=='Operations' || $PDID["$count2"]==$_COOKIE[psdata][user_id]) && ($defendant != "MAIL")){
				echo str_replace("[PAGE]","Set 1 (Affidavit $currentCounter of $totalPages)",$pagePD["$count2"]);
			}
		}
	}
	$count2=0;
	$currentCounter=0;
	while($count2 < $defs){$count2++;
		if ($pagee["$count2"] != ''){
			$currentCounter++;
			if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($_COOKIE[psdata][level]=='Operations' || $iIDe==$_COOKIE[psdata][user_id]) && ($defendant != "MAIL")){
				echo str_replace("[PAGE]","Set 2 (Affidavit $currentCounter of $totalPages)",$pagee["$count2"]);
			}
		}
		if ($paged["$count2"] != ''){
			$currentCounter++;
			if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($_COOKIE[psdata][level]=='Operations' || $iIDd==$_COOKIE[psdata][user_id]) && ($defendant != "MAIL")){
				echo str_replace("[PAGE]","Set 2 (Affidavit $currentCounter of $totalPages)",$paged["$count2"]);
			}
		}
		if ($pagec["$count2"] != ''){
			$currentCounter++;
			if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($_COOKIE[psdata][level]=='Operations' || $iIDc==$_COOKIE[psdata][user_id]) && ($defendant != "MAIL")){
				echo str_replace("[PAGE]","Set 2 (Affidavit $currentCounter of $totalPages)",$pagec["$count2"]);
			}
		}
		if ($pageb["$count2"] != ''){
			$currentCounter++;
			if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($_COOKIE[psdata][level]=='Operations' || $iIDb==$_COOKIE[psdata][user_id]) && ($defendant != "MAIL")){
				echo str_replace("[PAGE]","Set 2 (Affidavit $currentCounter of $totalPages)",$pageb["$count2"]);
			}
		}
		if ($pagea["$count2"] != ''){
			$currentCounter++;
			if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($_COOKIE[psdata][level]=='Operations' || $iIDa==$_COOKIE[psdata][user_id]) && ($defendant != "MAIL")){
				echo str_replace("[PAGE]","Set 2 (Affidavit $currentCounter of $totalPages)",$pagea["$count2"]);
			}
		}
		if ($pageI["$count2"] != ''){
			$currentCounter++;
			if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($_COOKIE[psdata][level]=='Operations' || $iID==$_COOKIE[psdata][user_id]) && ($defendant != "MAIL")){
				echo str_replace("[PAGE]","Set 2 (Affidavit $currentCounter of $totalPages)",$pageI["$count2"]);
			}
		}
		if ($pageII["$count2"] != ''){
			//if posting server also made attempt(s), do nothing
			if ($iID==$iiID){
			}else{
			//otherwise increase counter
				$currentCounter++;
			}
			if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($_COOKIE[psdata][level]=='Operations' || $iiID==$_COOKIE[psdata][user_id]) && ($defendant != "MAIL")){
				echo str_replace("[PAGE]","Set 2 (Affidavit $currentCounter of $totalPages)",$pageII["$count2"]);
			}
		}
		if ($pageIII["$count2"] != ''){
			$currentCounter++;
			if (($count2==$defendant || $defendant=="ALL" || $defendant=="MAIL") && ($_COOKIE[psdata][level]=='Operations') && ($defendant != "SERVER")){
				echo str_replace("[PAGE]","Set 2 (Affidavit $currentCounter of $totalPages)",$pageIII["$count2"]);
			}
		}
		if ($pagePD["$count2"] != ''){
			$currentCounter++;
			if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($_COOKIE[psdata][level]=='Operations' || $PDID["$count2"]==$_COOKIE[psdata][user_id]) && ($defendant != "MAIL")){
				echo str_replace("[PAGE]","Set 2 (Affidavit $currentCounter of $totalPages)",$pagePD["$count2"]);
			}
		}
	}
}
//execute affidavit code depending on inputs
if ($_GET[server]){
	$serveID=$_GET[server];
	if ($_GET[start]){
		$start=$_GET[start];
		if ($_GET[stop]){
			$stop=$_GET[stop];
			if ($stop < $start){
				echo "<br><br><br><center><h1 style='color:#FF0000; font-size:48px;'>THAT RANGE OF AFFIDAVITS CANNOT BE DISPLAYED.</h1></center>";
			}
			$q10="SELECT packet_id FROM standard_packets where (server_id='$serveID' OR server_ida='$serveID' OR server_idb='$serveID' OR server_idc='$serveID' OR server_idd='$serveID' OR server_ide='$serveID') AND packet_id >= '$start' AND packet_id <= '$stop' AND process_status <> 'CANCELLED' AND affidavit_status='SERVICE CONFIRMED' AND filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS'";
		}else{
			$q10="SELECT packet_id FROM standard_packets where (server_id='$serveID' OR server_ida='$serveID' OR server_idb='$serveID' OR server_idc='$serveID' OR server_idd='$serveID' OR server_ide='$serveID') AND packet_id >= '$start' AND process_status <> 'CANCELLED' AND affidavit_status='SERVICE CONFIRMED' AND filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS'";
		}
	}else{
		$q10="SELECT packet_id FROM standard_packets where (server_id='$serveID' OR server_ida='$serveID' OR server_idb='$serveID' OR server_idc='$serveID' OR server_idd='$serveID' OR server_ide='$serveID') AND process_status <> 'CANCELLED' AND affidavit_status='SERVICE CONFIRMED' AND filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS'";
	}
	$r10=@mysql_query($q10) or die ("Query: $q10<br>".mysql_error());
	while ($d10=mysql_fetch_array($r10, MYSQL_ASSOC)){
	//echo $d10[packet_id].'<br>';
	$packet=$d10[packet_id];
	makeAffidavit($packet,"ALL");
	}
}elseif($_GET[packet] && $_GET[mail]){
	makeAffidavit($_GET[packet],"MAIL");
}elseif($_GET[packet] && $_GET[ps]){
	makeAffidavit($_GET[packet],"SERVER");
}elseif ($_GET[packet] && $_GET[def]){
	makeAffidavit($_GET[packet],$_GET[def]);
}elseif($_GET[packet] && !$_GET[def]){
	makeAffidavit($_GET[packet],"ALL");
}
if ($_GET['autoPrint'] == 1){
echo "<script>
if (window.self) window.print();
self.close();
</script>";
}
debug(1256,"Affidavit End");

?>