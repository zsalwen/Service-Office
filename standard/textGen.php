<?
include 'common.php';

if (isset($_GET['packet'])){
$q="SELECT * from standard_packets WHERE packet_id='$_GET[packet]'";
$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
$d=mysql_fetch_array($r, MYSQL_ASSOC);

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
		$addList2[$i] = "1 address in ".strtoupper($d[city1]).", ".strtoupper($d[state1]);
		$addCount++;
		$i++;
	}
	if ($d['server_ida'] == $serverSlot){
		$addList = strtoupper($d[address1a])."<br>".strtoupper($d[city1a]).", ".strtoupper($d[state1a])." ".$d[zip1a]."<br>";
		$addList2[$i] = "1 address in ".strtoupper($d[city1a]).", ".strtoupper($d[state1a]);
		$addCount++;
		$i++;
	}
	if ($d['server_idb'] == $serverSlot){
		if ($addList != ''){	
			$addList .= "     --AND--<br>";
		}
		$addList .= strtoupper($d[address1b])."<br>".strtoupper($d[city1b]).", ".strtoupper($d[state1b])." ".$d[zip1b]."<br>";
		$addList2[$i] .= "1 address in ".strtoupper($d[city1b]).", ".strtoupper($d[state1b]);
		$addCount++;
		$i++;
	}
	if ($d['server_idc'] == $serverSlot){
		if ($addList != ''){	
			$addList .= "     --AND--<br>";
		}
		$addList .= strtoupper($d[address1c])."<br>".strtoupper($d[city1c]).", ".strtoupper($d[state1c])." ".$d[zip1c]."<br>";
		$addList2[$i] .= "1 address in ".strtoupper($d[city1c]).", ".strtoupper($d[state1c]);
		$addCount++;
		$i++;
	}
	if ($d['server_idd'] == $serverSlot){
		if ($addList != ''){	
			$addList .= "     --AND--<br>";
		}
		$addList .= strtoupper($d[address1d])."<br>".strtoupper($d[city1d]).", ".strtoupper($d[state1d])." ".$d[zip1d]."<br>";
		$addList2[$i] .= "1 address in ".strtoupper($d[city1d]).", ".strtoupper($d[state1d]);
		$addCount++;
		$i++;
	}
	if ($d['server_ide'] == $serverSlot){
		if ($addList != ''){	
			$addList .= "     --AND--<br>";
		}
		$addList .= strtoupper($d[address1e])."<br>".strtoupper($d[city1e]).", ".strtoupper($d[state1e])." ".$d[zip1e]."<br>";
		$addList2[$i] .= "1 address in ".strtoupper($d[city1e]).", ".strtoupper($d[state1e]);
		$addCount++;
	}
	
	$addList3='a file with ';
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
	//echo "<script>window.open('".$instructionLink."&autoSave=1','Service Instructions')</script>";
	//echo "<script>window.open('otdSave.php?packet=".$d[packet_id]."');</script>";
	//echo "<script>window.open('instructionSave.php?packet=".$d[packet_id]."');</script>";
	
}
$userID=$_COOKIE[psdata][user_id];
$q3="SELECT * FROM ps_users where id='$userID'";
$r3=@mysql_query($q3) or die ("Query: $q3<br>".mysql_error());
$d3=mysql_fetch_array($r3, MYSQL_ASSOC);
$now = date ('H');

