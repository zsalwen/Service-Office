<style>
body { font-variant:small-caps; }
div { font-variant:small-caps; }
td { font-variant:small-caps; }
</style>
<?
mysql_connect();
mysql_select_db('service');
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
$q="select * from ps_packets where packet_id = '$_GET[packet]'";
$r=@mysql_query($q);
$d=mysql_fetch_array($r,MYSQL_ASSOC);

$q2 = "SELECT bill410, bill420, bill430, address, server_id, packet_id, client_file, case_no, date_received, service_status, display_name, ps_packets.attorneys_id FROM ps_packets, attorneys WHERE ps_packets.attorneys_id = attorneys.attorneys_id AND packet_id = '$_GET[packet]'";
$r2=@mysql_query($q2);
$d2=mysql_fetch_array($r2,MYSQL_ASSOC);


$due = $d[bill410] + $d[bill420]+ $d[bill430] - $d[code410] - $d[code420] -$d[code430] - $d[code410a] - $d[code420a] -$d[code430a] - $d[code410b] - $d[code420b] -$d[code430b];
$mail_address = explode('-',$d2['address']);
$mail_address1 = trim($mail_address[0]);
$mail_address2 = trim($mail_address[1]);
$mail_address3 = trim($mail_address[2]);
$mail_address4 = trim($mail_address[3]);

?>
<table width="768px" align="center">
	<tr>
		<td><?=$mail_address1;?><br><?=$mail_address2;?><br><?=$mail_address3;?><br><?=$mail_address4;?><br>File #<b><?=$d[client_file]?></b></td>
		<td align="right"><img src="http://staff.mdwestserve.com/small.logo.gif"></td>
	</tr>
</table>
<h2 align="center">Invoice For Packet <?=$_GET[packet]?></h2>
<table cellspacing="0" cellpadding="3" width="768px" align="center">
	<tr>
	<? if ($due < 0){ ?>
		<td align="center" style="border:solid 1px #666666;"><b>OVERPAID $<?=$due*-1?></b></td>
	<? }elseif($due==0){ ?>	
		<td align="center" style="border:solid 1px #666666;"><b>PAID IN FULL</b></td>
	<? }else{ ?>	
		<td align="center" style="border:solid 1px #666666;"><b>DUE $<?=$due?></b></td>
	<? } ?>
		<td>Service Bill</td>
		<td>Mailing Bill</td>
		<td>Filing Bill</td>
	</tr>
	<tr>
		<td>Amount Due</td>
		<td>$<?=$d[bill410];?></td>
		<td>$<?=$d[bill420];?></td>
		<td>$<?=$d[bill430];?></td>
		
	</tr>

	<tr>
		<td>Check #<?=$d[client_check];?></td>
		<td>$<?=$d[code410];?></td>
		<td>$<?=$d[code420];?></td>
		<td>$<?=$d[code430];?></td>
	</tr>
	<tr>
		<td>Check #<?=$d[client_checka];?></td>
		<td>$<?=$d[code410a];?></td>
		<td>$<?=$d[code420a];?></td>
		<td>$<?=$d[code430a];?></td>
	</tr>
	<tr>
		<td>Check #<?=$d[client_checkb];?></td>
		<td>$<?=$d[code410b];?></td>
		<td>$<?=$d[code420b];?></td>
		<td>$<?=$d[code430b];?></td>
	</tr>
</table>
<table cellspacing="0" cellpadding="3" width="768px" align="center">
	<tr>
		<td>Filing Status</td>
		<td>Service Status</td>
		<td>Process Status</td>
		<td>Mail Status</td>
	</tr>
	<tr>
		<td style="font-size:10px"><?=$d[filing_status];?></td>
		<td style="font-size:10px"><?=$d[service_status];?></td>
		<td style="font-size:10px"><?=$d[process_status];?></td>
		<td style="font-size:10px"><?=$d[mail_status];?></td>
	</tr>
</table>
<table cellspacing="0" cellpadding="3" width="768px" align="center">
	<tr>
		<td valign="top"><center><div style="font-size:8px;text-align:left;"><b>Case Documents</b><br><?
	$q5="SELECT * FROM ps_affidavits WHERE packetID = '$d[packet_id]' order by defendantID";
	$r5=@mysql_query($q5) or die ("Query: $q5<br>".mysql_error());
	while ($d5=mysql_fetch_array($r5, MYSQL_ASSOC)){
			$defname = $d["name".$d5[defendantID]];
			echo "$defname: $d5[method]<br>";
	}
	?>
	</div></center></td>
	 <td valign="top" style="font-size:8px;text-align:left;"><b>Service Data</b><br>
	 <? if($d[name1]){ echo $d[name1];}?>
	 <? if($d[name2]){ echo "<br>$d[name2]";}?>
	 <? if($d[name3]){ echo "<br>$d[name3]";}?>
	 <? if($d[name4]){ echo "<br>$d[name4]";}?>
	 <? if($d[name5]){ echo "<br>$d[name5]";}?>
	 <? if($d[name6]){ echo "<br>$d[name6]";}?>
	 <? if($d[address1]){ echo "<br>$d[address1], $d[city1], $d[state1] $d[zip1]";}?>
	 <? if($d[address1a]){ echo "<br>$d[address1a], $d[city1a], $d[state1a] $d[zip1a]";}?>
	 <? if($d[address1b]){ echo "<br>$d[address1b], $d[city1b], $d[state1b] $d[zip1b]";}?>
	 <? if($d[address1c]){ echo "<br>$d[address1c], $d[city1c], $d[state1c] $d[zip1c]";}?>
	 <? if($d[address1d]){ echo "<br>$d[address1d], $d[city1d], $d[state1d] $d[zip1d]";}?>
	 <? if($d[address1e]){ echo "<br>$d[address1e], $d[city1e], $d[state1e] $d[zip1e]";}?>
	 </td>
</tr>
</table>
		<div align="center">MAKE PAYABLE TO<br>MDWestServe, Inc.<br>300 East Joppa Road<br>Hampton Plaza - Suite 1103<br>Baltimore, MD 21286</div>
