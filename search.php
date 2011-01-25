<? include 'common.php';
$search = $_GET['q'];
function systemLookup($field, $query){ 
	if ($_GET[field] == 'client_file'){
		$q2="client_file like '%$query%'";
		$query2=$query;
	}elseif($_GET[field] == 'case_no'){
		$q2="case_no like '%$query%'";
		$query2=$query;
	}elseif($_GET[field] == 'status'){
		$q2="status like '%$query%' or process_status like '%$query%' or service_status like '%$query%' or affidavit_status like '%$query%'";
		$query2=$query;
	}elseif($_GET[field] == 'packet_id'){
		$q2="packet_id like '%$query%'";
		$query2=$query;
	}elseif($_GET[field] == 'eviction_id'){
		$r=@mysql_query("SELECT client_file from evictionPackets where eviction_id LIKE '%$query%'");
		$d=mysql_fetch_array($r,MYSQL_ASSOC);
		$q2="client_file like '%".$d[client_file]."%'";
		$query2=$d[client_file];
	}elseif($_GET[field] == 'name'){
		$q2="name1 like '%$query%' OR name2 like '%$query%' OR name3 like '%$query%' OR name4 like '%$query%' OR name5 like '%$query%' OR name6 like '%$query%'";
		$query2=$query;
	}else{
		//field=address
		$q2="address1 like '%$query%' OR address1a like '%$query%' OR address1b like '%$query%' OR address1c like '%$query%' OR address1d like '%$query%' OR address1e like '%$query%'";
		$query2=$query;
	}
	hardLog('searching '.$query.' in '.$_GET[field],'user');
	$r2=@mysql_query("select * from ps_packets where ".$q2);
	while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)){
		if ($d2[packet_id]){
		$rTest=@mysql_query("select * from exportRequests where packetID = '$d2[packet_id]'");
		$dTest=mysql_fetch_array($rTest,MYSQL_ASSOC);
		?>
			<div style="border:solid 1px;">
			<div style="font-size:16px; text-align:left;">
			<? if ($dTest[byID]){ ?><em style="background-color:FF0000;"><b>EXPORT REQUESTED</b></em><? }?> 
			OTD<?=$d2[packet_id]?> <strong><?=$d2[status]?></strong>  
            <a target="_tab" href="http://staff.mdwestserve.com/otd/order.php?packet=<?=$d2[packet_id]?>">Order</a> 
            ::  <a target="_tab" href="http://staff.mdwestserve.com/otd/serviceSheet.php?id=<?=$d2[packet_id]?>">Service Sheet</a> 
            :: <a target="_tab" href="http://service.mdwestserve.com/customInstructions<? if($d2[attorneys_id] == '1'){ echo '.burson'; }elseif($d2[attorneys_id] == '56'){ echo '.brennan'; }?>.php?packet=<?=$d2[packet_id]?>">Instructions</a> 
            </div><div style="font-size:12px"><li>Found string '<?=$query2?>' in mdws1.mdwestserve.com.core.ps_packets.<?=$field?></li></div></div>
<?		}
	}
	}
	function systemLookup3($field, $query){ 
	if ($_GET[field] == 'client_file'){
		$q2="client_file like '%$query%'";
		$query2=$query;
	}elseif($_GET[field] == 'case_no'){
		$q2="case_no like '%$query%'";
		$query2=$query;
	}elseif($_GET[field] == 'status'){
		$q2="status like '%$query%' or process_status like '%$query%' or service_status like '%$query%' or affidavit_status like '%$query%'";
		$query2=$query;
	}elseif($_GET[field] == 'packet_id'){
		$q2="packet_id like '%$query%'";
		$query2=$query;
	}elseif($_GET[field] == 'eviction_id'){
		$r=@mysql_query("SELECT client_file from evictionPackets where eviction_id LIKE '%$query%'");
		$d=mysql_fetch_array($r,MYSQL_ASSOC);
		$q2="client_file like '%".$d[client_file]."%'";
		$query2=$d[client_file];
	}elseif($_GET[field] == 'name'){
		$q2="name1 like '%$query%' OR name2 like '%$query%' OR name3 like '%$query%' OR name4 like '%$query%' OR name5 like '%$query%' OR name6 like '%$query%'";
		$query2=$query;
	}else{
		//field=address
		$q2="address1 like '%$query%' OR address1a like '%$query%' OR address1b like '%$query%' OR address1c like '%$query%' OR address1d like '%$query%' OR address1e like '%$query%'";
		$query2=$query;
	}
	$r2=@mysql_query("select * from ps_export where ".$q2);
	while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)){
		if ($d2[packet_id]){?>
			<div style="border:solid 1px;">
			<div style="font-size:16px; text-align:left;">ARCHIVE: OTD<?=$d2[packet_id]?> <strong><?=$d2[status]?></strong>  
             
            :: <a href="http://staff.mdwestserve.com/otd/archive.php?packet=<?=$d2[packet_id]?>">Service Order</a> 
            
            </div><div style="font-size:12px"><li>Found string '<?=$query2?>' in mdws1.mdwestserve.com.core.ps_packets.<?=$field?></li></div></div>
<?		}
	}
	}
	function systemLookup4($field, $query){ 
	/*
	if ($_GET[field] == 'client_file'){
		$q2="client_file like '%$query%'";
		$query2=$query;
	}elseif($_GET[field] == 'case_no'){
		$q2="case_no like '%$query%'";
		$query2=$query;
	}elseif($_GET[field] == 'status'){
		$q2="status like '%$query%' or process_status like '%$query%' or service_status like '%$query%' or affidavit_status like '%$query%'";
		$query2=$query;
	}elseif($_GET[field] == 'packet_id'){
		$q2="packet_id like '%$query%'";
		$query2=$query;
	}elseif($_GET[field] == 'eviction_id'){
		$r=@mysql_query("SELECT client_file from evictionPackets where eviction_id LIKE '%$query%'");
		$d=mysql_fetch_array($r,MYSQL_ASSOC);
		$q2="client_file like '%".$d[client_file]."%'";
		$query2=$d[client_file];
	}elseif($_GET[field] == 'name'){
		$q2="name1 like '%$query%' OR name2 like '%$query%' OR name3 like '%$query%' OR name4 like '%$query%' OR name5 like '%$query%' OR name6 like '%$query%'";
		$query2=$query;
	}else{
		//field=address
		$q2="address1 like '%$query%' OR address1a like '%$query%' OR address1b like '%$query%' OR address1c like '%$query%' OR address1d like '%$query%' OR address1e like '%$query%'";
		$query2=$query;
	}
	$r2=@mysql_query("select * from ps_packets where ".$q2);
	while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)){
		if ($d2[packet_id]){?>
			<div style="border:solid 1px;">
			<div style="font-size:16px; text-align:center;">OTD<?=$d2[packet_id]?> <strong><?=$d2[status]?></strong>  
            :: <a href="http://staff.mdwestserve.com/otd/affidavitUpload.php?packet=<?=$d2[packet_id]?>">Document Upload Manager</a> 
            :: <a href="http://staff.mdwestserve.com/otd/order.php?packet=<?=$d2[packet_id]?>">Service Order</a> 
            ::  <a href="http://staff.mdwestserve.com/otd/serviceSheet.php?id=<?=$d2[packet_id]?>">Service Sheet</a> 
            :: <a href="http://service.mdwestserve.com/customInstructions<? if($d2[attorneys_id] == '1'){ echo '.burson'; }elseif($d2[attorneys_id] == '56'){ echo '.brennan'; }?>.php?packet=<?=$d2[packet_id]?>">Service Instructions</a> 
            </div><div style="font-size:12px"><li>Found string '<?=$query2?>' in mdws1.mdwestserve.com.core.ps_packets.<?=$field?></li></div></div>
<?		}
	}
	*/
}

