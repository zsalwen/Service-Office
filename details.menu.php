<?
$packet = $_GET[packet];
$rLegacy=@mysql_query("select * from sync where to_id = '$packet' ");
$dLegacy=mysql_fetch_array($rLegacy,MYSQL_ASSOC);
if($dLegacy[id]){
?>
<hr>
<small>a.k.a. <?=$dLegacy[product]?> <?=$dLegacy[from_id]?></small>
<hr>
<? } ?>
<table width="100%"  style="padding:0px; font-size: 11px;">
<tr>
<td align="center">
<? if (!$d[uspsVerify]){?><a href="supernova.php?packet=<?=$d[id]?>" target="preview">!!!Verify Addresses!!!</a><? }else{ ?><img src="http://www.usps.com/common/images/v2header/usps_hm_ci_logo2-159x36x8.gif" ><br>Verified by <? echo $d[uspsVerify]; } ?>
<?
// $deadline needs to be dynamic at some point
$received=strtotime($d[date_received]);
$deadline=$received+432000;
$deadline=date('F jS Y',$deadline);
$estFileDate=fileDate($d[estFileDate]);
$days=number_format((time()-$received)/86400,0);
$hours=number_format((time()-$received)/3600,0);
?>
 </td>
</tr><tr>
<td align="center">
<? if(!$d[caseVerify]){ ?> <a href="validateCase.php?case=<?=$d[case_no]?>&packet=<?=$d[id]?>&county=<?=$d[circuit_court]?>" target="preview">!!!Verify Case Number!!!</a><? }else{ ?><img src="http://www.courts.state.md.us/newlogosm.gif"><br>Verified by <? echo $d[caseVerify]; }?>
</td></tr><tr><td align="center">
<? if(!$d[qualityControl]){ ?> <a href="entryVerify.php?packet=<?=$d[id]?><? if ($d[service_status] == 'MAIL ONLY'){ echo '&matrix=1';} ?>&frame=no" target="preview">!!!Verify Data Entry!!!</a><? }else{ ?><img src="http://staff.mdwestserve.com/small.logo.gif" height="41" width="41"><br>Verified by <? echo $d[qualityControl]; }?>
</td></tr><tr><td align="center"><div style="font-size:15pt" ><?=$hours?> Hours<br> <?=$days?> Days<br>Serve Due:<br> <?=$estFileDate?><div></td></tr></table>

<hr>


<table border="1">
<tr>
<td><a href="http://staff.mdwestserve.com/sync.php?packet=<?=$packet?>" target="pane1"><img src="http://connect.stern.nyu.edu/zimbra/img/startup/ImgLeftArrow.gif" border="0"></a> Sync <a href="http://staff.mdwestserve.com/sync.php?packet=<?=$packet?>" target="pane2"><img src="http://connect.stern.nyu.edu/zimbra/img/startup/ImgRightArrow.gif" border="0"></a></td>
</tr>
<tr>
<td><a href="http://staff.mdwestserve.com/notes.php?packet=<?=$packet?>" target="pane1"><img src="http://connect.stern.nyu.edu/zimbra/img/startup/ImgLeftArrow.gif" border="0"></a> Notes <a href="http://staff.mdwestserve.com/notes.php?packet=<?=$packet?>" target="pane2"><img src="http://connect.stern.nyu.edu/zimbra/img/startup/ImgRightArrow.gif" border="0"></a></td>
</tr>
<tr>
<td> <a href="http://staff.mdwestserve.com/instruction.php?packet=<?=$packet?>" target="pane1"><img src="http://connect.stern.nyu.edu/zimbra/img/startup/ImgLeftArrow.gif" border="0"></a> Instructions <a href="http://staff.mdwestserve.com/instruction.php?packet=<?=$packet?>" target="pane2"><img src="http://connect.stern.nyu.edu/zimbra/img/startup/ImgRightArrow.gif" border="0"></a></td>
</tr>
<tr>
<td><a href="http://staff.mdwestserve.com/edit.php?packet=<?=$packet?>" target="pane1"><img src="http://connect.stern.nyu.edu/zimbra/img/startup/ImgLeftArrow.gif" border="0"></a> Edit Data <a href="http://staff.mdwestserve.com/edit.php?packet=<?=$packet?>" target="pane2"><img src="http://connect.stern.nyu.edu/zimbra/img/startup/ImgRightArrow.gif" border="0"></a></td>
</tr>
<tr>
<td><a href="http://staff.mdwestserve.com/upload.php?packet=<?=$packet?>" target="pane1"><img src="http://connect.stern.nyu.edu/zimbra/img/startup/ImgLeftArrow.gif" border="0"></a> Upload Inbox  <a href="http://staff.mdwestserve.com/upload.php?packet=<?=$packet?>" target="pane2"><img src="http://connect.stern.nyu.edu/zimbra/img/startup/ImgRightArrow.gif" border="0"></a></td>
</tr>
<tr>
<td><a href="http://staff.mdwestserve.com/dropbox.php?packet=<?=$packet?>" target="pane1"><img src="http://connect.stern.nyu.edu/zimbra/img/startup/ImgLeftArrow.gif" border="0"></a> Attachments  <a href="http://staff.mdwestserve.com/dropbox.php?packet=<?=$packet?>" target="pane2"><img src="http://connect.stern.nyu.edu/zimbra/img/startup/ImgRightArrow.gif" border="0"></a></td>
</tr>
</table>

