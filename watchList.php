<?
mysql_connect();
mysql_select_db('core');

function id2attorney($id){
	$q = "SELECT display_name FROM attorneys WHERE attorneys_id='$id'";
	$r = @mysql_query($q);
	$d = mysql_fetch_array($r, MYSQL_ASSOC);
	return $d['display_name'];
}
function countyList(){
	return "<option value='' selected='selected'>Default All</option>
	<option value='ALLEGANY COUNTY'>Allegany</option>
	<option value='ANNE ARUNDEL COUNTY'>Anne Arundel</option>
	<option value='BALTIMORE CITY'>Baltimore City</option>
	<option value='BALTIMORE COUNTY'>Baltimore</option>
	<option value='CALVERT COUNTY'>Calvert</option>
	<option value='CAROLINE COUNTY'>Caroline</option>
	<option value='CARROLL COUNTY'>Carroll</option>
	<option value='CECIL COUNTY'>Cecil</option>
	<option value='CHARLES COUNTY'>Charles</option>
	<option value='DORCHESTER COUNTY'>Dorchester</option>
	<option value='FREDERICK COUNTY'>Frederick</option>
	<option value='GARRETT COUNTY'>Garrett</option>
	<option value='HARFORD COUNTY'>Harford</option>
	<option value='HOWARD COUNTY'>Howard</option>
	<option value='KENT COUNTY'>Kent</option>
	<option value='MONTGOMERY COUNTY'>Montgomery</option>
	<option value='PRINCE GEORGE&#39;S COUNTY'>Prince George&#39;s</option>
	<option value='QUEEN ANNE&#39;S COUNTY'>Queen Anne&#39;s</option>
	<option value='SAINT MARY&#39;S COUNTY'>Saint Mary&#39;s</option>
	<option value='SOMERSET COUNTY'>Somerset</option>
	<option value='TALBOT COUNTY'>Talbot</option>
	<option value='WASHINGTON COUNTY'>Washington</option>
	<option value='WICOMICO COUNTY'>Wicomico</option>
	<option value='WORCESTER COUNTY'>Worcester</option>";
}

function searchForm($packet,$def){
	
	$q1="SELECT status, packetID, firstName, lastName, county, company from watchDog where packetID='$packet' AND defID='$def'";
	$r1=@mysql_query($q1);
	$d1=mysql_fetch_array($r1,MYSQL_ASSOC);
	if ($d1[packetID] != ''){
		$link = "<form style='display:inline;' name='$packet-$def' action='http://casesearch.courts.state.md.us/inquiry/inquirySearch.jis' target='preview'>";
		$link .= "<input type='hidden' name='disclaimer' value='Y'>";
		$link .= "<input type='hidden' name='lastName' value='".$d1[lastName]."'>";
		$link .= "<input type='hidden' name='firstName' value='".$d1[firstName]."'>";
		$link .= "<input type='hidden' name='middleName' value=''>";
		$link .= "<input type='hidden' name='partytype' value=''>";
		$link .= "<input type='hidden' name='site' value='CIVIL'>";
		$link .= "<input type='hidden' name='courtSystem' value='C'>";
		$link .= "<input type='hidden' name='countyName' value='".$d1[county]."'>";
		$link .= "<input type='hidden' name='filingStart' value='$start'>";
		$link .= "<input type='hidden' name='filingEnd' value='$end'>";
		$link .= "<input type='hidden' name='filingDate' value=''>";
		$link .= "<input type='hidden' name='company' value='".$d1[company]."'>";
		$link .= "<input type='hidden' name='action' value='Search'>";
		if ($d1[packetID] && $d1[status] == 'New Case Found'){
			$link .= "<input type='submit' style='color: green; background-color: green; display:inline; width:10; height:10;' value='*'>";
		}elseif ($d1[packetID] && $d1[status] != 'Search Complete'){
			$link .= "<input type='submit' style='color: red; background-color: red; display:inline; width:10; height:10;' value='*'>";
		}
		$link .= "</form>";
	}else{
		$link = watchLink($packet,$def);
	}
	return $link;
}
if ($_GET[complete]){
	@mysql_query("update watchDog set status = 'Search Complete' where watchID = '$_GET[complete]'");
}
if ($_GET[start]){
	@mysql_query("update watchDog set status = 'Case Watch Started' where watchID = '$_GET[start]'");
}
if ($_GET[reset]){
	$q="update watchDog set status='SEARCHING...', response=lastResult where watchID = '$_GET[reset]'";
	@mysql_query($q) or die ("Query: $q<br>".mysql_error());
}

if ($_POST[lastName] && $_POST[county]){

@mysql_query("insert into watchDog (packetID, defID, firstName, lastName, county, company) values ('$_POST[packetID]', '$_POST[defID]', '$_POST[firstName]', '$_POST[lastName]', '$_POST[county]', '$_POST[company]')");


}
if ($_GET[inactive]){
	$cR = @mysql_query("select * from watchDog WHERE status='Search Complete' order by status ASC, packetID ASC, defID ASC");
}else{
	$cR = @mysql_query("select * from watchDog WHERE status <> 'Search Complete' order by status ASC, packetID ASC, defID ASC");
}
//set search start and end dates
$start=date('Y');
$start="01/01/".($start-1);
$end=date('m/d/Y');
?>


