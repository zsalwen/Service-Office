<style>
legend { border:double 3px #0000FF;
		background-color:#CCFFFF;
		padding:5px;
		}
fieldset	{
		background-color:#CCCCCC;
		}
table {
		background-color:#FFFFFF;
		}
body	{
		background-color:#FFF;
		font-size:10px;
		border:solid 1px #999;
		padding-top:3px
		}
</style>

<?
mysql_connect();
mysql_select_db('service');
function countStatus($status){
	$r=@mysql_query("select packet_id from standard_packets where process_status = '$status'");
	$count=mysql_num_rows($r);
	return $count;
}
function talk($to,$message){
	include_once '/thirdParty/xmpphp/XMPPHP/XMPP.php';
	$conn = new XMPPHP_XMPP('talk.google.com', 5222, 'talkabout.files@gmail.com', '', 'xmpphp', 'gmail.com', $printlog=false, $loglevel=XMPPHP_Log::LEVEL_INFO);
	try {
		$conn->useEncryption(true);
		$conn->connect();
		$conn->processUntil('session_start');
		//$conn->presence("Ya, I'm online","available","talk.google.com");
		$conn->message($to, $message);
		$conn->disconnect();
	} catch(XMPPHP_Exception $e) {
		die($e->getMessage());
	}
}

function timeline($id,$note){
 	@mysql_query("insert into explorer (date,date_time,user,packet,uri) values (NOW(),NOW(),'".$_COOKIE[psdata][name]."','S$id','$note')") or die(mysql_error());
	//talk('insidenothing@gmail.com',"$note for standard packet $id");
	$q1 = "SELECT timeline FROM standard_packets WHERE packet_id = '$id'";		
	$r1 = @mysql_query ($q1) or die(mysql_error());
	$d1 = mysql_fetch_array($r1, MYSQL_ASSOC);
	$access=date('m/d/y g:i A');
	if ($d1[timeline] != ''){
		$notes = $d1[timeline]."<br>$access: ".$note;
	}else{
		$notes = $access.': '.$note;
	}
	$notes = addslashes($notes);
	$q1 = "UPDATE standard_packets set timeline='$notes' WHERE packet_id = '$id'";		
	$r1 = @mysql_query ($q1) or die(mysql_error());
}

