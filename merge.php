<?
/*if ($_POST[submit] && $_POST[src] && $_POST[dest]){
	echo "window.location.href='http://staff.mdwestserve.com/otd/temp/pdfConcat.php?src=".$_POST[src]."&dest=".$_POST[dest]."';";
}*/
?>
<form action="http://staff.mdwestserve.com/temp/pdfConcat.php">
<table align="center">
	<tr>
		<td colspan="2" align="center"><h1 style="letter-spacing: 5px;">MERGE PDFs</h1><br><span style="font-size: 12px; font-variant:small caps;">WARNING: THIS PROGRAM <i>WILL</i> REPLACE THE DESTINATION OTD FILE WITH A MERGED PDF.  USE WITH CAUTION.</span></td>
	</tr>
	<tr>
		<td>Source File (original)</td>
		<td><input name="src" value="Packet #" onclick="value=''"></td>
	</tr>
	<tr>
		<td>Destination File (supplement)</td>
		<td><input name="dest" value="Packet #" onclick="value=''"></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><input type="submit" name="submit" value="Submit"></td>
	</tr>
</table>
</form>