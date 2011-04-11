<? include 'common.php';

$_SESSION[estFileDate] = $_POST[estFileDate];

function bwaRate($zip){
	mysql_select_db ('core');
	$r=@mysql_query("select * from bwa where zip = '$zip'");
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	if ($d[rateTypeA]){
		return '[BWA-A'.trim($d[rateTypeA]).']';
	}
}
function defTotal($packet_id){
	mysql_select_db ('core');
	$q="SELECT name1, name2, name3, name4, name5, name6 FROM ps_packets WHERE packet_id='$packet_id'";
	$r=@mysql_query($q) or die("Query: defendantTotal: $q<br>".mysql_error());
	$i=0;
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	if($d[name1]){ $i++; }
	if($d[name2]){ $i++; }
	if($d[name3]){ $i++; }
	if($d[name4]){ $i++; }
	if($d[name5]){ $i++; }
	if($d[name6]){ $i++; }
	return $i;
}
function addTotal($packet_id){
	mysql_select_db ('core');
	$q="SELECT address1, address1a, address1b, address1c, address1d, address1e FROM ps_packets WHERE packet_id='$packet_id'";
	$r=@mysql_query($q) or die("Query: defendantTotal: $q<br>".mysql_error());
	$i=0;
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	if($d[address1]){ $i++; }
	foreach(range('a','e') as $letter){
		if($d["address1$letter"]){ $i++; }
	}
	return $i;
}
function outOfState($packet_id){
	$q="SELECT state1, state1a, state1b, state1c, state1d, state1e, state2, state2a, state2b, state2c, state2d, state2e, state3, state3a, state3b, state3c, state3d, state3e, state4, state4a, state4b, state4c, state4d, state4e, state5, state5a, state5b, state5c, state5d, state5e, state6, state6a, state6b, state6c, state6d, state6e from ps_packets WHERE packet_id = '$packet_id'";
	$r=@mysql_query($q) or die("Query: outOfState: $q<br>".mysql_error());
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	$i=0;
	if (strtoupper($d[state1e]) != 'MD' && $d[state1e] != ''){ $i++; }
	if (strtoupper($d[state1d]) != 'MD' && $d[state1d] != ''){ $i++; }
	if (strtoupper($d[state1c]) != 'MD' && $d[state1c] != ''){ $i++; }
	if (strtoupper($d[state1b]) != 'MD' && $d[state1b] != ''){ $i++; }
	if (strtoupper($d[state1a]) != 'MD' && $d[state1a] != ''){ $i++; }
	if (strtoupper($d[state1]) != 'MD' && $d[state1] != ''){ $i++; }
	if (strtoupper($d[state2e]) != 'MD' && $d[state2e] != ''){ $i++; }
	if (strtoupper($d[state2d]) != 'MD' && $d[state2d] != ''){ $i++; }
	if (strtoupper($d[state2c]) != 'MD' && $d[state2c] != ''){ $i++; }
	if (strtoupper($d[state2b]) != 'MD' && $d[state2b] != ''){ $i++; }
	if (strtoupper($d[state2a]) != 'MD' && $d[state2a] != ''){ $i++; }
	if (strtoupper($d[state2]) != 'MD' && $d[state2] != ''){ $i++; }
	if (strtoupper($d[state3e]) != 'MD' && $d[state3e] != ''){ $i++; }
	if (strtoupper($d[state3d]) != 'MD' && $d[state3d] != ''){ $i++; }
	if (strtoupper($d[state3c]) != 'MD' && $d[state3c] != ''){ $i++; }
	if (strtoupper($d[state3b]) != 'MD' && $d[state3b] != ''){ $i++; }
	if (strtoupper($d[state3a]) != 'MD' && $d[state3a] != ''){ $i++; }
	if (strtoupper($d[state3]) != 'MD' && $d[state3] != ''){ $i++; }
	if (strtoupper($d[state4e]) != 'MD' && $d[state4e] != ''){ $i++; }
	if (strtoupper($d[state4d]) != 'MD' && $d[state4d] != ''){ $i++; }
	if (strtoupper($d[state4c]) != 'MD' && $d[state4c] != ''){ $i++; }
	if (strtoupper($d[state4b]) != 'MD' && $d[state4b] != ''){ $i++; }
	if (strtoupper($d[state4a]) != 'MD' && $d[state4a] != ''){ $i++; }
	if (strtoupper($d[state4]) != 'MD' && $d[state4] != ''){ $i++; }
	if (strtoupper($d[state5e]) != 'MD' && $d[state5e] != ''){ $i++; }
	if (strtoupper($d[state5d]) != 'MD' && $d[state5d] != ''){ $i++; }
	if (strtoupper($d[state5c]) != 'MD' && $d[state5c] != ''){ $i++; }
	if (strtoupper($d[state5b]) != 'MD' && $d[state5b] != ''){ $i++; }
	if (strtoupper($d[state5a]) != 'MD' && $d[state5a] != ''){ $i++; }
	if (strtoupper($d[state5]) != 'MD' && $d[state5] != ''){ $i++; }
	if (strtoupper($d[state6e]) != 'MD' && $d[state6e] != ''){ $i++; }
	if (strtoupper($d[state6d]) != 'MD' && $d[state6d] != ''){ $i++; }
	if (strtoupper($d[state6c]) != 'MD' && $d[state6c] != ''){ $i++; }
	if (strtoupper($d[state6b]) != 'MD' && $d[state6b] != ''){ $i++; }
	if (strtoupper($d[state6a]) != 'MD' && $d[state6a] != ''){ $i++; }
	if (strtoupper($d[state6]) != 'MD' && $d[state6] != ''){ $i++; }
	return ($i * 20);
}

