<?
date_default_timezone_set('America/New_York');
include 'security.php';
if($_SERVER["SERVER_NAME"] == "staff2.mdwestserve.com"){
ini_set('mysql.default_host', '10.0.0.4');
$thisDB = '10.0.0.4';
}elseif($_SERVER["SERVER_NAME"] == "staff1.mdwestserve.com"){
ini_set('mysql.default_host', 'mdws2.mdwestserve.com');
$thisDB = 'mdws2.mdwestserve.com';
}else{
$thisDB = 'mdws1.mdwestserve.com';
}

mysql_connect();
mysql_select_db('core');
$q="UPDATE ps_users SET location='".$_SERVER['PHP_SELF']."', online_now='".time()."' WHERE id = '".$_COOKIE[psdata][user_id]."'";
@mysql_query($q);
 include '/gitbox/Service-Office/lock.php'; ?>
<meta http-equiv="refresh" content="300" />
<?
function courierDate($id){
	$r=@mysql_query("select date_format(estFileDate, '%W, %M %D %Y') as estFileDate	from ps_packets where packet_id = '$id' LIMIT 0,1");
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	return $d[estFileDate];
}
function courierDate2($id){
	$r=@mysql_query("select date_format(estFileDate, '%W, %M %D %Y') as estFileDate	from evictionPackets where eviction_id = '$id' LIMIT 0,1");
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	return $d[estFileDate];
}
function courierDate3($id){
	$r=@mysql_query("select date_format(estFileDate, '%W, %M %D %Y') as estFileDate	from standard_packets where packet_id = '$id' LIMIT 0,1");
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	return $d[estFileDate];
}
function standardCourt($str){
	if ($str == ''){
		return "<b style='color:red; text-decoration:blink;'>NO COURT SET</b>";
	}else{
		return strtoupper($str);
	}
}
function id2name($id){
	$q="SELECT name FROM ps_users WHERE id = '$id' LIMIT 0,1";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	return $d[name];
}
function id2attorney($id){
	$q="SELECT display_name FROM attorneys WHERE attorneys_id = '$id' LIMIT 0,1";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	return $d[display_name];
}
function getProduct($pid){
	$r=@mysql_query("SELECT name FROM product WHERE id='$pid' LIMIT 0,1");
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	return strtoupper(substr($d[name],0,1));
}
// ok this will use alot of data report
date_default_timezone_set('America/New_York');
$today=date('Y-m-d');
mysql_connect();
mysql_select_db('core');
echo "
<style>
.title1 { font-size:16px; background-color:FFcccc; } 
.title2 { font-size:16px; background-color:FFFFcc; } 
.title3 { font-size:16px; background-color:ccccFF; } 
.title4 { font-size:16px; background-color:9CFF88; } 
ol { padding:0px; margin:0px; padding-left:30px; } 
li { font-size:12px;}
td { font-size:12px;}
b { font-size:18px; }
a { text-decoration:none; } 
table
{
border-collapse:collapse;
}
</style>
";
echo "<table width='100%'><tr>
";

// data entry
$list='';
$r=@mysql_query("SELECT client_file, case_no, packet_id, date_received, affidavit_status, process_status, server_id, attorneys_id, estFileDate FROM standard_packets WHERE process_status = 'IN PROGRESS' order by packet_id");
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$optest = 1;
	$blink="";
	if ($d[estFileDate] == '0000-00-00'){
		$blink=" <span style='text-decoration:blink; color:red;'><b>MISSING EST. FILE DATE</b></span>";
	}elseif($d[estFileDate] < date('Y-m-d')){
		$blink=" <span style='text-decoration:blink; color:red;'><b>PAST DEADLINE: $d[estFileDate]</b></span>";
	}else{
		$blink=" [File: $d[estFileDate]]";
	}
	$list .= "<li><a target='_Blank' href='/standard/order.php?packet=$d[packet_id]'>S$d[packet_id]".$blink."$d[process_status]<br>".id2attorney($d[attorneys_id])." - ".id2name($d[server_id])."<br>($d[affidavit_status])</a></li>";
}
if ($list != ''){
	echo "<td valign='top'><div class='title4'><b>Open Std. Services</b><ol>$list</ol></div></td>";
	$list='';
}

