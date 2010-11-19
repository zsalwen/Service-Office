<?
//include '/sandbox/service/functions.php';
@mysql_connect ();
mysql_select_db ('core');
// start output buffering
$subtract='0';
function defCount($packet_id){
	$c=0;
	$r=@mysql_query("SELECT name1, name2, name3, name4, name5, name6 from ps_packets WHERE packet_id='$packet_id'");
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	if ($d[name1]){$c++;}
	if ($d[name2]){$c++;}
	if ($d[name3]){$c++;}
	if ($d[name4]){$c++;}
	if ($d[name5]){$c++;}
	if ($d[name6]){$c++;}
	return $c;
}
function attorneyCustomLang($att,$str){
	$r=@mysql_query("SELECT * FROM ps_str_replace where attorneys_id = '$att'");
	while ($d=mysql_fetch_array($r, MYSQL_ASSOC)){
		if ($d['str_search'] && $d['str_replace'] && $str && $att){
			$str = str_replace($d['str_search'], strtoupper($d['str_replace']), $str);
			$str = str_replace(strtoupper($d['str_search']), strtoupper($d['str_replace']), $str);
			//echo "<script>alert('Replacing ".strtoupper($d['str_search'])." with ".strtoupper($d['str_replace']).".');< /script>";
		}
	}
	return $str;
}
?>
<style>
.dim {
   /* the filter attribute is recognized in
   Internet Explorer and should be a percentage */
   filter: Alpha(opacity=40);
   /* the -moz-opacity attribute is recognized by 
   Gecko browsers and should be a decimal */
   -moz-opacity: .40;
   /* opacity is the proposed CSS3 method, supported
   in recent Gecko browsers */
   opacity: .40;
}
.dimmer {
   /* the filter attribute is recognized in
   Internet Explorer and should be a percentage */
   filter: Alpha(opacity=25);
   /* the -moz-opacity attribute is recognized by 
   Gecko browsers and should be a decimal */
   -moz-opacity: .25;
   /* opacity is the proposed CSS3 method, supported
   in recent Gecko browsers */
   opacity: .25;
}
td { font-variant:small-caps; font-size:12px;}
td.a {font-size:14px;}
td.b {font-size:24px;}
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
error_log("[".date('h:iA n/j/y')."] [".$_COOKIE[psdata][name]."] [".trim($_GET[packet])."] [start ob]  \n", 3, '/logs/fail.log');

