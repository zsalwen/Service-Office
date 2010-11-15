<?
session_start();

$_SESSION[items]=0;
$_SESSION[total]=0;
$_SESSION[dTotal]=0;
$_SESSION[miss]=0;
include 'functions.php';
/*
if ($_GET[svc] == 'Eviction'){
	$table = 'evictionPackets';
	$idType = 'eviction_id';
	$idName = 'EV-';
}else{
	$table2 = 'ps_packets';
	$idType2 = 'packet_id';
	$idName2 = 'OTD-';
}
*/
mysql_connect();
mysql_select_db('core');
include 'security.php';
hardLog($user[name].' Loaded '.$_SERVER[PHP_SELF].'+'.$_SERVER[QUERY_STRING ],'user');
if($_GET[sort]){
	$sort=$_GET[sort];
}else{
	$sort="Date Sent";
}
if($_GET[dir]){
	$dir=$_GET[dir];
}else{
	$dir="ASC";
}
 

function benchmark($a,$b){
if ($b != "0000-00-00" ){
$received=strtotime($a);
$deadline=strtotime($b.' 12:00:00');
$days=number_format(($deadline-$received)/86400,0);
$_SESSION[items]=$_SESSION[items] + 1;
$_SESSION[total]=$_SESSION[total] + $days;
return "$days";
}else{
$_SESSION[miss]=$_SESSION[miss] + 1;
}
}

function benchmark2($a,$b){
if ($b != "0000-00-00" ){
$received=strtotime($a);
$deadline=strtotime($b.' 12:00:00');
$days=number_format(($deadline-$received)/86400,0);
$_SESSION[dTotal]=$_SESSION[dTotal] + $days;
return "$days";
}else{
$_SESSION[miss]=$_SESSION[miss] + 1;
}
}

function attorney($id){
	$q = "SELECT full_name FROM attorneys WHERE attorneys_id='$id'";
	$r = @mysql_query($q);
	$d = mysql_fetch_array($r, MYSQL_ASSOC);
	return $d[full_name];
}

function occNotice($cn,$packet_id,$state){
	$pos=stripos(strtoupper($packet_id),"EV");
	if ($pos !== false){
		$eviction_id=explode("EV",strtoupper($packet_id));
		$eviction_id=$eviction_id[1];
	}
	$r=@mysql_query("select * from occNotices where caseNO = '$cn'");
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	if($d[sendDate]){
		if ($d[requirements] == "7-105.9(b)"){
			return "<a href='http://staff.mdwestserve.com/otd/occAffidavit.php?packet=$packet_id' target='_Blank'>$d[requirements] sent $d[sendDate], Affidavit</a>";
		}elseif($d[requirements] == "7-105.9(d)" && $eviction_id){
			return "<a href='http://staff.mdwestserve.com/ev/noticeAffidavit.php?packet=$eviction_id' target='_Blank'>$d[requirements] sent $d[sendDate], Affidavit</a>";
		}else{
			return "$d[requirements] sent $d[sendDate]";
		}
	}elseif($eviction_id && $state){
		return "<div style='background-color:#FF0000;'><a href='http://staff.mdwestserve.com/ev/evictionNotice.php?packet=$eviction_id' target='_Blank'>Occupant Notice</a>, <a target='_Blank' href='http://staff.mdwestserve.com/greencard.php?packet=$eviction_id&def=1&add=&name=&line1=&line2=&csz=&art=&card=envelope&svc=EV'>Envelope</a></div>";
	}elseif($packet_id && $state){
		return "<div style='background-color:#FF0000;'><a href='http://staff.mdwestserve.com/otd/occupant.php?packet=$packet_id' target='_Blank'>Occupant Notice</a>, <a target='_Blank' href='http://staff.mdwestserve.com/greencard.php?packet=$packet_id&def=1&add=&name=&line1=&line2=&csz=&art=&card=envelope&svc=OTD'>Envelope</a></div>";
	}else{
		return "awaiting data entry...";
	}
}
function sortToField($sort){
	if ($sort=="Date Sent"){ return "date_received"; }
	if ($sort=="Date Filed"){ return "fileDate"; }
	if ($sort=="County"){ return "circuit_court"; }
	if ($sort=="Client"){ return "attorneys_id"; }
	if ($sort=="Est File Date"){ return "estFileDate"; }
}
function rowBgColor($i){
    $bg1 = "#ccff99"; // color one   
    $bg2 = "#ccffff"; // color two
    if ( $i%2 ) {
        return $bg1;
    } else {
        return $bg2;
    }
}
function output($buffer){

	return $buffer;

}
ob_start();
$i=0;
$i2=0;
$i3=0;
$i4=0;
$from = $_GET[from];
$to = $_GET[to];