function systemLookup2($field, $query){
	if ($_GET[field] == 'client_file'){
		$q2="client_file like '%$query%'";
		$query2=$query;
	}elseif($_GET[field] == 'status'){
		$q2="status like '%$query%' or process_status like '%$query%' or service_status like '%$query%' or affidavit_status like '%$query%'";
		$query2=$query;
	}elseif($_GET[field] == 'case_no'){
		$q2="case_no like '%$query%'";
		$query2=$query;
	}elseif($_GET[field] == 'packet_id'){
		$q2="eviction_id like '%$query%'";
		$query2=$query;
	}elseif($_GET[field] == 'name'){
		$q2="name1 like '%$query%' OR name2 like '%$query%' OR name3 like '%$query%' OR name4 like '%$query%' OR name5 like '%$query%' OR name6 like '%$query%'";
		$query2=$query;
	}else{
		//field=address
		$q2="address1 like '%$query%' OR address1a like '%$query%' OR address1b like '%$query%' OR address1c like '%$query%' OR address1d like '%$query%' OR address1e like '%$query%'";
		$query2=$query;
	}
	$r2=@mysql_query("select * from evictionPackets where ".$q2);
	while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)){
		if ($d2[eviction_id]){?>
			<div style="border:solid 1px;">
			<div style="font-size:16px; text-align:left;">EV<?=$d2[eviction_id]?> <strong><?=$d2[status]?></strong>  
            :: <a href="http://staff.mdwestserve.com/ev/evUpload.php?eviction=<?=$d2[eviction_id]?>">Document Upload Manager</a> 
            :: <a href="http://staff.mdwestserve.com/ev/order.php?packet=<?=$d2[eviction_id]?>">Service Order</a> 
            ::  <a href="http://staff.mdwestserve.com/ev/evSheet.php?id=<?=$d2[eviction_id]?>">Service Sheet</a> 
            :: <a href="http://staff.mdwestserve.com/ev/ev_customInstructions.php?id=<?=$d2[eviction_id]?>">Service Instructions</a> 
            </div><div style="font-size:12px"><li>Found string '<?=$query2?>' in mdws1.mdwestserve.com.core.evictionPackets.<?=$field?></li></div></div>
<?		}
	}
}