function makeAffidavit($p,$defendant,$level,$user_id){
error_log("[".date('h:iA n/j/y')."] [".$_COOKIE[psdata][name]."] [".trim($_GET[packet])."] [makeAffidavit($p,$defendant,$level,$user_id]  \n", 3, '/logs/fail.log');

	$packet = $p;
	$def = 0;
	if (strpos($defendant,"!")){
		$overRide=1;
		$explode=explode('!',$defendant);
		$defendant=$explode[0];
	}
	// get main information
	if ($overRide == '1'){
		$q1="SELECT * FROM ps_packets WHERE packet_id='$packet'";
		$dim="";
	}else{
		$q1="SELECT * FROM ps_packets WHERE packet_id='$packet' AND affidavit_status='SERVICE CONFIRMED'";
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
	if (!preg_match("/CITY|D.C./", $court)){
		$court = str_replace('PRINCE GEORGES','PRINCE GEORGE\'S',$court);
		$court = str_replace('QUEEN ANNES','QUEEN ANNE\'S',$court);
		$court = str_replace('ST MARYS','ST MARY\'S',$court);
		$court = ucwords(strtolower($court))." County";
	} else {
		$court = ucwords(strtolower($court));
	}
	while ($def < defCount($packet)){$def++;
	if ($def < $defCount ){
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
	}
	if ($d1["name$def"] != ''){
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
	}else{
		$addlDocs="Order to Docket,";
	}
	mysql_select_db ('core');
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
	// get service history
	$q4="SELECT * from ps_history where packet_id = '$packet' AND defendant_id = '$def' and (wizard='FIRST EFFORT' or wizard='INVALID') and onAffidavit='checked' order by sort_value desc";
	$r4=@mysql_query($q4) or die(mysql_error());
	while ($d4=mysql_fetch_array($r4, MYSQL_ASSOC)){
		if ($d4[serverID] == $d1[server_id]){
			$attempts .= $d4[action_str];
			$iID = $d4[serverID];
		}elseif($d1[server_ida] && $d4[serverID] == $d1[server_ida]){
			$attemptsa .= $d4[action_str];
			$iIDa = $d4[serverID];
		}elseif($d1[server_idb] && $d4[serverID] == $d1[server_idb]){
			$attemptsb .= $d4[action_str];
			$iIDb = $d4[serverID];
		}elseif($d1[server_idc] && $d4[serverID] == $d1[server_idc]){
			$attemptsc .= $d4[action_str];
			$iIDc = $d4[serverID];
		}elseif($d1[server_idd] && $d4[serverID] == $d1[server_idd]){
			$attemptsd .= $d4[action_str];
			$iIDd = $d4[serverID];
		}elseif($d1[server_ide] && $d4[serverID] == $d1[server_ide]){
			$attemptse .= $d4[action_str];
			$iIDe = $d4[serverID];
		}
	}

	$q4="SELECT * from ps_history where packet_id = '$packet' AND defendant_id = '$def' and wizard='SECOND EFFORT' and onAffidavit='checked' order by sort_value";
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

	$q4="SELECT * from ps_history where packet_id = '$packet' AND defendant_id = '$def' and action_type = 'Posted Papers' and onAffidavit='checked'";
	$r4=@mysql_query($q4) or die(mysql_error());
	$d4=mysql_fetch_array($r4, MYSQL_ASSOC);
	$posting = $d4[action_str];
	$iiID = $d4[serverID];

	$q4="SELECT * from ps_history where packet_id = '$packet' AND defendant_id = '$def' and action_type = 'First Class C.R.R. Mailing' and onAffidavit='checked'";
	$r4=@mysql_query($q4) or die(mysql_error());
	while ($d4=mysql_fetch_array($r4, MYSQL_ASSOC)){
		$mailing .= $d4[action_str];
		$crr=$d4[action_type];
		$iiiID = $d4[serverID];
	}
	if ($mailing == ''){
		$q4="SELECT * from ps_history where packet_id = '$packet' AND defendant_id = '$def' and action_type = 'First Class Mailing' and onAffidavit='checked'";
		$r4=@mysql_query($q4) or die(mysql_error());
		while ($d4=mysql_fetch_array($r4, MYSQL_ASSOC)){
			$mailing .= $d4[action_str];
			$iiiID = $d4[serverID];
			$first = $d4[action_type];
		}
	}

	$q4="SELECT * from ps_history where packet_id = '$packet' AND defendant_id = '$def' and action_type = 'Served Defendant' and onAffidavit='checked'";
	$r4=@mysql_query($q4) or die(mysql_error());
	$d4=mysql_fetch_array($r4, MYSQL_ASSOC);
	$delivery = $d4[action_str];
	$deliveryID = $d4[serverID];
	$resident = $d1["name$def"];
	$residentDesc = $d4[residentDesc];
	$serveAddress = $d4[address];
	if ($delivery == ''){
		$q4="SELECT * from ps_history where packet_id = '$packet' AND defendant_id = '$def' and action_type = 'Served Resident' and onAffidavit='checked'";
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
		$type = 'pd';
	}else{
		$type = 'non';
	}
	// hard code
	$header="<td colspan='2' align='center' style='font-variant:small-caps;'><font size='5'>State of Maryland</font></td></tr>
		<tr><td colspan='2' align='center'><font size='4'>Circuit Court for ".$court."</font></td></tr>
		<tr></tr>
		<tr><td class='a'><font size='2'>".$plaintiff."<br><small>_____________________<br /><em>Plaintiff</em></small><br /><br />v.<br /><br />";
			if ($d1[onAffidavit1]=='checked'){$header .= strtoupper($d1['name1']).'<br>';}
			if ($d1['name2'] && $d1[onAffidavit2]=='checked'){$header .= strtoupper($d1['name2']).'<br>';}
			if ($d1['name3'] && $d1[onAffidavit3]=='checked'){$header .= strtoupper($d1['name3']).'<br>';}
			if ($d1['name4'] && $d1[onAffidavit4]=='checked'){$header .= strtoupper($d1['name4']).'<br>';}
			if ($d1['name5'] && $d1[onAffidavit5]=='checked'){$header .= strtoupper($d1['name5']).'<br>';}
			if ($d1['name6'] && $d1[onAffidavit6]=='checked'){$header .= strtoupper($d1['name6']).'<br>';}
			$header .=strtoupper($d1['address1']).'<br>';
			$header .=strtoupper($d1['city1']).', '.strtoupper($d1['state1']).' '.$d1['zip1'].'<br>';
			$header .= "<small>_____________________<br /><em>Defendant</em></small></font></td>
				<td align='right' valign='top' style='padding-left:200px; width:200px' nowrap='nowrap'><div style='border:solid 1px #666666;'><center><font size='5'>Case Number<br />&nbsp;".str_replace(0,'&Oslash;',$d1[case_no])."</font></center></div>";

	if ($type == "non"){
		$article = "14-209(b)";
		$result = "MAILING AND POSTING";
		if ($attempts != ''){
				$history = "<div style='font-weight:300'><u><font size='2'>Describe with particularity the good faith efforts to serve the mortgagor or grantor, ".$d1["name$def"].",  by personal delivery:</font></u></div>
				".$attempts;
			}elseif($attemptsa != ''){
				$history = "<div style='font-weight:300'><u><font size='2'>Describe with particularity the good faith efforts to serve the mortgagor or grantor, ".$d1["name$def"].",  by personal delivery:</font></u></div>
				".$attemptsa;
				$iID=$iIDa;
			}
			$history2 = "<div style='font-weight:300'><u><font-size='2'>Include the date of the posting and a description of the location of the posting on the property:</font></u></div>".$posting;
		if ($mailing == ''){
			$history3 = "<div class='dim' style='font-weight:300'><u><font size='2'>State the date on which the required papers were mailed by first-class and certified mail, return receipt requested, and the name and address of the addressee:</font></u>
				<center><font size='36 px'>AWAITING MAILING<br>DO NOT FILE</font></center></div>";
			$noMail = 1;
		}else{
			if ($crr != ''){
				$history3 = "<div style='font-weight:300'><u><font-size='2'>State the date on which the required papers were mailed by first-class and certified mail, return receipt requested, and the name and address of the addressee:</font></u></div>
				".$mailing;
			}elseif(($iiID == $d1[server_id]) || ($first != '' && $crr == '')){
				$history3 = "<div style='font-weight:300'><u><font-size='2'>State the date on which the required papers were mailed by first-class and the name and address of the addressee:</font></u></div>
				".$mailing;
			}
		}
			$history4 = "<font-size='2'><u>If available, the original certified mail return receipt shall be attached to the affidavit.</u><div style='height:50px; width:550px; border:double 4px; color:#666'>Affix original certified mail return receipt here.</div></font>";
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
		$historye = "";
		$historye = "<div style='font-weight:300'><u>Describe with particularity the good faith efforts to serve the mortgagor or grantor, ".$d1["name$def"].",  by personal delivery:</u></div>
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
	$cord=$d1[packet_id]."-".$def."-".$serverID."%";
	?>
		<table style='page-break-after:always;' width="100%" align="center" bgcolor="#FFFFFF" <? if (strtoupper($d1[affidavit_status]) != "SERVICE CONFIRMED"){ echo $dim;}?>>
		<tr><?=$header?><IMG SRC='http://staff.mdwestserve.com/barcode.php?barcode=<?=$cord?>&width=300&height=40'><center>File Number: <?=$d1[client_file]?><br>[PAGE]</center></td></tr>
		<tr>
			<td colspan="2" align="center" valign="top"><u><b><?=$amended?>Affidavit of Attempted Delivery<? if ($iID && !$iIDa && !$iIDb && !$iIDc && !$iIDd && !$iIDe){ echo " and Posting";}?></b></u></td>
		</tr>
		<tr>
			<td colspan="2" align="center" valign="top"><font size='4'><b><?=$result?></font></b></td>
		</tr>
		<tr>
			<td colspan="2" align="left"><font size='2'>Pursuant to Maryland Real Property Article 7-105.1 and Maryland Rules of Procedure <?=$article?> <?=$result?> a copy of the <?=$addlDocs?> and all other papers filed with it (the "Papers") in the above-captioned case by:</font></td>
		</tr>
		<tr>
			<td colspan="2" style="padding-left:20px;"><b><?=stripslashes($historye)?></b></td>
		</tr>   
		<tr>
			<td colspan="2"><font size='2'>I solemnly affirm under the penalties of perjury that the contents of this <?=strtolower($amended)?>affidavit are true and correct, to the best of my knowledge, information and belief<? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?>, and that I did attempt service as set forth above<? }?><? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?>, and that I served the <?=$addlDocs?> and all other papers filed with it to [PERSON SERVED]<? }?>.</font></td>
		</tr>
		<tr>
			<td colspan="2"><font size='2'>I, <?=$serverName?>, certify that I am over eighteen years old and not a party to this action<? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?> and that I served [PERSON SERVED], [RELATION TO DEFENDANT]<? }?><? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?> and that I did attempt service as set forth above<? }?>.</font></td>
		</tr>
		<tr>
			<td valign="top"><font size='2'>____________________________________<br />Notary Public<br /><br /><br />SEAL</font></td>
			<td valign="top"><font size='2'>________________________<u>DATE:</u>________<br /><?=$serverName?><br /><?=$serverAdd?><br /><?=$serverCity?>, <?=$serverState?> <?=$serverZip?><br /><?=$serverPhone?><br><?=$_SERVER[REMOTE_ADDR]?></font></td> 
		</tr>
	</table>
	<? }
	$pagee["$def"] = ob_get_clean();
	ob_start();
	//5th server
	if ($iIDd){
		$historyd = "";
		$historyd = "<div style='font-weight:300'><u>Describe with particularity the good faith efforts to serve the mortgagor or grantor, ".$d1["name$def"].",  by personal delivery:</u></div>
				".$attemptsd;
	 ?>
		<table style='page-break-after:always;' width="80%" align="center" bgcolor="#FFFFFF" <? if (strtoupper($d1[affidavit_status]) != "SERVICE CONFIRMED"){echo $dim;}?>>
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
	$cord=$d1[packet_id]."-".$def."-".$serverID."%";
	?>        
		<? echo "<tr>".$header."<IMG SRC='http://staff.mdwestserve.com/barcode.php?barcode=".$cord."&width=300&height=40'><center>File Number: ".$d1[client_file]."<br>[PAGE]</center></td></tr>"; ?>
		<tr>
			<td colspan="2" align="center" valign="top"><u><b><?=$amended?>Affidavit of Attempted Delivery</b></u></td>
		</tr>
		<tr>
			<td colspan="2" align="center" valign="top"><font size='4'><b><?=$result?></b></font></td>
		</tr>
		<tr>
			<td colspan="2" align="left"><font size='2'>Pursuant to Maryland Real Property Article 7-105.1 and Maryland Rules of Procedure <?=$article?> <?=$result?> a copy of the <?=$addlDocs?> and all other papers filed with it (the "Papers") in the above-captioned case by:</font></td>
		</tr>
		<tr>
			<td colspan="2" style="padding-left:20px;"><b><?=stripslashes($historyd)?></b></td>
		</tr>
		<tr>
			<td colspan="2"><font size='2'>I solemnly affirm under the penalties of perjury that the contents of this <?=strtolower($amended)?>affidavit are true and correct, to the best of my knowledge, information and belief<? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?>, and that I did attempt service as set forth above<? }?><? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?>, and that I served the <?=$addlDocs?> and all other papers filed with it to [PERSON SERVED]<? }?>.</font></td>
		</tr>
		<tr>
			<td colspan="2"><font size='2'>I, <?=$serverName?>, certify that I am over eighteen years old and not a party to this action<? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?> and that I served [PERSON SERVED], [RELATION TO DEFENDANT]<? }?><? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?> and that I did attempt service as set forth above<? }?>.</font></td>
		</tr>
		<tr>
			<td valign="top"><font size='2'>____________________________________<br />Notary Public<br /><br /><br />SEAL</font></td>
			<td valign="top"><font size='2'>________________________<u>DATE:</u>________<br /><?=$serverName?><br /><?=$serverAdd?><br /><?=$serverCity?>, <?=$serverState?> <?=$serverZip?><br /><?=$serverPhone?><br><?=$_SERVER[REMOTE_ADDR]?></font></td> 
		</tr>
	</table>
	<? } 
	$paged["$def"] = ob_get_clean();
	ob_start();
	//4th server
	if ($iIDc){
		$historyc = "";
		$historyc = "<div style='font-weight:300'><u>Describe with particularity the good faith efforts to serve the mortgagor or grantor, ".$d1["name$def"].",  by personal delivery:</u></div>
				".$attemptsc;
	?>
		<table style='page-break-after:always;' width="80%" align="center" bgcolor="#FFFFFF" <? if (strtoupper($d1[affidavit_status]) != "SERVICE CONFIRMED"){echo $dim;}?>>
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
	$cord=$d1[packet_id]."-".$def."-".$serverID."%";
	?>
		<? echo "<tr>".$header."<IMG SRC='http://staff.mdwestserve.com/barcode.php?barcode=".$cord."&width=300&height=40'><center>File Number: ".$d1[client_file]."<br>[PAGE]</center></td></tr>"; ?>
		<tr>
			<td colspan="2" align="center" valign="top"><b><u><?=$amended?>Affidavit of Attempted Delivery</u></b></td>
		</tr>
		<tr>
			<td colspan="2" align="center" valign="top"><font size='4'><b><?=$result?></b></font></td>
		</tr>
		<tr>
			<td colspan="2" align="left"><font size='2'>Pursuant to Maryland Real Property Article 7-105.1 and Maryland Rules of Procedure <?=$article?> <?=$result?> a copy of the <?=$addlDocs?> and all other papers filed with it (the "Papers") in the above-captioned case by:</font></td>
		</tr>
		<tr>
			<td colspan="2" style="padding-left:20px;"><b><?=stripslashes($historyc)?></b></td>
		</tr>
		<tr>
			<td colspan="2"><font size='2'>I solemnly affirm under the penalties of perjury that the contents of this <?=strtolower($amended)?>affidavit are true and correct, to the best of my knowledge, information and belief<? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?>, and that I did attempt service as set forth above<? }?><? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?>, and that I served the <?=$addlDocs?> and all other papers filed with it to [PERSON SERVED]<? }?>.</font></td>
		</tr>
		<tr>
			<td colspan="2"><font size='2'>I, <?=$serverName?>, certify that I am over eighteen years old and not a party to this action<? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?> and that I served [PERSON SERVED], [RELATION TO DEFENDANT]<? }?><? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?> and that I did attempt service as set forth above<? }?>.</td></td>
		</tr>
		<tr>
			<td valign="top"><font size='2'>____________________________________<br />Notary Public<br /><br /><br />SEAL</font></td>
			<td valign="top"><font size='2'>________________________<u>DATE:</u>________<br /><?=$serverName?><br /><?=$serverAdd?><br /><?=$serverCity?>, <?=$serverState?> <?=$serverZip?><br /><?=$serverPhone?><br><?=$_SERVER[REMOTE_ADDR]?></font></td> 
		</tr>
	</table>
	<? 
	}
	$pagec["$def"] = ob_get_clean();
	ob_start();
	//3rd server
	if ($iIDb){
		$historyb = "";
		$historyb = "<div style='font-weight:300'><u>Describe with particularity the good faith efforts to serve the mortgagor or grantor, ".$d1["name$def"].",  by personal delivery:</u></div>
				".$attemptsb;
	?>
		<table style='page-break-after:always;' width="80%" align="center" bgcolor="#FFFFFF" <? if (strtoupper($d1[affidavit_status]) != "SERVICE CONFIRMED"){echo $dim;}?>>
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
	$cord=$d1[packet_id]."-".$def."-".$serverID."%";
	?>   
		<? echo "<tr>".$header."<IMG SRC='http://staff.mdwestserve.com/barcode.php?barcode=".$cord."&width=300&height=40'><center>File Number: ".$d1[client_file]."<br>[PAGE]</center></td></tr>"; ?>
		<tr>
			<td colspan="2" align="center" valign="top"><b><u><?=$amended?>Affidavit of Attempted Delivery</u></b></td>
		</tr>
		<tr>
			<td colspan="2" align="center" valign="top"><font size='4'><b><?=$result?></b></font></td>
		</tr>
		<tr>
			<td colspan="2" align="left"><font size='2'>Pursuant to Maryland Real Property Article 7-105.1 and Maryland Rules of Procedure <?=$article?> <?=$result?> a copy of the <?=$addlDocs?> and all other papers filed with it (the "Papers") in the above-captioned case by:</font></td>
		</tr>
		<tr>
			<td colspan="2" style="padding-left:20px;"><b><?=stripslashes($historyb)?></b></td>
		</tr>     
		<tr>
			<td colspan="2"><font size='2'>I solemnly affirm under the penalties of perjury that the contents of this <?=strtolower($amended)?>affidavit are true and correct, to the best of my knowledge, information and belief<? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?>, and that I did attempt service as set forth above<? }?><? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?>, and that I served the <?=$addlDocs?> and all other papers filed with it to [PERSON SERVED]<? }?>.</font></td>
		</tr>
		<tr>
			<td colspan="2"><font size='2'>I, <?=$serverName?>, certify that I am over eighteen years old and not a party to this action<? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?> and that I served [PERSON SERVED], [RELATION TO DEFENDANT]<? }?><? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?> and that I did attempt service as set forth above<? }?>.</font></td>
		</tr>
		<tr>
			<td valign="top"><font size='2'>____________________________________<br />Notary Public<br /><br /><br />SEAL</font></td>
			<td valign="top"><font size='2'>________________________<u>DATE:</u>________<br /><?=$serverName?><br /><?=$serverAdd?><br /><?=$serverCity?>, <?=$serverState?> <?=$serverZip?><br /><?=$serverPhone?><br><?=$_SERVER[REMOTE_ADDR]?></font></td> 
		</tr>
	</table>
	<? 
	}
	$pageb["$def"] = ob_get_clean();
	ob_start();
	//2nd server
	if ($iIDa){
		$historya = "";
		$historya = "<div style='font-weight:300'><u>Describe with particularity the good faith efforts to serve the mortgagor or grantor, ".$d1["name$def"].",  by personal delivery:</u></div>
				".$attemptsa;
	?>
		<table style='page-break-after:always;' width="80%" align="center" bgcolor="#FFFFFF" <? if (strtoupper($d1[affidavit_status]) != "SERVICE CONFIRMED"){echo $dim;}?>>
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
	$cord=$d1[packet_id]."-".$def."-".$serverID."%";
	?>  
		<? echo "<tr>".$header."<IMG SRC='http://staff.mdwestserve.com/barcode.php?barcode=".$cord."&width=300&height=40'><center>File Number: ".$d1[client_file]."<br>[PAGE]</center></td></tr>"; ?>
		<tr>
			<td colspan="2" align="center" valign="top"><b><u><?=$amended?>Affidavit of Attempted Delivery</u></b></td>
		</tr>
		<tr>
			<td colspan="2" align="center" valign="top"><font size='4'><b><?=$result?></b></font></td>
		</tr>
		<tr>
			<td colspan="2" align="left"><font size='2'>Pursuant to Maryland Real Property Article 7-105.1 and Maryland Rules of Procedure <?=$article?> <?=$result?> a copy of the <?=$addlDocs?> and all other papers filed with it (the "Papers") in the above-captioned case by:</font></td>
		</tr>
		<tr>
			<td colspan="2" style="padding-left:20px;"><b><?=stripslashes($historya)?></b></td>
		</tr>      
		<tr>
			<td colspan="2"><font size='2'>I solemnly affirm under the penalties of perjury that the contents of this <?=strtolower($amended)?>affidavit are true and correct, to the best of my knowledge, information and belief<? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?>, and that I did attempt service as set forth above<? }?><? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?>, and that I served the <?=$addlDocs?> and all other papers filed with it to [PERSON SERVED]<? }?>.</font></td>
		</tr>
		<tr>
			<td colspan="2"><font size='2'>I, <?=$serverName?>, certify that I am over eighteen years old and not a party to this action<? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?> and that I served [PERSON SERVED], [RELATION TO DEFENDANT]<? }?><? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?> and that I did attempt service as set forth above<? }?>.</font></td>
		</tr>
		<tr>
			<td valign="top"><font size='2'>____________________________________<br />Notary Public<br /><br /><br />SEAL</font></td>
			<td valign="top"><font size='2'>________________________<u>DATE:</u>________<br /><?=$serverName?><br /><?=$serverAdd?><br /><?=$serverCity?>, <?=$serverState?> <?=$serverZip?><br /><?=$serverPhone?><br><?=$_SERVER[REMOTE_ADDR]?></font></td> 
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
		<table style='page-break-after:always;' width="80%" align="center" bgcolor="#FFFFFF" <? if (strtoupper($d1[affidavit_status]) != "SERVICE CONFIRMED"){echo $dim;}?>>
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
	$cord=$d1[packet_id]."-".$def."-".$serverID."%";
	 echo "<tr>".$header."<IMG SRC='http://staff.mdwestserve.com/barcode.php?barcode=".$cord."&width=300&height=40'><center>File Number: ".$d1[client_file]."<br>[PAGE]</center></td></tr>"; ?>
		<tr>
			<td colspan="2" align="center" valign="top"><b><u><?=$amended?>Affidavit of Attempted Delivery<? if ($iID==$iiID){ echo " and Posting";} ?></u></b></td>
		</tr>
		<tr>
			<td colspan="2" align="center" valign="top"><font size='4'><b><?=$result?></b></font></td>
		</tr>
		<tr>
			<td colspan="2" align="left"><font size='2'>Pursuant to Maryland Real Property Article 7-105.1 and Maryland Rules of Procedure <?=$article?> <?=$result?> a copy of the <?=$addlDocs?> and all other papers filed with it (the "Papers") in the above-captioned case by:</font></td>
		</tr>
		<tr>
			<td colspan="2" style="padding-left:20px;"><b><?=stripslashes($history)?></b></td>
		</tr>
	<?
	if ($iID == $iiID){
	}else{
	?>        
		<tr>
			<td colspan="2"><font size='2'>I solemnly affirm under the penalties of perjury that the contents of this <?=strtolower($amended)?>affidavit are true and correct to the best of my knowledge, information and belief<? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?>, and that I did attempt service as set forth above<? }?><? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?>, and that I served the <?=$addlDocs?> and all other papers filed with it to [PERSON SERVED]<? }?>.</font></td>
		</tr>
		<tr>
			<td colspan="2"><font size='2'>I, <?=$serverName?>, certify that I am over eighteen years old and not a party to this action<? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?> and that I served [PERSON SERVED], [RELATION TO DEFENDANT]<? }?><? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?> and that I did attempt service as set forth above<? }?>.</font></td>
		</tr>
		<tr>
			<td valign="top"><font size='2'>____________________________________<br />Notary Public<br /><br /><br />SEAL</font></td>
			<td valign="top"><font size='2'>________________________<u>DATE:</u>________<br /><?=$serverName?><br /><?=$serverAdd?><br /><?=$serverCity?>, <?=$serverState?> <?=$serverZip?><br /><?=$serverPhone?><br><?=$_SERVER[REMOTE_ADDR]?></font></td> 
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
		<table style='page-break-after:always;' width="80%" align="center" bgcolor="#FFFFFF" <? if (strtoupper($d1[affidavit_status]) != "SERVICE CONFIRMED"){echo $dim;}?>>
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
	$cord=$d1[packet_id]."-".$def."-".$serverID."%";
	?> 
		<? echo "<tr>".$header."<IMG SRC='http://staff.mdwestserve.com/barcode.php?barcode=".$cord."&width=300&height=40'><center>File Number: ".$d1[client_file]."<br>[PAGE]</center></td></tr>"; ?>
		<tr>
			<td colspan="2" align="center" valign="top"><b><u><?=$amended?>Affidavit of Posting</u></b></td>
		</tr>
		<tr>
			<td colspan="2" align="center" valign="top"><font size='4'><b><?=$result?></b></font></td>
		</tr>
		<tr>
			<td colspan="2" align="left"><font size='2'>Pursuant to Maryland Real Property Article 7-105.1 and Maryland Rules of Procedure <?=$article?> <?=$result?> a copy of the <?=$addlDocs?> and all other papers filed with it (the "Papers") in the above-captioned case by:</font></td>
		</tr>
		<? } ?>
		<tr>
			<td colspan="2" style="padding-left:20px"><b><?=stripslashes($history2)?></b></td>
		</tr>       
		<tr>
			<td colspan="2"><font size='2'>I solemnly affirm under the penalties of perjury that the contents of this <?=strtolower($amended)?>affidavit are true and correct to the best of my knowledge, information and belief.</font></td>
		</tr>
		<tr>
			<td colspan="2"><font size='2'>I, <?=$serverName?>, certify that I am over eighteen years old and not a party to this action.</font></td>
		</tr>
		<tr>
			<td valign="top"><font size='2'>____________________________________<br />Notary Public<br /><br /><br />SEAL</font></td>
			<td valign="top"><font size='2'>________________________<u>DATE:</u>________<br /><?=$serverName?><br /><?=$serverAdd?><br /><?=$serverCity?>, <?=$serverState?> <?=$serverZip?><br /><?=$serverPhone?><br><?=$_SERVER[REMOTE_ADDR]?></font></td> 
		</tr>
	</table>
	<? } 
	 $pageII["$def"] = ob_get_clean();
	 $postingID["$def"] = $iiID;
	 ob_start();
	  if($iiiID){ ?>
		<table style='page-break-after:always;' width="80%" align="center" bgcolor="#FFFFFF" <? if (strtoupper($d1[affidavit_status]) != "SERVICE CONFIRMED"){echo $dim;}?>>
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
	$cord=$d1[packet_id]."-".$def."-".$serverID."%";
	 echo "<tr>".$header."<IMG SRC='http://staff.mdwestserve.com/barcode.php?barcode=".$cord."&width=300&height=40'><center>File Number: ".$d1[client_file]."<br>[PAGE]</center></td></tr>"; ?>
		<tr>
			<td colspan="2" align="center" valign="top"><b><u><?=$amended?>Affidavit of Mailing</u></b></td>
		</tr>
		<tr>
			<td colspan="2" align="center" valign="top"><font size='4'><b><?=$result?></b></font></td>
		</tr>
		<tr>
			<td colspan="2" align="left"><font size='2'>Pursuant to Maryland Real Property Article 7-105.1 and Maryland Rules of Procedure <?=$article?> <?=$result?> a copy of the <?=$addlDocs?> and all other papers filed with it (the "Papers") in the above-captioned case by:</font></td>
		</tr>
		<tr>
			<td colspan="2" style="padding-left:20px"><b><?=stripslashes($history3)?></b></td>
		</tr>      
		<tr <? if($noMail == 1 && !$_GET[mail]){ echo 'class="dim"';}?>>
			<td colspan="2"><font size='2'>I solemnly affirm under the penalties of perjury that the contents of this <?=strtolower($amended)?>affidavit are true and correct to the best of my knowledge, information and belief.  And that I mailed the above papers under section 14-209(b) to <?=strtoupper($d1["name$def"])?>.</font></td>
		</tr>
		<tr <? if($noMail == 1 && !$_GET[mail]){ echo 'class="dim"';}?>>
			<td colspan="2"><font size='2'>I, <?=$serverName?>, certify that I am over eighteen years old and not a party to this action.</font></td>
		</tr>
		<tr <? if($noMail == 1 && !$_GET[mail]){ echo 'class="dim"';}?>>
			<td valign="top"><font size='2'>____________________________________<br />Notary Public<br /><br /><br />SEAL</font></td>
			<td valign="top"><font size='2'>________________________<u>DATE:</u>________<br /><?=$serverName?><br /><?=$serverAdd?><br /><?=$serverCity?>, <?=$serverState?> <?=$serverZip?><br /><?=$serverPhone?><br><?=$_SERVER[REMOTE_ADDR]?></font></td> 
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
	<table style='page-break-after:always;' width="80%" align="center" bgcolor="#FFFFFF" <? if (strtoupper($d1[affidavit_status]) != "SERVICE CONFIRMED"){echo $dim;}?>>
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
	$cord=$d1[packet_id]."-".$def."-".$serverID."%";
	?> 
	<? echo "<tr>".$header."<IMG SRC='http://staff.mdwestserve.com/barcode.php?barcode=".$cord."&width=300&height=40'><center>File Number: ".$d1[client_file]."<br>[PAGE]</center></td></tr>"; ?>
		<tr>
			<td colspan="2" align="center" valign="top"><b><u><?=$amended?>Affidavit of Personal Delivery</u></b></td>
		</tr>
		<tr>
			<td colspan="2" align="center" valign="top"><font size='4'><b><?=$result?></b></font></td>
		</tr>
		<tr>
			<td colspan="2" align="left"><font size='2'>Pursuant to Maryland Real Property Article 7-105.1 and Maryland Rules of Procedure <?=$article?> <?=$result?> a copy of the <?=$addlDocs?> and all other papers filed with it (the "Papers") in the above-captioned case by:</font></td>
		</tr>
	<? if ($residentDesc){
		$desc=strtoupper(str_replace('CO-A BORROWER IN THE ABOVE-REFERENCED CASE', 'A BORROWER IN THE ABOVE-REFERENCED CASE', str_replace('BORROWER','A BORROWER IN THE ABOVE-REFERENCED CASE', attorneyCustomLang($d1[attorneys_id],strtoupper($residentDesc)))));
	}?>
		<tr>
			<td colspan="2" style="padding-left:20px; padding-top:20px; padding-bottom:20px; line-height:2;"><font size='4'><b><?=stripslashes($delivery)?></b></font></td>
		</tr>       
		<tr>
			<td colspan="2"><font size='2'>I solemnly affirm under the penalties of perjury that the contents of <? if ($type == 'non'){ ?>section (i) of <? }?>this <?=strtolower($amended)?>affidavit are true and correct to the best of my knowledge, information and belief<? if (($type == 'pd' && $nondef == '1') || ($type == 'pd' && $d1[packet_id] >= "10000")){?>, and that I served<? if (($type == 'pd' && $nondef == '1') && (strpos($delivery,"USUAL PLACE OF ABODE") || strpos($delivery,"RESIDENTIAL PROPERTY"))){ ?> at the usual place of abode<? } ?> the <?=$addlDocs?> and other papers to <? if ($resident){ echo strtoupper($resident);}else{ echo '[PERSON SERVED]';}?>, <? if ($residentDesc){echo $desc;}else{ echo '[RELATION TO DEFENDANT]';}?><? if ($serveAddress){ echo ', at '.$serveAddress;}?><? }elseif($type == 'pd' && $nondef != '1'){?>, and that I served the <?=$addlDocs?> and other papers to <?=strtoupper($d1["name$def"])?><? if ($serveAddress){ echo ', at '.strtoupper($serveAddress);}?><? } ?>.</font></td>
		</tr>
		<tr>
			<td colspan="2"><font size='2'>I, <?=$serverName?>, certify that I am over eighteen years old and not a party to this action.</font></td>
		</tr>
		<tr>
			<td valign="top"><font size='2'>____________________________________<br />Notary Public<br /><br /><br />SEAL</font></td>
			<td valign="top"><font size='2'>________________________<u>DATE:</u>________<br /><?=$serverName?><br /><?=$serverAdd?><br /><?=$serverCity?>, <?=$serverState?> <?=$serverZip?><br /><?=$serverPhone?><br><?=$_SERVER[REMOTE_ADDR]?></font></td> 
		</tr>
	</table>
	<? 
	$pagePD["$def"] = ob_get_clean();
	$PDID["$def"]=$deliveryID;
	$PDADD["$def"]=$serveAddress;
	}
	}
	}
	//count pages and construct table of contents
	$count=0;
	$totalPages=0;
	$defs=defCount($packet);
	$checked='';
	while($count < $defs){$count++;
		if ($pagee["$count"] != ''){
			$totalPages++;
		}
		if ($paged["$count"] != ''){
			$totalPages++;
		}
		if ($pagec["$count"] != ''){
			$totalPages++;
		}
		if ($pageb["$count"] != ''){
			$totalPages++;
		}
		if ($pagea["$count"] != ''){
			$totalPages++;
		}
		if ($pageI["$count"] != ''){
			$totalPages++;
		}
		if ($pageII["$count"] != ''){
			//if posting server also made attempt(s), do nothing
			if ($iID==$iiID){
			}else{
			//otherwise increase counter
				$totalPages++;
			}
		}
		if ($pageIII["$count"] != ''){
			$totalPages++;
		}
		if ($pagePD["$count"] != ''){
			$totalPages++;
		}
	}
	//echo affidavits
	$count2=0;
	$currentCounter=0;
	while($count2 < $defs){$count2++;
		if ($pagee["$count2"] != ''){
			$currentCounter++;
			if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($level=='Operations' || $iIDe==$user_id) && ($defendant != "MAIL")){
				echo str_replace("[PAGE]","Set 1 (Affidavit $currentCounter of $totalPages)",$pagee["$count2"]);
			}
		}
		if ($paged["$count2"] != ''){
			$currentCounter++;
			if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($level=='Operations' || $iIDd==$user_id) && ($defendant != "MAIL")){
				echo str_replace("[PAGE]","Set 1 (Affidavit $currentCounter of $totalPages)",$paged["$count2"]);
			}
		}
		if ($pagec["$count2"] != ''){
			$currentCounter++;
			if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($level=='Operations' || $iIDc==$user_id) && ($defendant != "MAIL")){
				echo str_replace("[PAGE]","Set 1 (Affidavit $currentCounter of $totalPages)",$pagec["$count2"]);
			}
		}
		if ($pageb["$count2"] != ''){
			$currentCounter++;
			if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($level=='Operations' || $iIDb==$user_id) && ($defendant != "MAIL")){
				echo str_replace("[PAGE]","Set 1 (Affidavit $currentCounter of $totalPages)",$pageb["$count2"]);
			}
		}
		if ($pagea["$count2"] != ''){
			$currentCounter++;
			if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($level=='Operations' || $iIDa==$user_id) && ($defendant != "MAIL")){
				echo str_replace("[PAGE]","Set 1 (Affidavit $currentCounter of $totalPages)",$pagea["$count2"]);
			}
		}
		if ($pageI["$count2"] != ''){
			$currentCounter++;
			if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($level=='Operations' || $iID==$user_id) && ($defendant != "MAIL")){
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
			if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($level=='Operations' || $iiID==$user_id) && ($defendant != "MAIL")){
				echo str_replace("[PAGE]","Set 1 (Affidavit $currentCounter of $totalPages)",$pageII["$count2"]);
			}
		}
		if ($pageIII["$count2"] != ''){
			$currentCounter++;
			if (($count2==$defendant || $defendant=="ALL" || $defendant=="MAIL") && ($level=='Operations') && ($defendant != "SERVER")){
				echo str_replace("[PAGE]","Set 1 (Affidavit $currentCounter of $totalPages)",$pageIII["$count2"]);
			}
		}
		if ($pagePD["$count2"] != ''){
			$currentCounter++;
			if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($level=='Operations' || $PDID["$count2"]==$user_id) && ($defendant != "MAIL")){
				echo str_replace("[PAGE]","Set 1 (Affidavit $currentCounter of $totalPages)",$pagePD["$count2"]);
			}
		}
	}
	$count2=0;
	$currentCounter=0;
	while($count2 < $defs){$count2++;
		if ($pagee["$count2"] != ''){
			$currentCounter++;
			if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($level=='Operations' || $iIDe==$user_id) && ($defendant != "MAIL")){
				echo str_replace("[PAGE]","Set 2 (Affidavit $currentCounter of $totalPages)",$pagee["$count2"]);
			}
		}
		if ($paged["$count2"] != ''){
			$currentCounter++;
			if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($level=='Operations' || $iIDd==$user_id) && ($defendant != "MAIL")){
				echo str_replace("[PAGE]","Set 2 (Affidavit $currentCounter of $totalPages)",$paged["$count2"]);
			}
		}
		if ($pagec["$count2"] != ''){
			$currentCounter++;
			if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($level=='Operations' || $iIDc==$user_id) && ($defendant != "MAIL")){
				echo str_replace("[PAGE]","Set 2 (Affidavit $currentCounter of $totalPages)",$pagec["$count2"]);
			}
		}
		if ($pageb["$count2"] != ''){
			$currentCounter++;
			if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($level=='Operations' || $iIDb==$user_id) && ($defendant != "MAIL")){
				echo str_replace("[PAGE]","Set 2 (Affidavit $currentCounter of $totalPages)",$pageb["$count2"]);
			}
		}
		if ($pagea["$count2"] != ''){
			$currentCounter++;
			if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($level=='Operations' || $iIDa==$user_id) && ($defendant != "MAIL")){
				echo str_replace("[PAGE]","Set 2 (Affidavit $currentCounter of $totalPages)",$pagea["$count2"]);
			}
		}
		if ($pageI["$count2"] != ''){
			$currentCounter++;
			if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($level=='Operations' || $iID==$user_id) && ($defendant != "MAIL")){
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
			if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($level=='Operations' || $iiID==$user_id) && ($defendant != "MAIL")){
				echo str_replace("[PAGE]","Set 2 (Affidavit $currentCounter of $totalPages)",$pageII["$count2"]);
			}
		}
		if ($pageIII["$count2"] != ''){
			$currentCounter++;
			if (($count2==$defendant || $defendant=="ALL" || $defendant=="MAIL") && ($level=='Operations') && ($defendant != "SERVER")){
				echo str_replace("[PAGE]","Set 2 (Affidavit $currentCounter of $totalPages)",$pageIII["$count2"]);
			}
		}
		if ($pagePD["$count2"] != ''){
			$currentCounter++;
			if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($level=='Operations' || $PDID["$count2"]==$user_id) && ($defendant != "MAIL")){
				echo str_replace("[PAGE]","Set 2 (Affidavit $currentCounter of $totalPages)",$pagePD["$count2"]);
			}
		}
	}
}
//execute affidavit code depending on inputs
$level='Operations';
$user_id='1';
//if $_GET[server], determine file range
if ($_GET[server]){
	$serveID=$_GET[server];
	if ($_GET[start]){
		$start=$_GET[start];
		if ($_GET[stop]){
			$stop=$_GET[stop];
			if ($stop < $start){
				echo "<br><br><br><center><h1 style='color:#FF0000; font-size:48px;'>THAT RANGE OF AFFIDAVITS CANNOT BE DISPLAYED.</h1></center>";
			}
			$q10="SELECT packet_id FROM ps_packets where (server_id='$serveID' OR server_ida='$serveID' OR server_idb='$serveID' OR server_idc='$serveID' OR server_idd='$serveID' OR server_ide='$serveID') AND packet_id >= '$start' AND packet_id <= '$stop' AND process_status <> 'CANCELLED' AND affidavit_status='SERVICE CONFIRMED' AND filing_status <> 'PREP TO FILE' AND filing_status <> 'AWAITING CASE NUMBER' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'SEND TO CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS'";
		}else{
			$q10="SELECT packet_id FROM ps_packets where (server_id='$serveID' OR server_ida='$serveID' OR server_idb='$serveID' OR server_idc='$serveID' OR server_idd='$serveID' OR server_ide='$serveID') AND packet_id >= '$start' AND process_status <> 'CANCELLED' AND affidavit_status='SERVICE CONFIRMED' AND filing_status <> 'PREP TO FILE' AND filing_status <> 'AWAITING CASE NUMBER' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'SEND TO CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS'";
		}
	}else{
		if ($_GET[packet]){
			$q10="SELECT packet_id FROM ps_packets where packet_id='$_GET[packet]'";
		}else{
			$q10="SELECT packet_id FROM ps_packets where (server_id='$serveID' OR server_ida='$serveID' OR server_idb='$serveID' OR server_idc='$serveID' OR server_idd='$serveID' OR server_ide='$serveID') AND process_status <> 'CANCELLED' AND affidavit_status='SERVICE CONFIRMED' AND filing_status <> 'PREP TO FILE' AND filing_status <> 'AWAITING CASE NUMBER' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'SEND TO CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS'";
		}
	}
	$r10=@mysql_query($q10) or die ("Query: $q10<br>".mysql_error());
	while ($d10=mysql_fetch_array($r10, MYSQL_ASSOC)){
	//echo $d10[packet_id].'<br>';
	$packet=$d10[packet_id];
	makeAffidavit($packet,"ALL",$level,$user_id);
	}
}elseif($_GET[sendDate]){
	//select all mailing affidavits for specific date (for "MAIL ONLY" files)
	$q="select packet_id from ps_packets where service_status = 'MAIL ONLY' and closeOut='".$_GET[sendDate]."' order by packet_id";
	$r=@mysql_query($q);
	while($d=mysql_fetch_array($r, MYSQL_ASSOC)){$i++;
		makeAffidavit($d[packet_id],"ALL",$level,$user_id);
	}
}elseif($_GET[packet] && $_GET[mail]){
	//only display mailing affidavits for packet
	makeAffidavit($_GET[packet],"MAIL",$level,$user_id);
}elseif($_GET[packet] && $_GET[ps]){
	//only display server's affidavits for packet
	makeAffidavit($_GET[packet],"SERVER",$level,$user_id);
}elseif ($_GET[packet] && $_GET[def]){
	//only display specific defendant for packet
	makeAffidavit($_GET[packet],$_GET[def],$level,$user_id);
}elseif($_GET[packet] && !$_GET[def]){
	//else display all
	makeAffidavit($_GET[packet],"ALL",$level,$user_id);
}
error_log("[".date('h:iA n/j/y')."] [".$_COOKIE[psdata][name]."] [".trim($_GET[packet])."] [end ob]  \n", 3, '/logs/fail.log');
?>