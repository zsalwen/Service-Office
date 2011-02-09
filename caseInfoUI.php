<style>
td { font-size:10px;}

</style>
<?
mysql_connect();
mysql_select_db('service');

if ($_GET[complete]){
	@mysql_query("update watchDog set status = 'Search Complete' where watchID = '$_GET[complete]'");
	error_log("[".date('r')."] [caseInfoUI] [Search Complete] [$_GET[complete]] \n", 3, '/logs/webservice.log');

}
if ($_GET[restart]){
	@mysql_query("update watchDog set status = 'Case Watch Started' where watchID = '$_GET[restart]'");
	error_log("[".date('r')."] [caseInfoUI] [Search Restart] [$_GET[restart]] \n", 3, '/logs/webservice.log');
	
}


if ($_POST[firstName] && $_POST[lastName] && $_POST[county]){

@mysql_query("insert into watchDog (packetID, defID, firstName, lastName, county) values ('$_POST[packetID]', '$_POST[defID]', '$_POST[firstName]', '$_POST[lastName]', '$_POST[county]')");
	error_log("[".date('r')."] [caseInfoUI] [Search Start] [$_POST[firstName]] [$_POST[lastName]] [$_POST[county]] \n", 3, '/logs/webservice.log');


}
if ($_GET[inactive]){
	$cR = @mysql_query("select * from watchDog WHERE status='Search Complete' order by status DESC");
}else{
	$cR = @mysql_query("select * from watchDog WHERE status <> 'Search Complete' order by status DESC");
}
?>
<table><tr><td valign="top">

<table border="1" width="100%">
	<? if($_GET[inactive]){ ?>
	<tr>
		<td colspan='10' align='center'><a href='watchList.php'>Active Searches</a></td>
	</tr>
	<? }else{ ?>
	<tr>
		<td colspan='10' align='center'><a href='watchList.php?inactive=1'>Inactive Searches</a></td>
	</tr>
	<? } ?>
	<tr>
		<td>packetID</td>
		<td>defID</td>
		<td>firstName</td>
		<td>lastName</td>
		<td>county</td>
		<td>response</td>
		<td>watchStart</td>
		<td>status</td>
		<td>actions</td>
		<td>lastChecked</td>
		<td>lastResult</td>
	</tr>
<form action="watchList.php" method="post">
	<tr>
		<td><input size="10" name="packetID"></td>
		<td><input size="10" name="defID"></td>
		<td><input size="20" name="firstName"></td>
		<td><input size="20" name="lastName"></td>
		<td><input size="30" name="county"></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td><input type="submit" value="Add to watch list."></td>
		<td></td>
		<td></td>
	</tr>
</form>	
<? while($cD = mysql_fetch_array($cR,MYSQL_ASSOC)){ ?>
	<tr>
		<td><?=strtoupper($cD[packetID]);?></td>
		<td><?=strtoupper($cD[defID]);?></td>
		<td><?=strtoupper($cD[firstName]);?></td>
		<td><?=strtoupper($cD[lastName]);?></td>
		<td><?=strtoupper($cD[county]);?></td>
		<td><?=strtoupper($cD[response]);?></td>
		<td><?=strtoupper($cD[watchStart]);?></td>
		<td><?=strtoupper($cD[status]);?></td>
		<td><? if ($cD[status] != 'Search Complete'){ ?><a href="?complete=<?=$cD[watchID];?>">END SEARCH</a><? }else{ ?><a href="?restart=<?=$cD[watchID];?>">RESTART SEARCH</a><? } ?></td>
		<td><?=strtoupper($cD[lastChecked]);?></td>
		<td><?=strtoupper($cD[lastResult]);?></td>
	</tr>
<? } ?>
</table>
</td><td valign="top">
<table border="1" align="center">
	<tr>
		<td>Case Number</td>
		<td>Status</td>
	</tr>
<? 
$r = @mysql_query("select caseNumber, status from marylandCaseData where status = 'Active' order by caseNumber");
while($d = mysql_fetch_array($r,MYSQL_ASSOC)){ ?>
	<tr>
		<td><?=strtoupper($d[caseNumber]);?></td>
		<td><?=strtoupper($d[status]);?></td>
	</tr>
<? } ?>
</table>
</td></tr></table>