function systemLookup5($field, $query){ 
	if ($_GET[field] == 'client_file'){
		$q2="client_file like '%$query%'";
		$query2=$query;
	}elseif($_GET[field] == 'case_no'){
		$q2="case_no like '%$query%'";
		$query2=$query;
	}elseif($_GET[field] == 'status'){
		$q2="status like '%$query%' or process_status like '%$query%' or service_status like '%$query%' or affidavit_status like '%$query%'";
		$query2=$query;
	}elseif($_GET[field] == 'packet_id'){
		$q2="packet_id like '%$query%'";
		$query2=$query;
	}elseif($_GET[field] == 'eviction_id'){
		$r=@mysql_query("SELECT client_file from evictionPackets where eviction_id LIKE '%$query%'");
		$d=mysql_fetch_array($r,MYSQL_ASSOC);
		$q2="client_file like '%".$d[client_file]."%'";
		$query2=$d[client_file];
	}elseif($_GET[field] == 'name'){
		$q2="name1 like '%$query%' OR name2 like '%$query%' OR name3 like '%$query%' OR name4 like '%$query%' OR name5 like '%$query%' OR name6 like '%$query%'";
		$query2=$query;
	}else{
		//field=address
		$q2="address1 like '%$query%' OR address1a like '%$query%' OR address1b like '%$query%' OR address1c like '%$query%' OR address1d like '%$query%' OR address1e like '%$query%'";
		$query2=$query;
	}
	hardLog('searching '.$query.' in '.$_GET[field],'user');
	$r2=@mysql_query("select * from standard_packets where ".$q2);
	while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)){
		if ($d2[packet_id]){
		?>
			<div style="border:solid 1px;">
			<div style="font-size:16px; text-align:left;">
			S<?=$d2[packet_id]?> <strong><?=$d2[status]?></strong>  
            <a target="_tab" href="http://staff.mdwestserve.com/standard/order.php?packet=<?=$d2[packet_id]?>">Order</a> 
            </div><div style="font-size:12px"><li>Found string '<?=$query2?>' in mdws1.mdwestserve.com.core.ps_packets.<?=$field?></li></div></div>
<?		}
	}
	}
