<?
include "functions.php";
?>
<link rel="stylesheet" type="text/css" href="../fire.css" />
<style>
td {text-align:center; color:white;}
iframe {border;0px; margin:0px; padding:0px;}
</style>
<form>
<table align="center" width="50%" border="1" style='border-collapse:collapse;'>
	<tr>
		<td align="center" style='font-size:18px; color:white;' colspan='3'>SELECT GRAPHS TO DISPLAY</td>
	</tr>
	<tr>
		<td align="center" style='font-size:18px; color:white;' colspan='3'>GOOGLE GRAPHS</td>
	</tr>
	<tr>
		<td><input type='checkbox' name='cost1' value='checked'> Costs</td>
		<td><input type='checkbox' name='OTDrec' value='checked'> Presale Files Received</td>
		<td><input type='checkbox' name='OTDfiled' value='checked'> Presale File Dates</td>
	</tr>
	<tr>
		<td><input type='checkbox' name='OTDexp' value='checked'> Presale Exports</td>
		<td><input type='checkbox' name='EVrec' value='checked'> Eviction Files Received</td>
		<td><input type='checkbox' name='EVfiled' value='checked'> Eviction File Dates</td>
	</tr>
	<tr>
		<td><input type='checkbox' name='EVexp' value='checked'> Eviction Exports</td>
	</tr>
	<tr>
		<td align="center" style='font-size:18px; color:white;' colspan='3'>LINE GRAPH GENERATOR GRAPHS</td>
	</tr>
	<tr>
		<td><input type='checkbox' name='cost2' value='checked'> Costs</td>
		<td><input type='checkbox' name='OTDrec2' value='checked'> Presale Files Received</td>
		<td><input type='checkbox' name='OTDfiled2' value='checked'> Presale File Dates</td>
	</tr>
	<tr>
		<td><input type='checkbox' name='OTDexp2' value='checked'> Presale Exports</td>
		<td><input type='checkbox' name='EVrec2' value='checked'> Eviction Files Received</td>
		<td><input type='checkbox' name='EVfiled2' value='checked'> Eviction File Dates</td>
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
</table>