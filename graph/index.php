<?
include "functions.php";
?>
<script type="text/javascript">
function checkAllFields(ref)
{
var chkAll = document.getElementById('checkAll');
var checks = document.getElementsByName('delAnn[]');
var removeButton = document.getElementById('removeChecked');
var boxLength = checks.length;
var allChecked = false;
var totalChecked = 0;
	if ( ref == 1 )
	{
		if ( chkAll.checked == true )
		{
			for ( i=0; i < boxLength; i++ )
			checks[i].checked = true;
		}
		else
		{
			for ( i=0; i < boxLength; i++ )
			checks[i].checked = false;
		}
	}
	else
	{
		for ( i=0; i < boxLength; i++ )
		{
			if ( checks[i].checked == true )
			{
			allChecked = true;
			continue;
			}
			else
			{
			allChecked = false;
			break;
			}
		}
		if ( allChecked == true )
		chkAll.checked = true;
		else
		chkAll.checked = false;
	}
	for ( j=0; j < boxLength; j++ )
	{
		if ( checks[j].checked == true )
		totalChecked++;
	}
	removeButton.value = "Remove ["+totalChecked+"] Selected";
}
</script>
<link rel="stylesheet" type="text/css" href="../fire.css" />
<style>
td {text-align:center; color:white;}
iframe {border;0px; margin:0px; padding:0px;}
</style>
<form name="form1">
<table align="center" width="50%" border="1" style='border-collapse:collapse;'>
	<tr>
		<td align="center" style='font-size:18px; color:white;' colspan='3'>SELECT GRAPHS TO DISPLAY</td>
	</tr>
	<tr>
		<td align="center" style='font-size:18px; color:white;' colspan='3'>GOOGLE GRAPHS</td>
	</tr>
	<tr>
		<td><input type='checkbox' name='cost1' value='checked' <?=$_GET[cost1]?>> Costs</td>
		<td><input type='checkbox' name='OTDrec' value='checked' <?=$_GET[OTDrec]?>> Presale Files Received</td>
		<td><input type='checkbox' name='OTDfiled' value='checked' <?=$_GET[OTDfiled]?>> Presale File Dates</td>
	</tr>
	<tr>
		<td><input type='checkbox' name='OTDexp' value='checked' <?=$_GET[OTDexp]?>> Presale Exports</td>
		<td><input type='checkbox' name='EVrec' value='checked' <?=$_GET[EVrec]?>> Eviction Files Received</td>
		<td><input type='checkbox' name='EVfiled' value='checked' <?=$_GET[EVfiled]?>> Eviction File Dates</td>
	</tr>
	<tr>
		<td><input type='checkbox' name='EVexp' value='checked' <?=$_GET[EVexp]?>> Eviction Exports</td>
	</tr>
	<tr>
		<td align="center" style='font-size:18px; color:white;' colspan='3'>LINE GRAPH GENERATOR GRAPHS</td>
	</tr>
	<tr>
		<td><input type='checkbox' name='cost2' value='checked' <?=$_GET[cost2]?>> Costs</td>
		<td><input type='checkbox' name='OTDrec2' value='checked' <?=$_GET[OTDrec2]?>> Presale Files Received</td>
		<td><input type='checkbox' name='OTDfiled2' value='checked' <?=$_GET[OTDfiled2]?>> Presale File Dates</td>
	</tr>
	<tr>
		<td><input type='checkbox' name='OTDexp2' value='checked' <?=$_GET[OTDexp2]?>> Presale Exports</td>
		<td><input type='checkbox' name='EVrec2' value='checked' <?=$_GET[EVrec2]?>> Eviction Files Received</td>
		<td><input type='checkbox' name='EVfiled2' value='checked' <?=$_GET[EVfiled2]?>> Eviction File Dates</td>
	</tr>
	<tr>
		<td colspan='3'><input type='checkbox' name='checkAll' value='checked' onclick='checkAllFields(1);'> Check All | <input type='submit' name='Submit' value='GO!'></td>
	</tr>
</table>
</form>
<table align="center" width='80%'>
<? if ($_GET[cost1]){ ?>
<tr><td><iframe width='1210' height='490' src="http://staff.mdwestserve.com/graph/OTDcostGraph.php"></iframe></td>
</tr>
<? }
if ($_GET[OTDrec]){ ?>
<tr><td><iframe width='1210' height='490' src="http://staff.mdwestserve.com/graph/OTDreceivedGraph.php"></iframe></td>
</tr>
<? }
if ($_GET[OTDfiled]){ ?>
<tr><td><iframe width='1210' height='490' src="http://staff.mdwestserve.com/graph/OTDfiledGraph.php"></iframe></td>
</tr>
<? }
if ($_GET[OTDexp]){ ?>
<tr><td><iframe width='1210' height='490' src="http://staff.mdwestserve.com/graph/OTDexportGraph.php"></iframe></td>
</tr>
<? }
if ($_GET[EVrec]){ ?>
<tr><td><iframe width='1210' height='490' src="http://staff.mdwestserve.com/graph/EVreceivedGraph.php"></iframe></td>
</tr>
<? }
if ($_GET[EVfiled]){ ?>
<tr><td><iframe width='1210' height='490' src="http://staff.mdwestserve.com/graph/EVfiledGraph.php"></iframe></td>
</tr>
<? }
if ($_GET[EVexp]){ ?>
<tr><td><iframe width='1210' height='490' src="http://staff.mdwestserve.com/graph/EVexportGraph.php"></iframe></td>
</tr>
<? }
if ($_GET[cost2]){ ?>
<tr><td><img src="http://staff.mdwestserve.com/graph/cost.php?year=2008&attid=<?=$_GET[attid];?>"></td>
</tr>
<? }
if ($_GET[OTDrec2]){ ?>
<tr><td><img src="http://staff.mdwestserve.com/graph/time.php?year=2008&type=intake"></td>
</tr>
<? }
if ($_GET[OTDfiled2]){ ?>
<tr><td><img src="http://staff.mdwestserve.com/graph/time.php?year=2008"></td>
</tr>
<? }
if ($_GET[OTDexp2]){ ?>
<tr><td><img src="http://staff.mdwestserve.com/graph/time.php?year=2008&src=debug&type=intake"></td>
</tr>
<? }
if ($_GET[EVrec2]){ ?>
<tr><td><img src="http://staff.mdwestserve.com/graph/time.php?year=2008&type=intake&src=eviction"></td>
</tr>
<? }
if ($_GET[EVfiled2]){ ?>
<tr><td><img src="http://staff.mdwestserve.com/graph/time.php?year=2008&src=eviction"></td>
</tr>
<? } ?>
</table>