// data entry
$r=@mysql_query("SELECT client_file, case_no, packet_id, date_received FROM ps_packets WHERE status = 'NEW' and process_status <> 'CANCELLED' AND process_status <> 'DUPLICATE' AND  process_status <> 'DAMAGED PDF'");
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$optest = 1;
	$list .= "<li><a target='_Blank' href='/otd/order.php?packet=$d[packet_id]'>O$d[packet_id]</a></li>";
}
$r=@mysql_query("SELECT client_file, case_no, eviction_id, date_received FROM evictionPackets WHERE status = 'NEW' and process_status <> 'CANCELLED' AND process_status <> 'DUPLICATE' AND  process_status <> 'DAMAGED PDF'");
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$optest = 1;
	$list .= "<li><a target='_Blank' href='/ev/order.php?packet=$d[eviction_id]'>E$d[eviction_id]</a></li>";
}
if ($list != ''){
	echo "<td valign='top'><div class='title4'><b>Data Entry</b><ol>$list</ol></div></td>";
	$list='';
}
echo "";
$counter8a = 0;
$counter8b = 0;

// dispatch
$r=@mysql_query("select packet_id, package_id, circuit_court, uspsVerify, qualityControl from ps_packets where process_status = 'READY' and package_id = '' order by circuit_court");
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$optest = 1;
	if(($d[uspsVerify] == '') || ($d[qualityControl] == '')){ $color='#FF0000'; $counter8a++;}else{ $color='#00FF00';$counter8b++;}
	$list .= "<li style='white-space: pre; background-color:$color'><a target='_Blank' href='/otd/order.php?packet=$d[packet_id]'>$d[circuit_court]</a></li>";
}
$r=@mysql_query("select eviction_id, package_id, circuit_court, uspsVerify, qualityControl from evictionPackets where process_status = 'READY' and package_id = '' order by circuit_court");
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$optest = 1;
	if(($d[uspsVerify] == '') || ($d[qualityControl] == '')){ $color='#FF0000'; $counter8a++;}else{ $color='#00FF00'; $counter8b++; }
	$list .= "<li style='white-space: pre; background-color:$color'><a target='_Blank' href='/ev/order.php?packet=$d[eviction_id]'>$d[circuit_court]</a></li>";
}
$total8 = $counter8a + $counter8b;
if($counter8b > '0' && $total8 > '0'){
	$complete = str_replace('1.00','100',str_replace('0.','',number_format($counter8b / $total8,2)));
}
if ($complete != ''){
	$list .= "<li style='white-space: pre;'><b>$complete% confirmed</b></li>";
}
if ($list != ''){
	echo "<td valign='top'><div class='title4'><b>Dispatch</b><ol>$list</ol></div></td>";
	$list=''; 
}

$r=@mysql_query("SELECT packet_id FROM ps_packets WHERE process_status = 'ASSIGNED' AND (request_close = 'YES' OR request_closea = 'YES' OR request_closeb = 'YES' OR request_closec = 'YES' OR request_closed = 'YES' OR request_closee = 'YES')");
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$optest = 1;
	$list .= "<li><a target='_Blank' href='/otd/order.php?packet=$d[packet_id]'>O$d[packet_id]</a></li>";
}
$r=@mysql_query("SELECT eviction_id FROM evictionPackets WHERE process_status = 'ASSIGNED' AND request_close = 'YES'");
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$optest = 1;
	$list .= "<li><a target='_Blank' href='/ev/order.php?packet=$d[eviction_id]'>E$d[eviction_id]</a></li>";
}
if ($list != ''){
	echo "<td valign='top'><div class='title4'><b>Quality Control</b><ol>$list</ol></div></td>";
	$list='';
}


$r=@mysql_query("select packet_id, mail_status from ps_packets where (process_status = 'READY TO MAIL' OR mail_status='Printed Awaiting Postage') order by mail_status, packet_id");
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$optest = 1;
	$list .= "<li><a target='_Blank' href='/otd/order.php?packet=$d[packet_id]'>O$d[packet_id]-$d[mail_status]</a></li>";
}
$r=@mysql_query("select eviction_id, mail_status from evictionPackets where (process_status = 'READY TO MAIL' OR mail_status='Printed Awaiting Postage') order by mail_status, eviction_id");
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$optest = 1;
	$list .= "<li><a target='_Blank' href='/ev/order.php?packet=$d[eviction_id]'>E$d[eviction_id]-$d[mail_status]</a></li>";
}
//if (!$optest){ echo "<li>All Good =)</li>"; }
if ($list != ''){
	echo "<td valign='top'><div class='title4'><b>Mail Room</b><ol>$list</ol></div></td>";
	$list='';
}
echo "<td valign='top'><div class='title1'><b>Deadline Watch</b><table>
";