function getCounty($zip){
	$r=@mysql_query("select county from zip_code where zip_code = '$zip' LIMIT 0,1");
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	return strtoupper($d[county]);
}
function checkPay($packet,$product){
	$q="SELECT * from ps_pay WHERE packetID='$packet' AND product='$product' LIMIT 0,1";
	$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	if (!$d[payID]){
		@mysql_query("INSERT INTO ps_pay (packetID,product) VALUES ('$packet','$product')");
	}
}
if ($_COOKIE[psdata][level] != "Operations"){
			$event = 'packages.php';
			$email = $_COOKIE[psdata][email];
			$q1="INSERT into ps_security (event, email, entry_time) VALUES ('$event', '$email', NOW())";
			//@mysql_query($q1) or die(mysql_error());
			header('Location: http://staff.mdwestserve.com');
}

if ($_POST[submit]){
	if (!$_POST[package][contractor] || !$_POST[server_id]){
		echo '<script>alert("Please make sure that you have entered a contractor rate and selected a server.")</script>';
	}elseif($_POST[estFileDate] == '' || $_POST[estFileDate] == '0000-00-00'){
		echo '<script>alert("Please enter an Estimated Close Date.")</script>';
	}else{

function dispatchTimeline($pkg){
	$r=@mysql_query("select packet_id from ps_packets where package_id = '$pkg'");
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		timeline($d[packet_id],$_COOKIE[psdata][name]." Dispatched Order, Deadline: $_SESSION[estFileDate]");
	}
}
function packageFile($package_id, $file_id, $contractor_rate, $contractor_ratea, $contractor_rateb, $contractor_ratec, $contractor_rated, $contractor_ratee){
	$oosc=outOfState($file_id);
	timeline($file_id,$_COOKIE[psdata][name]." Packaged Order");
	$q = "UPDATE ps_packets, ps_pay SET 
									ps_packets.package_id='$package_id',
									ps_pay.contractor_rate='$contractor_rate',
									ps_pay.contractor_ratea='$contractor_ratea',
									ps_pay.contractor_rateb='$contractor_rateb',
									ps_pay.contractor_ratec='$contractor_ratec',
									ps_pay.contractor_rated='$contractor_rated',
									ps_pay.contractor_ratee='$contractor_ratee',
									ps_packets.outofstate_cost='$oosc',
									ps_packets.estFileDate='$_SESSION[estFileDate]',
									ps_packets.dispatchDate=NOW()
										WHERE packet_id = '$file_id' AND ps_packets.packet_id=ps_pay.packetID AND ps_pay.product='OTD'";
	$r=@mysql_query($q);
}

function makePackage($array1,$array2,$array3,$array4,$array5,$array6,$array7,$package_id){
//	echo "Package ID :: $package_id";
//	echo "Client Rate :: $array2[0]<br>";
//	echo "Contractor Rate :: $array3[0]<br>";
//	echo "for file id's (the foreach loop went here) :: ";
	foreach ($array1 as $id) {
		checkPay($id,'OTD');
		packageFile($package_id,$id,$array2[0],$array3[0],$array4[0],$array5[0],$array6[0],$array7[0]);
		//echo "$id ";
	}
}	


	$q1 = "INSERT into ps_packages (set_date, assign_date) values (NOW(), NOW()) ";
	$r1=@mysql_query($q1) or die("Query: $q1<br>".mysql_error());
	$packageID = mysql_insert_id();	
	makePackage($_POST[package]['id'],$_POST[package]['contractor'],$_POST[package]['contractora'],$_POST[package]['contractorb'],$_POST[package]['contractorc'],$_POST[package]['contractord'],$_POST[package]['contractore'],$packageID);
	hardLog('Created package '.$packageID,'user');

	//monitor('Your package "' .$_POST[name]. '" has been created.');
		foreach ($_POST[package]['id'] as $value){
		$q="SELECT address1a, address1b, address1c, address1d, address1e from ps_packets WHERE packet_id='$value'";
		$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
		$fileCount=0;
		while ($d=mysql_fetch_array($r, MYSQL_ASSOC)){$fileCount++;
			$q="UPDATE ps_packets SET server_id = '$_POST[server_id]', ";
			if ($d[address1a] && $_POST[server_ida]){
				$q .= "server_ida = '$_POST[server_ida]', ";
			}elseif($d[address1a]){
				$q .= "server_ida = '$_POST[server_id]', ";
			}
			if ($d[address1b] && $_POST[server_idb]){
				$q .= "server_idb = '$_POST[server_idb]', ";
			}elseif($d[address1b]){
				$q .= "server_idb = '$_POST[server_id]', ";
			}
			if ($d[address1c] && $_POST[server_idc]){
				$q .= "server_idc = '$_POST[server_idc]', ";
			}elseif($d[address1c]){
				$q .= "server_idc = '$_POST[server_id]', ";
			}
			if ($d[address1d] && $_POST[server_idd]){
				$q .= "server_idd = '$_POST[server_idd]', ";
			}elseif($d[address1d]){
				$q .= "server_idd = '$_POST[server_id]', ";
			}
			if ($d[address1e] && $_POST[server_ide]){
				$q .= "server_ide = '$_POST[server_ide]', ";
			}elseif($d[address1e]){
				$q .= "server_ide = '$_POST[server_id]', ";
			}
			$q.="process_status='ASSIGNED' WHERE packet_id ='$value'";
			@mysql_query($q) or die ("Query: $q<br>".mysql_error());
		}
		$packageName=$fileCount.initals(id2name($_POST[server_id]));
		if ($_POST[server_ida]){
			$packageName .= initals(id2name($_POST[server_ida]));
		}
		if ($_POST[server_idb]){
			$packageName .= initals(id2name($_POST[server_idb]));
		}
		if ($_POST[server_idc]){
			$packageName .= initals(id2name($_POST[server_idc]));
		}
		if ($_POST[server_idd]){
			$packageName .= initals(id2name($_POST[server_idd]));
		}
		if ($_POST[server_ide]){
			$packageName .= initals(id2name($_POST[server_ide]));
		}
		$packageName = $packageName.date('mdY-H:i:s');
		$q3 = "UPDATE ps_packages SET name='$packageName', description='Est. Close $_SESSION[estFileDate]' where id = '$packageID'";
		@mysql_query($q3) or die ("Query: $q3<br>".mysql_error());
		hardLog('Dispatched file '.$value,'user');
		timeline($value,$_COOKIE[psdata][name]." Dispatched Order, Deadline: $_SESSION[estFileDate]");
	}
	}
}

