<?
mysql_connect();
// this is a read only file 
// keep it simple
// maybe offer to limit by date and month
mysql_select_db('core');
$r2=@mysql_query("select display_name from attorneys where attorneys_id = '$_GET[attid]'");
$d2=mysql_fetch_array($r2,MYSQL_ASSOC);
function leading_zeros($value, $places){
    if(is_numeric($value)){
        for($x = 1; $x <= $places; $x++){
            $ceiling = pow(10, $x);
            if($value < $ceiling){
                $zeros = $places - $x;
                for($y = 1; $y <= $zeros; $y++){
                    $leading .= "0";
                }
            $x = $places + 1;
            }
        }
        $output = $leading . $value;
    }
    else{
        $output = $value;
    }
    return $output;
}

if ($_GET[year] && $_GET[month]){
$statement = $_GET[year].'-'.$_GET[month];

if ($_GET[week]){
	$week1 = getdate(mktime(0,0,0,$_GET[month],1,$_GET[year]));
	$week2 = getdate(mktime(0,0,0,$_GET[month],8,$_GET[year]));
	$week3 = getdate(mktime(0,0,0,$_GET[month],15,$_GET[year]));
	$week4 = getdate(mktime(0,0,0,$_GET[month],22,$_GET[year]));
	$week5 = getdate(mktime(0,0,0,$_GET[month],29,$_GET[year]));

	$w1end = 7 - $week1[wday];
	$w2start = $w1end + 1;
	$w2end = $w1end + 7;
	$w3start = $w1end + 8;
	$w3end = $w1end + 14;
	$w4start = $w1end + 15;
	$w4end = $w1end + 21;
	$w5start = $w1end + 22;
	$w5end = $w1end + 28;

	if ($_GET[week] == 1){
		$weekStart = $_GET[year].'-'.$_GET[month].'-01';
		$weekEnd = $_GET[year].'-'.$_GET[month].'-'.leading_zeros($w1end, 2);
		$day[$week1[wday]] = "01";
		$day[$week1[wday]+1] = "02";
		$day[$week1[wday]+2] = "03";
		$day[$week1[wday]+3] = "04";
		$day[$week1[wday]+4] = "05";
		$day[$week1[wday]+5] = "06";
		$day[$week1[wday]+6] = "07";
	}

	if ($_GET[week] == 2){
		$weekStart = $_GET[year].'-'.$_GET[month].'-'.leading_zeros($w2start,2);
		$weekEnd = $_GET[year].'-'.$_GET[month].'-'.leading_zeros($w2end, 2);
		$day[$week1[wday]] = leading_zeros($w2start,2);
		$day[$week1[wday]+1] = leading_zeros($w2start+1,2);
		$day[$week1[wday]+2] = leading_zeros($w2start+2,2);
		$day[$week1[wday]+3] = leading_zeros($w2start+3,2);
		$day[$week1[wday]+4] = leading_zeros($w2start+4,2);
		$day[$week1[wday]+5] = leading_zeros($w2start+5,2);
		$day[$week1[wday]+6] = leading_zeros($w2start+6,2);
	}

	if ($_GET[week] == 3){
		$weekStart = $_GET[year].'-'.$_GET[month].'-'.leading_zeros($w3start,2);
		$weekEnd = $_GET[year].'-'.$_GET[month].'-'.leading_zeros($w3end, 2);
		$day[$week1[wday]] = leading_zeros($w3start,2);
		$day[$week1[wday]+1] = leading_zeros($w3start+1,2);
		$day[$week1[wday]+2] = leading_zeros($w3start+2,2);
		$day[$week1[wday]+3] = leading_zeros($w3start+3,2);
		$day[$week1[wday]+4] = leading_zeros($w3start+4,2);
		$day[$week1[wday]+5] = leading_zeros($w3start+5,2);
		$day[$week1[wday]+6] = leading_zeros($w3start+6,2);
	}

	if ($_GET[week] == 4){
		$weekStart = $_GET[year].'-'.$_GET[month].'-'.leading_zeros($w4start,2);
		$weekEnd = $_GET[year].'-'.$_GET[month].'-'.leading_zeros($w4end, 2);
		$day[$week1[wday]] = leading_zeros($w4start,2);
		$day[$week1[wday]+1] = leading_zeros($w4start+1,2);
		$day[$week1[wday]+2] = leading_zeros($w4start+2,2);
		$day[$week1[wday]+3] = leading_zeros($w4start+3,2);
		$day[$week1[wday]+4] = leading_zeros($w4start+4,2);
		$day[$week1[wday]+5] = leading_zeros($w4start+5,2);
		$day[$week1[wday]+6] = leading_zeros($w4start+6,2);
	}

	if ($_GET[week] == 5){
		$weekStart = $_GET[year].'-'.$_GET[month].'-'.leading_zeros($w5start,2);
		$weekEnd = $_GET[year].'-'.$_GET[month].'-'.leading_zeros($w5end, 2);
		$day[$week1[wday]] = leading_zeros($w5start,2);
		$day[$week1[wday]+1] = leading_zeros($w5start+1,2);
		$day[$week1[wday]+2] = leading_zeros($w5start+2,2);
		$day[$week1[wday]+3] = leading_zeros($w5start+3,2);
		$day[$week1[wday]+4] = leading_zeros($w5start+4,2);
		$day[$week1[wday]+5] = leading_zeros($w5start+5,2);
		$day[$week1[wday]+6] = leading_zeros($w5start+6,2);
	}


	//echo "<li>Week $_GET[week]: $weekStart to $weekEnd</li>";
	$con2 = " and ( evictionPackets.date_received like '$_GET[year]-$_GET[month]-$day[0]%' or";
	$con2 .= " evictionPackets.date_received like '$_GET[year]-$_GET[month]-$day[1]%' or";
	$con2 .= " evictionPackets.date_received like '$_GET[year]-$_GET[month]-$day[2]%' or";
	$con2 .= " evictionPackets.date_received like '$_GET[year]-$_GET[month]-$day[3]%' or";
	$con2 .= " evictionPackets.date_received like '$_GET[year]-$_GET[month]-$day[4]%' or";
	$con2 .= " evictionPackets.date_received like '$_GET[year]-$_GET[month]-$day[5]%' or";
	$con2 .= " evictionPackets.date_received like '$_GET[year]-$_GET[month]-$day[6]%' )";
	$con1="evictionPackets.attorneys_id= '$_GET[attid]' $con2";
}else{
	$con1="evictionPackets.date_received like '$statement%' and evictionPackets.attorneys_id= '$_GET[attid]'";
}

}

