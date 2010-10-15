<style>
body
{
margin:0px;
padding:0px;
}
table
{
border-collapse:collapse;
background-color:#FFF;
margin:0px;
padding:0px;
}
h1
{
background-color:#FFF;
}
li
{
background-color:#FFF;
}
img
{
margin:0px;
padding:0px;
}
td
{
margin:0px;
padding:0px;
}
</style>
<?
mysql_connect();
mysql_select_db('core');
// last 30 days
function newSTANDARDService($date){
$r=@mysql_query("SELECT packet_id FROM standard_packets WHERE date_received LIKE '$date %'");
$count=mysql_num_rows($r);
return "<image src='http://hss.fullerton.edu/philosophy/Black%20Square.gif' height='$count' width='10' title='$count Files'>";
}
function newOTDService($date){
$r=@mysql_query("SELECT packet_id FROM ps_packets WHERE date_received LIKE '$date %' and service_status = 'MAIL ONLY'");
$count=mysql_num_rows($r);
$r2=@mysql_query("SELECT packet_id FROM ps_packets WHERE date_received LIKE '$date %' and service_status <> 'MAIL ONLY'");
$count2=mysql_num_rows($r2);
return "<table><tr><td><image src='http://www.bellaonline.us/quilting/kentucky/kentucky-step1.gif' height='$count' width='10' title='$count Files'></td></tr><tr><td><image src='http://www.martin.com/color/small/purple509.1.gif' height='$count2' width='10' title='$count2 Files'></td></tr></table>";
}
function svcCompleteOTDService($date){
$r=@mysql_query("SELECT packet_id FROM ps_packets WHERE closeOut = '$date'");
$count=mysql_num_rows($r);
return "<image src='http://i2.squidoocdn.com/resize/squidoo_images/50/lens2112515_1218247838green-beans-1.jpg' height='$count' width='10' title='$count Files'>";
}
function closeOTDService($date){
$r=@mysql_query("SELECT packet_id FROM ps_packets WHERE estFileDate = '$date'");
$count=mysql_num_rows($r);
return "<image src='http://www.lseclimb.org/wp-content/themes/demar/img/red_square_32px.jpg' height='$count' width='10' title='$count Files'>";
}
function estCloseOTDService($date){
$r=@mysql_query("SELECT packet_id FROM ps_packets WHERE fileDate = '$date'");
$count=mysql_num_rows($r);
return "<image src='http://4.bp.blogspot.com/_UQmvUodh4h4/Saf1kylPhnI/AAAAAAAAC0w/rlTxf-P3eDQ/s400/Green+Square+copy.jpg' height='$count' width='10' title='$count Files'>";
}
function webserviceOTDService($date){
$r=@mysql_query("SELECT create_id FROM defendants WHERE statusdate = '$date'");
$count=mysql_num_rows($r);
return "<image src='http://www.sitecreations.co.uk/orange.gif' height='$count' width='10' title='$count Files'>";
}
function newEVService($date){
$r=@mysql_query("SELECT eviction_id FROM evictionPackets WHERE date_received LIKE '$date %'");
$count=mysql_num_rows($r);
return "<image src='http://www.student.oulu.fi/~laurirai/www/css/middle/blue.gif' height='$count' width='10' title='$count Files'>";
}
function closeEVService($date){
$r=@mysql_query("SELECT eviction_id FROM evictionPackets WHERE estFileDate = '$date'");
$count=mysql_num_rows($r);
return "<image src='http://mathforum.org/alejandre/green.square.gif' height='$count' width='10' title='$count Files'>";
}
function estCloseEVService($date){
$r=@mysql_query("SELECT eviction_id FROM evictionPackets WHERE fileDate = '$date'");
$count=mysql_num_rows($r);
return "<image src='http://www.martin.com/color/small/pink307.1.gif' height='$count' width='10' title='$count Files'>";
}

?>
<? if($_GET[last30] == 'on'){ ?>
<div>Last 30 Days</div>
<?
$i=0;
echo "<table border='1'><tr>";
while($i < 30){
$time  = mktime(0, 0, 0, date("m")  , date("d")-$i, date("Y"));
$date = date('Y-m-d', $time);
$date2 = date('n/j/Y', $time);
$disp = str_replace(date('Y').'-','',$date);
echo "<td valign='bottom'><table><tr>
										<td valign='bottom'>".newOTDService($date)."</td>
										<td valign='bottom'>".closeOTDService($date)."</td>
										<td valign='bottom'>".svcCompleteOTDService($date)."</td>
										<td valign='bottom'>".estCloseOTDService($date)."</td>
										<td valign='bottom'>".webserviceOTDService($date2)."</td>
										<td valign='bottom'>".newEVService($date)."</td>
										<td valign='bottom'>".closeEVService($date)."</td>
										<td valign='bottom'>".estCloseEVService($date)."</td>
										<td valign='bottom'>".newSTANDARDService($date)."</td>
									</tr></table>$date</td>";
$i++;
}
echo "</tr></table>";
}
?>

