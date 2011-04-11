<? include 'common.php';
		
$_SESSION[estFileDate] = $_POST[estFileDate];

function defTotal($eviction_id){
mysql_select_db ('core');
	$q="SELECT name1, name2, name3, name4, name5, name6 FROM evictionPackets WHERE eviction_id='$eviction_id'";
	$r=@mysql_query($q) or die("Query: defendantTotal: $q<br>".mysql_error());
	$i=0;
	while($d=mysql_fetch_array($r, MYSQL_ASSOC)){
		if($d[name1]){ $i++; }
		if($d[name2]){ $i++; }
		if($d[name3]){ $i++; }
		if($d[name4]){ $i++; }
		if($d[name5]){ $i++; }
		if($d[name6]){ $i++; }
	}
	return $i;
}
function checkPay($packet,$product){
	$q="SELECT * from ps_pay WHERE packetID='$packet' AND product='$product' LIMIT 0,1";
	$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	if (!$d[payID]){
		@mysql_query("INSERT INTO ps_pay (packetID,product) VALUES ('$packet','$product')");
	}
}
function packageFile($package_id, $file_id, $contractor_rate, $contractor_ratea){
	timeline($file_id,$_COOKIE[psdata][name]." Packaged Order");
	$q = "UPDATE evictionPackets, ps_pay SET 
									evictionPackets.package_id='$package_id',
									ps_pay.contractor_rate='$contractor_rate',
									ps_pay.contractor_ratea='$contractor_ratea',
									evictionPackets.estFileDate='$_SESSION[estFileDate]',
									evictionPackets.dispatchDate=NOW()
										WHERE evictionPackets.eviction_id = '$file_id' AND evictionPackets.eviction_id=ps_pay.packetID AND ps_pay.product='EV'";
	$r=@mysql_query($q);
}

function makePackage($array1,$array2,$array3,$package_id){
//	echo "Package ID :: $package_id";
//	echo "Client Rate :: $array2[0]<br>";
//	echo "Contractor Rate :: $array3[0]<br>";
//	echo "for file id's (the foreach loop went here) :: ";
	foreach ($array1 as $id) {
		checkPay($id,'EV');
		packageFile($package_id,$id,$array2[0],$array3[0]);
		//echo "$id ";
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
		$q1 = "INSERT into evictionPackages (set_date, assign_date) values (NOW(), NOW()) ";
		$r1=@mysql_query($q1) or die("Query: $q1<br>".mysql_error());
		$packageID = mysql_insert_id();	
		makePackage($_POST[package]['id'],$_POST[package]['contractor'],$_POST[package]['contractora'],$packageID);
		hardLog('Created package '.$packageID,'user');

		//monitor('Your package "' .$_POST[name]. '" has been created.');
		foreach ($_POST[package]['id'] as $value){
			$q="SELECT address1 from evictionPackets WHERE eviction_id='$value'";
			$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
			$fileCount=0;
			while ($d=mysql_fetch_array($r, MYSQL_ASSOC)){$fileCount++;
				$q="UPDATE evictionPackets SET server_id = '$_POST[server_id]', process_status='ASSIGNED' WHERE eviction_id ='$value'";
				@mysql_query($q) or die ("Query: $q<br>".mysql_error());
			}
			$packageName=$fileCount.initals(id2name($_POST[server_id]));
			$packageName = $packageName.date('mdY-H:i:s');
			$q3 = "UPDATE evictionPackages SET name='$packageName' where id = '$packageID'";
			@mysql_query($q3) or die ("Query: $q3<br>".mysql_error());
			hardLog('Dispatched file '.$value,'user');
			timeline($value,$_COOKIE[psdata][name]." Dispatched Order, Deadline: $_SESSION[estFileDate]");
		}
	}
}


?>
<form method="post">
<div style="background-color:#FF0000; font-size:18px;">Estimated Close Date (YYYY-MM-DD): <input name="estFileDate" value="<?=$_SESSION[estFileDate]?>"></div>
<table width="100%"><tr><td valign="top">
<table border="1" style="border-collapse:collapse" align="center" width="100%">
    <tr bgcolor="<?  echo row_color(2,'#ccccff','#99cccc'); ?>">
		<td>Links</td>
        <td>Client</td>
        <td>Date Received</td>
        <td>Client File</td>
		<td>Case No.</td>
        <td>Circuit Court</td>
        <td>D</td>
        <td>Cities</td>
        <td>Notes</td>
	</tr>
<?
$q= "select * from evictionPackets where process_status = 'READY' and package_id = '' order by circuit_court";
$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
$i=0;
while ($d=mysql_fetch_array($r, MYSQL_ASSOC)) {$i++;
?>
    <tr bgcolor="<? echo row_color($i,'#FFFFFF','#cccccc'); ?>">
		<td nowrap="nowrap">
		<? if(($d[uspsVerify] != '') && ($d[qualityControl] != '') && ($d[caseVerify] != '')){ ?><input type="checkbox" name="package[id][<?=$d[eviction_id]?>]" value="<?=$d[eviction_id]?>" /><? } ?>&nbsp;<a href="order.php?packet=<?=$d[eviction_id]?>" target="_blank" style="text-decoration:none"><?=$d[eviction_id]?>)</a></td>
        <td><?=id2attorney($d[attorneys_id])?></td>
        <td><?=substr($d[date_received],0,10)?></td>
        <td><?=$d[client_file] ?></td>
		<td><?=$d[case_no] ?></td>
        <td><?=str_replace(' ','&nbsp;',$d[circuit_court]) ?></td>
        <td align="center"><?=defTotal($d[eviction_id]);?></td>
        <td nowrap="nowrap"><? echo "<a target='_Blank' href='http://staff.mdwestserve.com/dispatcher.php?aptsut=&address=$d[address1]&city=$d[city1]&state=$d[state1]&miles=20' title='$d[address1], $d[city1], $d[state1], $d[zip1]'>Serve &amp; Post: $d[address1], $d[city1], $d[state1] $d[zip1]</a>"; ?></td>
            <td><?=strtoupper($d[processor_notes])?></td>
	</tr>
<?  
} 
?>
</table>
<style>
.ppd{border-bottom:hidden; border-right:hidden;}
.ppp{border-right:hidden;}
</style>
<table width="100%" class="noprint">
	<tr bgcolor="<?=row_color($i,'#99cccc','#ccccff')?>">
        <td class="ppd" align="left">Service Rate: <input size="3" name="package[contractor][<?=$d[eviction_id]?>]"/><br>
		Server: <select name="server_id"><option value=''>Select Server</option>
<?
$q2= "select * from ps_users where contract = 'YES'";
$r2=@mysql_query($q2) or die("Query: $q2<br>".mysql_error());
while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)) {
?>
<option value="<?=$d2[id]?>"><? if ($d2[company]){echo $d2[company].', '.$d2[name] ;}else{echo $d2[name] ;}?></option>
<?        } ?>
        </select></td>
        <td class="ppp"><input type="submit" name="submit" value="Package Files" /></td>
    </tr>
</form>
</table></td></tr></table>
<script>document.title='Eviction Dispatch and Packaging'</script>
<? include 'footer.php' ; ?>