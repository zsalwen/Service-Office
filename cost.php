<?
mysql_connect();
mysql_select_db('core');
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
<form><table align="center">
	<tr>
		<td align="center">SELECT MONTH<br><select name="month"><?=mkmonth($_GET[month])?></select> <select name="year"><?=mkyear($_GET[year])?></select> <select name="att_id">
		<?
		$q8 = "SELECT * FROM attorneys where attorneys_id >'0' ORDER BY attorneys_id";		
		$r8 = @mysql_query ($q8) or die(mysql_error());
		while ($data8 = mysql_fetch_array($r8, MYSQL_ASSOC)){ 
	echo "<option value='$data8[attorneys_id]'>$data8[display_name]</option>";
		}
		?>
</select> <input type="submit" name="submit" value="GO!"></td>
		<td align="center" valign='top'>OR</td>
		<td align="center" valign='top'><a href="?all=1">VIEW ALL</a></td>
	</tr>
</table></form>
<?
if ($_GET[month]){
	$date=$_GET[year].'-'.$_GET[month];
	$for=' For '.monthConvert($_GET[month]).', '.$_GET[year];
	$q="SELECT ps_packets.state1, ps_packets.state1a, ps_packets.state1b, ps_packets.state1c, ps_packets.state1d, ps_packets.state1e, ps_pay.bill410, ps_pay.bill420, ps_pay.bill430, ps_pay.client_paid, ps_pay.client_paida, ps_pay.client_paidb, ps_pay.client_paidc, ps_pay.client_paidd, ps_pay.client_paide, ps_pay.contractor_paid, ps_pay.contractor_paida, ps_pay.contractor_paidb, ps_pay.contractor_paidc, ps_pay.contractor_paidd, ps_pay.contractor_paide, ps_packets.packet_id, ps_packets.attorneys_id FROM ps_packets, ps_pay WHERE (ps_packets.process_status <> 'DAMAGED PDF' AND ps_packets.process_status <> 'FILE COPY') AND ps_packets.date_received LIKE '%$date%' AND ps_packets.attorneys_id='".$_GET[att_id]."' AND ps_packets.packet_id=ps_pay.packetID AND ps_pay.product='OTD' order by packet_id ASC";
}elseif($_GET[all]){
	$for='';
	if ($_GET[att_id]){
		$q="SELECT ps_packets.state1, ps_packets.state1a, ps_packets.state1b, ps_packets.state1c, ps_packets.state1d, ps_packets.state1e, ps_pay.bill410, ps_pay.bill420, ps_pay.bill430, ps_pay.client_paid, ps_pay.client_paida, ps_pay.client_paidb, ps_pay.client_paidc, ps_pay.client_paidd, ps_pay.client_paide, ps_pay.contractor_paid, ps_pay.contractor_paida, ps_pay.contractor_paidb, ps_pay.contractor_paidc, ps_pay.contractor_paidd, ps_pay.contractor_paide, ps_packets.packet_id, ps_packets.attorneys_id FROM ps_packets, ps_pay WHERE (ps_packets.process_status <> 'DAMAGED PDF' AND ps_packets.process_status <> 'FILE COPY') AND ps_packets.attorneys_id='".$_GET[att_id]."' AND ps_pay.client_paid <> '' AND ps_packets.packet_id=ps_pay.packetID AND ps_pay.product='OTD' order by packet_id ASC";
	}else{
		$q="SELECT ps_packets.state1, ps_packets.state1a, ps_packets.state1b, ps_packets.state1c, ps_packets.state1d, ps_packets.state1e, ps_pay.bill410, ps_pay.bill420, ps_pay.bill430, ps_pay.client_paid, ps_pay.client_paida, ps_pay.client_paidb, ps_pay.client_paidc, ps_pay.client_paidd, ps_pay.client_paide, ps_pay.contractor_paid, ps_pay.contractor_paida, ps_pay.contractor_paidb, ps_pay.contractor_paidc, ps_pay.contractor_paidd, ps_pay.contractor_paide, ps_packets.packet_id, ps_packets.attorneys_id FROM ps_packets, ps_pay WHERE (ps_packets.process_status <> 'DAMAGED PDF' AND ps_packets.process_status <> 'FILE COPY') AND ps_packets.packet_id=ps_pay.packetID AND ps_pay.product='OTD' order by packet_id ASC";
	}
}
if ($q){
	$r=@mysql_query($q) or die (mysql_error());
	$contents = "<table align='center' border='1' style='border-collapse:collapse'><tr><td colspan='4' align='center'>Service Costs$for</td></tr><tr><td>Packet #</td><td>CLIENT PAID/TOTAL AMOUNT OWED</td><td>PAID (to contractor)</td><td>Remainder</td></tr>";
	$chargeTotal='';
	$paidTotal='';
	$owedTotal='';
	$remainderTotal='';
	$i=0;
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){$i++;
		$charge='';
		$paid='';
		$owed='';
		$remainder='';
		$charge=$d[client_paid]+$d[client_paida]+$d[client_paidb]+$d[client_paidc]+$d[client_paidd]+$d[client_paide];
		$paid=$d[contractor_paid]+$d[contractor_paida]+$d[contractor_paidb]+$d[contractor_paidc]+$d[contractor_paidd]+$d[contractor_paide];
		//$paid=$paid+($d[bill420]*0.4);
		$owed=$d[bill410]+$d[bill420]+$d[bill430];
		$remainder=$charge-$paid;
		$chargeTotal=$chargeTotal+$charge;
		$paidTotal=$paidTotal+$paid;
		$owedTotal=$owedTotal+$owed;
		$remainderTotal=$remainderTotal+$remainder;
		if (!$_GET[total]){
			$contents2 .= "<tr><td>$d[packet_id]|$d[state1]|$d[state1a]|$d[state1b]|$d[state1c]|$d[state1d]|$d[state1e]</td><td>$charge / $owed</td><td>$paid</td><td>$remainder</td></tr>";
		}
	}
	$contents2.= "<tr><td>TOTAL:</td><td>$chargeTotal / $owedTotal </td><td>$paidTotal</td><td>$remainderTotal</td></tr>";
	$chargeAverage=number_format($chargeTotal/$i,2);
	$paidAverage=number_format($paidTotal/$i,2);
	$owedAverage=number_format($owedTotal/$i,2);
	$remainderAverage=number_format($remainderTotal/$i,2);
	$contents.= "<tr><td>TOTAL:</td><td>$chargeTotal / $owedTotal </td><td>$paidTotal</td><td>$remainderTotal</td></tr>";
	$contents .= "<tr><td>AVERAGE:</td><td>$chargeAverage / $owedAverage</td><td>$paidAverage</td><td>$remainderAverage</td></tr>";
	$contents2 .= "<tr><td>AVERAGE:</td><td>$chargeAverage / $owedAverage</td><td>$paidAverage</td><td>$remainderAverage</td></tr></table>";
	echo $contents.$contents2;
}
?>