if ($_GET[analysis]){
	@mysql_query("update standard_packets set process_status = 'ANALYSIS' where packet_id = '$_GET[analysis]'");
	mail('service@mdwestserve.com','Standard Packet '.$_GET[analysis].' requires skip trace analysis','s'.$_GET[analysis].' requires skip trace analysis by: '.$_COOKIE[psdata][name]);
	timeline($_GET['analysis'],'Status updated to ANALYSIS by: '.$_COOKIE[psdata][name]);
}
if ($_GET[skip]){
	@mysql_query("update standard_packets set process_status = 'SKIP TRACE' where packet_id = '$_GET[skip]'");
	mail('service@mdwestserve.com','Standard Packet '.$_GET[skip].' requires skip trace','s'.$_GET[skip].' requires skip trace by: '.$_COOKIE[psdata][name]);
	timeline($_GET['skip'],'Status updated to SKIP TRACE by: '.$_COOKIE[psdata][name]);
}
if ($_GET[mail]){
	@mysql_query("update standard_packets set process_status = 'READY TO MAIL' where packet_id = '$_GET[mail]'");
	mail('service@mdwestserve.com','Standard Packet '.$_GET[mail].' ready to mail','s'.$_GET[mail].' ready to mail by: '.$_COOKIE[psdata][name]);
	timeline($_GET['mail'],'Status updated to READY TO MAIL by: '.$_COOKIE[psdata][name]);
}
if ($_GET['print']){
	@mysql_query("update standard_packets set process_status = 'READY TO PRINT' where packet_id = $_GET[print]");
	mail('service@mdwestserve.com','Standard Packet '.$_GET['print'].' ready to print','s'.$_GET['print'].' ready to print by: '.$_COOKIE[psdata][name]);
	timeline($_GET['print'],'Status updated to READY TO PRINT by: '.$_COOKIE[psdata][name]);
}
if ($_GET['affidavits']){
	@mysql_query("update standard_packets set process_status = 'READY FOR AFFIDAVITS' where packet_id = $_GET[affidavits]");
	mail('service@mdwestserve.com','Standard Packet '.$_GET[affidavits].' ready for affidavits','s'.$_GET[affidavits].' ready for affidavits by: '.$_COOKIE[psdata][name]);
	timeline($_GET['affidavits'],'Status updated to READY FOR AFFIDAVITS by: '.$_COOKIE[psdata][name]);
}
if ($_GET['qc']){
	@mysql_query("update standard_packets set process_status = 'AFFIDAVIT QUALITY CONTROL' where packet_id = $_GET[qc]");
	mail('service@mdwestserve.com','Standard Packet '.$_GET[qc].' ready for affidavit quality control','s'.$_GET[qc].' ready for affidavit quality control by: '.$_COOKIE[psdata][name]);
	timeline($_GET['qc'],'Status updated to AFFIDAVIT QUALITY CONTROL by: '.$_COOKIE[psdata][name]);
}
if ($_GET['returnAffs']){
	@mysql_query("update standard_packets set process_status = 'AWAITING SIGNED AFFIDAVITS', fileDate = NOW() where packet_id = $_GET[returnAffs]");
	mail('service@mdwestserve.com','Standard Packet '.$_GET[returnAffs].' waiting for signed affidavits, close date set to '.date('Y-m-d'),'s'.$_GET[returnAffs].' waiting for signed affidavits, close date set to '.date('Y-m-d').' by: '.$_COOKIE[psdata][name]);
	timeline($_GET['returnAffs'],'Status updated to AWAITING SIGNED AFFIDAVITS, close date set to '.date('Y-m-d').', by: '.$_COOKIE[psdata][name]);
}
if ($_GET['restart']){
	@mysql_query("update standard_packets set process_status = 'AWAITING RESTART' where packet_id = $_GET[restart]");
	mail('service@mdwestserve.com','Standard Packet '.$_GET[restart].' waiting for restart','s'.$_GET[restart].' waiting for restart by: '.$_COOKIE[psdata][name]);
	timeline($_GET['restart'],'Status updated to AWAITING RESTART by: '.$_COOKIE[psdata][name]);
}
if ($_GET['complete']){
	@mysql_query("update standard_packets set process_status = 'ORDER COMPLETE' where packet_id = $_GET[complete]");
	mail('service@mdwestserve.com','Standard Packet '.$_GET[complete].' order complete','s'.$_GET[complete].' order complete by: '.$_COOKIE[psdata][name]);
	timeline($_GET['complete'],'Status updated to ORDER COMPLETE by: '.$_COOKIE[psdata][name]);
}
if ($_GET['start']){
	@mysql_query("update standard_packets set process_status = 'IN PROGRESS' where packet_id = $_GET[start]");
	mail('service@mdwestserve.com','Standard Packet '.$_GET[start].' in progress','s'.$_GET[start].' in progress by: '.$_COOKIE[psdata][name]);
	timeline($_GET['start'],'Status updated to IN PROGRESS by: '.$_COOKIE[psdata][name]);
}
if ($_GET['courier']){
	@mysql_query("update standard_packets set process_status = 'WITH COURIER' where packet_id = $_GET[courier]");
	mail('service@mdwestserve.com','Standard Packet '.$_GET[courier].' with courier','s'.$_GET[courier].' with courier by: '.$_COOKIE[psdata][name]);
	timeline($_GET['courier'],'Status updated to WITH COURIER by: '.$_COOKIE[psdata][name]);
}
if ($_GET['payment']){
	@mysql_query("update standard_packets set process_status = 'AWAITING PAYMENT' where packet_id = $_GET[payment]");
	mail('service@mdwestserve.com','Standard Packet '.$_GET[payment].' awaiting payment','s'.$_GET[payment].' awaiting payment by: '.$_COOKIE[psdata][name]);
	timeline($_GET['payment'],'Status updated to AWAITING PAYMENT by: '.$_COOKIE[psdata][name]);
}
if ($_GET['purge']){
	@mysql_query("update standard_packets set process_status = 'PURGE QUEUE' where packet_id = $_GET[purge]");
	mail('service@mdwestserve.com','Standard Packet '.$_GET[purge].' awaiting purge','s'.$_GET[purge].' awaiting purge by: '.$_COOKIE[psdata][name]);
	timeline($_GET['purge'],'Status updated to PURGE QUEUE by: '.$_COOKIE[psdata][name]);
}
if ($_GET['delete']){
	@mysql_query("update standard_packets set process_status = 'PURGE QUEUE' where packet_id = $_GET[purge]");
	mail('service@mdwestserve.com','Standard Packet '.$_GET[purge].' awaiting purge','s'.$_GET[purge].' awaiting purge by: '.$_COOKIE[psdata][name]);
	timeline($_GET['purge'],'Status updated to PURGE QUEUE by: '.$_COOKIE[psdata][name]);
}