$r=@mysql_query("select * from ps_packets where process_status <> 'CANCELLED' and fileDate = '0000-00-00' AND estFileDate < '$today' AND estFileDate <> '0000-00-00' order by estFileDate, circuit_court");
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$test11 = 1;
	echo "<tr><td><a target='_Blank' href='/otd/order.php?packet=$d[packet_id]'>O$d[packet_id]</a></td><td>$d[estFileDate]</td><td>$d[circuit_court]</td><td>$d[filing_status]</td><td>#$d[server_id]</td><td>#$d[server_ida]</td><td>#$d[server_idb]</td></tr>";
}




$r=@mysql_query("select * from evictionPackets where process_status <> 'CANCELLED' and fileDate = '0000-00-00' AND estFileDate < '$today' AND estFileDate <> '0000-00-00' order by estFileDate, circuit_court");
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$test11 = 1;
	echo "<tr><td><a target='_Blank' href='/ev/order.php?packet=$d[eviction_id]'>E$d[eviction_id]</a></td><td>$d[estFileDate]</td><td>$d[circuit_court]</td><td>$d[filing_status]</td><td>#$d[server_id]</td><td>#$d[server_ida]</td><td>#$d[server_idb]</td></tr>";
}








if (!$test11){ echo "<li>All Good =)</li>"; }
echo "</table></div></td>";

// couriers
$r=@mysql_query("select packet_id, circuit_court from ps_packets where process_status <> 'CANCELLED' and courierID = '' and estFileDate >= '$today' and fileDate = '0000-00-00' order by circuit_court");
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$list .= "<li><a target='_Blank' href='/otd/order.php?packet=$d[packet_id]'>OTD$d[packet_id], $d[circuit_court], ".courierDate($d[packet_id])."</a></li>";
}
$r=@mysql_query("select eviction_id, circuit_court from evictionPackets where process_status <> 'CANCELLED' and courierID = '' and estFileDate >= '$today' and fileDate = '0000-00-00' order by circuit_court");
$count=mysql_num_rows($r);
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$list .= "<li><a target='_Blank' href='/ev/order.php?packet=$d[eviction_id]'>EV$d[eviction_id], $d[circuit_court], ".courierDate2($d[eviction_id])."</a></li>";
}
$r=@mysql_query("select packet_id, circuit_court from standard_packets where process_status <> 'CANCELLED' and courierID = '' and estFileDate >= '$today' and fileDate = '0000-00-00' order by circuit_court");
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$list .= "<li><a target='_Blank' href='/standard/order.php?packet=$d[packet_id]'>S$d[packet_id], ".standardCourt($d[circuit_court]).", ".courierDate3($d[packet_id])."</a></li>";
}
if ($list != ''){
	echo "<td valign='top'><div class='title2'><b>Files Missing Courier</b><ol>$list</ol></div></td>";
	$list='';
}
// close date

$r=@mysql_query("select * from ps_packets where process_status <> 'CANCELLED' and filing_status <> '' and filing_status <> 'PREP TO FILE' and filing_status <> 'AWAITING CASE NUMBER' and filing_status <> 'REOPENED' and filing_status <> 'DO NOT FILE' and fileDate = '0000-00-00' AND process_status <> 'Assigned' order by date_received DESC");
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$list .= "<li><a target='_Blank' href='/otd/order.php?packet=$d[packet_id]'>OTD$d[packet_id], close date set.</a></li>";
}
$r=@mysql_query("select * from evictionPackets where process_status <> 'CANCELLED' and filing_status <> '' and filing_status <> 'PREP TO FILE' and filing_status <> 'DO NOT FILE' and fileDate = '0000-00-00' AND process_status <> 'Assigned' order by date_received DESC");
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$list .= "<li><a target='_Blank' href='/ev/order.php?packet=$d[eviction_id]'>EV$d[eviction_id], close date set.</a></li>";
}
if ($list != ''){
	echo "<td valign='top'><div class='title2'><b>Benchmark</b><ol>$list</ol></div></td>";
	$list='';
}

$r=@mysql_query("select * from exportRequests where exportDate = '0000-00-00 00:00:00'");
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$list .= "<li>$d[packetID], export review.</li>";
}
$r=@mysql_query("select * from rescanRequests where rescanDate = '0000-00-00 00:00:00'");
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$list .= "<li>$d[packetID], documents rescanned.</li>";
}
if ($list != ''){
	echo "<td valign='top'><div class='title3'><b>Export</b><ol>$list<ol></div></td>";
}
echo "</tr></table>";
?>
<hr>























Testing UpStream Packet Status Monitoring (should match above ~2hr sync delay)
<table border="1"><tr><td valign="top">

