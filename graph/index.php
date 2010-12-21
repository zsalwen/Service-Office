<?
include "functions.php";
?>
<link rel="stylesheet" type="text/css" href="../fire.css" />
<style>
td {text-align:center;}
iframe {border;0px; margin:0px; padding:0px;}
</style>
<table align="center" width='80%'>

<tr><td><iframe width='1210' height='490' src="http://staff.mdwestserve.com/graph/OTDcostGraph.php"></iframe></td>
</tr>
<tr><td><iframe width='1210' height='490' src="http://staff.mdwestserve.com/graph/OTDreceivedGraph.php"></iframe></td>
</tr>
<tr><td><iframe width='1210' height='490' src="http://staff.mdwestserve.com/graph/OTDfiledGraph.php"></iframe></td>
</tr>

<tr><td><iframe width='1210' height='490' src="http://staff.mdwestserve.com/graph/OTDexportGraph.php"></iframe></td>
</tr>
<tr><td><iframe width='1210' height='490' src="http://staff.mdwestserve.com/graph/EVreceivedGraph.php"></iframe></td>
</tr>
<tr><td><iframe width='1210' height='490' src="http://staff.mdwestserve.com/graph/EVfiledGraph.php"></iframe></td>
</tr>
<tr><td><iframe width='1210' height='490' src="http://staff.mdwestserve.com/graph/EVexportGraph.php"></iframe></td>
</tr>

<tr><td><img src="http://staff.mdwestserve.com/graph/cost.php?year=2008&attid=<?=$_GET[attid];?>"></td>
</tr>
<tr><td><img src="http://staff.mdwestserve.com/graph/time.php?year=2008&type=intake"></td>
</tr>
<tr><td><img src="http://staff.mdwestserve.com/graph/time.php?year=2008"></td>
</tr>
<tr><td><img src="http://staff.mdwestserve.com/graph/time.php?year=2008&src=debug&type=intake"></td>
</tr>
<tr><td><img src="http://staff.mdwestserve.com/graph/time.php?year=2008&type=intake&src=eviction"></td>
</tr>
<tr><td><img src="http://staff.mdwestserve.com/graph/time.php?year=2008&src=eviction"></td>
</tr>
</table>