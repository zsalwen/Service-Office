<?
if ($_POST[submit]){
	$link="http://staff.mdwestserve.com/".$_POST[card]."?".$_POST[type].'=1&start='.$_POST[start].'&stop='.$_POST[stop];
	echo "<script>window.open('".$link."','".$_POST[card]." Print Range')</script>";
}
?>
<form method="post">
<table align="center">
<tr>
	<td colspan='4' align="center"><h2>MAIL PRINT RANGE</h2></td>
</tr>
<tr>
	<td><select name='card'><option value='greenMaster.php'>Green Cards</option><option value='whiteMaster.php'>White Cards</option><option value='envelopePrint.php'>Envelopes</option></select></td>
	<td><select name='type'><option>OTD</option><option>EV</option></select></td>
	<td><input name="start" onclick="value=''" value='<? if ($_POST[start]){ echo $_POST[start];}else{ echo 'Start';}?>'></td>
	<td><input name="stop" onclick="value=''" value='<? if ($_POST[stop]){ echo $_POST[stop];}else{ echo 'Stop';}?>'></td>
</tr>
<tr>
	<td colspan='4' align="center"><input type="submit" name="submit" value="GO"></td>
</tr>
</table>
</form>