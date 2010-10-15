<?
session_start();
mysql_connect();
mysql_select_db('core');
function breakdown($county,$state){
	$r=@mysql_query("select county from defendants where county = '$county' and defendantstate = '$state' and packet = '' and defendantfullname <> 'ALL OCCUPANTS' and defendantfullname <> 'OCCUPANT'");
	$count=mysql_num_rows($r);
	$_SESSION[a] = $_SESSION[a] + $count;
	return "<tr><td>$county</td><td>$state</td><td>$count</td></tr>";
}
function breakdown2($state){
	$r=@mysql_query("select defendantstate from defendants where defendantstate = '$state' and packet = '' and defendantfullname <> 'ALL OCCUPANTS' and defendantfullname <> 'OCCUPANT'");
	$count=mysql_num_rows($r);
	$_SESSION[b] = $_SESSION[b] + $count;
	return "<tr><td>$state</td><td>$count</td></tr>";
}
$_SESSION[a]=0;
$_SESSION[b]=0;

?>
<center>
<div>Webservice Queue Breakdown</div>
<title>Webservice Queue Breakdown</title>
<table border="1" cellspacing="0">
<tr><td valign="top"><center>In-State Service</center>
<table border="1" cellspacing="0">
	<tr>
		<td>County</td>
		<td>State</td>
		<td>Defendant/Address Pairs</td>
	</tr>
<?
$r=@mysql_query("select distinct county, defendantstate from defendants where packet = '' and defendantfullname <> 'ALL OCCUPANTS' and defendantfullname <> 'OCCUPANT' and defendantstate = 'MD' order by defendantstate, county");
while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	echo breakdown($d[county],$d[defendantstate]);
}
$_SESSION[c] = $_SESSION[a];
?>
	<tr>
		<td>-</td>
		<td>-</td>
		<td><?=$_SESSION[a];?></td>
	</tr>
</table>
</td><td valign="top"><center>Out of State Service</center>
<table border="1" cellspacing="0">
	<tr>
		<td>County</td>
		<td>State</td>
		<td>Defendant/Address Pairs</td>
	</tr>
<?
$r=@mysql_query("select distinct county, defendantstate from defendants where packet = '' and defendantfullname <> 'ALL OCCUPANTS' and defendantfullname <> 'OCCUPANT' and defendantstate <> 'MD' order by defendantstate, county");
while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	echo breakdown($d[county],$d[defendantstate]);
}
?>
	<tr>
		<td>-</td>
		<td>-</td>
		<td><?=$_SESSION[a] - $_SESSION[c];?></td>
	</tr>
</table>
</td><td valign="top"><center>Service by State</center>
<table border="1" cellspacing="0">
	<tr>
		<td>State</td>
		<td>Defendant/Address Pairs</td>
	</tr>
<?
$r=@mysql_query("select distinct defendantstate from defendants where packet = '' and defendantfullname <> 'ALL OCCUPANTS' and defendantfullname <> 'OCCUPANT' order by defendantstate, county");
while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	echo breakdown2($d[defendantstate]);
}
?>
	<tr>
		<td>-</td>
		<td><?=$_SESSION[b];?></td>
	</tr>

</table>
</td></tr></table></center>