<? if($_GET[last120] == 'on'){ ?>
<div>Last 120 Days</div>
<?
$i=0;
echo "<table border='1'><tr>";
while($i < 120){
$time  = mktime(0, 0, 0, date("m")  , date("d")-$i, date("Y"));
$date = date('Y-m-d', $time);
$date2 = date('n/j/Y', $time);
$disp = str_replace(date('Y').'-','',$date);
echo "<td valign='bottom'><table><tr>
										<td valign='bottom'>".newOTDService($date)."</td>
										<td valign='bottom'>".closeOTDService($date)."</td>
										<td valign='bottom'>".svcCompleteOTDService($date)."</td>
										<td valign='bottom'>".estCloseOTDService($date)."</td>
										<td valign='bottom'>".webserviceOTDService($date2)."</td>
										<td valign='bottom'>".newEVService($date)."</td>
										<td valign='bottom'>".closeEVService($date)."</td>
										<td valign='bottom'>".estCloseEVService($date)."</td>
										<td valign='bottom'>".newSTANDARDService($date)."</td>
									</tr></table>$date</td>";
$i++;
}
echo "</tr></table>";
}
?>

<? if($_GET[last7] == 'on'){ ?>
<div>Last 7 Days</div>
<?
$i=0;
echo "<table border='1'><tr>";
while($i < 7){
$time  = mktime(0, 0, 0, date("m")  , date("d")-$i, date("Y"));
$date = date('Y-m-d', $time);
$date2 = date('n/j/Y', $time);
$disp = str_replace(date('Y').'-','',$date);
echo "<td valign='bottom'><table><tr>
										<td valign='bottom'>".newOTDService($date)."</td>
										<td valign='bottom'>".closeOTDService($date)."</td>
										<td valign='bottom'>".svcCompleteOTDService($date)."</td>
										<td valign='bottom'>".estCloseOTDService($date)."</td>
										<td valign='bottom'>".webserviceOTDService($date2)."</td>
										<td valign='bottom'>".newEVService($date)."</td>
										<td valign='bottom'>".closeEVService($date)."</td>
										<td valign='bottom'>".estCloseEVService($date)."</td>
										<td valign='bottom'>".newSTANDARDService($date)."</td>
									</tr></table>$date</td>";
$i++;
}
echo "</tr></table>";
}
?>



<? if($_GET[next60] == 'on'){ ?>
<div>Next 60 Days</div>
<?
$i=-60;
echo "<table border='1'><tr>";
while($i < 0){
$time  = mktime(0, 0, 0, date("m")  , date("d")-$i, date("Y"));
$date = date('Y-m-d', $time);
$date2 = date('n/j/Y', $time);
$disp = str_replace(date('Y').'-','',$date);
echo "<td valign='bottom'><table><tr><td valign='bottom'>".newOTDService($date)."</td>
										<td valign='bottom'>".closeOTDService($date)."</td>
										<td valign='bottom'>".svcCompleteOTDService($date)."</td>
										<td valign='bottom'>".estCloseOTDService($date)."</td>
										<td valign='bottom'>".webserviceOTDService($date2)."</td>
										<td valign='bottom'>".newEVService($date)."</td>
										<td valign='bottom'>".closeEVService($date)."</td>
										<td valign='bottom'>".estCloseEVService($date)."</td>
										<td valign='bottom'>".newSTANDARDService($date)."</td>
								</tr></table>$disp</td>";
$i++;
}
echo "</tr></table>";
}
?>


<? if($_GET[next7] == 'on'){ ?>
<div>Next 7 Days</div>
<?
$i=-7;
echo "<table border='1'><tr>";
while($i < 0){
$time  = mktime(0, 0, 0, date("m")  , date("d")-$i, date("Y"));
$date = date('Y-m-d', $time);
$date2 = date('n/j/Y', $time);
$disp = str_replace(date('Y').'-','',$date);
echo "<td valign='bottom'><table><tr><td valign='bottom'>".newOTDService($date)."</td>
										<td valign='bottom'>".closeOTDService($date)."</td>
										<td valign='bottom'>".svcCompleteOTDService($date)."</td>
										<td valign='bottom'>".estCloseOTDService($date)."</td>
										<td valign='bottom'>".webserviceOTDService($date2)."</td>
										<td valign='bottom'>".newEVService($date)."</td>
										<td valign='bottom'>".closeEVService($date)."</td>
										<td valign='bottom'>".estCloseEVService($date)."</td>
										<td valign='bottom'>".newSTANDARDService($date)."</td>
									</tr></table>$disp</td>";
$i++;
}
echo "</tr></table>";
}
?>


