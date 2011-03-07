<?
$packet = $_GET[packet];
?>
<table border="1">
<tr>
<td><a href="http://staff.mdwestserve.com/instruction.php?packet=<?=$packet?>" target="pane1">Instructions</a></td>
</tr>
<tr>
<td><a href="http://staff.mdwestserve.com/edit.php?packet=<?=$packet?>" target="pane1">Edit Data</a></td>
</tr>
<tr>
<td><a href="http://staff.mdwestserve.com/upload.php?packet=<?=$packet?>" target="pane1">Upload Inbox</a></td>
</tr>
<? /*
<tr>
<td><a href="http://staff.mdwestserve.com/otd/minips_pay.php?id=<?=$d[packet_id]?>" target="preview">Payments</a></td>
</tr>
<tr>
<td><a href="http://staff.mdwestserve.com/standardExport.php?packet=<?=$d[packet_id]?>" target="preview">Transfer</a></td>
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
<td><a href="mailings.php?OTD=<?=$d[packet_id]?>" target="preview">Mailings</a><? if (webservice($d[client_file]) && ($d[attorneys_id] == 1)){ ?> | <a href="http://staff.mdwestserve.com/otd/webservice.php?fileNumber=<?=$d[client_file];?>" target="preview">Webservice Data</a><? }?></td>
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
*/
?>
</table>