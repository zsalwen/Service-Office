<?
$memory_last_line = exec('free |grep Mem',$memory);
$full = str_replace(' ','.',$memory_last_line);
$full = str_replace('Mem:.......','',$full);
$full = str_replace('....','X',$full);
$full = str_replace('.','',$full);
$parts = explode('X',$full);
//echo '<pre>'.$full.'</pre><hr>';
$kb = $parts['2'];
$mb = $kb / 1000; 
$final = number_format($mb,0);
$final = str_replace(',','',$final);
if ($final < 100){
	error_log("[".date('r')."] [Server is running with ".$final."MB Free, clearing cache] \n", 3, '/logs/cache.log');
	error_log("[".date('r')."] [system('sync')] \n", 3, '/logs/reboot.log');
	system('sync');
	error_log("[".date('r')."] [system('echo 3 > /proc/sys/vm/drop_caches')] \n", 3, '/logs/cache.log');
	system('echo 3 > /proc/sys/vm/drop_caches');
}
mysql_connect();
mysql_select_db('core');

function id2name($id){
	$q="SELECT name FROM ps_users WHERE id = '$id'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
return $d[name];
}

function lastPunch($id){
mysql_select_db('core');
$r=@mysql_query("select user_id, punch_time, action from MDWestServeTimeClock where user_id = '$id' order by punch_id desc");
$d=mysql_fetch_array($r, MYSQL_ASSOC);
$name = explode(' ',id2name($id));
$action = explode(' ',$d[action]);
if (strtoupper($action[1]) == "IN"){
return substr($d[punch_time],0,5).": ".strtoupper($name[0]).", ";
}}


