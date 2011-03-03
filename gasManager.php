<?
mysql_connect();
mysql_select_db('core');
include 'common.php';

//Submit
if ($_POST[submit]){
	$id=$_COOKIE[psdata][user_id];
	@mysql_query("INSERT into gasRates (gasPrice, client_rate, contractor_rate, entryDate, entryID) VALUES ('$_POST[gasPrice]', '$_POST[client_rate]', '$_POST[contractor_rate]', NOW(), '$id')") or die(mysql_error());
}

//Enter New Rate
echo "<form method='post'><table align='center' border='1' style='border-collapse:collapse;'>
	<tr><td colspan='2' align='center'><b>ENTER RATE</b></td></tr>
	<tr><td>Gas Price</td><td><input name='gasPrice'></td></tr>
	<tr><td>Client Rate</td><td><input name='client_rate'></td></tr>
	<tr><td>Contractor Rate</td><input name='contractor_rate'></tr>
	<tr><td colspan='2' align='center'><input type='submit' name='submit' value='GO!'></td></tr>
	</table></form>";
	
//List of Previous Rates
echo "<table align='center' border='1' style='border-collapse:collapse;'><tr><td>#</td><td>Gas Price</td><td>Client Rate</td><td>Contractor Rate</td><td>Entry Date</td><td>Entered By</td></tr>";
$i=0;
$q="SELECT * FROM gasRates ORDER BY id DESC";
$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){$i++;
	echo "<tr><td>$i</td><td>$d[gasPrice]</td><td>$d[client_rate]</td><td>$d[contractor_rate]</td><td>$d[entryDate]</td><td>".id2name($d[entryID])."</td></tr>";
}
echo "</table>";
?>