<? if($_GET[over6] == 'on'){ ?>
<div>6 Week Span</div>
<?
$i=-21;
echo "<table border='1'><tr>";
while($i < 21){
$time  = mktime(0, 0, 0, date("m")  , date("d")-$i, date("Y"));
$date = date('Y-m-d', $time);
$date2 = date('n/j/Y', $time);
$disp = str_replace(date('Y').'-','',$date);
echo "<td valign='bottom'><table><tr><td valign='bottom'>".newOTDService($date)."</td>
										<td valign='bottom'>".closeOTDService($date)."</td>
										<td valign='bottom'>".svcCompleteOTDService($date)."</td>
										<td valign='bottom'>".estCloseOTDService($date)."</td>
										<td valign='bottom'>".webserviceOTDService($date2)."</td>
										<td valign='bottom'>".newEVService($date)."</td>
										<td valign='bottom'>".closeEVService($date)."</td>
										<td valign='bottom'>".estCloseEVService($date)."</td>
										<td valign='bottom'>".newSTANDARDService($date)."</td>
								</tr></table>$disp</td>";
$i++;
}
echo "</tr></table>";
}
?>


<? if($_GET[monthly] == 'on'){ ?>
<div>Next 2 and Last 48 Months</div>
<?
$i=-2;
echo "<table border='1'><tr>";
while($i < 48){
$time  = mktime(0, 0, 0, date("m")-$i  , date("d"), date("Y"));
$date = date('Y-m', $time);
$date2 = date('m/%/Y', $time);
//$disp = str_replace(date('Y').'-','',$date);
$disp = $date;
echo "<td valign='bottom'><table><tr><td valign='bottom'>".graphInMonth($date)."</td><td valign='bottom'>".graphMonthlyPub($date2)."</td><td valign='bottom'>".graphMonthly($date)."</td></tr></table>$disp</td>";
$i++;
}
echo "</tr></table>";
}
?>
<table><tr><td>
<li><image src='http://www.martin.com/color/small/purple509.1.gif' height='10' width='10'> New OTD Files</li>
<li><image src='http://www.bellaonline.us/quilting/kentucky/kentucky-step1.gif' height='10' width='10'> New OTD Files (Mail Only)</li>
<li><image src='http://www.lseclimb.org/wp-content/themes/demar/img/red_square_32px.jpg' height='10' width='10'> Estimate Closed OTD Files</li>
<li><image src='http://i2.squidoocdn.com/resize/squidoo_images/50/lens2112515_1218247838green-beans-1.jpg' height='10' width='10'> Service Complete OTD Files</li>
<li><image src='http://4.bp.blogspot.com/_UQmvUodh4h4/Saf1kylPhnI/AAAAAAAAC0w/rlTxf-P3eDQ/s400/Green+Square+copy.jpg' height='10' width='10'> Filed / Closed OTD Files</li>
<li><image src='http://www.sitecreations.co.uk/orange.gif' height='10' width='10'> New OTD Webservice Data</li>
<li><image src='http://www.student.oulu.fi/~laurirai/www/css/middle/blue.gif' height='10' width='10'> New EV Files</li>
<li><image src='http://mathforum.org/alejandre/green.square.gif' height='10' width='10'> Estimate Closed EV Files</li>
<li><image src='http://www.martin.com/color/small/pink307.1.gif' height='10' width='10'> Actual Close EV Files</li>
<li><image src='http://hss.fullerton.edu/philosophy/Black%20Square.gif' height='10' width='10'> New Standard Files</li>
</td><td>
<form>
<table>
	<tr>
		<td><input type="checkbox" name="over6"></td>
		<td>6 Week Span</td>
	</tr>
	<tr>
		<td><input type="checkbox" name="last7"></td>
		<td>Last 7 Days</td>
	</tr>
	<tr>
		<td><input type="checkbox" name="last30"></td>
		<td>Last 30 Days</td>
	</tr>
	<tr>
		<td><input type="checkbox" name="last120"></td>
		<td>Last 120 Days</td>
	</tr>
	<tr>
		<td><input type="checkbox" name="next7"></td>
		<td>Next 7 Days</td>
	</tr>
	<tr>
		<td><input type="checkbox" name="next60"></td>
		<td>Next 60 Days</td>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" value="Load Trend Graphs"></td>
	</tr>
</table>	
</form>
</td></tr></table>
