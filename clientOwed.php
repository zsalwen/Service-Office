<?
mysql_connect();
mysql_select_db('service');
include "common.php";
function mkmonth($keep){
	//if (!$keep){$keep = date('M');}
	$opt = "<option selected value='$keep'>$keep</option>";
	$opt .= "<option value='01'>1</option>";
	$opt .= "<option value='02'>2</option>";
	$opt .= "<option value='03'>3</option>";
	$opt .= "<option value='04'>4</option>";
	$opt .= "<option value='05'>5</option>";
	$opt .= "<option value='06'>6</option>";
	$opt .= "<option value='07'>7</option>";
	$opt .= "<option value='08'>8</option>";
	$opt .= "<option value='09'>9</option>";
	$opt .= "<option value='10'>10</option>";
	$opt .= "<option value='11'>11</option>";
	$opt .= "<option value='12'>12</option>";
	return $opt;
}
function mkyear($keep){
	$opt = "<option selected value='$keep'>$keep</option>";
	$opt .= "<option value='2006'>2006</option>";
	$opt .= "<option value='2007'>2007</option>";
	$opt .= "<option value='2008'>2008</option>";
	$opt .= "<option value='2009'>2009</option>";
	$opt .= "<option value='2010'>2010</option>";
	$opt .= "<option value='2011'>2011</option>";
	return $opt;
}
function monthConvert($month){
	if ($month == '01'){ return 'January'; }
	if ($month == '02'){ return 'February'; }
	if ($month == '03'){ return 'March'; }
	if ($month == '04'){ return 'April'; }
	if ($month == '05'){ return 'May'; }
	if ($month == '06'){ return 'June'; }
	if ($month == '07'){ return 'July'; }
	if ($month == '08'){ return 'August'; }
	if ($month == '09'){ return 'September'; }
	if ($month == '10'){ return 'October'; }
	if ($month == '11'){ return 'November'; }
	if ($month == '12'){ return 'December'; }
}

?>
<style>
a{text-decoration:none; color: red;}
</style>
<form><table align="center">
	<tr>
		<td align="center">MONTH/YEAR<br><select name="month"><?=mkmonth($_GET[month])?></select> <select name="year"><?=mkyear($_GET[year])?></select></td>
		<td align="center">ATTORNEY<br><select name="attid"><option value="ALL">ALL</option>
		<?
		$q1="SELECT DISTINCT attorneys_id FROM ps_packets WHERE attorneys_id <> ''";
		$r1=@mysql_query($q1) or die(mysql_error());
		while ($d1=mysql_fetch_array($r1,MYSQL_ASSOC)){
			echo "<option value='$d1[attorneys_id]'>".id2attorney($d1[attorneys_id])."</option>";
		}
		?>
		</select></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><input type="submit" name="submit" value="GO!"></td>
	</tr>
	<tr>
		<td align="center" valign='top'>OR</td>
		<td align="center" valign='top'><a href="?all=1">VIEW ALL</a></td>
	</tr>
</table></form>
<?
if ($_GET[attid]){
	$date=$_GET[year]."-".$_GET[month];
	if ($_GET[attid] == "ALL"){
		$q="SELECT packet_id, attorneys_id, bill410, bill420, bill430, client_paid, client_paida, client_paidb, client_paidc, client_paidd, client_paide FROM ps_packets WHERE (process_status <> 'DAMAGED PDF' AND process_status <> 'FILE COPY') AND date_received LIKE '%$date%' order by packet_id ASC";
	}else{
		$q="SELECT packet_id, attorneys_id, bill410, bill420, bill430, client_paid, client_paida, client_paidb, client_paidc, client_paidd, client_paide FROM ps_packets WHERE (process_status <> 'DAMAGED PDF' AND process_status <> 'FILE COPY') AND date_received LIKE '%$date%' and attorneys_id='".$_GET[attid]."' order by packet_id ASC";
	}
}elseif($_GET[all]){
	$date=$_GET[year]."-".$_GET[month];
	$q="SELECT packet_id, attorneys_id, bill410, bill420, bill430, client_paid, client_paida, client_paidb, client_paidc, client_paidd, client_paide FROM ps_packets WHERE (process_status <> 'DAMAGED PDF' AND process_status <> 'FILE COPY') AND date_received LIKE '%$date%' order by packet_id ASC";
}
echo "<table align='center' style='border-collapse:collapse;' border='1'><tr><td>Packet ID</td><td>Client Owes</td><td>Client Paid</td><td>Remainder</td></tr>";
$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
$totalRemainder=0;
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$owed=$d[bill410]+$d[bill420]+$d[bill430];
	$paid=$d[client_paid]+$d[client_paida]+$d[client_paidb]+$d[client_paidc]+$d[client_paidd]+$d[client_paide];
	$remainder=$owed-$paid;
	$totalRemainder=$totalRemainder+$remainder;
	echo "<tr><td><a href='/otd/order.php?packet=$d[packet_id]' target='_blank'>$d[packet_id]</a></td><td>$owed</td><td>$paid</td><td>$remainder</td></tr>";
}
echo "<tr><td colspan='3' align='right'>Total Remainder:</td><td>$totalRemainder</td></tr>";
echo "</table>";
?>