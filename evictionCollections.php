<?
mysql_connect();
mysql_select_db('core');
if ($_GET[months]){
	$months = $_GET[months];
}else{
	$months = 3;
}
$lastmonth = mktime(0, 0, 0, date("m")-$months, date("d"),   date("Y"));
$pastDue = date('Y-m-d H:i:s',$lastmonth);

function id2attorney($id){
	$q="SELECT display_name FROM attorneys WHERE attorneys_id = '$id'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
return $d[display_name];
}

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
?>
<table>
	<tr>
		<td valign="top">
		<form><h1><input name="months" value="<?=$months;?>"> Months Past Close <input name="attid" size="2" value="<?=$_GET[attid];?>"> ATTid <input type="submit"></h1></form>

<table border="1">
<?
if ($_GET[attid]){
$q="select eviction_id, date_received, service_status, filing_status, bill410, bill420, bill430, code410, code420, code430, code410a, code420a, code430a, code410b, code420b, code430b, attorneys_id from evictionPackets where bill410 <> '' AND date_received < '$pastDue' AND attorneys_id = '$_GET[attid]' order by eviction_id";
}else{
$q="select eviction_id, date_received, service_status, filing_status, bill410, bill420, bill430, code410, code420, code430, code410a, code420a, code430a, code410b, code420b, code430b, attorneys_id from evictionPackets where bill410 <> '' AND date_received < '$pastDue' order by eviction_id";
}
$r=@mysql_query($q);
$i=0;
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
$due = $d[bill410] + $d[bill420]+ $d[bill430] - $d[code410] - $d[code420] -$d[code430] - $d[code410a] - $d[code420a] -$d[code430a] - $d[code410b] - $d[code420b] -$d[code430b];
if ($due > 0){
$i++;
$masterDue = $masterDue + $due;
?>
<tr <? if (!$d[bill410]){ echo "style='background-color:FF0000;'";} else{ echo "style='background-color:00FFFF;'"; } ?>>
	<td style="border-top:solid 5px;">Packet <?=$d[eviction_id];?> for <?=id2attorney($d[attorneys_id])?><br><a href="http://staff.mdwestserve.com/ev/minips_pay.php?id=<?=$d[eviction_id];?>" target="_Blank">Update Payments</a>, <a href="http://staff.mdwestserve.com/ev/order.php?packet=<?=$d[eviction_id];?>" target="_Blank">Update Order</a> </td>
	<td style="border-top:solid 5px;">Service Bill: $<?=$d[bill410];?></td>
	<td style="border-top:solid 5px;">Mailing Bill: $<?=$d[bill420];?></td>
	<td style="border-top:solid 5px;">Filing Bill: $<?=$d[bill430];?></td>
	<td style="border-top:solid 5px;" rowspan="4"><a href="http://staff.mdwestserve.com/invoice.html.php?packet=EV<?=$d[eviction_id];?>" target="_Blank"><b>Unpaid<br>$<?=$due?></b></a></td>
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
<? }  } ?>
</table>
		</td>
		<td valign="top">
		<h1>Client Overpayments</h1>
<table border="1">
<?
if ($_GET[attid]){
$q="select eviction_id, date_received, service_status, filing_status, bill410, bill420, bill430, code410, code420, code430, code410a, code420a, code430a, code410b, code420b, code430b, attorneys_id from evictionPackets where bill410 <> '' AND attorneys_id = '$_GET[attid]' order by eviction_id";
}else{
$q="select eviction_id, date_received, service_status, filing_status, bill410, bill420, bill430, code410, code420, code430, code410a, code420a, code430a, code410b, code420b, code430b, attorneys_id from evictionPackets where bill410 <> '' order by eviction_id";
}
$r=@mysql_query($q);
$i2=0;
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
$due = $d[bill410] + $d[bill420]+ $d[bill430] - $d[code410] - $d[code420] -$d[code430] - $d[code410a] - $d[code420a] -$d[code430a] - $d[code410b] - $d[code420b] -$d[code430b];
if ($due < 0){
$i2++;
$masterDue2 = $masterDue2 + $due;
?>
<tr <? if (!$d[bill410]){ echo "style='background-color:FF0000;'";} else{ echo "style='background-color:00FFFF;'"; } ?>>
	<td style="border-top:solid 5px;">Packet <?=$d[eviction_id];?> for <?=id2attorney($d[attorneys_id])?><br><a href="http://staff.mdwestserve.com/ev/minips_pay.php?id=<?=$d[eviction_id];?>" target="_Blank">Update Payments</a>, <a href="http://staff.mdwestserve.com/ev/order.php?packet=<?=$d[eviction_id];?>" target="_Blank">Update Order</a> </td>
	<td style="border-top:solid 5px;">Service Bill: $<?=$d[bill410];?></td>
	<td style="border-top:solid 5px;">Mailing Bill: $<?=$d[bill420];?></td>
	<td style="border-top:solid 5px;">Filing Bill: $<?=$d[bill430];?></td>
	<td style="border-top:solid 5px;" rowspan="4"><a href="http://staff.mdwestserve.com/invoice.html.php?packet=EV<?=$d[eviction_id];?>" target="_Blank"><b>Overpaid<br>$<?=$due*-1?></b></a></td>
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
<? }  } ?>
</table>

		</td>
	</tr>
</table>

<script>
document.title = "90 Days Past Due: <?=$i;?> $<?=number_format($masterDue,2);?> | Overpaid: <?=$i2;?> $<?=number_format($masterDue2*-1,2);?>";
</script>