function whoIs($id){
	$q="SELECT display_name FROM attorneys WHERE attorneys_id = '$id'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
return $d[display_name];
}


function hasAffs($packet){
$r=@mysql_query("SELECT packet FROM affidavits WHERE product = 'S' and packet = '$packet' and status = 'visible'");
$d=mysql_fetch_array($r,MYSQL_ASSOC);
return $d[packet];
}


function openSvc($affidavit_status,$process_status){

?>
<fieldset><legend><b><?=strtoupper($affidavit_status);?></b></legend>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
	<tr style='background-color:#FFFFCC'>
		<td>Order Placed</td>
		<td>Options</td>
		<td>Service Info</td>
	</tr><?
$r=@mysql_query("SELECT name1, name2, name3, name4, name5,name6, circuit_court, oldOTD, fileDate, affidavitType, date_received, client_file, addlDocs, attorneys_id, service_status, client_file, case_no, packet_id, date_received, affidavit_status, process_status FROM standard_packets WHERE process_status = '$process_status' and affidavit_status='$affidavit_status' and process_status <> 'PURGE QUEUE' order by date_received ASC");
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	echo "<tr style='background-color:";
	if(hasAffs($d[packet_id])){ echo "#CCFFFF"; }else{ echo "#99FF99"; } 
	echo "'>
			<td nowrap='nowrap'>
			<li><a href='/standard/order.php?packet=$d[packet_id]' target='_Blank'>S$d[packet_id]</a> ".substr($d[date_received],0,10)."</li>";
			//<li><a href='?status=$process_status&restart=$d[packet_id]'>restart</a></li>
			//<li><a href='?status=$process_status&purge=$d[packet_id]'>purge</a></li>";
			if($d[oldOTD]){
				echo "<li>OTD$d[oldOTD]</li>";
			}
	echo "	
			<li>".$d[addlDocs]."</li>
			<li>".whoIs($d[attorneys_id])."</li>
			<li>$d[client_file]</li>
			</td>
			<td nowrap='nowrap'>
			";
			
	$rX=@mysql_query("select * from affidavits where packet = '$d[packet_id]' and product = 'S' and status= 'visible' ");
	while($dX=mysql_fetch_array($rX,MYSQL_ASSOC)){
		echo "<li><a href='http://staff.mdwestserve.com/wizard.php?id=$dX[id]' target='_Blank' style='font-size:12px'>V</a> | <a href='http://staff.mdwestserve.com/builder.php?edit=$dX[id]' target='_Blank'  style='font-size:12px'>E</a> | <a href='http://staff.mdwestserve.com/builder.php?delete=$dX[id]' target='_Blank'  style='font-size:12px'>D</a> | $dX[processor] / $dX[qc]</li>";
	}
if ($process_status == 'WITH COURIER'){ echo "<li><a href='?status=$process_status&complete=$d[packet_id]'>Complete Order</a></li></li>"; }
if ($process_status == 'AWAITING SIGNED AFFIDAVITS'){ echo "<li><a href='?status=$process_status&complete=$d[packet_id]'>Complete Order</a></li><li><a href='?status=$process_status&courier=$d[packet_id]'>With Courier</a></li><li><a href='?status=$process_status&payment=$d[packet_id]'>Awaiting Payment</a></li>"; }
if ($process_status == 'AWAITING RESTART'){ echo "<li><a href='?status=$process_status&complete=$d[packet_id]'>Complete Order</a></li><li><a href='?status=$process_status&start=$d[packet_id]'>Start Order</a></li>"; }
if ($process_status == 'READY TO PRINT'){ echo "<li><a href='?status=$process_status&returnAffs=$d[packet_id]'>Sent and Waiting for Server Affidavits</a></li>"; }
if ($process_status == 'AFFIDAVIT QUALITY CONTROL'){ echo "<li><a href='?status=$process_status&print=$d[packet_id]'>Above are all Ready to Print</a></li>"; }
if ($process_status == 'READY FOR AFFIDAVITS'){ echo "<li><a href='?status=$process_status&qc=$d[packet_id]'>Affidavit Quality Control</a></li>"; }
if ($process_status == 'IN PROGRESS'){ echo "<li><a href='?status=$process_status&mail=$d[packet_id]'>Ready to Mail</a></li>"; }
if ($process_status == 'IN PROGRESS'){ echo "<li><a href='?status=$process_status&skip=$d[packet_id]'>Start Skip Trace</a></li>"; }
if ($process_status == 'SKIP TRACE'){ echo "<li><a href='?status=$process_status&analysis=$d[packet_id]'>Analyze Trace Data</a></li><li><a href='?status=$process_status&start=$d[packet_id]'>Resume Service</a></li>"; }
if ($process_status == 'ANALYSIS'){ echo "<li><a href='?status=$process_status&start=$d[packet_id]'>Resume Service</a></li>"; }
if ($process_status == 'READY TO MAIL' || $process_status == 'IN PROGRESS'){ echo "<li><a href='?status=$process_status&affidavits=$d[packet_id]'>Ready for Affidavits</a></li>"; }
			
			echo "
			</td>
			<td nowrap='nowrap'>";
			if($d[name1]){ echo "<li>$d[name1]</li>"; } 
			if($d[name2]){ echo "<li>$d[name2]</li>"; } 
			if($d[name3]){ echo "<li>$d[name3]</li>"; } 
			if($d[name4]){ echo "<li>$d[name4]</li>"; } 
			if($d[name5]){ echo "<li>$d[name5]</li>"; } 
			if($d[name6]){ echo "<li>$d[name6]</li>"; } 
			echo "<li>$d[case_no]</li>
			<li>$d[circuit_court]</li>
			<li>Close $d[fileDate]</li>
			</td>
		 </tr>";
}
?>
</table></fieldset>
<? }?>
<div>
<table align="right" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td align="center" style="<? if(!$_GET[status]){ echo "background-image:url('http://staff.mdwestserve.com/standard/green.png'); "; }else{ echo "background-image:url('http://staff.mdwestserve.com/standard/red.png'); "; } ?> width:240px; height:40px; border-bottom:solid 3px #000;"><a href="open.php" style="text-decoration:none; font-size:15px; color:#FFF;">Monitor Standard Services</a></td>
		<?
		$r2=@mysql_query("select distinct process_status from standard_packets where process_status <> 'PURGE QUEUE' AND process_status <> 'ORDER COMPLETE' AND process_status <> 'CANCELLED'");
		while($d2=mysql_fetch_array($r2,MYSQL_ASSOC)){
		?>
		<td align="center" style="<? if($_GET[status] == $d2[process_status]){ echo "background-image:url('http://staff.mdwestserve.com/standard/green.png'); "; }else{ echo "background-image:url('http://staff.mdwestserve.com/standard/red.png'); "; } ?> width:240px; height:40px; border-bottom:solid 3px #000;"><a style="text-decoration:none; font-size:15px; color:#FFF;" href='?status=<?=$d2[process_status];?>'><?=$d2[process_status];?> <b>(<?=countStatus($d2[process_status]);?>)</b></a></td>
		<?
		}
		?>
	</tr>
