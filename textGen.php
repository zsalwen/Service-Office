<?
include 'common.php';
function county2envelope2($county){
	$county=strtoupper($county);
	if ($county == 'BALTIMORE'){
		$search='BALTIMORE COUNTY';
	}elseif($county == 'PRINCE GEORGES'){
		$search='PRINCE GEORGE';
	}elseif($county == 'ST MARYS'){
		$search='ST MARY';
	}elseif($county == 'QUEEN ANNES'){
		$search='QUEEN ANNE';
	}else{
		$search=$county;
	}
	$r=@mysql_query("SELECT to1 FROM envelopeImage WHERE to1 LIKE '%$search%' AND addressType='COURT'");
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	return $d[to1];
}

function id2attorneyName($id){
	$q="SELECT full_name FROM attorneys WHERE attorneys_id = '$id'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	return $d[full_name];
}
if (isset($_GET['packet'])){
$q="SELECT * from ps_packets WHERE packet_id='$_GET[packet]'";
$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
$d=mysql_fetch_array($r, MYSQL_ASSOC);
if ($d[attorneys_id] == 1){
	$stuffLink="http://staff.mdwestserve.com/otd/stuffPacket.2.php?packet=$d[packet_id]&sb=1";
}elseif($d[attorneys_id] == 70){
	$stuffLink="http://staff.mdwestserve.com/otd/stuffPacket.bgw.php?packet=$d[packet_id]";
}else{
	$stuffLink="http://staff.mdwestserve.com/otd/stuffPacket.2.php?packet=$d[packet_id]";
}
$defNames=strtoupper($d['name1']);
$defCount=1;
if ($d['name2']){
	$defNames.=', '.strtoupper($d['name2']);
	$defCount++;
}
if ($d['name3']){
	$defNames.=', '.strtoupper($d['name3']);
	$defCount++;
}
if ($d['name4']){
	$defNames.=', '.strtoupper($d['name4']);
	$defCount++;
}
if ($d['name5']){
	$defNames.=', '.strtoupper($d['name5']);
	$defCount++;
}
if ($d['name6']){
	$defNames.=', '.strtoupper($d['name6']);
	$defCount++;
}

$serverSelect="<select name ='server'>";
if ($d['server_id'] && ($d[state1] != 'MD' && $d[state1] != 'md' && $d[state1] != '')){
	$serverSelect .= "<option value=''>".strtoupper(id2name($d['server_id']))."</option>";
}
if ($d['server_ida'] && $d['server_ida'] != $d['server_id']){
	$serverSelect .= "<option value='a'>".strtoupper(id2name($d['server_ida']))."</option>";
}
if ($d['server_idb'] && $d['server_idb'] != $d['server_ida'] && $d['server_idb'] != $d['server_id']){
	$serverSelect .= "<option value='b'>".strtoupper(id2name($d['server_idb']))."</option>";
}
if ($d['server_idc'] && $d['server_idc'] != $d['server_idb'] && $d['server_idc'] != $d['server_ida'] && $d['server_idc'] != $d['server_id']){
	$serverSelect .= "<option value='c'>".strtoupper(id2name($d['server_idc']))."</option>";
}
if ($d['server_idd'] && $d['server_idd'] != $d['server_idc'] && $d['server_idd'] != $d['server_idb'] && $d['server_idd'] != $d['server_ida'] && $d['server_idd'] != $d['server_id']){
	$serverSelect .= "<option value='d'>".strtoupper(id2name($d['server_idd']))."</option>";
}
if ($d['server_ide'] && $d['server_ide'] != $d['server_idd'] && $d['server_ide'] != $d['server_idc'] && $d['server_ide'] != $d['server_idb'] && $d['server_ide'] != $d['server_ida'] && $d['server_ide'] != $d['server_id']){
	$serverSelect .= "<option value='e'>".strtoupper(id2name($d['server_ide']))."</option>";
}
$serverSelect .= "</select>";

if (isset($_POST['server'])){
	$slot="server_id".$_POST['server'];
	$serverSlot=$d["$slot"];
	$q2="SELECT * from ps_users where id='$serverSlot'";
	$r2=@mysql_query($q2) or die("Query: $q2<br>".myqsl_error());
	$d2=mysql_fetch_array($r2, MYSQL_ASSOC);
	timeline($_GET[packet],$_COOKIE[psdata][name]." Sent Dispatch Email to ".$d2[name]);
	$addList='';
	$addList2='';	
	$addCount=0;
	$i=0;
	if ($d['server_id'] == $serverSlot){
		$addList = strtoupper($d[address1])."<br>".strtoupper($d[city1]).", ".strtoupper($d[state1])." ".$d[zip1]."<br>";
		$addList2[$i] = "in ".strtoupper($d[city1]).", ".strtoupper($d[state1]);
		$addCount++;
		$i++;
	}
	if ($d['server_ida'] == $serverSlot){
		$addList = strtoupper($d[address1a])."<br>".strtoupper($d[city1a]).", ".strtoupper($d[state1a])." ".$d[zip1a]."<br>";
		$addList2[$i] = "in ".strtoupper($d[city1a]).", ".strtoupper($d[state1a]);
		$addCount++;
		$i++;
	}
	if ($d['server_idb'] == $serverSlot){
		if ($addList != ''){	
			$addList .= "     --AND--<br>";
		}
		$addList .= strtoupper($d[address1b])."<br>".strtoupper($d[city1b]).", ".strtoupper($d[state1b])." ".$d[zip1b]."<br>";
		$addList2[$i] .= "in ".strtoupper($d[city1b]).", ".strtoupper($d[state1b]);
		$addCount++;
		$i++;
	}
	if ($d['server_idc'] == $serverSlot){
		if ($addList != ''){	
			$addList .= "     --AND--<br>";
		}
		$addList .= strtoupper($d[address1c])."<br>".strtoupper($d[city1c]).", ".strtoupper($d[state1c])." ".$d[zip1c]."<br>";
		$addList2[$i] .= "in ".strtoupper($d[city1c]).", ".strtoupper($d[state1c]);
		$addCount++;
		$i++;
	}
	if ($d['server_idd'] == $serverSlot){
		if ($addList != ''){	
			$addList .= "     --AND--<br>";
		}
		$addList .= strtoupper($d[address1d])."<br>".strtoupper($d[city1d]).", ".strtoupper($d[state1d])." ".$d[zip1d]."<br>";
		$addList2[$i] .= "in ".strtoupper($d[city1d]).", ".strtoupper($d[state1d]);
		$addCount++;
		$i++;
	}
	if ($d['server_ide'] == $serverSlot){
		if ($addList != ''){	
			$addList .= "     --AND--<br>";
		}
		$addList .= strtoupper($d[address1e])."<br>".strtoupper($d[city1e]).", ".strtoupper($d[state1e])." ".$d[zip1e]."<br>";
		$addList2[$i] .= "in ".strtoupper($d[city1e]).", ".strtoupper($d[state1e]);
		$addCount++;
	}
	
	//$addList3='a file with ';
	$t=0;
	$andLength=count($addList2)-1;
	while ($t < count($addList2)){
		if (($t == $andLength) && $t > 0){
		$addList3 .= ', and';
		}elseif($t == 0){
		}elseif($t == count($addList2)){
		}else{
		$addList3 .= ', ';
		}
		$addList3 .= $addList2[$t];
		$t++;
	}

	$instructionLink="http://service.mdwestserve.com/customInstructions.php?packet=".$d[packet_id];
	echo "<script>window.open('".$instructionLink."&autoSave=1','Service Instructions')</script>";
	echo "<script>window.open('otdSave.php?packet=".$d[packet_id]."');</script>";
	//echo "<script>window.open('$stuffLink');</script>";
	//echo "<script>window.open('instructionSave.php?packet=".$d[packet_id]."');</script>";
	
}
$userID=$_COOKIE[psdata][user_id];
$q3="SELECT * FROM ps_users where id='$userID'";
$r3=@mysql_query($q3) or die ("Query: $q3<br>".mysql_error());
$d3=mysql_fetch_array($r3, MYSQL_ASSOC);
$now = date ('H');

$otdStr=str_replace('portal//var/www/dataFiles/service/orders/','PS_PACKETS/',$d[otd]);
$otdStr=str_replace('data/service/orders/','PS_PACKETS/',$otdStr);
$otdStr=str_replace('portal/','',$otdStr);
$otdStr2=str_replace('http://mdwestserve.com','',$d[otd]);
$otdStr2=str_replace('PS_PACKETS/','data/service/orders/',$otdStr2);
$instructionStr='/data/service/unknown/Service Instructions For Packet '.$d['packet_id'].'.PDF';
$instructionLink="http://mdwestserve.com/ps/customInstructions";
if ($d[attorneys_id] == '1'){
	$instructionLink .= ".burson";
}elseif($d['attorneys_id'] == "56"){
	$instructionLink .= ".brennan";
}
$instructionLink .= ".php?packet=".$d[packet_id];
?>
<style type="text/css">
    @media print {
      .noprint { display: none; }
    }
 </style> 
<form method="post">
<table align='center' style='font-size:14px;'>
<? if (isset($_POST['server'])){
	error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Sending Out-of-State Service Email To ".$d2[name]." For OTD".$_GET[packet]." \n",3,"/logs/user.log");
}else{
	echo "<tr class='noprint'><td align='center'>$serverSelect <input type='submit' name='submit' value='Submit'></td></tr>";
}?>
	<tr>
		<td><b>To:</b> <? if (isset($_POST['server'])){ echo $d2[name]." &lt;".$d2[email]."&gt;";}else{echo "<i><u>SERVER NOT YET SELECTED</u></i>";} ?><br></td>
	</tr>
	<tr>
		<td><b>Subject:</b> Process Service Quote, Documents, and Instructions for File # <?=$d['packet_id']?><br></td>
	</tr>
	<tr>
		<td>To Whom It May Concern,<br></td>
	</tr>
	<tr>
		<td>I spoke on the phone with <? if (isset($_POST['server'])){ echo $d2[name];}else{echo "<i><u>SERVER NOT YET SELECTED</u></i>";} ?>
		today concerning process service <? if (isset($_POST['server'])){echo $addList3;}else{echo "<i>ADDRESS NOT YET SELECTED</i>";}?>.<br>
		I was quoted a price of <? if (isset($_POST['server'])){echo "<i><b>[$$$$]</b></i>"; }else{echo "<i><u>SERVER NOT YET SELECTED</u></i>";} ?>
		for two attempts on <? if ($defCount == '1'){ echo "1 defendant";}else{ echo $defCount." defendants";}?> at 
		<? if (isset($addCount) && $addCount == '1'){echo "1 address";}elseif(isset($addCount) && $addCount > 1){echo $addCount." addresses";
		}else{ echo "<i>NO SERVER SELECTED</i>";}?>, and <i><b>[$$$$]</b></i> for overnight delivery of affidavits back to our office, which would make the total cost of service <i><b>INSERT SERVICE TOTAL</b></i>.<br>
		I have included the documents for service in an USPS Express Mailer, along with an instruction sheet for easy reference (you can disregard the names of any other servers/addresses, as they only concern MD service).  The tracking number of the package is <b style='background-color:#FFFF00;'>[####]</b>.  <b>You should be receiving the documents sometime tomorrow; can you please email or call our office when the documents are received?</b><br><br>
		<? 
		if ($d[lossMit] != '' && $d[lossMit] != 'N/A - OLD L'){
			if ($d[attorneys_id] == 1){
				//if file is a final or preliminary, instruct to include available envelope stuffings
				$toAttorney=id2attorneyName($d[attorneys_id]);
				$toCounty=county2envelope2($d[circuit_court]);
				if ($d[lossMit] != 'FINAL'){
					//if preliminary, instruct to include one envelope to client
					$lossMit="Per Maryland law HB472, <b>please include one green #10 envelope addressed to '$toAttorney'  with each defendant's service documents</b>.";
				}else{
					//if final, instruct to include two envelopes: one to court and one to client
					$lossMit = "Per Maryland law HB472, <b>please include one green #10 envelope addressed to '$toAttorney', AND one green #10 envelope addressed to '$toCounty' with each defendant's service documents</b>.";
				}
			}else{
				//if file requires white BGW-style envelopes, then instruct to include envelope for attorney
				$toAttorney=id2attorneyName($d[attorneys_id]);
				$lossMit="Per Maryland law HB472, <b>please include one white #10 envelope addressed to '$toAttorney'";
				if ($d[lossMit] == 'FINAL'){
					//if file is a final, instruct to include envelope for court
					$toCounty=county2envelope2($d[circuit_court]);
					$lossMit .= " AND one white #10 envelope addressed to '$toCounty'";
				}
				$lossMit .= "</b> with each defendant's service documents.";
			}
			echo $lossMit."  ";
		}
		?>
		Also, per new Maryland state requirements <b style='background-color:#FFFF00;'>please separate service attempts by at least 24 hours and try and make one attempt before noon and one after 6pm.</b><br>
		The documents for service can be listed on the affidavit as "a copy of the <?if ($d[addlDocs] != ''){echo $d[addlDocs];}else{ echo "Order to Docket";} ?> and all other papers filed with it in the above-captioned case".<br>
		<b>Packet <?=$d['packet_id']?> - Service on <?=$defNames?></b><br>
<? if (isset($_POST['server'])){echo $addList; }else{echo "<i>ADDRESS NOT YET SELECTED</i>";}?><br>
<div style='background-color: #FF0000; font-weight:bold;'>PLEASE SERVE ONLY LISTED ADDRESSES.  ADDITIONALLY, IF THE ADDRESS IS A BUSINESS, APPEARS VACANT, THE DEFENDANTS ARE NOT KNOWN AT THE ADDRESS, OR ADDITIONAL ADDRESSES ARE DISCOVERED BEYOND THOSE LISTED, PLEASE NOTIFY OUR OFFICE BEFORE TAKING ANY FURTHER ACTION FOR DIRECTIONS ON HOW TO PROCEED.</div><br>
As each step of service is performed, please call or email our office with updates.  Also, upon completion of service, please contact us with the details of how service was completed.  Service can be completed by:<br><br>		
Personal delivery (only can be achieved in the following manner):<br>
1.  Delivery to the defendants themselves.<br>
2.  OR delivery to someone over the age of 18 who resides with the defendant in question at the address being served.<br>
<b>For ALL personal delivery service Maryland law REQUIRES a physical description of the individual served.</b><br><br>
Non-Service (if personal delivery cannot be effected):<br>
1.  Take note of the date and time of the attempts made, as well as a description of the residence at which service is being attempted.<br>
<b>For ALL non-service affidavits Maryland law REQUIRES a physical description of the residence.</b><br><br>
<div style='background-color:#FFFF00;'>REGARDLESS OF SERVICE ACHIEVED, after it is complete, CALL OR EMAIL our office with the details and WE WILL PREPARE THE AFFIDAVITS, and send them for you to sign, notarize, and RETURN VIA OVERNIGHT SHIPPING TO OUR OFFICE:</div><br>
<b>MDWestServe, Inc.<br>
300 E. Joppa Road Suite 1102<br>
Towson, MD 21286</b><br><br>
	If there are any difficulties with this service, please have the server directly contact our office from the field.  During business hours (9-5 EST), please contact Alex at 410-828-4569.  Patrick, our Operations Manager, can be reached after hours at (443) 386-2584, please do not hesitate to call him.  Lastly, around 6 PM EST you should receive a confirmation email of all files sent from our office today.  If you have any issues with the documents or instructions that we have sent you, you must contact our office within 24 hours of receipt, or your office will be held responsible for any delays.<br><br>
Please let either myself or Patrick know if there are any questions or issues.<br><br>

Thank you,<br>
<?=ucwords(strtolower($d3[name]))?><br>
service@mdwestserve.com<br>
<? if ($d3[id] == '296'){ echo "(410) 616-8881";}else{ echo "(410) 828-4569";} ?></td>
	</tr>
	<tr>
		<td><table style="border:solid 2px; font-weight:bold;" align="center" class='noprint'><tr><td>
	Next, Attach The Following Documents to the Email:<br>
	<a href="<?=$instructionLink?>&autoPrint=1" target="_blank">Instructions for Packet <?=$d[packet_id]?></a><br>
<? if (file_exists($instructionStr)){?>
	<a href="instructionSave.php?packet=<?=$d[packet_id]?>">Download Instructions for Packet <?=$d[packet_id]?></a>
<? } ?>
	<a href="otdSave.php?packet=<?=$d[packet_id]?>">Download Process Service Documents for Packet <?=$d[packet_id]?></a><br>
	<a href="<?=$stuffLink?>" target='_blank'>Download Server Envelope Stuffings</a>
		</td></tr></table></td>
	</tr>
</table>
</form>
<? }else{ ?>
<form method='get'>
<table align='center'>
	<tr>
		<td><input name="packet" value="Packet #" onClick="value=''"> <input type="submit" name="submit" value="GO"></td>
	</tr>
</table>
</form>
<?  } 
?>