if ($con1){
	$q="select evictionPackets.eviction_id, evictionPackets.date_received, evictionPackets.service_status, evictionPackets.filing_status, ps_pay.bill410, ps_pay.bill420, ps_pay.bill430, ps_pay.code410, ps_pay.code420, ps_pay.code430, ps_pay.code410a, ps_pay.code420a, ps_pay.code430a, ps_pay.code410b, ps_pay.code420b, ps_pay.code430b from evictionPackets, ps_pay where $con1 AND evictionPackets.eviction_id=ps_pay.packetID AND ps_pay.product='EV' order by eviction_id";
}else{
	if ($_GET[limit]){
		$q="select evictionPackets.eviction_id, evictionPackets.date_received, evictionPackets.service_status, evictionPackets.filing_status, ps_pay.bill410, ps_pay.bill420, ps_pay.bill430, ps_pay.code410, ps_pay.code420, ps_pay.code430, ps_pay.code410a, ps_pay.code420a, ps_pay.code430a, ps_pay.code410b, ps_pay.code420b, ps_pay.code430b from evictionPackets, ps_pay where bill410 = '' AND evictionPackets.eviction_id=ps_pay.packetID AND ps_pay.product='EV' order by eviction_id limit 0, $_GET[limit]";
	}else{
		$q="select evictionPackets.eviction_id, evictionPackets.date_received, evictionPackets.service_status, evictionPackets.filing_status, ps_pay.bill410, ps_pay.bill420, ps_pay.bill430, ps_pay.code410, ps_pay.code420, ps_pay.code430, ps_pay.code410a, ps_pay.code420a, ps_pay.code430a, ps_pay.code410b, ps_pay.code420b, ps_pay.code430b from evictionPackets, ps_pay where bill410 = '' AND evictionPackets.eviction_id=ps_pay.packetID AND ps_pay.product='EV' order by eviction_id";
	}
}
$r=@mysql_query($q);
?>
<script>
//document.title = "Statement <?=$_GET[year];?>-<?=$_GET[month];?> for <?=$d2[display_name];?>";
document.title = "<?=$q;?>";

</script>
<table border="1">
<?
$i=0;
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$i++;
	$due = $d[bill410] +$d[bill410]+ $d[bill410] - $d[code410] - $d[code420] -$d[code430] - $d[code410a] - $d[code420a] -$d[code430a] - $d[code410b] - $d[code420b] -$d[code430b];
?>
<tr <? if (!$d[bill410]){ echo "style='background-color:FF0000;'";} else{ echo "style='background-color:00FFFF;'"; } ?>>
	<td style="border-top:solid 5px;"><a href="/AC/minips_pay.php?id=<?=$d[eviction_id];?>" target="_Blank">Packet <?=$d[eviction_id];?></a></td>
	<td style="border-top:solid 5px;">Service Bill: $<?=$d[bill410];?></td>
	<td style="border-top:solid 5px;">Mailing Bill: $<?=$d[bill420];?></td>
	<td style="border-top:solid 5px;">Filing Bill: $<?=$d[bill430];?></td>
	<td style="border-top:solid 5px;" rowspan="4"><a href="http://mdwestserve.com/ps/ps_write_invoice.php?id=<?=$d[eviction_id];?>" target="_Blank"><b>DUE<br>$<?=$due?></b></a></td>
</tr>

<tr <? if (!$d[code410]){ echo "style='background-color:FF0000;'";} else{ echo "style='background-color:00FFFF;'"; }  ?>>
	<td><?=$d[date_received];?></td>
	<td>$<?=$d[code410];?></td>
	<td>$<?=$d[code420];?></td>
	<td>$<?=$d[code430];?></td>
</tr>
<tr>
	<td><?=$d[service_status];?></td>
	<td>$<?=$d[code410a];?></td>
	<td>$<?=$d[code420a];?></td>
	<td>$<?=$d[code430a];?></td>
</tr>
<tr>
	<td><?=$d[filing_status];?></td>
	<td>$<?=$d[code410b];?></td>
	<td>$<?=$d[code420b];?></td>
	<td>$<?=$d[code430b];?></td>
</tr>
<? }  ?>
</table>
<script>document.title='<?=$i?> Files'</script>
<? mysql_close();?>