<?
include 'functions.php';
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
function county2envelope2($county){
	$county=strtoupper($county);
	if ($county == 'BALTIMORE'){
		$search='BALTIMORE COUNTY';
	}elseif($county == 'PRINCE GEORGES'){
		$search='PRINCE GEORGE';
	}elseif($county == 'ST MARYS'){
		$search='ST. MARY';
	}elseif($county == 'QUEEN ANNES'){
		$search='QUEEN ANNE';
	}else{
		$search=$county;
	}
	$r=@mysql_query("SELECT to1 FROM envelopeImage WHERE to1 LIKE '%$search%' AND addressType='COURT' LIMIT 0,1");
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	return $d[to1];
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

//Affidavit Page Layout:
//PageE=server_ide attempts
//PageD=server_idd attempts
//PageC=server_idc attempts
//PageB=server_idb attempts
//PageA=server_ida attempts
//PageI=server_id attempts (and posting if same address--uses bottom half of PageII)
//PageII=server_id posting (if at different address)
//PageIII=staff mailing
//PagePD=personal delivery page

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
	}else{
		$q1="SELECT * FROM ps_packets WHERE packet_id='$packet' AND affidavit_status='SERVICE CONFIRMED'";
	}
	$r1=@mysql_query($q1) or die(mysql_error());
	$d1=mysql_fetch_array($r1, MYSQL_ASSOC);
	if (strtoupper($d1[affidavit_status]) == "SERVICE CONFIRMED" || $overRide == '1'){
		$dim='';
	}else{
		$dim="class='dim'";
	}
	$amended='';
	if ($d1[amendedAff] == "checked"){
		$amended="Amended ";
	}
	$lossMit='';
	if ($d1[lossMit] != "N/A - OLD L" && $d1[lossMit] != ''){
		$lossMit=", accompanied by a pre-printed envelope addressed to the attorney handling the foreclosure action";
		if ($d1[lossMit] == "FINAL"){
			$toCounty=county2envelope2($d1[circuit_court]);
			$lossMit .= ", and another pre-printed envelope addressed to '$toCounty'";
		}
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
				$iID2["$def"] = $d4[serverID];
			}elseif($d1[server_ida] && $d4[serverID] == $d1[server_ida]){
				$attemptsa .= $d4[action_str];
				$iIDa = $d4[serverID];
				$iIDa2["$def"] = $d4[serverID];
			}elseif($d1[server_idb] && $d4[serverID] == $d1[server_idb]){
				$attemptsb .= $d4[action_str];
				$iIDb = $d4[serverID];
				$iIDb2["$def"] = $d4[serverID];
			}elseif($d1[server_idc] && $d4[serverID] == $d1[server_idc]){
				$attemptsc .= $d4[action_str];
				$iIDc = $d4[serverID];
				$iIDc2["$def"] = $d4[serverID];
			}elseif($d1[server_idd] && $d4[serverID] == $d1[server_idd]){
				$attemptsd .= $d4[action_str];
				$iIDd = $d4[serverID];
				$iIDd2["$def"] = $d4[serverID];
			}elseif($d1[server_ide] && $d4[serverID] == $d1[server_ide]){
				$attemptse .= $d4[action_str];
				$iIDe = $d4[serverID];
				$iIDe2["$def"] = $d4[serverID];
			}
		}

		$q4="SELECT * from ps_history where packet_id = '$packet' AND defendant_id = '$def' and wizard='SECOND EFFORT' and onAffidavit='checked' order by sort_value";
		$r4=@mysql_query($q4) or die(mysql_error());
		while ($d4=mysql_fetch_array($r4, MYSQL_ASSOC)){
			if ($d4[serverID]==$d1[server_id]){
				$attempts .= $d4[action_str];
				$iID = $d4[serverID];
				$iID2["$def"] = $d4[serverID];
			}elseif($d1[server_ida] && $d4[serverID]==$d1[server_ida]){
				$attemptsa .= $d4[action_str];
				$iIDa = $d4[serverID];
				$iIDa2["$def"] = $d4[serverID];
			}elseif($d1[server_idb] && $d4[serverID]==$d1[server_idb]){
				$attemptsb .= $d4[action_str];
				$iIDb = $d4[serverID];
				$iIDb2["$def"] = $d4[serverID];
			}elseif($d1[server_idc] && $d4[serverID]==$d1[server_idc]){
				$attemptsc .= $d4[action_str];
				$iIDc = $d4[serverID];
				$iIDc2["$def"] = $d4[serverID];
			}elseif($d1[server_idd] && $d4[serverID]==$d1[server_idd]){
				$attemptsd .= $d4[action_str];
				$iIDd = $d4[serverID];
				$iIDd2["$def"] = $d4[serverID];
			}elseif($d1[server_ide] && $d4[serverID]==$d1[server_ide]){
				$attemptse .= $d4[action_str];
				$iIDe = $d4[serverID];
				$iIDe2["$def"] = $d4[serverID];
			}
		}

		$q4="SELECT * from ps_history where packet_id = '$packet' AND defendant_id = '$def' and action_type = 'Posted Papers' and onAffidavit='checked'";
		$r4=@mysql_query($q4) or die(mysql_error());
		while ($d4=mysql_fetch_array($r4, MYSQL_ASSOC)){
			$posting .= $d4[action_str];
			$iiID = $d4[serverID];
			$iiID2["$def"] = $d4[serverID];
		}

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
		// hard code header 
		$header="<tr cellpadding='0' cellspacing='0'><td cellpadding='0' cellspacing='0' colspan='2' align='center' style='font-variant:small-caps; padding-top:0px; padding-bottom:0px;'><font size='5'>State of Maryland</font><br>
		<font size='4'>Circuit Court for ".$court."</font></td></tr>
			<tr cellpadding='0' cellspacing='0'><td cellpadding='0' cellspacing='0' class='a' width='75%' style='padding-top:0px; padding-bottom:0px;'><font size='1'>".$plaintiff."<br /><small>_____________________<br /><em>Plaintiff</em></small><br /><br />v.<br /><br />";
		if ($d1[onAffidavit1]=='checked'){$header .= strtoupper($d1['name1']).'<br />';}
		if ($d1['name2'] && $d1[onAffidavit2]=='checked'){$header .= strtoupper($d1['name2']).'<br />';}
		if ($d1['name3'] && $d1[onAffidavit3]=='checked'){$header .= strtoupper($d1['name3']).'<br />';}
		if ($d1['name4'] && $d1[onAffidavit4]=='checked'){$header .= strtoupper($d1['name4']).'<br />';}
		if ($d1['name5'] && $d1[onAffidavit5]=='checked'){$header .= strtoupper($d1['name5']).'<br />';}
		if ($d1['name6'] && $d1[onAffidavit6]=='checked'){$header .= strtoupper($d1['name6']).'<br />';}
		$header .=strtoupper($d1['address1']).'<br />';
		$header .=strtoupper($d1['city1']).', '.strtoupper($d1['state1']).' '.$d1['zip1'].'<br />';
		$header .= "<small>_____________________<br /><em>Defendant</em></small></font></td>
			<td cellpadding='0' cellspacing='0' align='right' width='25%' valign='top' style='padding-left:200px; padding-top:0px; padding-bottom:0px;' nowrap='nowrap'><div style='border:solid 1px #666666; width:300px;'><center><font size='4'>Case Number<br />&nbsp;".str_replace(0,'&Oslash;',$d1[case_no])."</font></center></div><IMG SRC='http://staff.mdwestserve.com/barcode.php?barcode=[CORD]&width=300&height=40' width='300' height='40'><center>File Number: $d1[client_file]<br>[PAGE]</center></td></tr>";
		
		//hard code footer
		$footer="<tr cellpadding='0' cellspacing='0'>
						<td cellpadding='0' cellspacing='0' style='padding-top:0px; padding-bottom:0px;' valign='top'><font size='2'>____________________________________<br />Notary Public<br><br><br>SEAL</font></td>
						<td cellpadding='0' cellspacing='0' style='padding-top:0px; padding-bottom:0px;' valign='top'><font size='2'>________________________<u>DATE:</u>________<br>[INFO]</font></td> 
					</tr>";
		
		if ($type == "non"){
			$article = "14-209(b)";
			$result = "MAILING AND POSTING";
			if ($attempts != ''){
				$history = "<u><font size='2'>Describe with particularity the good faith efforts to serve the mortgagor or grantor, ".$d1["name$def"].",  by personal delivery:<br>I, [SERVERNAME], made the following efforts:</font></u>
				<b>".$attempts."</b>";
			}elseif($attemptsa != ''){
				$history = "<u><font-size='2'>Describe with particularity the good faith efforts to serve the mortgagor or grantor, ".$d1["name$def"].",  by personal delivery:<br>I, [SERVERNAME], made the following efforts:</font></u>
				<b>".$attemptsa."</b>";
				$iID=$iIDa;
			}
			$history2 = "<u><font-size='2'>Include the date of the posting and a description of the location of the posting on the property:<br>I, [SERVERNAME], posted the Papers to the property in the following manner:</font></u><b>".$posting."</b>";
			if ($mailing == ''){
				$history3 = "<div class='dim' style='font-weight:300'><u><font size='2'>State the date on which the required papers were mailed by first-class and certified mail, return receipt requested, and the name and address of the addressee:</font></u>
					<center><font size='36 px'>AWAITING MAILING<br>DO NOT FILE</font></center></div>";
				$noMail = 1;
			}else{
				if ($crr != ''){
					$history3 = "<u><font-size='2'>State the date on which the required papers were mailed by first-class and certified mail, return receipt requested, and the name and address of the addressee:</font></u>
					<b>".$mailing."</b>";
				}elseif(($iiID == $d1[server_id]) || ($first != '' && $crr == '')){
					$history3 = "<u><font-size='2'>State the date on which the required papers were mailed by first-class and the name and address of the addressee:</font></u>
					<b>".$mailing."</b>";
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
					$r5=@mysql_query("SELECT * from ps_signatory where serverID='$iIDe' AND packetID='$packet'");
					$d5=mysql_fetch_array($r5, MYSQL_ASSOC);
					$serverID=$hiID;
					$serverName=$d5[name];
					$info="$d5[name]<br>$d5[address]<br>$d5[city], $d5[state] $d5[zip]<br>$d5[phone]<br>$_SERVER[REMOTE_ADDR]";
					if (!$d5){
						$r3=@mysql_query("SELECT * from ps_users where id = '$iIDe'") or die(mysql_error());
						$d3=mysql_fetch_array($r3, MYSQL_ASSOC);
						$serverName=$d3[name];
						$info="$d3[name]<br>$d3[address]<br>$d3[city], $d3[state] $d3[zip]<br>$d3[phone]<br>$_SERVER[REMOTE_ADDR]";
					}
					$historye = "";
					$historye = "<u>Describe with particularity the good faith efforts to serve the mortgagor or grantor, ".$d1["name$def"].",  by personal delivery:<br>I, $serverName, made the following efforts:</u>
							<b>".$attemptse."</b>";
					$cord=$d1[packet_id]."-".$def."-".$serverID."%";
					?>
						<table style='border-collapse:collapse; page-break-after:always; padding:0px;' cellpadding="0" cellspacing="0" width="780px" align="right" bgcolor="#FFFFFF" <?=$dim?>>
						<?=str_replace('[CORD]',$cord,$header); ?>
						<tr cellpadding='0' cellspacing='0'>
							<td cellpadding='0' cellspacing='0' style="padding-top:0px; padding-bottom:0px;" colspan="2" align="left" valign="top"><center><u><b><?=$amended?>Affidavit of Attempted Delivery<? if ($iID && !$iIDa && !$iIDb && !$iIDc && !$iIDd && !$iIDe){ echo " and Posting";}?></b></u></center>
							<center font size='4'><b><?=$result?></font></b></center>
							<font size='2'>Pursuant to Maryland Real Property Article 7-105.1 and Maryland Rules of Procedure <?=$article?> <?=$result?> a copy of the <?=$addlDocs?> and all other papers filed with it in the above-captioned case<?=$lossMit?> (the "Papers"), I, <?=$serverName?>, do hereby affirm that the contents of the following <?=strtolower($amended)?>affidavit are true and correct, based on my personal knowledge:</font><br>
							<div style='padding-left:20px;'><?=stripslashes($historye)?></div>
							<font size='2'>I solemnly affirm under the penalties of perjury that the contents of this <?=strtolower($amended)?>affidavit are true and correct, to the best of my knowledge, information and belief<? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?>, and that I did attempt service as set forth above<? }?><? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?>, and that I served the <?=$addlDocs?> and all other papers filed with it to [PERSON SERVED]<? }?>.<br>
							I, <?=$serverName?>, certify that I am over eighteen years old and not a party to this action<? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?> and that I served [PERSON SERVED], [RELATION TO DEFENDANT]<? }?><? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?> and that I did attempt service as set forth above<? }?>.</font><br /></td>
						</tr>
						<?=str_replace('[INFO]',$info,$footer);?>
					</table>
				<? }
				$pagee["$def"] = ob_get_clean();
				ob_start();
				//5th server
				if ($iIDd){
					$r5=@mysql_query("SELECT * from ps_signatory where serverID='$iIDd' AND packetID='$packet'");
					$d5=mysql_fetch_array($r5, MYSQL_ASSOC);
					$serverID=$iIDd;
					$serverName=$d5[name];
					$info="$d5[name]<br>$d5[address]<br>$d5[city], $d5[state] $d5[zip]<br>$d5[phone]<br>$_SERVER[REMOTE_ADDR]";
					if (!$d5){
						$r3=@mysql_query("SELECT * from ps_users where id = '$iIDd'") or die(mysql_error());
						$d3=mysql_fetch_array($r3, MYSQL_ASSOC);
						$serverName=$d3[name];
						$info="$d3[name]<br>$d3[address]<br>$d3[city], $d3[state] $d3[zip]<br>$d3[phone]<br>$_SERVER[REMOTE_ADDR]";
					}
						$historyd = "";
						$historyd = "<u>Describe with particularity the good faith efforts to serve the mortgagor or grantor, ".$d1["name$def"].",  by personal delivery:<br>I, $serverName, made the following efforts:</u>
								<b>".$attemptsd."</b>";
					$cord=$d1[packet_id]."-".$def."-".$serverID."%"; ?>
					<table style='border-collapse:collapse; page-break-after:always; padding:0px;' cellpadding="0" cellspacing="0" width="780px" cellpadding="0" cellspacing="0" align="right" bgcolor="#FFFFFF" <?=$dim?>>
						<?=str_replace('[CORD]',$cord,$header); ?>
						<tr cellpadding='0' cellspacing='0'>
							<td cellpadding='0' cellspacing='0' style="padding-top:0px; padding-bottom:0px;" colspan="2" align="left" valign="top"><center><u><b><?=$amended?>Affidavit of Attempted Delivery</b></u></center>
							<center><font size='4'><b><?=$result?></b></font></center>
							<font size='2'>Pursuant to Maryland Real Property Article 7-105.1 and Maryland Rules of Procedure <?=$article?> <?=$result?> a copy of the <?=$addlDocs?> and all other papers filed with it in the above-captioned case<?=$lossMit?> (the "Papers"), I, <?=$serverName?>, do hereby affirm that the contents of the following <?=strtolower($amended)?>affidavit are true and correct, based on my personal knowledge:</font><br>
							<div style='padding-left:20px;'><?=stripslashes($historyd)?></div>
							<font size='2'>I solemnly affirm under the penalties of perjury that the contents of this <?=strtolower($amended)?>affidavit are true and correct, to the best of my knowledge, information and belief<? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?>, and that I did attempt service as set forth above<? }?><? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?>, and that I served the <?=$addlDocs?> and all other papers filed with it to [PERSON SERVED]<? }?>.<br>
							I, <?=$serverName?>, certify that I am over eighteen years old and not a party to this action<? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?> and that I served [PERSON SERVED], [RELATION TO DEFENDANT]<? }?><? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?> and that I did attempt service as set forth above<? }?>.</font><br></td></tr>
						</tr>
						<?=str_replace('[INFO]',$info,$footer);?>
					</table>
				<? } 
				$paged["$def"] = ob_get_clean();
				ob_start();
				//4th server
				if ($iIDc){
					$r5=@mysql_query("SELECT * from ps_signatory where serverID='$iIDc' AND packetID='$packet'");
					$d5=mysql_fetch_array($r5, MYSQL_ASSOC);
					$serverID=$iIDc;
					$serverName=$d5[name];
					$info="$d5[name]<br>$d5[address]<br>$d5[city], $d5[state] $d5[zip]<br>$d5[phone]<br>$_SERVER[REMOTE_ADDR]";
					if (!$d5){
						$r3=@mysql_query("SELECT * from ps_users where id = '$iIDc'") or die(mysql_error());
						$d3=mysql_fetch_array($r3, MYSQL_ASSOC);
						$serverName=$d3[name];
						$info="$d3[name]<br>$d3[address]<br>$d3[city], $d3[state] $d3[zip]<br>$d3[phone]<br>$_SERVER[REMOTE_ADDR]";
					}
						$historyc = "";
						$historyc = "<u>Describe with particularity the good faith efforts to serve the mortgagor or grantor, ".$d1["name$def"].",  by personal delivery:<br>I, $serverName, made the following efforts:</u>
								<b>".$attemptsc."</b>";
					$cord=$d1[packet_id]."-".$def."-".$serverID."%"; ?>
					<table style='border-collapse:collapse; page-break-after:always; padding:0px;' cellpadding="0" cellspacing="0" width="780px" align="right" bgcolor="#FFFFFF" <?=$dim?>>
						<?=str_replace('[CORD]',$cord,$header); ?>
						<tr cellpadding='0' cellspacing='0'>
							<td cellpadding='0' cellspacing='0' style="padding-top:0px; padding-bottom:0px;" colspan="2" align="left" valign="top"><center><b><u><?=$amended?>Affidavit of Attempted Delivery</u></b></center>
							<center><font size='4'><b><?=$result?></b></font></center>
							<font size='2'>Pursuant to Maryland Real Property Article 7-105.1 and Maryland Rules of Procedure <?=$article?> <?=$result?> a copy of the <?=$addlDocs?> and all other papers filed with it in the above-captioned case<?=$lossMit?> (the "Papers"), I, <?=$serverName?>, do hereby affirm that the contents of the following <?=strtolower($amended)?>affidavit are true and correct, based on my personal knowledge:</font><br>
							<div style='padding-left:20px;'><?=stripslashes($historyc)?></div>
							<font size='2'>I solemnly affirm under the penalties of perjury that the contents of this <?=strtolower($amended)?>affidavit are true and correct, to the best of my knowledge, information and belief<? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?>, and that I did attempt service as set forth above<? }?><? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?>, and that I served the <?=$addlDocs?> and all other papers filed with it to [PERSON SERVED]<? }?>.<br>
							I, <?=$serverName?>, certify that I am over eighteen years old and not a party to this action<? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?> and that I served [PERSON SERVED], [RELATION TO DEFENDANT]<? }?><? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?> and that I did attempt service as set forth above<? }?>.</font><br></td>
						</tr>
						<?=str_replace('[INFO]',$info,$footer);?>
					</table>
				<? 
				}
				$pagec["$def"] = ob_get_clean();
				ob_start();
				//3rd server
				if ($iIDb){
					$r5=@mysql_query("SELECT * from ps_signatory where serverID='$iIDb' AND packetID='$packet'");
					$d5=mysql_fetch_array($r5, MYSQL_ASSOC);
					$serverID=$iIDb;
					$serverName=$d5[name];
					$info="$d5[name]<br>$d5[address]<br>$d5[city], $d5[state] $d5[zip]<br>$d5[phone]<br>$_SERVER[REMOTE_ADDR]";
					if (!$d5){
						$r3=@mysql_query("SELECT * from ps_users where id = '$iIDb'") or die(mysql_error());
						$d3=mysql_fetch_array($r3, MYSQL_ASSOC);
						$serverName=$d3[name];
						$info="$d3[name]<br>$d3[address]<br>$d3[city], $d3[state] $d3[zip]<br>$d3[phone]<br>$_SERVER[REMOTE_ADDR]";
					}
					$historyb = "";
					$historyb = "<u>Describe with particularity the good faith efforts to serve the mortgagor or grantor, ".$d1["name$def"].",  by personal delivery:<br>I, $serverName, made the following efforts:</u>
							<b>".$attemptsb."</b>";
					$cord=$d1[packet_id]."-".$def."-".$serverID."%"; ?>
					<table style='border-collapse:collapse; page-break-after:always; padding:0px;' cellpadding="0" cellspacing="0" width="780px" align="right" bgcolor="#FFFFFF" <?=$dim?>>
						<?=str_replace('[CORD]',$cord,$header); ?>
						<tr cellpadding='0' cellspacing='0'>
							<td cellpadding='0' cellspacing='0' style="padding-top:0px; padding-bottom:0px;" colspan="2" align="left" valign="top"><center><b><u><?=$amended?>Affidavit of Attempted Delivery</u></b></center>
							<center><font size='4'><b><?=$result?></b></font></center>
							<font size='2'>Pursuant to Maryland Real Property Article 7-105.1 and Maryland Rules of Procedure <?=$article?> <?=$result?> a copy of the <?=$addlDocs?> and all other papers filed with it in the above-captioned case<?=$lossMit?> (the "Papers"), I, <?=$serverName?>, do hereby affirm that the contents of the following <?=strtolower($amended)?>affidavit are true and correct, based on my personal knowledge:</font><br>
							<div style='padding-left:20px;'><?=stripslashes($historyb)?></div>
							<font size='2'>I solemnly affirm under the penalties of perjury that the contents of this <?=strtolower($amended)?>affidavit are true and correct, to the best of my knowledge, information and belief<? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?>, and that I did attempt service as set forth above<? }?><? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?>, and that I served the <?=$addlDocs?> and all other papers filed with it to [PERSON SERVED]<? }?>.<br>
							I, <?=$serverName?>, certify that I am over eighteen years old and not a party to this action<? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?> and that I served [PERSON SERVED], [RELATION TO DEFENDANT]<? }?><? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?> and that I did attempt service as set forth above<? }?>.</font><br></td>
						</tr>
						<?=str_replace('[INFO]',$info,$footer);?>
					</table>
				<? 
				}
				$pageb["$def"] = ob_get_clean();
				ob_start();
				//2nd server
				if ($iIDa){
					$r5=@mysql_query("SELECT * from ps_signatory where serverID='$iIDa' AND packetID='$packet'");
					$d5=mysql_fetch_array($r5, MYSQL_ASSOC);
					$serverID=$iIDa;
					$serverName=$d5[name];
					$info="$d5[name]<br>$d5[address]<br>$d5[city], $d5[state] $d5[zip]<br>$d5[phone]<br>$_SERVER[REMOTE_ADDR]";
					if (!$d5){
						$r3=@mysql_query("SELECT * from ps_users where id = '$iIDa'") or die(mysql_error());
						$d3=mysql_fetch_array($r3, MYSQL_ASSOC);
						$serverName=$d3[name];
						$info="$d3[name]<br>$d3[address]<br>$d3[city], $d3[state] $d3[zip]<br>$d3[phone]<br>$_SERVER[REMOTE_ADDR]";
					}
						$historya = "";
						$historya = "<u>Describe with particularity the good faith efforts to serve the mortgagor or grantor, ".$d1["name$def"].",  by personal delivery:<br>I, $serverName, made the following efforts:</u>
								<b>".$attemptsa."</b>";
					$cord=$d1[packet_id]."-".$def."-".$serverID."%"; ?>
					<table style='border-collapse:collapse; page-break-after:always; padding:0px;' cellpadding="0" cellspacing="0" width="780px" align="right" bgcolor="#FFFFFF" <?=$dim?>>
						<?=str_replace('[CORD]',$cord,$header); ?>
						<tr cellpadding='0' cellspacing='0'>
							<td cellpadding='0' cellspacing='0' style="padding-top:0px; padding-bottom:0px;" colspan="2" align="left" valign="top"><center><b><u><?=$amended?>Affidavit of Attempted Delivery</u></b></center>
							<center><font size='4'><b><?=$result?></b></font></center>
							<font size='2'>Pursuant to Maryland Real Property Article 7-105.1 and Maryland Rules of Procedure <?=$article?> <?=$result?> a copy of the <?=$addlDocs?> and all other papers filed with it in the above-captioned case<?=$lossMit?> (the "Papers"), I, <?=$serverName?>, do hereby affirm that the contents of the following <?=strtolower($amended)?>affidavit are true and correct, based on my personal knowledge:</font><br>
							<div style='padding-left:20px;'><?=stripslashes($historya)?></div>
							<font size='2'>I solemnly affirm under the penalties of perjury that the contents of this <?=strtolower($amended)?>affidavit are true and correct, to the best of my knowledge, information and belief<? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?>, and that I did attempt service as set forth above<? }?><? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?>, and that I served the <?=$addlDocs?> and all other papers filed with it to [PERSON SERVED]<? }?>.<br>
							I, <?=$serverName?>, certify that I am over eighteen years old and not a party to this action<? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?> and that I served [PERSON SERVED], [RELATION TO DEFENDANT]<? }?><? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?> and that I did attempt service as set forth above<? }?>.</font><br /></td>
						</tr>
						<?=str_replace('[INFO]',$info,$footer);?>
					</table>
				<? 
				} 
				$pagea["$def"] = ob_get_clean();
				ob_start();
				//1st server, or servera if non-Burson
				//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
				if ($iID != $iIDa){
					$r5=@mysql_query("SELECT * from ps_signatory where serverID='$iID' AND packetID='$packet'");
					$d5=mysql_fetch_array($r5, MYSQL_ASSOC);
					$serverID=$iID;
					$serverName=$d5[name];
					$info="$d5[name]<br>$d5[address]<br>$d5[city], $d5[state] $d5[zip]<br>$d5[phone]<br>$_SERVER[REMOTE_ADDR]";
					if (!$d5){
						$r3=@mysql_query("SELECT * from ps_users where id = '$iID'") or die(mysql_error());
						$d3=mysql_fetch_array($r3, MYSQL_ASSOC);
						$serverName=$d3[name];
						$info="$d3[name]<br>$d3[address]<br>$d3[city], $d3[state] $d3[zip]<br>$d3[phone]<br>$_SERVER[REMOTE_ADDR]";
					}
					$cord=$d1[packet_id]."-".$def."-".$serverID."%"; ?>
					<table style='border-collapse:collapse; page-break-after:always; padding:0px;' cellpadding="0" cellspacing="0" width="780px" align="right" bgcolor="#FFFFFF" <?=$dim?>>
						<?=str_replace('[CORD]',$cord,$header);?>
						<tr cellpadding='0' cellspacing='0'>
							<td colspan="2" align="left" style="font-weight:bold; text-decoration:underline" height="30px" valign="top"><center><b><u><?=$amended?>Affidavit of Attempted Delivery<? if ($iID==$iiID){ echo " and Posting";} ?></u></b></center>
							<center><font size='4'><b><?=$result?></b></font></center>
							<font size='2'>Pursuant to Maryland Real Property Article 7-105.1 and Maryland Rules of Procedure <?=$article?> <?=$result?> a copy of the <?=$addlDocs?> and all other papers filed with it in the above-captioned case<?=$lossMit?> (the "Papers"), I, <?=$serverName?>, do hereby affirm that the contents of the following <?=strtolower($amended)?>affidavit are true and correct, based on my personal knowledge:</font><br>
							<div style='padding-left:20px;'><?=stripslashes(str_replace('[SERVERNAME]',$serverName,$history))?></div>
					<? if ($iID != $iiID){ ?>        
						<font size='2'>I solemnly affirm under the penalties of perjury that the contents of this <?=strtolower($amended)?>affidavit are true and correct to the best of my knowledge, information and belief<? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?>, and that I did attempt service as set forth above<? }?><? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?>, and that I served the <?=$addlDocs?> and all other papers filed with it to [PERSON SERVED]<? }?>.<br>
						I, <?=$serverName?>, certify that I am over eighteen years old and not a party to this action<? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?> and that I served [PERSON SERVED], [RELATION TO DEFENDANT]<? }?><? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?> and that I did attempt service as set forth above<? }?>.</font><br /></td>
						</tr>
						<?=str_replace('[INFO]',$info,$footer);?>
					</table>
					<? }
				 }
				$pageI["$def"] = ob_get_clean();
				ob_start();
				 //Multiple servers' attempts end here
				if($posting){
					if ($iID != $iiID){
						$r5=@mysql_query("SELECT * from ps_signatory where serverID='$iiID' AND packetID='$packet'");
						$d5=mysql_fetch_array($r5, MYSQL_ASSOC);
						$serverID=$iiID;
						$serverName=$d5[name];
						$info="$d5[name]<br>$d5[address]<br>$d5[city], $d5[state] $d5[zip]<br>$d5[phone]<br>$_SERVER[REMOTE_ADDR]";
						if (!$d5){
							$r3=@mysql_query("SELECT * from ps_users where id = '$iiID'") or die(mysql_error());
							$d3=mysql_fetch_array($r3, MYSQL_ASSOC);
							$serverName=$d3[name];
							$info="$d3[name]<br>$d3[address]<br>$d3[city], $d3[state] $d3[zip]<br>$d3[phone]<br>$_SERVER[REMOTE_ADDR]";
						}
						$cord=$d1[packet_id]."-".$def."-".$serverID."%"; ?>
						<table style='border-collapse:collapse; page-break-after:always; padding:0px;' cellpadding="0" cellspacing="0" width="780px" align="right" bgcolor="#FFFFFF" <?=$dim?>>
							<?=str_replace('[CORD]',$cord,$header);?>
							<tr cellpadding='0' cellspacing='0'>
								<td cellpadding='0' cellspacing='0' style="padding-top:0px; padding-bottom:0px;" colspan="2" align="left" valign="top"><center><b><u><?=$amended?>Affidavit of Posting</u></b></center>
								<center><font size='4'><b><?=$result?></b></font></center>
								<font size='2'>Pursuant to Maryland Real Property Article 7-105.1 and Maryland Rules of Procedure <?=$article?> <?=$result?> a copy of the <?=$addlDocs?> and all other papers filed with it in the above-captioned case<?=$lossMit?> (the "Papers"), I, <?=$serverName?>, do hereby affirm that the contents of the following <?=strtolower($amended)?>affidavit are true and correct, based on my personal knowledge:</font><br>
						<? } ?>
						<div style='padding-left:20px;'><?=stripslashes(str_replace('[SERVERNAME]',$serverName,$history2))?></div>
						<font size='2'>I solemnly affirm under the penalties of perjury that the contents of this <?=strtolower($amended)?>affidavit are true and correct to the best of my knowledge, information and belief.<br>I, <?=$serverName?>, certify that I am over eighteen years old and not a party to this action.</font><br /></td>
						</tr>
						<?=str_replace('[INFO]',$info,$footer);?>
					</table>
				<? } 
				$pageII["$def"] = ob_get_clean();
				$postingID["$def"] = $iiID;
				ob_start();
				if($iiiID){
					$r5=@mysql_query("SELECT * from ps_signatory where serverID='$iiiID' AND packetID='$packet'");
					$d5=mysql_fetch_array($r5, MYSQL_ASSOC);
					$serverID=$iiiID;
					$serverName=$d5[name];
					$info="$d5[name]<br>$d5[address]<br>$d5[city], $d5[state] $d5[zip]<br>$d5[phone]<br>$_SERVER[REMOTE_ADDR]";
					if (!$d5){
						$r3=@mysql_query("SELECT * from ps_users where id = '$iiiID'") or die(mysql_error());
						$d3=mysql_fetch_array($r3, MYSQL_ASSOC);
						$serverName=$d3[name];
						$info="$d3[name]<br>$d3[address]<br>$d3[city], $d3[state] $d3[zip]<br>$d3[phone]<br>$_SERVER[REMOTE_ADDR]";
					}
					$cord=$d1[packet_id]."-".$def."-".$serverID."%"; ?>
					<table style='border-collapse:collapse; page-break-after:always; padding:0px;' cellpadding="0" cellspacing="0" width="780px" align="right" bgcolor="#FFFFFF" <?=$dim?>>
						<?=str_replace('[CORD]',$cord,$header); ?>
						<tr cellpadding='0' cellspacing='0'>
							<td cellpadding='0' cellspacing='0' style="padding-top:0px; padding-bottom:0px;" colspan="2" align="left" valign="top"><center><b><u><?=$amended?>Affidavit of Mailing</u></b></center>
							<center><font size='4'><b><?=$result?></b></font></center>
							<font size='2'>Pursuant to Maryland Real Property Article 7-105.1 and Maryland Rules of Procedure <?=$article?> <?=$result?> a copy of the <?=$addlDocs?> and all other papers filed with it in the above-captioned case<?=$lossMit?> (the "Papers"), I, <?=$serverName?>, do hereby affirm that the contents of the following <?=strtolower($amended)?>affidavit are true and correct, based on my personal knowledge:</font><br>
							<div style='padding-left:20px;'><?=stripslashes($history3)?></div>    
						<font size='2' <? if($noMail == 1 && !$_GET[mail]){ echo 'class="dim"';}?>>I solemnly affirm under the penalties of perjury that the contents of this <?=strtolower($amended)?>affidavit are true and correct to the best of my knowledge, information and belief.  And that I mailed the above papers under section 14-209(b) to <?=strtoupper($d1["name$def"])?>.<br>
						I, <?=$serverName?>, certify that I am over eighteen years old and not a party to this action.</font><br /></td>
						</tr>
						<?=str_replace('[INFO]',$info,$footer);?>
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
				$r5=@mysql_query("SELECT * from ps_signatory where serverID='$deliveryID' AND packetID='$packet'");
				$d5=mysql_fetch_array($r5, MYSQL_ASSOC);
				$serverID=$deliveryID;
				$serverName=$d5[name];
				$info="$d5[name]<br>$d5[address]<br>$d5[city], $d5[state] $d5[zip]<br>$d5[phone]<br>$_SERVER[REMOTE_ADDR]";
				if (!$d5){
					$r3=@mysql_query("SELECT * from ps_users where id = '$deliveryID'") or die(mysql_error());
					$d3=mysql_fetch_array($r3, MYSQL_ASSOC);
					$serverName=$d3[name];
					$info="$d3[name]<br>$d3[address]<br>$d3[city], $d3[state] $d3[zip]<br>$d3[phone]<br>$_SERVER[REMOTE_ADDR]";
				}
				$cord=$d1[packet_id]."-".$def."-".$serverID."%";
				if ($residentDesc){
					$desc=strtoupper(str_replace('CO-A BORROWER IN THE ABOVE-REFERENCED CASE', 'A BORROWER IN THE ABOVE-REFERENCED CASE', str_replace('BORROWER','A BORROWER IN THE ABOVE-REFERENCED CASE', attorneyCustomLang($d1[attorneys_id],strtoupper($residentDesc)))));
				} ?>
				<table style='border-collapse:collapse; border-collapse:collapse; page-break-after:always; padding:0px;' cellpadding="0" cellspacing="0" width="780px" align="right" bgcolor="#FFFFFF" <?=$dim?>>
					<?=str_replace('[CORD]',$cord,$header); ?>
					<tr cellpadding='0' cellspacing='0'>
						<td cellpadding='0' cellspacing='0' style="padding-top:0px; padding-bottom:0px;" colspan="2" align="left" valign="top"><center><b><u><?=$amended?>Affidavit of Personal Delivery</u></b></center>
						<center><font size='3'><b><?=$result?></b></font></center>
						<font size='2'>Pursuant to Maryland Real Property Article 7-105.1 and Maryland Rules of Procedure <?=$article?> <?=$result?> a copy of the <?=$addlDocs?> and all other papers filed with it in the above-captioned case<?=$lossMit?> (the "Papers"), I, <?=$serverName?>, do hereby affirm that the contents of the following <?=strtolower($amended)?>affidavit are true and correct, based on my personal knowledge:</font><br>
						<div style='padding-left:20px;'><font size='3'><b><?=stripslashes($delivery)?></b></font></div>
						<font size='2'>I solemnly affirm under the penalties of perjury that the contents of <? if ($type == 'non'){ ?>section (i) of <? }?>this <?=strtolower($amended)?>affidavit are true and correct to the best of my knowledge, information and belief<? if (($type == 'pd' && $nondef == '1') || ($type == 'pd' && $d1[packet_id] >= "10000") || strtotime($d1[reopenDate]) >= strtotime('2010-05-01')){?>, and that I served<? if (($type == 'pd' && $nondef == '1') && (strpos($delivery,"USUAL PLACE OF ABODE") || strpos($delivery,"RESIDENTIAL PROPERTY"))){ ?> at the usual place of abode<? } ?> the <?=$addlDocs?> and other papers to <? if ($resident){ echo strtoupper($resident);}else{ echo '[PERSON SERVED]';}?>, <? if ($residentDesc){echo $desc;}else{ echo '[RELATION TO DEFENDANT]';}?><? if ($serveAddress){ echo ', at '.$serveAddress;}?><? }elseif($type == 'pd' && $nondef != '1'){?>, and that I served the <?=$addlDocs?> and other papers to <?=strtoupper($d1["name$def"])?><? if ($serveAddress){ echo ', at '.strtoupper($serveAddress);}?><? } ?>.<br>
						I, <?=$serverName?>, certify that I am over eighteen years old and not a party to this action.</font><br /></td>
					</tr>
					<?=str_replace('[INFO]',$info,$footer);?>
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
                error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Affidavits for OTD$packet, DEF: $count2, Page E ID [$iIDe] \n",3,"/logs/debug.log");
            }else{
error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Affidavits for OTD$packet, DEF: $count2, Page E ID [$iIDe]-NO DISPLAY: defendant $defendant | level $level | user_id $user_id \n",3,"/logs/debug.log");
}
        }else{
error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Affidavits for OTD$packet, DEF: $count2, Page E EMPTY \n",3,"/logs/debug.log");
}
        if ($paged["$count2"] != ''){
            $currentCounter++;
            if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($level=='Operations' || $iIDd==$user_id) && ($defendant != "MAIL")){
                echo str_replace("[PAGE]","Set 1 (Affidavit $currentCounter of $totalPages)",$paged["$count2"]);
                error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Affidavits for OTD$packet, DEF: $count2, Page D ID [$iIDd] \n",3,"/logs/debug.log");
            }else{
error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Affidavits for OTD$packet, DEF: $count2, Page D ID [$iIDd]-NO DISPLAY: defendant $defendant | level $level | user_id $user_id \n",3,"/logs/debug.log");
}
        }else{
error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Affidavits for OTD$packet, DEF: $count2, Page D EMPTY \n",3,"/logs/debug.log");
}
        if ($pagec["$count2"] != ''){
            $currentCounter++;
            if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($level=='Operations' || $iIDc==$user_id) && ($defendant != "MAIL")){
                echo str_replace("[PAGE]","Set 1 (Affidavit $currentCounter of $totalPages)",$pagec["$count2"]);
                error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Affidavits for OTD$packet, DEF: $count2, Page C ID [$iIDc] \n",3,"/logs/debug.log");
            }else{
error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Affidavits for OTD$packet, DEF: $count2, Page C ID [$iIDc]-NO DISPLAY: defendant $defendant | level $level | user_id $user_id \n",3,"/logs/debug.log");
}
        }else{
error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Affidavits for OTD$packet, DEF: $count2, Page C EMPTY \n",3,"/logs/debug.log");
}
        if ($pageb["$count2"] != ''){
            $currentCounter++;
            if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($level=='Operations' || $iIDb==$user_id) && ($defendant != "MAIL")){
                echo str_replace("[PAGE]","Set 1 (Affidavit $currentCounter of $totalPages)",$pageb["$count2"]);
                error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Affidavits for OTD$packet, DEF: $count2, Page B ID [$iIDb] \n",3,"/logs/debug.log");
            }if($iIDb == ''){
				error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Affidavits for OTD$packet, DEF: $count2, iIDb EMPTY \n",3,"/logs/debug.log");
				$iIDb=$iIDb2["$count2"];
				if($iIDb == ''){
					$iIDb=$d1[server_idb];
				}
				if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($level=='Operations' || $iIDb==$user_id) && ($defendant != "MAIL")){
					echo str_replace("[PAGE]","Set 1 (Affidavit $currentCounter of $totalPages)",$pageb["$count2"]);
					error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Affidavits for OTD$packet, DEF: $count2, Page B ID [$iIDb] \n",3,"/logs/debug.log");
				}else{
					error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Affidavits for OTD$packet, DEF: $count2, Page B ID [$iIDb]-NO DISPLAY: defendant $defendant | level $level | user_id $user_id \n",3,"/logs/debug.log");
				}

}
        }else{
error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Affidavits for OTD$packet, DEF: $count2, Page B EMPTY \n",3,"/logs/debug.log");
}
        if ($pagea["$count2"] != ''){
            $currentCounter++;
            if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($level=='Operations' || $iIDa==$user_id) && ($defendant != "MAIL")){
                echo str_replace("[PAGE]","Set 1 (Affidavit $currentCounter of $totalPages)",$pagea["$count2"]);
                error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Affidavits for OTD$packet, DEF: $count2, Page A ID [$iIDa] \n",3,"/logs/debug.log");
            }elseif($iIDa == ''){
				error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Affidavits for OTD$packet, DEF: $count2, iIDa EMPTY \n",3,"/logs/debug.log");
				$iIDa=$iIDa2["$count2"];
				if($iIDa == ''){
					$iIDa=$d1[server_ida];
				}
				if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($level=='Operations' || $iIDa==$user_id) && ($defendant != "MAIL")){
					echo str_replace("[PAGE]","Set 1 (Affidavit $currentCounter of $totalPages)",$pagea["$count2"]);
					error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Affidavits for OTD$packet, DEF: $count2, Page A ID [$iIDa] \n",3,"/logs/debug.log");
				}else{
					error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Affidavits for OTD$packet, DEF: $count2, Page A ID [$iIDa]-NO DISPLAY: defendant $defendant | level $level | user_id $user_id \n",3,"/logs/debug.log");
				}

}
        }else{
error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Affidavits for OTD$packet, DEF: $count2, Page A EMPTY \n",3,"/logs/debug.log");
}
        if ($pageI["$count2"] != ''){
            $currentCounter++;
            if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($level=='Operations' || $iID==$user_id) && ($defendant != "MAIL")){
                echo str_replace("[PAGE]","Set 1 (Affidavit $currentCounter of $totalPages)",$pageI["$count2"]);
                error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Affidavits for OTD$packet, DEF: $count2, Page I ID [$iID] \n",3,"/logs/debug.log");
			}elseif($iID == ''){
				error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Affidavits for OTD$packet, DEF: $count2, iID EMPTY \n",3,"/logs/debug.log");
				$iID=$iID2["$count2"];
				if($iID == ''){
					$iID=$d1[server_id];
				}
				if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($level=='Operations' || $iID==$user_id) && ($defendant != "MAIL")){
					echo str_replace("[PAGE]","Set 1 (Affidavit $currentCounter of $totalPages)",$pageI["$count2"]);
					error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Affidavits for OTD$packet, DEF: $count2, Page I ID [$iID] \n",3,"/logs/debug.log");
				}else{
					error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Affidavits for OTD$packet, DEF: $count2, Page I ID [$iID]-NO DISPLAY: defendant $defendant | level $level | user_id $user_id \n",3,"/logs/debug.log");
				}
            }else{
error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Affidavits for OTD$packet, DEF: $count2, Page I ID [$iID]-NO DISPLAY: defendant $defendant | level $level | user_id $user_id \n",3,"/logs/debug.log");
			}
		}else{
			error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Affidavits for OTD$packet, DEF: $count2, Page I EMPTY \n",3,"/logs/debug.log");
		}
        if ($pageII["$count2"] != ''){
            //if posting server also made attempt(s), do nothing
            if ($iID==$iiID){
            }else{
            //otherwise increase counter
                $currentCounter++;
				if ($iiID=='' && $iID != ''){
					$iiID=$iiID2["$count2"];
				}
            }
            if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($level=='Operations' || $iiID==$user_id) && ($defendant != "MAIL")){
                echo str_replace("[PAGE]","Set 1 (Affidavit $currentCounter of $totalPages)",$pageII["$count2"]);
                error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Affidavits for OTD$packet, DEF: $count2, Page II ID [$iiID] \n",3,"/logs/debug.log");
            }elseif($iiID == ''){
				error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Affidavits for OTD$packet, DEF: $count2, iiID EMPTY \n",3,"/logs/debug.log");
				$iiID=$iiID2["$count2"];
				if($iiID == ''){
					$iiID=$d1[server_id];
				}
				if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($level=='Operations' || $iiID==$user_id) && ($defendant != "MAIL")){
					echo str_replace("[PAGE]","Set 1 (Affidavit $currentCounter of $totalPages)",$pageII["$count2"]);
					error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Affidavits for OTD$packet, DEF: $count2, Page II ID [$iiID] \n",3,"/logs/debug.log");
				}else{
					error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Affidavits for OTD$packet, DEF: $count2, Page II ID [$iiID]-NO DISPLAY: defendant $defendant | level $level | user_id $user_id \n",3,"/logs/debug.log");
				}
            }else{
error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Affidavits for OTD$packet, DEF: $count2, Page II ID [$iiID]-NO DISPLAY: defendant $defendant | level $level | user_id $user_id \n",3,"/logs/debug.log");
			}
        }else{
error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Affidavits for OTD$packet, DEF: $count2, Page II EMPTY \n",3,"/logs/debug.log");
}
        if ($pageIII["$count2"] != ''){
            $currentCounter++;
            if (($count2==$defendant || $defendant=="ALL" || $defendant=="MAIL") && ($level=='Operations') && ($defendant != "SERVER")){
                echo str_replace("[PAGE]","Set 1 (Affidavit $currentCounter of $totalPages)",$pageIII["$count2"]);
                error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Affidavits for OTD$packet, DEF: $count2, Page III ID [$iiiID] \n",3,"/logs/debug.log");
            }else{
error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Affidavits for OTD$packet, DEF: $count2, Page III ID [$iiiID]-NO DISPLAY: defendant $defendant | level $level | user_id $user_id \n",3,"/logs/debug.log");
}
        }else{
error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Affidavits for OTD$packet, DEF: $count2, Page III EMPTY \n",3,"/logs/debug.log");
}
        if ($pagePD["$count2"] != ''){
            $currentCounter++;
            if (($count2==$defendant || $defendant=="ALL" || $defendant=="SERVER") && ($level=='Operations' || $PDID["$count2"]==$user_id) && ($defendant != "MAIL")){
                echo str_replace("[PAGE]","Set 1 (Affidavit $currentCounter of $totalPages)",$pagePD["$count2"]);
                error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Affidavits for OTD$packet, DEF: $count2, Page PD ID [".$PDID["$count2"]."] \n",3,"/logs/debug.log");
            }else{
error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Affidavits for OTD$packet, DEF: $count2, Page PD ID [".$PDID["$count2"]."]-NO DISPLAY: defendant $defendant | level $level | user_id $user_id \n",3,"/logs/debug.log");
}
        }else{
error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Affidavits for OTD$packet, DEF: $count2, Page PD EMPTY \n",3,"/logs/debug.log");
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
if ($_GET[level]){
	$level=$_GET[level];
}else{
	$level=$_COOKIE[psdata][level];
}
if ($_GET[user_id]){
	$user_id=$_GET[user_id];
}else{
	$user_id=$_COOKIE[psdata][user_id];
}
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
			$q10="SELECT packet_id FROM ps_packets where (server_id='$serveID' OR server_ida='$serveID' OR server_idb='$serveID' OR server_idc='$serveID' OR server_idd='$serveID' OR server_ide='$serveID') AND packet_id >= '$start' AND packet_id <= '$stop' AND process_status <> 'CANCELLED' AND affidavit_status='SERVICE CONFIRMED' AND filing_status <> 'PREP TO FILE' AND filing_status <> 'AWAITING CASE NUMBER' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'SEND TO CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND affidavit_status2 <> 'AWAITING MAILING'";
		}else{
			$q10="SELECT packet_id FROM ps_packets where (server_id='$serveID' OR server_ida='$serveID' OR server_idb='$serveID' OR server_idc='$serveID' OR server_idd='$serveID' OR server_ide='$serveID') AND packet_id >= '$start' AND process_status <> 'CANCELLED' AND affidavit_status='SERVICE CONFIRMED' AND filing_status <> 'PREP TO FILE' AND filing_status <> 'AWAITING CASE NUMBER' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'SEND TO CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND affidavit_status2 <> 'AWAITING MAILING'";
		}
	}else{
		if ($_GET[packet]){
			$q10="SELECT packet_id FROM ps_packets where packet_id='$_GET[packet]'";
		}else{
			$q10="SELECT packet_id FROM ps_packets where (server_id='$serveID' OR server_ida='$serveID' OR server_idb='$serveID' OR server_idc='$serveID' OR server_idd='$serveID' OR server_ide='$serveID') AND process_status <> 'CANCELLED' AND affidavit_status='SERVICE CONFIRMED' AND filing_status <> 'PREP TO FILE' AND filing_status <> 'AWAITING CASE NUMBER' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'SEND TO CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND affidavit_status2 <> 'AWAITING MAILING'";
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
if ($_GET['autoPrint'] == 1){
echo "<script>
if (window.self) window.print();
self.close();
</script>";
}
?>