if (!$from && !$to){
$from = "2007-01-01";
$to = "3007-01-01";
}
$type = $_GET[type];
if ($type == 'CLOSED'){
	//$cond = "(filing_status = 'FILED WITH COURT' or filing_status = 'FILED WITH COURT - FBS' or filing_status = 'SEND TO CLIENT')";
	$cond2 = "fileDate >= '$from' and fileDate <= '$to'";
} else {
	$cond = "filing_status <> 'FILED WITH COURT' AND service_status <> 'SERVICE COMPLETE' AND process_status <> 'PURGE QUEUE' AND process_status <> 'CANCELLED' AND process_status <> 'ORDER COMPLETE' AND process_status <> 'SERVICE COMPLETE' AND filing_status <> 'FILED WITH COURT - FBS' AND filing_status <> 'FILED BY CLIENT' AND filing_status <> 'REQUESTED-DO NOT FILE!' AND filing_status <> 'SEND TO CLIENT' AND status <> 'FILE COPY' AND status <> 'DAMAGED PDF' AND status <> 'CANCELLED'  and filing_status <> 'CANCELLED' ";
	//$cond2 = "date_received > '$from' and date_received < '$to'";
}

		//if (!$_GET[attid] || $_GET[attid] == "ALL" ){
			$r=@mysql_query("select * from ps_packets where $cond $cond2") or die(mysql_error());
			$r2=@mysql_query("select * from evictionPackets where $cond $cond2") or die(mysql_error());
			$r3=@mysql_query("select * from standard_packets where $cond $cond2") or die(mysql_error());
		//}else{
		//	$r=@mysql_query("select * from $table where attorneys_id= '$_GET[attid]' and $cond $cond2 ORDER BY ".sortToField($sort)." $dir") or die(mysql_error());
		//}
?>
<script src="sorttable.js"></script>
<style>
/* Sortable tables */
table.sortable thead {
    background-color:#eee;
    color:#666666;
    font-weight: bold;
    cursor: default;
}
</style>

<table class="sortable" cellspacing="0" cellpadding="2" border="1" width="100%" style="border-colapse:colaspe;">
<thead>
	<tr>
		<td>Type</td>
		<td>Packet</td>
		<td>Date</td>
		<td>Notice(s)</td>
		<td>State(s)</td>
		<td>Client</td>
		<td>Service ID</td>
		<td>Civil Case</td>
		<td>File Number</td>
		<td>County</td>
		<td>Status</td>
		<td>Sub-Status</td>
		<td>Est. Close Date</td>
		<td>Disp. Benchmark</td>
		<td>Svc. Benchmark</td>
	</tr>
