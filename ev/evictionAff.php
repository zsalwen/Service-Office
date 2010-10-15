<?
include 'functions.php';
@mysql_connect ();
mysql_select_db ('core');
// start output buffering
	$subtract='0';

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
table { page-break-after:always;}
p {border-style:solid; border-width:thick; border-collapse:collapse;}
</style>
<?
/*
if ($_GET[server]){
	$serveID=$_GET[server];
	$def = 0;
}elseif ($_GET[id]){
	$packet = $_GET[id];
	$def = 0;
}*/
function makeAffidavit($p,$defendant){
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
	$court = $d1[circuit_court];
	if (!preg_match("/CITY|D.C./", $court)){
		$court = str_replace('PRINCE GEORGES','PRINCE GEORGE\'S',$court);
		$court = str_replace('QUEEN ANNES','QUEEN ANNE\'S',$court);
		$court = str_replace('ST MARYS','ST MARY\'S',$court);
		$court = ucwords(strtolower($court))." County";
	} else {
		$court = ucwords(strtolower($court));
	}
	// get plaintiff information
	mysql_select_db ('ccdb');
	$q2="SELECT * from attorneys where attorneys_id = '$d1[attorneys_id]'";
	$r2=@mysql_query($q2) or die(mysql_error());
	$d2=mysql_fetch_array($r2, MYSQL_ASSOC);
	if ($d1[altPlaintiff] != ''  && $d1[attorneys_id] != '1'){
		$plaintiff = str_replace('-','<br>',$d1[altPlaintiff]);
	}else{
		$plaintiff = str_replace('-','<br>',$d2[ps_plaintiff]);
	}
	mysql_select_db ('core');
	// get service history
	$q4="SELECT * from ps_history where packet_id = '$packet' and (wizard='FIRST EFFORT' or wizard='INVALID') order by sort_value desc";
	$r4=@mysql_query($q4) or die(mysql_error());
	while ($d4=mysql_fetch_array($r4, MYSQL_ASSOC)){
		if ($d4[serverID] == $d1[server_id]){
			$attempts .= $d4[action_str];
			$iID = $d4[serverID];
		}elseif($d1[server_ida] && $d4[serverID] == $d1[server_ida]){
			$attemptsa .= $d4[action_str];
			$iIDa = $d4[serverID];
		}
	}

	$q4="SELECT * from ps_history where packet_id = '$packet' and wizard='SECOND EFFORT' order by sort_value";
	$r4=@mysql_query($q4) or die(mysql_error());
	while ($d4=mysql_fetch_array($r4, MYSQL_ASSOC)){
		if ($d4[serverID]==$d1[server_id]){
			$attempts .= $d4[action_str];
			$iID = $d4[serverID];
		}
	}

	$q4="SELECT * from ps_history where packet_id = '$packet' and action_type = 'Posted Papers'";
	$r4=@mysql_query($q4) or die(mysql_error());
	$d4=mysql_fetch_array($r4, MYSQL_ASSOC);
	$posting = $d4[action_str];
	$iiID = $d4[serverID];

	$q4="SELECT * from ps_history where packet_id = '$packet' and action_type = 'First Class C.R.R. Mailing'";
	$r4=@mysql_query($q4) or die(mysql_error());
	while ($d4=mysql_fetch_array($r4, MYSQL_ASSOC)){
		$mailing .= $d4[action_str];
		$crr=$d4[action_type];
		$iiiID = $d4[serverID];
	}
	if ($mailing == ''){
		$q4="SELECT * from ps_history where packet_id = '$packet' and action_type = 'First Class Mailing'";
		$r4=@mysql_query($q4) or die(mysql_error());
		while ($d4=mysql_fetch_array($r4, MYSQL_ASSOC)){
			$mailing .= $d4[action_str];
			$iiiID = $d4[serverID];
			$first = $d4[action_type];
		}
	}

	$q4="SELECT * from ps_history where packet_id = '$packet' and action_type = 'Served Defendant'";
	$r4=@mysql_query($q4) or die(mysql_error());
	$d4=mysql_fetch_array($r4, MYSQL_ASSOC);
	$delivery = $d4[action_str];
	$deliveryID = $d4[serverID];
	$serveAddress = $d4[address];
	if ($delivery == ''){
		$q4="SELECT * from ps_history where packet_id = '$packet' and action_type = 'Served Resident'";
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
	$header="<td colspan='2' align='center' style='font-size:24px; font-variant:small-caps;'>State of Maryland</td></tr>
		<tr><td colspan='2' align='center' style='font-size:20px;'>Circuit Court for ".$court."</td></tr>
		<tr></tr>
		<tr><td class='a'>".$plaintiff."<br><small>_____________________<br /><em>Plaintiff</em></small><br /><br />v.<br /><br />";
			if ($d1[onAffidavit1]=='checked'){$header .= strtoupper($d1['name1']).'<br>';}
			if ($d1['name2'] && $d1[onAffidavit2]=='checked'){$header .= strtoupper($d1['name2']).'<br>';}
			if ($d1['name3'] && $d1[onAffidavit3]=='checked'){$header .= strtoupper($d1['name3']).'<br>';}
			if ($d1['name4'] && $d1[onAffidavit4]=='checked'){$header .= strtoupper($d1['name4']).'<br>';}
			if ($d1['name5'] && $d1[onAffidavit5]=='checked'){$header .= strtoupper($d1['name5']).'<br>';}
			if ($d1['name6'] && $d1[onAffidavit6]=='checked'){$header .= strtoupper($d1['name6']).'<br>';}
			$header .=strtoupper($d1['address1']).'<br>';
			$header .=strtoupper($d1['city1']).', '.strtoupper($d1['state1']).' '.$d1['zip1'].'<br>';
			$header .= "<small>_____________________<br /><em>Defendant</em></small></td>
				<td align='right' valign='top' style='padding-left:200px; width:200px' nowrap='nowrap'><div style='font-size:24px; border:solid 1px #666666; text-align:center;'>Case Number<br />".str_replace(0,'&Oslash;',$d1[case_no])."</div>";

	if ($type == "non"){
		$article = "14-102 (d) (3) (A) (ii)";
		$result = "MAILING AND POSTING";
		if ($attempts != ''){
				$history = "<div style='font-weight:300'><u>Describe with particularity the good faith efforts to serve the occupant, by personal delivery:</u></div>
				".$attempts;
			}elseif($attemptsa != ''){
				$history = "<div style='font-weight:300'><u>Describe with particularity the good faith efforts to serve the occupant, by personal delivery:</u></div>
				".$attemptsa;
				$iID=$iIDa;
			}
			$history2 = "<div style='font-weight:300'><u>Include the date of the posting and a description of the location of the posting on the property:</u></div>".$posting;
		if ($mailing == ''){
			$history3 = "<div class='dim' style='font-weight:300'><u>State the date on which the required papers were mailed by first-class and certified mail, return receipt requested, and the address:</u>
				<center><font size='36 px'>AWAITING MAILING<br>DO NOT FILE</font></center></div>";
			$noMail = 1;
		}else{
			if ($crr != ''){
				$history3 = "<div style='font-weight:300'><u>State the date on which the required papers were mailed by first-class and certified mail, return receipt requested, and the address:</u></div>
				".$mailing;
			}elseif(($iiID == $d1[server_id]) || ($first != '' && $crr == '')){
				$history3 = "<div style='font-weight:300'><u>State the date on which the required papers were mailed by first-class and the address:</u></div>
				".$mailing;
			}
		}
			//$history4 = "<u>If available, the original certified mail return receipt shall be attached to the affidavit.</u><div style='height:50px; width:550px; border:double 4px; color:#666'>Affix original certified mail return receipt here.</div>";
	}
	if ($type == "pd"){
		$article = "14-102 (d) (3) (A) (i)";
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

	//$topPage = ob_get_clean();
	//ob_start();
	if ($iIDa){
		$historya = "<div style='font-weight:300'><u>Describe with particularity the good faith efforts to serve the mortgagor or grantor, ".$d1["name$def"].",  by personal delivery:</u></div>
				".$attemptsa;
	?>
		<table width="80%" align="center" bgcolor="#FFFFFF" <? if (strtoupper($d1[affidavit_status]) != "SERVICE CONFIRMED"){echo "class='dimmer'";}?>>
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
	$cord=$d1[packet_id]."-".$serverID."%";
	?>  
		<? echo "<tr>".$header."<IMG SRC='barcode.php?barcode=".$cord."&width=250&height=40'><center>File Number: ".$d1[client_file]."<br>[PAGE]</center></td></tr>"; ?>
		<tr>
			<td colspan="2" align="center" style="font-weight:bold; text-decoration:underline" height="30px" valign="top">Affidavit of Attempted Delivery</td>
		</tr>
		<tr>
			<td colspan="2" align="center" style="font-weight:bold; font-size:20px;" height="30px" valign="top"><?=$result?></td>
		</tr>
		<tr>
			<td colspan="2" align="left">Pursuant to Maryland Real Property Article 7-105.1 and Maryland Rules of Procedure <?=$article?> <?=$result?> a copy of the MOTION FOR JUDGMENT AWARDING POSSESSION and all other papers filed with it (the "Papers") in the above-captioned case by:<br></td>
		</tr>
		<tr>
			<td colspan="2" style="font-weight:bold; padding-left:20px;"><?=stripslashes($historya)?></td>
		</tr>      
		<tr>
			<td colspan="2">I solemnly affirm under the penalties of perjury that the contents of this affidavit are true and correct<? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?> and that I did attempt service as set forth above<? }?><? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?> and that I served the MOTION FOR JUDGMENT AWARDING POSSESSION all other papers filed with it to [PERSON SERVED]<? }?>.<br></td>
		</tr>
		<tr>
			<td colspan="2">I, <?=$serverName?>, certify that I am over eighteen years old and not a party to this action<? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?> and that I served [PERSON SERVED], [RELATION TO DEFENDANT]<? }?><? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?> and that I did attempt service as set forth above<? }?>.<br /></td>
		</tr>
		<tr>
			<td valign="top" style="font-size:14px">____________________________________<br />+Notary Public+<br /><br /><br />SEAL</td>
			<td valign="top" style="font-size:14px">________________________<u>DATE:</u>________<br /><?=$serverName?><br /><?=$serverAdd?><br /><?=$serverCity?>, <?=$serverSstate?> <?=$serverZip?><br /><?=$serverPhone?><br><?=$_SERVER[REMOTE_ADDR]?></td> 
		</tr>
	</table>
	<? 
	} 
	$pagea = ob_get_clean();
	ob_start();
	//1st server, or servera if non-Burson
	//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	if ($iID){
	?>
		<table width="80%" align="center" bgcolor="#FFFFFF" <? if (strtoupper($d1[affidavit_status]) != "SERVICE CONFIRMED"){echo "class='dimmer'";}?>>
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
	$cord=$d1[packet_id]."-".$serverID."%";
	 echo "<tr>".$header."<IMG SRC='barcode.php?barcode=".$cord."&width=250&height=40'><center>File Number: ".$d1[client_file]."<br>[PAGE]</center></td></tr>"; ?>
		<tr>
			<td colspan="2" align="center" style="font-weight:bold; text-decoration:underline" height="30px" valign="top">Affidavit of Attempted Delivery<? if ($iID==$iiID){ echo " and Posting";} ?></td>
		</tr>
		<tr>
			<td colspan="2" align="center" style="font-weight:bold; font-size:20px;" height="30px" valign="top"><?=$result?></td>
		</tr>
		<tr>
			<td colspan="2" align="left">Pursuant to Maryland Real Property Article 7-105.1 and Maryland Rules of Procedure <?=$article?> <?=$result?> a copy of the MOTION FOR JUDGMENT AWARDING POSSESSION and all other papers filed with it (the "Papers") in the above-captioned case by:<br></td>
		</tr>
		<tr>
			<td colspan="2" style="font-weight:bold; padding-left:20px;"><?=stripslashes($history)?></td>
		</tr>
	<?
	if ($iID == $iiID){
	}else{
	?>        
		<tr>
			<td colspan="2">I solemnly affirm under the penalties of perjury that the contents of this affidavit are true and correct<? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?> and that I did attempt service as set forth above<? }?><? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?> and that I served the MOTION FOR JUDGMENT AWARDING POSSESSION and all other papers filed with it to [PERSON SERVED]<? }?>.<br></td>
		</tr>
		<tr>
			<td colspan="2">I, <?=$serverName?>, certify that I am over eighteen years old and not a party to this action<? if ($type != 'non' && $d1[attorneys_id] == "1"){ ?> and that I served [PERSON SERVED], [RELATION TO DEFENDANT]<? }?><? if ($type == 'non' && $d1[attorneys_id] == "1"){ ?> and that I did attempt service as set forth above<? }?>.<br /></td>
		</tr>
		<tr>
			<td valign="top" style="font-size:14px">____________________________________<br />+Notary Public+<br /><br /><br />SEAL</td>
			<td valign="top" style="font-size:14px">________________________<u>DATE:</u>________<br /><?=$serverName?><br /><?=$serverAdd?><br /><?=$serverCity?>, <?=$serverState?> <?=$serverZip?><br /><?=$serverPhone?><br><?=$_SERVER[REMOTE_ADDR]?></td> 
		</tr>
	</table>
	<? }
	 }
	$pageI = ob_get_clean();
	ob_start();
	 //Multiple servers' attempts end here
	if($history2){
	if ($iID==$iiID){
	}else{
	?>
		<table width="80%" align="center" bgcolor="#FFFFFF" <? if (strtoupper($d1[affidavit_status]) != "SERVICE CONFIRMED"){echo "class='dimmer'";}?>>
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
	$cord=$d1[packet_id]."-".$serverID."%";
	?> 
		<? echo "<tr>".$header."<IMG SRC='barcode.php?barcode=".$cord."&width=250&height=40'><center>File Number: ".$d1[client_file]."<br>[PAGE]</center></td></tr>"; ?>
		<tr>
			<td colspan="2" align="center" style="font-weight:bold; text-decoration:underline" height="30px" valign="top">Affidavit of Posting</td>
		</tr>
		<tr>
			<td colspan="2" align="center" style="font-weight:bold; font-size:20px;" height="30px" valign="top"><?=$result?></td>
		</tr>
		<tr>
			<td colspan="2" align="left">Pursuant to Maryland Real Property Article 7-105.1 and Maryland Rules of Procedure <?=$article?> <?=$result?> a copy of the MOTION FOR JUDGMENT AWARDING POSSESSION and all other papers filed with it (the "Papers") in the above-captioned case by:<br></td>
		</tr>
		<? } ?>
		<tr>
			<td colspan="2" style="font-weight:bold; padding-left:20px"><?=stripslashes($history2)?></td>
		</tr>       
		<tr>
			<td colspan="2">I solemnly affirm under the penalties of perjury that the contents of this affidavit are true and correct.<br></td>
		</tr>
		<tr>
			<td colspan="2">I, <?=$serverName?>, certify that I am over eighteen years old and not a party to this action.<br /></td>
		</tr>
		<tr>
			<td valign="top" style="font-size:14px">____________________________________<br />Notary Public<br /><br /><br />SEAL</td>
			<td valign="top" style="font-size:14px">________________________<u>DATE:</u>________<br /><?=$serverName?><br /><?=$serverAdd?><br /><?=$serverCity?>, <?=$serverState?> <?=$serverZip?><br /><?=$serverPhone?><br><?=$_SERVER[REMOTE_ADDR]?></td> 
		</tr>
	</table>
	<? }
	 $pageII = ob_get_clean();
	 $postingID = $iiID;
	 ob_start();
	  if($iiiID){ ?>
		<table width="80%" align="center" bgcolor="#FFFFFF" <? if (strtoupper($d1[affidavit_status]) != "SERVICE CONFIRMED"){echo "class='dimmer'";}?>>
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
	$cord=$d1[packet_id]."-".$serverID."%";
	 echo "<tr>".$header."<IMG SRC='barcode.php?barcode=".$cord."&width=250&height=40'><center>File Number: ".$d1[client_file]."<br>[PAGE]</center></td></tr>"; ?>
		<tr>
			<td colspan="2" align="center" style="font-weight:bold; text-decoration:underline" height="30px" valign="top">Affidavit of Mailing</td>
		</tr>
		<tr>
			<td colspan="2" align="center" style="font-weight:bold; font-size:20px;" height="30px" valign="top"><?=$result?></td>
		</tr>
		<tr>
			<td colspan="2" align="left">Pursuant to Maryland Real Property Article 7-105.1 and Maryland Rules of Procedure <?=$article?> <?=$result?> a copy of the MOTION FOR JUDGMENT AWARDING POSSESSION and all other papers filed with it (the "Papers") in the above-captioned case by:<br></td>
		</tr>
		<tr>
			<td colspan="2" style="font-weight:bold; padding-left:20px"><?=stripslashes($history3)?></td>
		</tr>      
		<tr <? if($noMail == 1){ echo 'class="dim"';}?>>
			<td colspan="2">I solemnly affirm under the penalties of perjury that the contents of this affidavit are true and correct.  And that I mailed the above papers under section 14-102 (d) (3) (A) (ii) to occupant.<br></td>
		</tr>
		<tr <? if($noMail == 1){ echo 'class="dim"';}?>>
			<td colspan="2">I, <?=$serverName?>, certify that I am over eighteen years old and not a party to this action.<br /></td>
		</tr>
		<tr <? if($noMail == 1){ echo 'class="dim"';}?>>
			<td valign="top" style="font-size:14px">____________________________________<br />+Notary Public+<br /><br /><br />SEAL</td>
			<td valign="top" style="font-size:14px">________________________<u>DATE:</u>________<br /><?=$serverName?><br /><?=$serverAdd?><br /><?=$serverCity?>, <?=$serverState?> <?=$serverZip?><br /><?=$serverPhone?><br><?=$_SERVER[REMOTE_ADDR]?></td> 
		</tr>
		<tr>
			<td colspan="2" style="padding-left:20px"><?=stripslashes($history4)?></td>
		</tr>
	</table>
	<? }
	 $pageIII = ob_get_clean();
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
	<table width="80%" align="center" bgcolor="#FFFFFF" <? if (strtoupper($d1[affidavit_status]) != "SERVICE CONFIRMED"){echo "class='dimmer'";}?>>
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
	$cord=$d1[packet_id]."-".$serverID."%";
	?> 
	<? echo "<tr>".$header."<IMG SRC='barcode.php?barcode=".$cord."&width=250&height=40'><center>File Number: ".$d1[client_file]."<br>[PAGE]</center></td></tr>"; ?>
		<tr>
			<td colspan="2" align="center" style="font-weight:bold; text-decoration:underline" height="30px" valign="top">Affidavit of Personal Delivery</td>
		</tr>
		<tr>
			<td colspan="2" align="center" style="font-weight:bold; font-size:20px;" height="30px" valign="top"><?=$result?></td>
		</tr>
		<tr>
			<td colspan="2" align="left">Pursuant to Maryland Real Property Article 7-105.1 and Maryland Rules of Procedure <?=$article?> <?=$result?> a copy of the MOTION FOR JUDGMENT AWARDING POSSESSION and all other papers filed with it (the "Papers") in the above-captioned case by:<br></td>
		</tr>
	<? if ($residentDesc){
		$desc=strtoupper(str_replace('CO-A BORROWER IN THE ABOVE-REFERENCED CASE', 'A BORROWER IN THE ABOVE-REFERENCED CASE', str_replace('BORROWER','A BORROWER IN THE ABOVE-REFERENCED CASE', attorneyCustomLang($d1[attorneys_id],strtoupper($residentDesc)))));
	}?>
		<tr>
			<td colspan="2" style="font-weight:bold; font-size:14px; padding-left:20px; padding-top:20px; padding-bottom:20px; line-height:2;"><?=stripslashes($delivery)?><?  if ($type == 'pd' && $d1[attorneys_id] != "1" && $nondef == '1' && $residentDesc != ''){ echo "<br />RESIDENT DESCRIPTION: ".$desc;}?></td>
		</tr>       
		<tr>
			<td colspan="2">I solemnly affirm under the penalties of perjury that the contents of <? if ($type == 'non'){ ?>section (i) of <? }?>this affidavit are true and correct<? if ($type == 'pd' && $d1[attorneys_id] == "1" && $nondef == '1'){?>,  and that I served the MOTION FOR JUDGMENT AWARDING POSSESSION and other papers to <? if ($resident){ echo strtoupper($resident);}else{ echo '[PERSON SERVED]';}?>, <? if ($residentDesc){echo $desc;}else{ echo '[RELATION TO DEFENDANT]';}?><? if ($serveAddress){ echo ', at '.$serveAddress;}?><? }elseif($type == 'pd' && $d1[attorneys_id] == "1" && $nondef != '1'){?>, and that I served the MOTION FOR JUDGMENT AWARDING POSSESSION and other papers to <?=strtoupper($d1["name$def"])?><? if ($serveAddress){ echo ', at '.$serveAddress;}?><? } ?>.<br><br /></td>
		</tr>
		<tr>
			<td colspan="2">I, <?=$serverName?>, certify that I am over eighteen years old and not a party to this action.<br /></td>
		</tr>
		<tr>
			<td valign="top" style="font-size:14px">____________________________________<br />Notary Public<br /><br /><br />SEAL</td>
			<td valign="top" style="font-size:14px">________________________<u>DATE:</u>________<br /><?=$serverName?><br /><?=$serverAdd?><br /><?=$serverCity?>, <?=$serverState?> <?=$serverZip?><br /><?=$serverPhone?><br><?=$_SERVER[REMOTE_ADDR]?></td> 
		</tr>
	</table>
	<? 
	$pagePD = ob_get_clean();
	$PDID=$deliveryID;
	$PDADD=$serveAddress;
	}

	//count pages and construct table of contents
	$count=0;
	$totalPages=0;

	$checked='';
	if ($pageI != ''){
		$totalPages++;
		if ($iID==$iiID){
			if (($_COOKIE[psdata][level]=='Operations' || $postingID==$_COOKIE[psdata][user_id]) && ($defendant != "MAIL")){
				$contents .= "<tr bgcolor='".row_color($totalPages,'#FFFFFF','#cccccc')."'><td class='a'><input type='checkbox' DISABLED checked='yes'></td><td class='a'>Page $totalPages of [PAGE]</td><td class='a'>".id2name($postingID)."</td><td class='a'>ATTEMPTS & POSTING</td><td class='a'>".$d1[address1].", ".$d1[city1].", ".$d1[state1]." ".$d1[zip1]."</td></tr>";
			$checked=1;
			}else{
				$contents .= "<tr bgcolor='#FF0000'><td class='a'><input type='checkbox' DISABLED></td><td class='a'>Page $totalPages of [PAGE]</td><td class='a'>".id2name($postingID)."</td><td class='a'>ATTEMPTS & POSTING FOR <b>OCCUPANT</b></td><td class='a'>".$d1[address1].", ".$d1[city1].", ".$d1[state1]." ".$d1[zip1]."</td></tr>";
				if ($missing == ''){
				$missing = $totalPages;
			}else{
				$missing .= ', '.$totalPages;
			}
			}
		}else{
			if (($_COOKIE[psdata][level]=='Operations' || $iID==$_COOKIE[psdata][user_id]) && ($defendant != "MAIL")){
				$contents .= "<tr bgcolor='".row_color($totalPages,'#FFFFFF','#cccccc')."'><td class='a'><input type='checkbox' DISABLED checked='yes'></td><td class='a'>Page $totalPages of [PAGE]</td><td class='a'>".id2name($iID)."</td><td class='a'>ATTEMPTING TO SERVE </td><td class='a'>".$d1[address1].", ".$d1[city1].", ".$d1[state1]." ".$d1[zip1]."</td></tr>";
			$checked=1;
			}else{
				$contents .= "<tr bgcolor='#FF0000'><td class='a'><input type='checkbox' DISABLED></td><td class='a'>Page $totalPages of [PAGE]</td><td class='a'>".id2name($iID)."</td><td class='a'>ATTEMPTING TO SERVE <b>OCCUPANT</b></td><td class='a'>".$d1[address1].", ".$d1[city1].", ".$d1[state1]." ".$d1[zip1]."</td></tr>";
				if ($missing == ''){
				$missing = $totalPages;
			}else{
				$missing .= ', '.$totalPages;
			}
			}
		}
	}
	if ($pageII != ''){
		//if posting server also made attempt(s), do nothing
		if ($iID==$iiID){
		}else{
		//otherwise increase counter
			$totalPages++;
			if (($_COOKIE[psdata][level]=='Operations' || $postingID==$_COOKIE[psdata][user_id]) && ($defendant != "MAIL")){
				$contents .= "<tr bgcolor='".row_color($totalPages,'#FFFFFF','#cccccc')."'><td class='a'><input type='checkbox' DISABLED checked='yes'></td><td class='a'>Page $totalPages of [PAGE]</td><td class='a'>".id2name($postingID)."</td><td class='a'>POSTING </td><td class='a'>".$d1[address1].", ".$d1[city1].", ".$d1[state1]." ".$d1[zip1]."</td></tr>";
			$checked=1;
			}else{
				$contents .= "<tr bgcolor='#FF0000'><td class='a'><input type='checkbox' DISABLED></td><td class='a'>Page $totalPages of [PAGE]</td><td class='a'>".id2name($iiID)."</td><td class='a'>POSTING FOR <b>OCCUPANT</b></td><td class='a'>".$d1[address1].", ".$d1[city1].", ".$d1[state1]." ".$d1[zip1]."</td></tr>";
				if ($missing == ''){
				$missing = $totalPages;
			}else{
				$missing .= ', '.$totalPages;
			}
			}
		}
	}
	if ($pageIII != ''){
		$totalPages++;
		$add1x = $d1["address1"].' '.$d1["city1"].', '.$d1["state1"].' '.$d1["zip1"];
		if ($defendant=="MAIL" || $_COOKIE[psdata][level]=='Operations' || $iiiID==$_COOKIE[psdata][user_id]){
			$contents .= "<tr bgcolor='".row_color($totalPages,'#FFFFFF','#cccccc')."'><td class='a'><input type='checkbox' DISABLED checked='yes'></td><td class='a'>Page $totalPages of [PAGE]</td><td class='a'>".id2name($iiiID)."</td><td class='a'>MAILING </td><td class='a'>$add1x</td></tr>";
			$checked=1;
		}else{
			$contents .= "<tr bgcolor='#FF0000'><td class='a'><input type='checkbox' DISABLED></td><td class='a'>Page $totalPages of [PAGE]</td><td class='a'>".id2name($iiiID)."</td><td class='a'>MAILING FOR <b>OCCUPANT</b></td><td class='a'>$add1x</td></tr>";
			if ($missing == ''){
				$missing = $totalPages;
			}else{
				$missing .= ', '.$totalPages;
			}
		}
	}
	if ($pagePD != ''){
		$totalPages++;
		$defName="name".$count;
		if (($_COOKIE[psdata][level]=='Operations' || $PDID==$_COOKIE[psdata][user_id]) && ($defendant != "MAIL")){
			$contents .= "<tr bgcolor='".row_color($totalPages,'#FFFFFF','#cccccc')."'><td class='a'><input type='checkbox' DISABLED checked='yes'></td><td class='a'>Page $totalPages of [PAGE]</td><td class='a'>".id2name($PDID)."</td><td class='a'>PERSONAL DELIVERY</b></td><td class='a'>".$PDADD."</td></tr>";
			$checked=1;
		}else{
			$contents .= "<tr bgcolor='#FF0000'><td class='a'><input type='checkbox' DISABLED></td><td class='a'>Page $totalPages of [PAGE]</td><td class='a'>".id2name($PDID)."</td><td class='a'>DELIVERY TO <b>OCCUPANT</b></td><td class='a'>".$PDADD."</td></tr>";
			if ($missing == ''){
				$missing = $totalPages;
			}else{
				$missing .= ', '.$totalPages;
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

		if (!$_GET[mail] && $checked == '1' && $overRide != '1'){
			echo "<table width='90%' align='center' bgcolor='#FFFFFF' style='border-style:solid 2px; border-collapse:collapse;' border='1' height='450px'><tr><td colspan='5' align='center' class='b'>TABLE OF CONTENTS FOR EVICTION PACKET $packet</td></tr><tr align='center'><td>Present</td><td>Page #</td><td>Server</td><td>Action(s)</td><td>Deed of Trust</td></tr>".$contents."</table>";
		}
	$count2=0;
	$currentCounter=0;
	if ($pageI != ''){
		$currentCounter++;
		if (($_COOKIE[psdata][level]=='Operations' || $iID==$_COOKIE[psdata][user_id]) && ($defendant != "MAIL")){
			echo str_replace("[PAGE]","Set 1 (Affidavit $currentCounter of $totalPages)",$pageI);
		}
	}
	if ($pageII != ''){
		//if posting server also made attempt(s), do nothing
		if ($iID==$iiID){
		}else{
		//otherwise increase counter
			$currentCounter++;
		}
		if (($_COOKIE[psdata][level]=='Operations' || $iiID==$_COOKIE[psdata][user_id]) && ($defendant != "MAIL")){
			echo str_replace("[PAGE]","Set 1 (Affidavit $currentCounter of $totalPages)",$pageII);
		}
	}
	if ($pageIII != ''){
		$currentCounter++;
		if ($_COOKIE[psdata][level]=='Operations'){
			echo str_replace("[PAGE]","Set 1 (Affidavit $currentCounter of $totalPages)",$pageIII);
		}
	}
	if ($pagePD != ''){
		$currentCounter++;
		if (($_COOKIE[psdata][level]=='Operations' || $PDID==$_COOKIE[psdata][user_id]) && ($defendant != "MAIL")){
			echo str_replace("[PAGE]","Set 1 (Affidavit $currentCounter of $totalPages)",$pagePD);
		}
	}
	$count2=0;
	$currentCounter=0;
	if ($pageI != ''){
		$currentCounter++;
		if (($_COOKIE[psdata][level]=='Operations' || $iID==$_COOKIE[psdata][user_id]) && ($defendant != "MAIL")){
			echo str_replace("[PAGE]","Set 2 (Affidavit $currentCounter of $totalPages)",$pageI);
		}
	}
	if ($pageII != ''){
		//if posting server also made attempt(s), do nothing
		if ($iID==$iiID){
		}else{
		//otherwise increase counter
			$currentCounter++;
		}
		if (($_COOKIE[psdata][level]=='Operations' || $iiID==$_COOKIE[psdata][user_id]) && ($defendant != "MAIL")){
			echo str_replace("[PAGE]","Set 2 (Affidavit $currentCounter of $totalPages)",$pageII);
		}
	}
	if ($pageIII != ''){
		$currentCounter++;
		if ($_COOKIE[psdata][level]=='Operations'){
			echo str_replace("[PAGE]","Set 2 (Affidavit $currentCounter of $totalPages)",$pageIII);
		}
	}
	if ($pagePD != ''){
		$currentCounter++;
		if (($_COOKIE[psdata][level]=='Operations' || $PDID==$_COOKIE[psdata][user_id]) && ($defendant != "MAIL")){
			echo str_replace("[PAGE]","Set 2 (Affidavit $currentCounter of $totalPages)",$pagePD);
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
			$q10="SELECT packet_id FROM ps_packets where (server_id='$serveID' OR server_ida='$serveID' OR server_idb='$serveID' OR server_idc='$serveID' OR server_idd='$serveID' OR server_ide='$serveID') AND packet_id >= '$start' AND packet_id <= '$stop' AND process_status <> 'CANCELLED' AND affidavit_status='SERVICE CONFIRMED' AND filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS'";
		}else{
			$q10="SELECT packet_id FROM ps_packets where (server_id='$serveID' OR server_ida='$serveID' OR server_idb='$serveID' OR server_idc='$serveID' OR server_idd='$serveID' OR server_ide='$serveID') AND packet_id >= '$start' AND process_status <> 'CANCELLED' AND affidavit_status='SERVICE CONFIRMED' AND filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS'";
		}
	}else{
		$q10="SELECT packet_id FROM ps_packets where (server_id='$serveID' OR server_ida='$serveID' OR server_idb='$serveID' OR server_idc='$serveID' OR server_idd='$serveID' OR server_ide='$serveID') AND process_status <> 'CANCELLED' AND affidavit_status='SERVICE CONFIRMED' AND filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS'";
	}
	$r10=@mysql_query($q10) or die ("Query: $q10<br>".mysql_error());
	while ($d10=mysql_fetch_array($r10, MYSQL_ASSOC)){
	//echo $d10[packet_id].'<br>';
	$packet=$d10[packet_id];
	makeAffidavit($packet,"ALL");
	}
}elseif($_GET[id] && $_GET[mail]){
	makeAffidavit($_GET[id],"MAIL");
}elseif ($_GET[id] && $_GET[def]){
	makeAffidavit($_GET[id],$_GET[def]);
}elseif($_GET[id] && !$_GET[def]){
	makeAffidavit($_GET[id],"ALL");
}
?>
<script type="text/javascript">
var browser=navigator.appName;

if (browser == 'Microsoft Internet Explorer'){
alert('Unable to load in IE, we will now take you to why...');
location.href='http://www.google.com/search?hl=en&q=ie7+css+page+break+bug';
}
</script>
<?
if ($_GET['autoPrint'] == 1){
echo "<script>
if (window.self) window.print();
self.close();
</script>";
}
?>