<?
// dispatch
$r=@mysql_query("select id, package_id, circuit_court, qualityControl, product_id from packet where process_status = 'READY' and package_id = '' order by circuit_court") or die(mysql_error());
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$product=getProduct($d[product_id]);
	$optest = 1;
	if(($d[qualityControl] == '')){ $color='#FF0000'; $counter8a++;}else{ $color='#00FF00';$counter8b++;}
	$list .= "<tr><td style='white-space: pre; background-color:$color'><a target='_Blank' href='/details.php?packet=$d[id]'>$product$d[id]</a></td><td> $d[circuit_court]</td></tr>";
}
$total8 = $counter8a + $counter8b;
if($counter8b > '0' && $total8 > '0'){
	$complete = str_replace('1.00','100',str_replace('0.','',number_format($counter8b / $total8,2)));
}
if ($complete != ''){
	$list .= "<tr><td colspan='2'><b>$complete% confirmed</b></td></tr>";
}
if ($list != ''){
	echo "<td valign='top'><b>Dispatch Queue</b><table border='1'>$list</table></td>";
	$list=''; 
}
?>

</td><td valign="top">

<?
// Deadline Watch
$r=@mysql_query("select * from packet where process_status <> 'CANCELLED' and fileDate = '0000-00-00' AND estFileDate < '$today' AND estFileDate <> '0000-00-00' order by estFileDate, circuit_court");
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$product=getProduct($d[product_id]);
	$list= "<tr><td><a target='_Blank' href='/details.php?packet=$d[id]'>$product$d[id]</a></td><td>$d[estFileDate]</td><td>$d[circuit_court]</td><td>$d[filing_status]</td><td>#$d[server_id]</td><td>#$d[server_ida]</td><td>#$d[server_idb]</td></tr>";
}
if ($list != ''){
	echo "<b>Deadline Alert</b><table border='1'>$list</table>";
	$list='';
}
?>
</td><td valign="top">
<?
// couriers
$r=@mysql_query("select id, circuit_court, product_id from packet where process_status <> 'CANCELLED' and courierID = '' and estFileDate >= '$today' and fileDate = '0000-00-00' order by circuit_court");
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$product=getProduct($d[product_id]);
	$list .= "<tr>
<td><a target='_Blank' href='/details.php?packet=$d[id]'>$product$d[id]</a></td>
<td>".standardCourt($d[circuit_court])."</td>
<td>".courierDate($d[id])."</td></tr>";
}
if ($list != ''){
	echo "<b>Missing Courier</b><table border='1'>$list</table>";
	$list='';
}
?>
</td></tr></table>
<hr>
<?
$r=@mysql_query("SELECT client_file, case_no, id, date_received FROM packet WHERE status = 'NEW' and process_status <> 'CANCELLED' AND process_status <> 'DUPLICATE' AND process_status <> 'DAMAGED PDF'") or die(mysql_error());
$count=mysql_num_rows($r);
if($count){
$active = $active + $count;
$xml .= "$count N, ";
}
$r=@mysql_query("select id, package_id from packet where process_status = 'READY' and package_id = ''") or die(mysql_error());
$count=mysql_num_rows($r);
if($count){
$active = $active + $count;
$xml .= "$count D, ";
}
$r=@mysql_query("SELECT id from packet WHERE process_status = 'ASSIGNED'") or die(mysql_error());
$count=mysql_num_rows($r);
if($count){
$active = $active + $count;
$xml .= "$count A, ";
}
$r=@mysql_query("SELECT id FROM packet WHERE process_status = 'ASSIGNED' AND (request_close = 'YES' OR request_closea = 'YES' OR request_closeb = 'YES' OR request_closec = 'YES' OR request_closed = 'YES' OR request_closee = 'YES')") or die(mysql_error());
$count=mysql_num_rows($r);
if($count){
$active = $active + $count;
$xml .= "$count Q, ";
}
$r=@mysql_query("select id, mail_status from packet where (process_status = 'READY TO MAIL' OR mail_status='Printed Awaiting Postage') order by mail_status, id") or die(mysql_error());
$count=mysql_num_rows($r);
if($count){
$active = $active + $count;
$xml .= "$count M, ";
}
$r=@mysql_query("SELECT id from packet where affidavit_status = 'SERVICE CONFIRMED' and filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'DO NOT FILE' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY' ") or die(mysql_error());
$count=mysql_num_rows($r);
if($count){
$active = $active + $count;
$xml .= "$count B, ";
}
?>

<?=$xml;?>

<hr>
<?
mysql_close();
$headers = apache_request_headers();
$lb = $headers["X-Forwarded-Host"];
$mirror = $_SERVER['HTTP_HOST'];
?>
<title>Mysql <?=$thisDB;?> Closed on <?=$mirror;?> from <?=$lb;?></title>