</thead>
<? while($d=mysql_fetch_array($r,MYSQL_ASSOC)){ $i++; 
if ($d[estFileDate] == '0000-00-00' && $type == 'OPEN'){
	echo "<script>alert('File ".$d["$idType"]." Missing Est. Close Date!')</script>";
}
if ($d[attorneys_id] == '3' || $d[attorneys_id] == '68' || $d[attorneys_id] == '7'){
	$notice=occNotice(trim($d[case_no]),$d[packet_id],$d[state1]);
}else{
	$notice='';
}
?>
	<tr style="background-color:<? if( $d[filing_status]=="REOPENED"){ echo "#FF8866";}else{ echo rowBgColor($i);}?>;">
		<td>PRESALE</td>
		<td><?=$d[packet_id];?></td>
		<? if ($type == "CLOSED"){ ?>
		<td><?=$d[fileDate];?></td>
		<? }else{?>
		<td><?=$d[date_received];?></td>
		<? } ?>
		<td><?=$notice;?></td>
		<td><?=$d[state1];?><?=$d[state1a];?><?=$d[state1b];?><?=$d[state1c];?><?=$d[state1d];?><?=$d[state1e];?></td>
		<td><?=attorney($d[attorneys_id])?></td>
		<td><?=$d[server_id];?>.<?=$d[server_ida];?>.<?=$d[server_idc];?>.<?=$d[server_idd];?>.<?=$d[server_ide];?></td>
		<td><?=trim($d[case_no]);?></td>
		<td><u><?=$d[client_file]?></u></td>
		<td><?=$d[circuit_court]?></td>
		<td><? if($d[bill410] && $type == "CLOSED"){ echo "CLOSED AND BILLED";}elseif( $d[filing_status]=="REOPENED"){ echo "Reopen-$d[reopenDate]";}elseif($d[affidavit_status] == "SERVICE CONFIRMED"){ echo "Blackhole"; }else{ echo "Active"; } ?></td>
		<td><? if($d[service_status] == 'MAIL ONLY' ){echo "MAIL ONLY"; $i4++; }else{?><?=$d[lossMit]; }?></td>
		<td><a href="courier.php?date=<?=$d[estFileDate]?>" target="_Blank"><?=$d[estFileDate]?></a></td>
		<? if ($d[fileDate] != "0000-00-00"){ $end = $d[fileDate]; } else { $end =$d[estFileDate] ;} ?>
		<? if ($d[reopenDate] != "0000-00-00"){ $start = $d[reopenDate].' 12:00:00'; } elseif ($d[dispatchDate] != '0000-00-00 00:00:00') { $start =$d[dispatchDate] ;}else{ $start =$d[date_received] ; } ?>
		<td><?=benchMark2($d[date_received],$start);?>-<?=($d[date_received],$start);?></td>
		<td><?=benchmark($start,$end);?></td>
	</tr>
<? } $count= $i;  $count4= $i4; ?>
<? while($d2=mysql_fetch_array($r2,MYSQL_ASSOC)){ $i2++; 
if ($d2[estFileDate] == '0000-00-00' && $type == 'OPEN'){
	echo "<script>alert('File ".$d2["$idType"]." Missing Est. Close Date!')</script>";
}
$notice='';
?>
	<tr style="background-color:<? if( $d2[filing_status]=="REOPENED"){ echo "#FF8866";}else{ echo rowBgColor($i);}?>;">
		<td>EVICTION</td>
		<td><?=$d2[eviction_id];?></td>
		<? if ($type == "CLOSED"){ ?>
		<td><?=$d2[fileDate];?></td>
		<? }else{?>
		<td><?=$d2[date_received];?></td>
		<? } ?>
		<td><?=$notice;?></td>
		<td><?=$d2[state1];?><?=$d2[state1a];?><?=$d2[state1b];?><?=$d2[state1c];?><?=$d2[state1d];?><?=$d2[state1e];?></td>
		<td><?=attorney($d2[attorneys_id])?></td>
		<td><?=$d2[server_id];?>.<?=$d2[server_ida];?>.<?=$d2[server_idc];?>.<?=$d2[server_idd];?>.<?=$d2[server_ide];?></td>
		<td><?=trim($d2[case_no]);?></td>
		<td><u><?=$d2[client_file]?></u></td>
		<td><?=$d2[circuit_court]?></td>
		<td><? if($d2[bill410] && $type == "CLOSED"){ echo "CLOSED AND BILLED";}elseif( $d2[filing_status]=="REOPENED"){ echo "Reopen-$d2[reopenDate]";}elseif($d2[affidavit_status] == "SERVICE CONFIRMED"){ echo "Blackhole"; }else{ echo "Active"; } ?></td>
		<td> </td>
		<td><a href="courier.php?date=<?=$d2[estFileDate]?>" target="_Blank"><?=$d2[estFileDate]?></a></td>
		<? if ($d2[fileDate] != "0000-00-00"){ $end = $d2[fileDate]; } else { $end =$d2[estFileDate] ;} ?>
		<? if ($d2[reopenDate] != "0000-00-00"){ $start = $d2[reopenDate].' 12:00:00'; }  elseif ($d2[dispatchDate] != '0000-00-00 00:00:00') { $start =$d2[dispatchDate] ;}else{ $start =$d2[date_received] ; } ?>
		<td><?=benchMark2($d2[date_received],$start);?>-<?=($d2[date_received],$start);?></td>
		<td><?=benchmark($start,$end);?></td>
	</tr>
<? } $count2= $i2; ?>
<? while($d3=mysql_fetch_array($r3,MYSQL_ASSOC)){ $i3++; 
if ($d3[estFileDate] == '0000-00-00' && $type == 'OPEN'){
	echo "<script>alert('File ".$d3["$idType"]." Missing Est. Close Date!')</script>";
}
$notice='';
?>
	<tr style="background-color:<? if( $d3[filing_status]=="REOPENED"){ echo "#FF8866";}else{ echo rowBgColor($i);}?>;">
		<td>STANDARD</td>
		<td><?=$d3[packet_id];?></td>
		<? if ($type == "CLOSED"){ ?>
		<td><?=$d3[fileDate];?></td>
		<? }else{?>
		<td><?=$d3[date_received];?></td>
		<? } ?>
		<td><?=$notice;?></td>
		<td><?=$d3[state1];?><?=$d3[state1a];?><?=$d3[state1b];?><?=$d3[state1c];?><?=$d3[state1d];?><?=$d3[state1e];?></td>
		<td><?=attorney($d3[attorneys_id])?></td>
		<td><?=$d3[server_id];?>.<?=$d3[server_ida];?>.<?=$d3[server_idc];?>.<?=$d3[server_idd];?>.<?=$d3[server_ide];?></td>
		<td><?=trim($d3[case_no]);?></td>
		<td><u><?=$d3[client_file]?></u></td>
		<td><?=$d3[circuit_court]?></td>
		<td><? if($d3[bill410] && $type == "CLOSED"){ echo "CLOSED AND BILLED";}elseif( $d3[filing_status]=="REOPENED"){ echo "Reopen-$d3[reopenDate]";}elseif($d3[affidavit_status] == "SERVICE CONFIRMED"){ echo "Blackhole"; }else{ echo "Active"; } ?></td>
		<td><?=$d3[affidavit_status]?></td>
		<td><a href="courier.php?date=<?=$d3[estFileDate]?>" target="_Blank"><?=$d3[estFileDate]?></a></td>
		<? if ($d3[fileDate] != "0000-00-00"){ $end = $d3[fileDate]; } else { $end =$d3[estFileDate] ;} ?>
		<? if ($d3[reopenDate] != "0000-00-00"){ $start = $d3[reopenDate].' 12:00:00'; }  elseif ($d3[dispatchDate] != '0000-00-00 00:00:00') { $start =$d3[dispatchDate] ;}else{ $start =$d3[date_received] ; } ?>
		<td><?=benchMark2($d3[date_received],$start);?>-<?=($d3[date_received],$start);?></td>
		<td><?=benchmark($start,$end);?></td>
	</tr>
<? } $count3= $i3; ?>
	
	
<?
$report = ob_get_clean();
$r=@mysql_query("select distinct fileDate from ps_packets where fileDate <> '0000-00-00' order by fileDate DESC");
while ($dloop = mysql_fetch_array($r,MYSQL_ASSOC)){
$options .= "<option>$dloop[fileDate]</option>";
}