$today=date('Y-m-d');
/*
$r=@mysql_query("select packet_id from ps_packets where courierID = '' and estFileDate > '$today' and fileDate = '0000-00-00'");
$count=mysql_num_rows($r);
$r=@mysql_query("select eviction_id from evictionPackets where courierID = '' and estFileDate > '$today' and fileDate = '0000-00-00'");
$count2=mysql_num_rows($r);
$count = $count + $count2;
if ($count){
//$xml .= "[NoCOURIER: $count]";
}

$r=@mysql_query("select * from exportRequests where exportDate = '0000-00-00 00:00:00'");
$count=mysql_num_rows($r);
if ($count){
//$xml .= "[PURGE: $count]";
}
$r=@mysql_query("select * from ps_packets where fileDate = '0000-00-00' order by date_received DESC");
$count=mysql_num_rows($r);
if ($count){
//$xml .= "[NoCLOSE: $count]";
}
$r=@mysql_query("select * from rescanRequests where rescanDate = '0000-00-00 00:00:00'");
$count=mysql_num_rows($r);
if ($count){
//$xml .= "[RESCAN: $count]";
}


$r=@mysql_query("select * from systemStatus");
$d=mysql_fetch_array($r,MYSQL_ASSOC);
//$xml .= "[OTD: $d[activeBenchmark]/$d[closeBenchmark] EV: $d[activeBenchmark2]/$d[closeBenchmark2]]";

$r=@mysql_query("SELECT client_file, case_no, packet_id, date_received FROM ps_packets WHERE status = 'NEW' and process_status <> 'CANCELLED' AND process_status <> 'DUPLICATE' AND  process_status <> 'DAMAGED PDF'");
$count=mysql_num_rows($r);
if($count){
$xml .= "$count N, ";
}
$r=@mysql_query("select packet_id, package_id from ps_packets where process_status = 'READY' and package_id = ''");
$count=mysql_num_rows($r);
if($count){
$xml .= "$count D, ";
}
$r=@mysql_query("SELECT packet_id from ps_packets WHERE process_status = 'ASSIGNED'");
$count=mysql_num_rows($r);
if($count){
$xml .= "$count A, ";
}
$r=@mysql_query("SELECT packet_id FROM ps_packets WHERE process_status = 'ASSIGNED' AND (request_close = 'YES' OR request_closea = 'YES' OR request_closeb = 'YES' OR request_closec = 'YES' OR request_closed = 'YES' OR request_closee = 'YES')");
$count=mysql_num_rows($r);
if($count){
$xml .= "$count Q, ";
}
$r=@mysql_query("select packet_id, mail_status from ps_packets where (process_status = 'READY TO MAIL' OR mail_status='Printed Awaiting Postage') order by mail_status, packet_id");
$count=mysql_num_rows($r);
if($count){
$xml .= "$count M, ";
}
$r=@mysql_query("SELECT packet_id from ps_packets where affidavit_status = 'SERVICE CONFIRMED' and   filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'DO NOT FILE' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY'   ");
$count=mysql_num_rows($r);
if($count){
$xml .= "$count B";
}
$xml .= "]";
// EV Counters
$xml .= "[EV: ";
$r=@mysql_query("SELECT client_file, case_no, eviction_id, date_received FROM evictionPackets WHERE status = 'NEW' and process_status <> 'CANCELLED' AND process_status <> 'DUPLICATE' AND  process_status <> 'DAMAGED PDF'");
$count=mysql_num_rows($r);
if($count){
$xml .= "$count N, ";
}
$r=@mysql_query("select eviction_id, package_id from evictionPackets where process_status = 'READY' and package_id = ''");
$count=mysql_num_rows($r);
if($count){
$xml .= "$count D, ";
}
$r=@mysql_query("SELECT eviction_id from evictionPackets WHERE process_status = 'ASSIGNED'");
$count=mysql_num_rows($r);
if($count){
$xml .= "$count A, ";
}
$r=@mysql_query("SELECT DISTINCT eviction_id, attorneys_id, service_status, affidavit_status, filing_status, server_id, server_ida, server_idb, server_idc, server_idd, server_ide, processor_notes, extended_notes, date_received, client_file, rush, priority FROM evictionPackets WHERE process_status = 'ASSIGNED' AND (request_close = 'YES' OR request_closea = 'YES' OR request_closeb = 'YES' OR request_closec = 'YES' OR request_closed = 'YES' OR request_closee = 'YES')");
$count=mysql_num_rows($r);
if($count){
$xml .= "$count Q, ";
}
$r=@mysql_query("select eviction_id, mail_status from evictionPackets where (process_status = 'READY TO MAIL' OR mail_status='Printed Awaiting Postage') order by mail_status, eviction_id");
$count=mysql_num_rows($r);
if($count){
$xml .= "$count M, ";
}
$r=@mysql_query("SELECT eviction_id from evictionPackets where   affidavit_status = 'SERVICE CONFIRMED' and   filing_status <> 'FILED WITH COURT' AND filing_status <> 'FILED WITH COURT - FBS' AND status <> 'CANCELLED' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'DO NOT FILE' AND filing_status <> 'SEND TO CLIENT' AND status <> 'DUPLICATE' AND status <> 'FILE COPY'   ");
$count=mysql_num_rows($r);
if($count){
$xml .= "$count B ";
}
$xml .= "]";
*/

$r=@mysql_query("SELECT distinct user_id from MDWestServeTimeClock where user_id <> '0' and user_id <> '7728' and user_id <> '1' and user_id <> '309' and user_id <> '8' and user_id <> '286' and user_id <> '34' and user_id <> '95' and user_id <> '192' and user_id <> '236' and user_id <> '260' and user_id <> '269' and user_id <> '268' and user_id <> '307' and user_id <> '295' order by user_id ");
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
$xml .= lastPunch($d[user_id]);
}

	$swap = exec('/sbin/swapon -s', $retval);
	$swap = explode('partition',$swap);
	$swap = trim($swap[1]);
	$swap = explode('	',$swap);



$load = strtoupper(exec("uptime"));
$parts = explode(',',$load);
$xml2 .= "Server ".trim($parts[3])."/".trim($parts[4])."/".trim($parts[5]).",";

$xml2 .= " Available RAM: ".$final."MB";

	$swap = $swap['1'] / 1000; 
	$swap = number_format($swap,0);



$xml2 .= ", Used swap: ".$swap.' MB';



require "twitter.class.php";
$tweet = new Twitter("MDWSsvrHealth", "");
$success = $tweet->update('On the clock: '.$xml);
$tweet = new Twitter("MDWestserve", "sixhour");
$success = $tweet->update($xml2);

?>