<table border="1" width="100%">
	<? if($_GET[inactive]){ ?>
	<tr>
		<td colspan='10' align='center'><a href='watchList.php'>Active Searches</a></td>
	</tr>
	<? }else{ ?>
	<tr>
		<td colspan='10' align='center'><a href='watchList.php?inactive=1'>Inactive Searches</a></td>
	</tr>
	<? } ?>
	<tr>
		<td>packetID</td>
		<td>Case #/Client</td>
		<td>firstName</td>
		<td>lastName</td>
		<td>county</td>
		<td>company</td>
		<td>response</td>
		<td>watchStart</td>
		<td>status</td>
		<td>Actions</td>
		<td>lastChecked</td>
		<td>lastResult</td>
	</tr>
<form action="watchList.php" method="post">
	<tr>
		<td><input size="6" name="packetID" value="packet" onclick="value=''"></td>
		<td><input size="1" name="defID" value="def" onclick="value=''"></td>
		<td><input size="15" name="firstName" value="first name" onclick="value=''"></td>
		<td><input size="15" name="lastName" value="last name" onclick="value=''"></td>
		<td><select name="county"><?=countyList()?></select></td>
		<td><input size="1" name="company" value="N"></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td><input type="submit" value="Add to watch list."></td>
		<td></td>
		<td></td>
	</tr>
</form>	
<? while($cD = mysql_fetch_array($cR,MYSQL_ASSOC)){ 
	$pR= @mysql_query("SELECT case_no, attorneys_id, process_status FROM ps_packets WHERE packet_id='".$cD[packetID]."'");
	$pD = mysql_fetch_array($pR,MYSQL_ASSOC);
	if ($pD[process_status] == 'CANCELLED'){
		$bg="style='background-color:red;'";
		$font=" style='font-size:28px;'";
	}elseif ($pD[case_no] != ''){
		$bg="style='background-color:#FFCCFF;'";
		$font=" style='font-size:28px;'";
	}elseif($Cd[status] == 'New Case Found'){
		$bg="style='background-color:#CCFFCC;'";
		$font="";
	}else{
		$bg='';
		$font="";
	}
		$link = '';
		$link = "<form style='display:inline;' name='".$cD[packetID]."-".$cD[defID]."' action='http://casesearch.courts.state.md.us/inquiry/inquirySearch.jis' target='_blank'>";
		$link .= "<input type='hidden' name='disclaimer' value='Y'>";
		$link .= "<input type='hidden' name='lastName' value='".$cD[lastName]."'>";
		$link .= "<input type='hidden' name='firstName' value='".$cD[firstName]."'>";
		$link .= "<input type='hidden' name='middleName' value=''>";
		$link .= "<input type='hidden' name='partytype' value=''>";
		$link .= "<input type='hidden' name='site' value='CIVIL'>";
		$link .= "<input type='hidden' name='courtSystem' value='C'>";
		$link .= "<input type='hidden' name='countyName' value='".$cD[county]."'>";
		$link .= "<input type='hidden' name='filingStart' value='$start'>";
		$link .= "<input type='hidden' name='filingEnd' value='$end'>";
		$link .= "<input type='hidden' name='filingDate' value=''>";
		$link .= "<input type='hidden' name='company' value='".$cD[company]."'>";
		$link .= "<input type='hidden' name='action' value='Search'>";
		if ($cD[status] == 'New Case Found'){
			$link .= "<input type='submit' style='color: green; background-color: green; display:inline; width:10; height:10;' value='*'>";
		}elseif ($cD[status] != 'Search Complete'){
			$link .= "<input type='submit' style='color: red; background-color: red; display:inline; width:10; height:10;' value='*'>";
		}
		$link .= "</form>";
?>
	<tr <?=$bg?>>
		<td><a href="http://staff.mdwestserve.com/otd/order.php?packet=<?=$cD[packetID]?>" target="_blank"><?=strtoupper($cD[packetID]);?></a>-<?=strtoupper($cD[defID]);?> <?=$link?> </td>
		<td<?=$font?>><?=$pD[case_no]?>/<?=id2attorney($pD[attorneys_id])?></td>
		<td><?=strtoupper($cD[firstName]);?></td>
		<td><?=strtoupper($cD[lastName]);?></td>
		<td><?=strtoupper($cD[county]);?></td>
		<td><?=strtoupper($cD[company]);?></td>
		<td><?=strtoupper($cD[response]);?></td>
		<td><?=strtoupper($cD[watchStart]);?></td>
		<td><?=strtoupper($cD[status]);?></td>
		<td><? if ($cD[status] != 'Search Complete'){ ?><a href="?complete=<?=$cD[watchID];?>">END</a><? }else{ ?><a href="?start=<?=$cD[watchID];?>">START</a><? } ?><? if ($cD[response] != $cD[lastResult] && $cD[lastResult] != ''){ ?> / <a href="?reset=<?=$cD[watchID];?>">RESET</a><? } ?></td>
		<td><?=strtoupper($cD[lastChecked]);?></td>
		<td><?=strtoupper($cD[lastResult]);?></td>
	</tr>
<? } ?>
</table>