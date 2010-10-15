<script>
function updateServiceInstructions(typeChar,defNum){
	var string = "http://service.mdwestserve.com/customInstructions.php?packet=<?=$packet?>&def=";
	if(defNum = '0'){
		string=string+"ALL";
	}else{
		string=string+defNum;
	}
	if (typeChar != 'REVERT'){
		string=string+"&type=";
		string=string+typeChar;
		alert(typeChar);
	}else{
		alert(typeChar);
	}
	alert(string);
	preview.location=string;
}
</script>
<?
if ($def == 'ALL'){
	$def2=0;
}else{
	$def2=$def;
}
/*$typeA = addslashes(trim(getPage("http://staff.mdwestserve.com/otd/instructGen.php?packet=$packet&def=$def&type=A", 'MDWS Instructions Type A', '5', '')));
$typeB = addslashes(trim(getPage("http://staff.mdwestserve.com/otd/instructGen.php?packet=$packet&def=$def&type=B", 'MDWS Instructions Type B', '5', '')));
$typeC = addslashes(trim(getPage("http://staff.mdwestserve.com/otd/instructGen.php?packet=$packet&def=$def&type=C", 'MDWS Instructions Type C', '5', '')));*/
?>
<form method="post">
<input type="hidden" name="packet" value="<?=$packet?>">
<? if ($_POST[def] != 'ALL'){?>
<b>Editing Defendant <?=$def?>: <?=getDef($def,$packet)?></b>
<? }else{ ?>
<b>Editing All Defendants</b>
<? } ?>
<input type="hidden" name="def" value="<?=$def?>">
<input type="hidden" name="i" value="3" />
<table align="center" style="padding:0px;">
<? if ($_POST[def] != 'ALL'){?>
	<tr>
		<td><input type="checkbox" name="serveA<?=$def?>" <? if ($ddr["serveA$def"] == 'checked'){ echo "checked";} ?> value="checked"> Serve This Defendant</td>
	</tr>
<? }else{ 
	echo defCheckList($packet);
 } ?>
	<tr>
		<td><center style="border: 1px solid; border-collapse: collapse;"><div style="font-size:12px;">Select a Service Type from the Dropdown OR Use the Freeform Below</div></center><select name="types" onchange="updateServiceInstructions(this.value,<?=$def2?>);"><option value="">SELECT</option><option value="A">Service Type "A"</option><option value="B">Service Type "B"</option><option value="C">Service Type "C"</option><option value="D">Service Type "D"</option><option value="REVERT">Revert</option></select>    <input onclick="updateServiceInstructions(types.value,<?=$def2?>);" type="button" name="instructPreview" value="Preview"></td>
	</tr>
	<tr>
		<td align="right"><input type="submit" name="submit" value="Continue"></td>
	</tr>
</table>
</form>