?>
<style>
.ppd{border-bottom:hidden; border-right:hidden;}
.ppp{border-right:hidden;}
a:visited{color:CC00EE;}
</style>
<form method="post">
<div style="background-color:#FF0000; font-size:18px;">Estimated Close Date (YYYY-MM-DD): <input name="estFileDate" value="<?=$_SESSION[estFileDate]?>"></div>
<table width="100%"><tr><td valign="top">
<table border="1" style="border-collapse:collapse" align="center" width="100%">
    <tr bgcolor="<? if (outOfState($d[packet_id]) > 0){ echo '#ffff00';}else{ echo row_color(2,'#ccccff','#99cccc'); }?>">
		<td>Links</td>
        <td>Client</td>
        <td>Date Received</td>
        <td>Client File</td>
		<td>Case No.</td>
        <td>Circuit Court</td>
        <td>D</td>
        <td>Cities</td>
		<td>Loss Mitigation Type</td>
        <td>Notes</td>
	</tr>


<?
$q= "select * from ps_packets where process_status = 'READY' and package_id = '' order by circuit_court";
$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
$i=0;
while ($d=mysql_fetch_array($r, MYSQL_ASSOC)) {$i++;
?>
    <tr style='background-color:<? if(addTotal($d[packet_id]) == 1 && $d[avoidDOT] == 'checked'){ echo "#000000; color: #FFFFFF; font-weight:bold;"; }elseif (outOfState($d[packet_id]) > 0){ echo row_color(2,'#009999','#00FFFF');}else{ echo row_color($i,'#FFFFFF','#cccccc'); }?>;'>
		<td nowrap="nowrap">
		<? if(($d[uspsVerify] != '') && ($d[qualityControl] != '')){ ?><input type="checkbox" name="package[id][<?=$d[packet_id]?>]" value="<?=$d[packet_id]?>" /><? } ?>&nbsp;<a href="order.php?packet=<?=$d[packet_id]?>" target="_blank" style="text-decoration:none"><?=$d[packet_id]?>)</a></td>
        <td><?=id2attorney($d[attorneys_id])?></td>
        <td><?=substr($d[date_received],0,10)?></td>
        <td><?=$d[client_file] ?></td>
		<td><?=$d[case_no] ?></td>
        <td><?=str_replace(' ','&nbsp;',$d[circuit_court]) ?></td>
        <td align="center"><?=defTotal($d[packet_id]);?></td>
        <td nowrap="nowrap"><? 
			$list='';
			foreach (range('a','e') as $letter){
				if ($d["address1$letter"]){
					$list .= "<a target='_Blank' href='http://staff.mdwestserve.com/dispatcher.php?aptsut=&address=".$d["address1$letter"]."&city=".$d["city1$letter"]."&state=".$d["state1$letter"]."&miles=20' title='".$d["address1$letter"].", ".$d["city1$letter"].", ".$d["state1$letter"]." ".$d["zip1$letter"]."'>Service: ".$d["address1$letter"].", ".$d["city1$letter"].", ".$d["state1$letter"]." ".$d["zip1$letter"]." <b>[".getCounty($d["zip1$letter"])."]</b></a>".bwaRate($d["zip1$letter"])."<br>";
				}
			}
			if ($list == '' || $d[attorneys_id] == 1){
				$list .= "<a target='_Blank' href='http://staff.mdwestserve.com/dispatcher.php?aptsut=&address=$d[address1]&city=$d[city1]&state=$d[state1]&miles=20' title='$d[address1], $d[city1], $d[state1], $d[zip1]'>Serve &amp; Post: $d[address1], $d[city1], $d[state1] $d[zip1] <b>[".getCounty($d[zip1])."]</b></a>".bwaRate($d[zip1]);
			}else{
				$list .= "<a target='_Blank' href='http://staff.mdwestserve.com/dispatcher.php?aptsut=&address=$d[address1]&city=$d[city1]&state=$d[state1]&miles=20' title='$d[address1], $d[city1], $d[state1], $d[zip1]'>Posting: $d[address1], $d[city1], $d[state1] $d[zip1] <b>[".getCounty($d[zip1])."]</b></a>".bwaRate($d[zip1]);
			}
			echo $list;
			?></td>
			<td <? if($d[avoidDOT] == 'checked'){echo "style='background-color:#FF0000;'";}?>><? if(addTotal($d[packet_id]) == 1 && $d[avoidDOT] == 'checked'){echo "<blink>";} ?><?=$d[lossMit]?><? if(addTotal($d[packet_id]) == 1 && $d[avoidDOT] == 'checked'){echo "</blink>";} ?><? if ($d[rush]){ echo "<b style='display:block; background-color:FFBB00;'>RUSH</b>";} ?></td>
            <td><?if ($d[attorneys_id] == '70' && $d[lossMit] != 'N/A - OLD L'){ echo "<a href='http://staff.mdwestserve.com/otd/stuffPacket.bgw.php?packet=$d[packet_id]' target='_blank'>[ENVELOPE]</a> ";} if(($d[uspsVerify] == '') || ($d[qualityControl] == '')){ echo "<b>NEEDS VERIFICATION</b><br>"; } ?><?=strtoupper($d[processor_notes])?></td>
	</tr>