?>
<center><b><?=date('l, F jS g:i:s a');?></b>
<div style="width:400px;" class="noprint">
<b>MDWestServe, Inc.</b><br>
<table cellspacing="0" width="400px;" style='background-color:#fff;'>
<form id="form">
<input type="hidden" and name="uid" value="<?=$_GET[uid]?>">
	<tr>
		<td>Report:</td>
		<td><select name="type" onChange="form.submit()"><option><?=$_GET[type]?></option><option>CLOSED</option><option>OPEN</option></select>Open/Closed</td>
	</tr>
	<? if ($_GET[type] == "CLOSED"){ ?>
	<tr>
		<td>Run Date:</td>
		<td><select name="from" onChange="form.submit()"><option><?=$_GET[from];?></option><?=$options;?></select> to <select name='to' onChange="form.submit()"><option><?=$_GET[to];?></option><?=$options;?></select></td>
	</tr>
	<? }?>
	<tr>
		<td>Presale Cases:</td>
		<td><?=$count;?> (<?=$count4?> Mail Only)</td>
	</tr>
	<tr>
		<td>Eviction Cases:</td>
		<td><?=$count2;?></td>
	</tr>
	<tr>
		<td>Standard Cases:</td>
		<td><?=$count3;?></td>
	</tr>
</form>
</table>	
</div>
</center>
<? $header = ob_get_clean(); ?>


<!--<link rel="stylesheet" type="text/css" href="fire.css" />-->

<style>
td { white-space:pre; font-size:10px; }
a { text-decoration:none; color:#330088;}
body { margin:0px; padding:0px;}
    @media print {
      .noprint { display: none; }
    }

</style>
<center>
<?=$header;?>
<?=output($report);?>
</center>


<meta http-equiv="refresh" content="300" />

<div class="noprint" style="position:absolute; top:0px; right:0px; border:solid 5px #ffcc99; background-color:#FFF; font-size:16px;">
Service Closed in <? $live = number_format($_SESSION[total]/$_SESSION[items],2); echo $live;?> days<br>
Dispatched in <? $live2 = number_format($_SESSION[dTotal]/$_SESSION[items],2); echo $live2;?> days<br>
<?=$_SESSION[miss];?> unreported cases<br>
Reported days: <?=$_SESSION[total];?><br>
Reported cases: <?=$_SESSION[items];?>
<? 
if ($_GET[type] != "CLOSED" && $_GET[svc] != 'Eviction' && (!$_GET[attid] || $_GET[attid] == "ALL") ){
@mysql_query("update systemStatus set updated=NOW(), activeBenchmark = '$live' "); 
}
if ($_GET[type] == "CLOSED" && $_GET[svc] != 'Eviction' && (!$_GET[attid] || $_GET[attid] == "ALL") ){
@mysql_query("update systemStatus set updated=NOW(), closeBenchmark = '$live' "); 
}
if ($_GET[type] != "CLOSED" && $_GET[svc] == 'Eviction' && (!$_GET[attid] || $_GET[attid] == "ALL") ){
@mysql_query("update systemStatus set updated=NOW(), activeBenchmark2 = '$live' "); 
}
if ($_GET[type] == "CLOSED" && $_GET[svc] == 'Eviction' && (!$_GET[attid] || $_GET[attid] == "ALL") ){
@mysql_query("update systemStatus set updated=NOW(), closeBenchmark2 = '$live' "); 
}
?>
</div>
<script>document.title='Open / Close Report'</script>