<hr>


<LEGEND ACCESSKEY=C>Legacy Service Links</LEGEND>
<table style="padding:0px; font-weight:bold; border-collapse:collapse; height:150px !important; font-size:11px;" cellpadding="0" cellspacing="0">

	<tr>
		<td><a href="http://staff.mdwestserve.com/otd/minips_pay.php?id=<?=$d[packet_id]?>" target="preview">Payments</a></td>
	</tr>

	<tr>
		<td><a href="historyModify.php?packet=<?=$d[packet_id]?>&form=1" target="preview">History (<?=$server?>)</a></td>
	</tr>
	<tr>
		<td><a href="http://service.mdwestserve.com/customInstructions.php?packet=<?=$d[packet_id]?>" target="preview">Instructions <?=id2attorney($d[attorneys_id])?></a>-<a href="instructMatrix.php?packet=<?=$d[packet_id]?>" <?=$customBG?> target="preview"><small>[CUSTOMIZE]</small></a></td>
	</tr>
	<tr>
		<td><a href="<?=$otdStr?>" target="preview">OTD</a> | <a href="serviceReview.php?packet=<?=$d[packet_id]?>" target="preview">Timeline</a> | <a href="<?=$checkLink?>" target="_blank">Checklist</a></td>
	</tr>
	<tr>
		<td><a href="../photoDisplay.php?packet=<?=$d[packet_id]?>" target="preview"><?$photoCount=photoCount($d[packet_id]); echo $photoCount;?> Photo<? if($photoCount != 1){echo "s";}?></a></td>
	</tr>
	<tr>
		<td><a href="http://staff.mdwestserve.com/penalize.php?packet=<?=$d[eviction_id]?>&svc=OTD&display=1" target="preview">Penalties</a></td>
	</tr>
	<tr>
		<td><a href="mailings.php?OTD=<?=$d[packet_id]?>" target="preview">Mailings</a><? 	if (webservice($d[client_file]) && ($d[attorneys_id] == 1)){ ?> | <a href="http://staff.mdwestserve.com/otd/webservice.php?fileNumber=<?=$d[client_file];?>" target="preview">Webservice Data</a><? }?></td>
	</tr>
	<?
$FC = trim(getPage("http://data.mdwestserve.com/findFC.php?clientFile=$d[client_file]", "MDWS File Copy for Packet $d[packet_id]", '5', ''));
if ($FC != '' && $FC != '1'){
	echo "<tr><td>$FC</td></tr>";
}
$folder=getFolder($d[otd]);
$rfm='/data/service/orders/'.$folder.'/RequestforMediation.pdf';
$trioAff='/data/service/orders/'.$folder.'/TrioAffidavitService.pdf';
if (file_exists($rfm)){
	echo "<tr><td><a href='http://mdwestserve.com/PS_PACKETS/$folder/RequestforMediation.pdf' target='preview'>Request For Mediation</a></td></tr>";
}
if (file_exists($trioAff)){
	echo "<tr><td><a href='http://mdwestserve.com/PS_PACKETS/$folder/TrioAffidavitService.pdf' target='preview'>Trio Aff</a></td></tr>";
}
if ($dc[packet_id]){
	echo "<tr><td><a href='http://staff.mdwestserve.com/otd/serviceCertificate.php?packet=$d[packet_id]' target='preview'>Certificate of Service</a></td></tr>";
}
	?>
	
</table>
</FIELDSET>


<hr>


<strong>
	<div align="center" style="background-color:#FFFF00">
    	<a onClick="hideshow(document.getElementById('track'))">Tracking</a> &curren; 
    	<a onClick="hideshow(document.getElementById('addresses'))">Legacy Addresses</a> &curren; 
    	<a onClick="hideshow(document.getElementById('pobox'))">Legacy Mail Only</a> &curren; 
    	<a onClick="hideshow(document.getElementById('status'))">Status</a> &curren; 
        <a onClick="hideshow(document.getElementById('servers'))">Legacy Servers</a> &curren; 
        <a onClick="hideshow(document.getElementById('notes'))">Notes</a> &curren; 
    </div>
</strong>