<?  
}
$q2= "select * from ps_users where contract = 'YES'";
$r2=@mysql_query($q2) or die("Query: $q2<br>".mysql_error());
while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)) {
	$sList .= "<option value='$d2[id]'>";
	if ($d2[company]){
		$sList .= $d2[company].', '.$d2[name];
	}else{
		$sList .=  $d2[name];
	}
	$sList .= "</option>";
} 
?>
</table>
<table width="100%" class="noprint">
	<tr bgcolor="<?=row_color($i,'#99cccc','#ccccff')?>">
        <td class="ppd" align="left">Service Rate: <input size="3" name="package[contractor][<?=$d[packet_id]?>]"/><br>
		Server: <select name="server_id"><option value=''>Select Server</option><?=$sList?></select></td>
        <td class="ppd" align="left">Service Rate "a": <input size="3" name="package[contractora][<?=$d[packet_id]?>]"/><br>
		Server "A": <select name="server_ida"><option value=''>Select Server 'A'</option><?=$sList?></select></td>
    	<td class="ppd">Service Rate "b": <input size="3" name="package[contractorb][<?=$d[packet_id]?>]" /><br>
		Server "B": <select name="server_idb"><option value=''>Select Server 'B'</option><?=$sList?></select></td><td></td></tr><tr bgcolor="<?=row_color($i,'#99cccc','#ccccff')?>">
		<td class="ppd">Service Rate "c": <input size="3" name="package[contractorc][<?=$d[packet_id]?>]" /><br>
		Server "C": <select name="server_idc"><option value=''>Select Server 'C'</option><?=$sList?></select></td>
		<td class="ppd">Service Rate "d": <input size="3" name="package[contractord][<?=$d[packet_id]?>]" /><br>
		Server "D": <select name="server_idd"><option value=''>Select Server 'D'</option><?=$sList?></select></td>
		<td class="ppd">Service Rate "e": <input size="3" name="package[contractore][<?=$d[packet_id]?>]" /><br>
		Server "E": <select name="server_ide"><option value=''>Select Server 'E'</option><?=$sList?></select></td>
        <td class="ppp"><input type="submit" name="submit" value="Package Files" /></td>
    </tr>
</form>
</table></td></tr></table>
<script>document.title='Pre-Sale Dispatch and Packaging'</script>
<? include 'footer.php' ; ?>