</table>
</div>
<?

if($_GET[status]){

$r=@mysql_query("select distinct affidavit_status from standard_packets where process_status = '$_GET[status]' order by packet_id");
while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	openSvc($d[affidavit_status],$_GET[status]);
}
}else{
$where = "( process_status <> 'ORDER COMPLETE' AND process_status <> 'CANCELLED' AND process_status <> 'PURGE QUEUE' )";



ob_start();


// check for dispatch
echo "<center><table border='1'><tr><td valign='top'><div style='background-color:#FFF; font-size:20px;'><b>Data Entry</b>";
$r=@mysql_query("SELECT packet_id, address1 FROM standard_packets WHERE $where order by packet_id");
while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	if (!$d[address1]){$test=1;  echo "<li><a href='order.php?packet=$d[packet_id]' target='_Blank'>S-$d[packet_id]</a></li>"; }
}
echo "</div></td>";
// check for dispatch
echo "<td valign='top'><div style='background-color:#FFF; font-size:20px;'><b>Dispatch Monitor</b>";
$r=@mysql_query("SELECT packet_id, address1, address1a, address1b, address1c, address1d, address1e, state1, state1a, state1b, state1c, state1d, state1e, server_id, server_ida, server_idb, server_idc, server_idd, server_ide FROM standard_packets WHERE $where order by packet_id");
while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	if ($d[address1] && !$d[server_id]){$test=1;  echo "<li><a href='order.php?packet=$d[packet_id]' target='_Blank'>S-$d[packet_id]</a> to $d[address1], $d[state1]</li>"; }
	if ($d[address1a] && !$d[server_ida]){$test=1;  echo "<li><a href='order.php?packet=$d[packet_id]' target='_Blank'>S-$d[packet_id]</a> to $d[address1a], $d[state1a]</li>"; }
	if ($d[address1b] && !$d[server_idb]){$test=1;  echo "<li><a href='order.php?packet=$d[packet_id]' target='_Blank'>S-$d[packet_id]</a> to $d[address1b], $d[state1b]</li>"; }
	if ($d[address1c] && !$d[server_idc]){$test=1;  echo "<li><a href='order.php?packet=$d[packet_id]' target='_Blank'>S-$d[packet_id]</a> to $d[address1c], $d[state1c]</li>"; }
	if ($d[address1d] && !$d[server_idd]){$test=1;  echo "<li><a href='order.php?packet=$d[packet_id]' target='_Blank'>S-$d[packet_id]</a> to $d[address1d], $d[state1d]</li>"; }
	if ($d[address1e] && !$d[server_ide]){$test=1;  echo "<li><a href='order.php?packet=$d[packet_id]' target='_Blank'>S-$d[packet_id]</a> to $d[address1e], $d[state1e]</li>"; }
}
echo "</div></td>";
// check for out of state service
echo "<td valign='top'><div style='background-color:#FFF; font-size:20px;'><b>Out of State Monitor</b>";
$r=@mysql_query("SELECT packet_id, address1, address1a, address1b, address1c, address1d, address1e, state1, state1a, state1b, state1c, state1d, state1e, server_id, server_ida, server_idb, server_idc, server_idd, server_ide FROM standard_packets WHERE $where order by packet_id");
while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	if ($d[address1] && strtoupper($d[state1]) != 'MD'){$test=1;  echo "<li><a href='order.php?packet=$d[packet_id]' target='_Blank'>S-$d[packet_id]</a> at $d[address1], $d[state1]</li>"; }
	if ($d[address1a] && strtoupper($d[state1a]) != 'MD'){$test=1;  echo "<li><a href='order.php?packet=$d[packet_id]' target='_Blank'>S-$d[packet_id]</a> at $d[address1a], $d[state1a]</li>"; }
	if ($d[address1b] && strtoupper($d[state1b]) != 'MD'){$test=1;  echo "<li><a href='order.php?packet=$d[packet_id]' target='_Blank'>S-$d[packet_id]</a> at $d[address1b], $d[state1b]</li>"; }
	if ($d[address1c] && strtoupper($d[state1c]) != 'MD'){$test=1;  echo "<li><a href='order.php?packet=$d[packet_id]' target='_Blank'>S-$d[packet_id]</a> at $d[address1c], $d[state1c]</li>"; }
	if ($d[address1d] && strtoupper($d[state1d]) != 'MD'){$test=1;  echo "<li><a href='order.php?packet=$d[packet_id]' target='_Blank'>S-$d[packet_id]</a> at $d[address1d], $d[state1d]</li>"; }
	if ($d[address1e] && strtoupper($d[state1e]) != 'MD'){$test=1;  echo "<li><a href='order.php?packet=$d[packet_id]' target='_Blank'>S-$d[packet_id]</a> at $d[address1e], $d[state1e]</li>"; }
}
echo "</div></td>";
// check for quality control
echo "<td valign='top'><div style='background-color:#FFF; font-size:20px;'><b>Affidavit Quality Control</b>";
$r=@mysql_query("SELECT * FROM affidavits WHERE product = 'S' and qc = '' and status = 'visible' order by packet");
while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	if (!$d[qc]){ $test=1; echo "<li><a href='order.php?packet=$d[packet]' target='_Blank'>S-$d[packet]-$d[defendantID]</a> by $d[processor].</li>"; }
}
echo "</div></td></tr></table></center>";

// ob output

$buffer = ob_get_clean();

if ($test == 1){
echo $buffer;
} else {
echo "<br><div style='font-size:20px;padding-left:10px;'><em>All Monitors are Clear.</em></div><br>";
}

}

?>