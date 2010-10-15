<?
include 'common.php';
if ($_GET[date]){
	$today=$_GET[date];
}else{
	$today=date('Y-m-d');
}
$_SESSION[fileDate] = $today;

function attachmentList($packet,$type){
$list = "<fieldset><legend>Electronic File Storage</legend>";
mysql_select_db('core');
if ($type == 'EV'){
	$packet='EV'.$packet;
}
$r=@mysql_query("select * from ps_affidavits where packetID = '$packet' order by defendantID");
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$affidavit=$d[affidavit];
	$affidavit=str_replace('http://mdwestserve.com/ps/affidavits/','http://mdwestserve.com/affidavits/',$affidavit);
$list .= "<li><a href='$affidavit'>$d[method]</a></li>";
}
$list .= "</fieldset>";
return $list;
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

$stop=0;
if ($_GET[update] && $_GET[id]){
	if ($_GET[update] == 'FILED WITH COURT' || $_GET[update] == 'FILED WITH COURT - FBS'){
		if ($_GET[svc] == 'EV'){
			ev_timeline($_GET[id],$_COOKIE[psdata][name]." Confirmed Filing by Return From Court");
			$qdr=@mysql_query("SELECT * from evictionPackets where eviction_id='$_GET[id]'");
		}else{
			timeline($_GET[id],$_COOKIE[psdata][name]." Confirmed Filing by Return From Court");
			$qdr=@mysql_query("SELECT * from ps_packets where packet_id='$_GET[id]'");
		}
		$ddr=mysql_fetch_array($qdr, MYSQL_ASSOC);
		if ($ddr[filing_status] != 'FILED WITH COURT' && $ddr[filing_status] != 'FILED WITH COURT - FBS'){
			if ($_GET[svc] == 'EV'){
				$packet=$ddr[eviction_id];
				$svc='Eviction';
			}else{
				$packet=$ddr[packet_id];
				$svc='Packet';
			}
			$file1 = "http://mdwestserve.com/ps/liveAffidavit.php?packet=$packet&def=1";
			$file2 = "http://mdwestserve.com/ps/liveAffidavit.php?packet=$packet&def=2"; 
			$file3 = "http://mdwestserve.com/ps/liveAffidavit.php?packet=$packet&def=3";
			$file4 = "http://mdwestserve.com/ps/liveAffidavit.php?packet=$packet&def=4";
			$file5 = "http://mdwestserve.com/ps/liveAffidavit.php?packet=$packet";
			// email client invoice
			$to = "MDWestServe Archive <mdwestserve@gmail.com>";
			$subject = "$ddr[client_file], File Completed for $svc $packet";
			$headers  = "MIME-Version: 1.0 \n";
			$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
			$headers .= "From: File Complete <file.complete@mdwestserve.com> \n";
			if ($ddr[affidavitType=='DEFAULTING PURCHASER']){
				$attR = @mysql_query("select ps_alt2 from attorneys where attorneys_id = '$ddr[attorneys_id]'");
				$attD = mysql_fetch_array($attR, MYSQL_BOTH);
				$cc = explode(',',$attD[ps_alt2]);
			}else{
				$attR = @mysql_query("select ps_to from attorneys where attorneys_id = '$ddr[attorneys_id]'");
				$attD = mysql_fetch_array($attR, MYSQL_BOTH);
				$cc = explode(',',$attD[ps_to]);
			}
			$c=0;
			$ccC = count($cc);
			while ($c < $ccC){
			$headers .= "Cc: ".$cc[$c]."\n";
			$c++;
			}
			$body ="<strong>Thank you for selecting MDWestServe as Your Process Service Provider.</strong><br>";
			if ($ddr[closeOut] != '0000-00-00'){
				$co=explode('-',$ddr[closeOut]);
				$month=monthConvert($co[1]);
				$closeOut=$month.' '.$co[2].', '.$co[0];
				$body .= "Service for $svc $packet (<strong>$ddr[client_file]</strong>) was completed on ".$closeOut.", ";
			}else{
				$body .= "Service for $svc $packet (<strong>$ddr[client_file]</strong>) is completed, ";
			}
			$body .= "and affidavits have been filed.  Closeout documents as follows:
			";
			$body .= attachmentList($_GET[id],$_GET[svc]).$_COOKIE[psdata][name]."<br>MDWestServe<br>service@mdwestserve.com<br>".time()."<br>".md5(time());
			if ($stop == 0){ // prevents from running for each defendant
			psActivity("serviceFiled");
			mail($to,$subject,$body,$headers);
			$stop++;
			} // end prevention code
			if ($ddr[process_status] == 'CLOSED' || $ddr[process_status] == 'AWAITING PAYMENT'){
			}else{
				if ($_GET[svc] == 'EV'){
					$q10="UPDATE evictionPackets SET process_status='INVOICED', affidavit_status='SERVICE CONFIRMED', fileDate='$today', fileDT=NOW() WHERE eviction_id = '$packet'";
				}else{
					$q10="UPDATE ps_packets SET process_status='INVOICED', affidavit_status='SERVICE CONFIRMED', fileDate='$today', fileDT=NOW() WHERE packet_id = '$packet'";
				}
				$r10=@mysql_query($q10) or die ("Query: $q10<br>".mysql_error());
			}
		}
	}else{
		psActivity("servicePrep");
		if ($_GET[svc] == 'EV'){
			ev_timeline($_GET[id],$_COOKIE[psdata][name]." Prepared Affidavits for Filing");
		}else{
			timeline($_GET[id],$_COOKIE[psdata][name]." Prepared Affidavits for Filing");
		}
	}
	if ($_GET[svc] == 'EV'){
		@mysql_query("update evictionPackets set filing_status = '$_GET[update]' where eviction_id = '$_GET[id]'");
	}else{
		@mysql_query("update ps_packets set filing_status = '$_GET[update]', affidavit_status2='' where packet_id = '$_GET[id]'");
	}
	opLog($_COOKIE[psdata][name]." Court Manager #$_GET[id] $_GET[update]");
}
?>
<style>
tr { background-color:#FFFFFF;}
.R{ background-color:#33CCFF;}
.W{ background-color:#FFFF00;}
.F{ background-color:#00FF00;}
.S{ background-color:#FF9966;}
.H{ background-color:#FF0000;}
a { color:#000000; text-decoration:none;}
td { font-size:12px;}
.P { background-color:#FFFF00;}
table { background-color:#FFFFFF;}
</style>

<link rel="stylesheet" type="text/css" href="fire.css" />
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

<br>
<?


?>
<table align='center'>
	<tr>
		<td valign='top'>
<form>Display Attorney ID:<input size="2"  value="<?=$_GET[att]?>" name="att" /><? if ($_GET[att]){ ?> Limit:<input size="2"  value="<?=$_GET[limits]?>" name="limits" /><? }?>
		</td>
		<td valign='top'>
		Set Close Date:<input size='10' value="<?=$today?>" name="date">
		</td>
		<td valign='top'>
		<input type="submit" value="GO &gt; &gt; &gt;" /></form>
		</td>
	</tr>
</table>

<table  class="sortable" style="border-collapse:collapse;" border="1" align='center'>
<thead>
<tr class="noprint">
<td nowrap>Received</td>
<td nowrap>Court</td>
    <td nowrap>Case</td>
    <td nowrap>Client</td>
	<td nowrap>Loss Mitigation</td>
	<td nowrap>Service Status</td>
	<td nowrap>Affidavit Status</td>
	<td nowrap>Filing Status</td>
    <td nowrap="nowrap">Green Card Status</td>
    <td nowrap colspan="2"> Update Filing Status</td>
    <td nowrap>Packet</td>
    <td nowrap="nowrap">Server(s)</td>
</tr>
</thead>

<?
if ($_GET[all]){
	if ($_GET[server]){
		$q= "select * from ps_packets where server_id='$_GET[server]' ORDER BY packet_id";
		$q2= "select * from evictionPackets where server_id='$_GET[server]' ORDER BY eviction_id";
	}else{
		$q= "select * from ps_packets ORDER BY packet_id";
		$q2= "select * from evictionPackets ORDER BY eviction_id";
	}
}elseif($_GET[att]){
	if ($_GET[limits]){
		$q= "select * from ps_packets where service_status <> 'CANCELLED' and service_status <> 'MAIL ONLY' and status <> 'CANCELLED' AND filing_status <> 'REQUESTED-DO NOT FILE!' and filing_status <> 'CANCELLED' and filing_status <> 'FILED WITH COURT' and filing_status <> 'FILED WITH COURT - FBS' and filing_status <> 'FILED BY CLIENT' and filing_status <> 'SEND TO CLIENT' and attorneys_id='$_GET[att]' ORDER BY date_received limit 0,$_GET[limits]";
		$q2= "select * from evictionPackets where service_status <> 'CANCELLED' and status <> 'CANCELLED' AND filing_status <> 'REQUESTED-DO NOT FILE!' and filing_status <> 'CANCELLED' and filing_status <> 'FILED WITH COURT' and filing_status <> 'FILED WITH COURT - FBS' and filing_status <> 'FILED BY CLIENT' and filing_status <> 'SEND TO CLIENT' and attorneys_id='$_GET[att]' ORDER BY date_received limit 0,$_GET[limits]";
	}else{
		$q= "select * from ps_packets where service_status <> 'CANCELLED' and status <> 'CANCELLED' AND filing_status <> 'REQUESTED-DO NOT FILE!' and filing_status <> 'CANCELLED' and filing_status <> 'FILED WITH COURT' and filing_status <> 'FILED WITH COURT - FBS' and filing_status <> 'FILED BY CLIENT' and filing_status <> 'SEND TO CLIENT' and attorneys_id='$_GET[att]' ORDER BY date_received";
		$q2= "select * from evictionPackets where service_status <> 'CANCELLED' and status <> 'CANCELLED' AND filing_status <> 'REQUESTED-DO NOT FILE!' and filing_status <> 'CANCELLED' and filing_status <> 'FILED WITH COURT' and filing_status <> 'FILED WITH COURT - FBS' and filing_status <> 'FILED BY CLIENT' and filing_status <> 'SEND TO CLIENT' and attorneys_id='$_GET[att]' ORDER BY date_received";

	}
}else{
	if ($_GET[server]){
		$q= "select * from ps_packets where service_status <> 'CANCELLED' and service_status <> 'MAIL ONLY' and status <> 'CANCELLED' AND filing_status <> 'REQUESTED-DO NOT FILE!' and filing_status <> 'CANCELLED' and filing_status <> 'FILED WITH COURT' and filing_status <> 'FILED WITH COURT - FBS' and filing_status <> 'FILED BY CLIENT' and filing_status <> 'SEND TO CLIENT' and server_id='$_GET[server]' ORDER BY date_received";
		$q2= "select * from evictionPackets where service_status <> 'CANCELLED' and status <> 'CANCELLED' AND filing_status <> 'REQUESTED-DO NOT FILE!' and filing_status <> 'CANCELLED' and filing_status <> 'FILED WITH COURT' and filing_status <> 'FILED WITH COURT - FBS' and filing_status <> 'FILED BY CLIENT' and filing_status <> 'SEND TO CLIENT' and server_id='$_GET[server]' ORDER BY date_received";
	}else{
		$q= "select * from ps_packets where service_status <> 'CANCELLED' and service_status <> 'MAIL ONLY' and status <> 'CANCELLED' AND filing_status <> 'REQUESTED-DO NOT FILE!' and filing_status <> 'CANCELLED' and filing_status <> 'FILED WITH COURT' and filing_status <> 'FILED WITH COURT - FBS' and filing_status <> 'FILED BY CLIENT' and filing_status <> 'SEND TO CLIENT' ORDER BY date_received";
		$q2= "select * from evictionPackets where service_status <> 'CANCELLED' and status <> 'CANCELLED' AND filing_status <> 'REQUESTED-DO NOT FILE!' and filing_status <> 'CANCELLED' and filing_status <> 'FILED WITH COURT' and filing_status <> 'FILED WITH COURT - FBS' and filing_status <> 'FILED BY CLIENT' and filing_status <> 'SEND TO CLIENT' ORDER BY date_received";
	}
}
$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
$i=0;
while ($d=mysql_fetch_array($r, MYSQL_ASSOC)) {$i++;
	$q1="SELECT method FROM ps_affidavits WHERE packetID='$d[packet_id]' AND (method LIKE '%Return from court%' OR method LIKE '%Copy of%')";
	$r1=@mysql_query($q1) or die ("Query: $q1<br>".mysql_error());
	$d1=mysql_fetch_array($r1, MYSQL_ASSOC);
?>
<tr class="<?=substr($d[filing_status],0,1)?>">
        <td nowrap><?=substr($d[date_received],0,10)?></td>
<td nowrap><?=$d[circuit_court]?> <?=$d[state1a]?><?=$d[state1b]?><?=$d[state1c]?><?=$d[state1d]?><?=$d[state1e]?></td>
    <td nowrap><?=$d[case_no]?></td>
    <td nowrap><?=id2attorney($d[attorneys_id])?></td>
    <td nowrap <? if($d[avoidDOT] != ''){echo "style='background-color:red;'";} ?>><?=$d[lossMit]?></td>
	<td nowrap><?=$d[service_status]?></td>
	<td nowrap><?=$d[affidavit_status]?></td>
	<td nowrap><?=$d[filing_status]?></td>
	<td nowrap><?=$d[gcStatus]?></td>
	<? if (!$d1){ ?>
	<td nowrap colspan="2" style="background-color:#00FF00;"><b><i>NO SCANNED AFFIDAVITS</i></b></td    >
	<? }else{ ?>
    <td nowrap class="noprint" style="background-color:#00FF00;"><a href="?update=FILED WITH COURT&id=<?=$d[packet_id]?>&svc=OTD&date=<?=$today?>"> FILED BY STAFF </a></td    >
    <td nowrap class="noprint" style="background-color:#00FF00;"><a href="?update=FILED WITH COURT - FBS&id=<?=$d[packet_id]?>&svc=OTD&date=<?=$today?>"> FILED BY SERVER </a></td    >
	<? } ?>
    <td nowrap align="center"><a href="http://staff.mdwestserve.com/lightboard.php?packet=<?=$d[packet_id]?>" target="_blank">OTD<?=$d[packet_id]?></a></td>
    <td nowrap="nowrap">
    <?
	echo initals(id2name($d[server_id]));
	if ($d[server_ida]){
		echo ', '.initals(id2name($d[server_ida]));
	}
	if ($d[server_idb]){
		echo ', '.initals(id2name($d[server_idb]));
	}
	if ($d[server_idc]){
		echo ', '.initals(id2name($d[server_idc]));
	}
	if ($d[server_idd]){
		echo ', '.initals(id2name($d[server_idd]));
	}
	if ($d[server_ide]){
		echo ', '.initals(id2name($d[server_ide]));
	}
	?>
    </td>
</tr>
<? }
$r2=@mysql_query($q2) or die("Query: $q2<br>".mysql_error());
while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)) {$i++;
	$q1="SELECT method FROM ps_affidavits WHERE packetID='EV$d2[eviction_id]' AND (method LIKE '%Return from court%' OR method LIKE '%Copy of%')";
	$r1=@mysql_query($q1) or die ("Query: $q1<br>".mysql_error());
	$d1=mysql_fetch_array($r1, MYSQL_ASSOC);
?>
<tr class="<?=substr($d2[filing_status],0,1)?>">
        <td nowrap><?=substr($d2[date_received],0,10)?></td>
<td nowrap><?=$d2[circuit_court]?> <?=$d2[state1a]?><?=$d2[state1b]?><?=$d2[state1c]?><?=$d2[state1d]?><?=$d2[state1e]?></td>
    <td nowrap><?=$d2[case_no]?></td>
    <td nowrap><?=id2attorney($d2[attorneys_id])?></td>
	<td nowrap></td>
	<td nowrap><?=$d2[service_status]?></td>
	<td nowrap><?=$d2[affidavit_status]?></td>
	<td nowrap><?=$d2[filing_status]?></td>
	<td nowrap><?=$d2[gcStatus]?></td>
	<? if (!$d1){ ?>
	<td nowrap colspan="2" style="background-color:#00FF00;"><b><i>NO SCANNED AFFIDAVITS</i></b></td    >
	<? }else{ ?>
    <td nowrap class="noprint" style="background-color:#00FF00;"><a href="?update=FILED WITH COURT&id=<?=$d2[eviction_id]?>&svc=EV&date=<?=$today?>"> FILED BY STAFF </a></td    >
    <td nowrap class="noprint" style="background-color:#00FF00;"><a href="?update=FILED WITH COURT - FBS&id=<?=$d2[eviction_id]?>&svc=EV&date=<?=$today?>"> FILED BY SERVER </a></td    >
	<? } ?>
    <td nowrap align="center"><a href="http://staff.mdwestserve.com/lightboard.php?packet=EV<?=$d2[eviction_id]?>" target="_blank">EV<?=$d2[eviction_id]?></a></td>
    <td nowrap="nowrap">
    <?
	echo initals(id2name($d2[server_id]));
	if ($d2[server_ida]){
		echo ', '.initals(id2name($d2[server_ida]));
	}
	if ($d2[server_idb]){
		echo ', '.initals(id2name($d2[server_idb]));
	}
	if ($d2[server_idc]){
		echo ', '.initals(id2name($d2[server_idc]));
	}
	if ($d2[server_idd]){
		echo ', '.initals(id2name($d2[server_idd]));
	}
	if ($d2[server_ide]){
		echo ', '.initals(id2name($d2[server_ide]));
	}
	?>
    </td>
</tr>
<? }?>
<div style="text-align:center;">Tracking Report: <?=$i?> Cases in Maryland Circuit Courts <? if ($_GET[server]){echo "for".id2name($d[server_id]);} ?>. <a href="?all=1">Show All</a>.</div>
</table>
<? include 'footer.php'; ?>
<script>document.title='Court Manager: <?=$i?> Cases'</script>