?>
<link rel="stylesheet" type="text/css" href="fire.css" />
<style>
form { margin:0px; padding:0px;}
</style>
<center><form><input name="q" value="<?=$_GET[q];?>" /> <select name="field">
		<option value="client_file">File</option>
		<option value="packet_id">Packet</option>
		<option value="eviction_id">Eviction</option>
		<option value="case_no">Case</option>
		<option value="status">Status</option>
		<option value="name">Name</option>
		<option value="address">Address</option>
	</select> <input type="submit" value="Search Records" /></form></center>
<?
 if ($_GET['q']){?>
<style>a {text-decoration:none} a:hover {text-decoration:underline overline;}</style>
<div style="font-size:24px; border:solid 1px; color:#FFFFFF;" align="center">Running search of process serving files for <b><?=$_GET[field]?>, <?=$search?></b></div>
<table width='100%' align='center'>
<tr><td width='33%' align='center' style="font-weight:bold; letter-spacing: 5px;background-color:00BBAA;">FORECLOSURES</td>
<td width='33%' align='center' style="font-weight:bold; letter-spacing: 5px; background-color:99AAEE;">EVICTIONS</td>
<td width='33%' align='center' style="font-weight:bold; letter-spacing: 5px; background-color:CC6600;">STANDARD</td></tr>
<tr><td width='33%' valign="top" bgcolor="99FF99">
<?	systemLookup($_GET['field'], $search);?>
</td><td width='33%' valign="top" bgcolor="99CCDD">
<?	systemLookup2($_GET['field'], $search);?>
</td><td width='33%' valign="top" bgcolor="FFAA00">
<?	systemLookup5($_GET['field'], $search);?>
</td></tr>
<tr><td width='33%' align='center' style="font-weight:bold; letter-spacing: 5px;background-color:00BBAA;">FORECLOSURES ARCHIVE</td>
<td width='33%' align='center' style="font-weight:bold; letter-spacing: 5px; background-color:99AAEE;">EVICTIONS ARCHIVE</td></tr>
<tr><td width='33%' valign="top" bgcolor="99FF99">
<?	systemLookup3($_GET['field'], $search);?>
</td><td width='33%' valign="top" bgcolor="99CCDD">
<?	systemLookup4($_GET['field'], $search);?>
<? } ?>
</td></tr></table>
<div style="background-color:#FFF;" align="center">Webservice Data</div>
<table style="background-color:#FFF;" align="center">
<?
function ps2ws($in){
if ($in == 'client_file'){ return 'filenumber'; }
if ($in == 'name'){ return 'defendantfullname'; }
if ($in == 'address'){ return 'defendantaddress1'; }
}
$qRow="select * from defendants where ".ps2ws($_GET['field'])." like '%$search%'";
$r=@mysql_query($qRow) or die ("Query: $qRow<br>".mysql_error());
while($row=mysql_fetch_array($r,MYSQL_ASSOC)){

	echo '<tr>';
	echo '<td>'.$row['filenumber'].'</td><td>'.$row['defendantfullname'].'</td>
		<td>'.$row['defendantaddress1'].'</td>';
			echo '<td valign="top">'.$row['defendantaddress2'].'</td> <td valign="top">'.$row['defendantcity'].'</td> <td valign="top">'.$row['defendantstate'].'</td>  <td valign="top">'.$row['defendantzip'].'</td>';
	echo '<td valign="top">'.$row['status'].'</td> <td valign="top">'.$row['statusdate'].'</td> ';
		
		
	echo '</tr>';


 }
 ?></table>
<form><input name="q" value="<?=$_GET[q];?>" /> <select name="field">
		<option value="client_file">File</option>
		<option value="packet_id">Packet</option>
		<option value="eviction_id">Eviction</option>
		<option value="case_no">Case</option>
		<option value="status">Status</option>
		<option value="name">Name</option>
		<option value="address">Address</option>
	</select> <input type="submit" value="Search Records" /></form>
<?

//include 'footer.php';?>