if ($now < 12)
$dayTime='morning';
else if ($now < 18)
$dayTime='afternoon';
else if ($now >= 18 )
$dayTime='evening';
$otdStr=str_replace('portal//var/www/dataFiles/service/orders/','standard_packets/',$d[otd]);
$otdStr=str_replace('data/service/orders/','standard_packets/',$otdStr);
$otdStr=str_replace('portal/','',$otdStr);
$otdStr2=str_replace('http://mdwestserve.com','',$d[otd]);
$otdStr2=str_replace('standard_packets/','data/service/orders/',$otdStr2);
$instructionStr='/data/service/unknown/Service Instructions For Packet '.$d['packet_id'].'.PDF';
$instructionLink="http://mdwestserve.com/ps/customInstructions";
if ($d[attorneys_id] == '1'){
	$instructionLink .= ".burson";
}elseif($d['attorneys_id'] == "56"){
	$instructionLink .= ".brennan";
}
$instructionLink .= ".php?packet=".$d[packet_id];
if ($d['attorneys_id'] == '70'){
	$additional="Additionally, if the address is a business, appears vacant, or the defendants are not known at the address, do not make a second attempt, but instead prepare an affidavit of non-service.<br>";
}elseif($d['attorneys_id'] == '1'){
	$additional="Additionally, if the address is a business, appears vacant, or the defendants are not known at the address, please contact our office at the earliest opportunity, <b>then continue with remaining attempts</b>.<br>";
}else{
	$additional='';
}
?>
<form method="post">
<table align='center'>
<? if (isset($_POST['server'])){ }else{ echo	"<tr><td align='center'>$serverSelect <input type='submit' name='submit' value='Submit'></td></tr>";}?>
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
		this <?=$dayTime?>, concerning process service on <? if (isset($_POST['server'])){echo $addList3;}else{echo "<i>ADDRESS NOT YET SELECTED</i>";}?>.<br>
		I was quoted a price of <? if (isset($_POST['server'])){echo "<i><b>INSERT SERVICE COST</b></i>"; }else{echo "<i><u>SERVER NOT YET SELECTED</u></i>";} ?>
		for two attempts on <? if ($defCount == '1'){ echo "1 defendant";}else{ echo $defCount." defendants";}?> at 
		<? if (isset($addCount) && $addCount == '1'){echo "1 address";}elseif(isset($addCount) && $addCount > 1){echo $addCount." addresses";
		}else{ echo "<i>NO SERVER SELECTED</i>";}?>, which would make the total cost of service <i><b>INSERT SERVICE TOTAL</b></i>.<br>Please also provide photographs of any attempts or personal deliveries.  If there are any difficulties with this service, please have the server directly contact our office from the field.  During business hours (9-5 EST), please contact Alex at 410-828-4569.  Patrick, our Operations Manager, can be reached after hours at (443) 386-2584, please do not hesitate to call him.<br>
		The documents for service can be listed as "a copy of the <?if ($d[addlDocs] != ''){echo $d[addlDocs];}else{ echo "Order to Docket";} ?> and all other papers filed with it in the above-captioned case".<br>
		<?=$additional;?>
		In addition to the service, it will also be necessary for two notarized affidavits to be prepared for
		<? if ($defCount == '1'){ echo "the defendant (which makes for a total of *2* affidavits)";
		}else{ echo "each defendant (which makes for a total of <b>*".(2*$defCount)."*</b> affidavits)";}?>, and mailed via USPS Express Mail to our office at:<br><br>
		MDWestServe, Inc.<br>
		300 E. Joppa Road Suite 1103<br>
		Towson, MD 21286<br><br>

<b>Packet <?=$d['packet_id']?> - Service on <?=$defNames?></b><br>
<? if (isset($_POST['server'])){echo $addList; }else{echo "<i>ADDRESS NOT YET SELECTED</i>";}?><br>
As each step of service is performed, please call our office with updates.  Also, upon completion of service, please contact us with the details of how service was completed.<br>
I have attached the documents for service to this email, along with an instruction sheet for easy reference
	(you can disregard the names of any other servers/addresses, as they only concern MD service).  Personal delivery can only be achieved in the following manner: delivery to the defendants themselves, or delivery to someone <b>over the age of 18</b> who resides with the defendant in question at the address being served.  All personal delivery affidavits should contain a physical description of the individual served.  <b>Please only serve listed addresses</b>, and if personal delivery cannot be effected, please prepare affidavits of non-service listing the dates and times of two unsuccessful attempts (made on separate days) to reach the defendant at the property in question.  Please include a physical description of the property.  IF ANY ADDITIONAL ADDRESSES ARE DISCOVERED BEYOND THOSE LISTED, <B>YOU MUST NOTIFY OUR OFFICE <U>BEFORE</U> TAKING ANY FURTHER ACTION.</B><br><br>
Lastly, around 6 PM EST you should receive a confirmation email of all files sent from our office today.  If you have any issues with the documents or instructions that we have sent you, you must contact our office within 24 hours of receipt, or your office will be held responsible for any delays.<br><br>
Please let either myself or Patrick know if there are any questions or issues.<br><br>

Thank you,<br>
<?=ucwords(strtolower($d3[name]))?><br>
<?=$d3[email]?><br>
<? if ($d3[id] == '296'){ echo "(410) 616-8881";}else{ echo "(410) 828-4569";} ?></td>
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