<b>Overall File Settings</b>
<form method="post">
<? if ($_POST[bypass]){echo "<input type='hidden' name='bypass' value='1'>";}?>
<input type="hidden" name="packet" value="<?=$packet?>">
<input type="hidden" name="i" value="5" />
<table align="center" style="padding:0px;" width="100%">
	<tr>
	<small><?=defCheckList($packet);?></small>
	</tr>
	<tr>
		<td>No. of Attempts<br><div align="center" style="font-size:10px;" size='3'>(# or unlimited)</div></td>
		<td><input name="attempts" value="<?=$ddr[attempts]?>"></td>
		<td>Allow Posting?</td>
		<td><input type="checkbox" name="allowPosting" <? if ($ddr[allowPosting] == 'checked'){ echo "checked";} ?> value="checked"></td>
	</tr>
	<tr>
		<td>Photographs</td>
		<td><input name="photograph" value="<?if ($ddr[photograph]){ echo $ddr[photograph];}else{ echo "Only Posting"; }?>" size="20"></td>
		<td>Post on Separate Day?</td>
		<td><input type="checkbox" name="postSeparateDay" <? if ($ddr[postSeparateDay] == 'checked'){ echo "checked";} ?> value="checked"></td>
	</tr>
	<tr>
		<td>Affidavit</td>
		<td><input name="affidavitTemplate" value="<? if ($ddr[affidavitTemplate]){ echo $ddr[affidavitTemplate];}else{ echo "obAffidavit"; }?>" size="20"></td>
		<td>Allow Substitute Service?</td>
		<td><input type="checkbox" name="allowSubService" <? if ($ddr[allowSubService] == 'checked'){ echo "checked";} ?> value="checked"></td>	
	</tr>
</table>
<table align="center" style="padding:0px;" width="100%">
	<tr>
		<td>Help</td>
		<td><textarea rows="3" cols="50" name="help"><?=$ddr["help"]?></textarea></td>
	</tr>
</table>
<table align="center" style="padding:0px;" width="100%">
	<tr>
		<? if ($_POST[bypass]){?><td align='center'><a href='http://service.mdwestserve.com/customInstructions.php?packet=<?=$packet?>'>BACK TO PREVIEW</a></td><? } ?>
		<td align="right"><input type="submit" name="submit" value="GO!"></td>
	</tr>
</table>
</form>