<?
include 'functions.php';
include 'lock.php';
mysql_connect();
mysql_select_db('service');

function sampleResident(){
	$r=mysql_query("select resident from standard_history where resident <> '' order by RAND()");
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	return $d[resident];
}

function sampleResidentDesc(){
	$r=mysql_query("select residentDesc from standard_history where residentDesc <> '' order by RAND()");
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	return $d[residentDesc];
}
function sampleAddress(){
	$r=mysql_query("select address from standard_history where address <> '' order by RAND()");
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	return $d[address];
}
function sampleActionStr(){
	$r=mysql_query("select action_str from standard_history where action_str <> '' order by RAND()");
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	return htmlspecialchars($d[action_str]);
}


if ($_POST[packet_id] && $_POST[defendant_id]){
	@mysql_query("insert into standard_history ( packet_id, defendant_id, action_type, action_str, serverID, sort_value, recordDate, wizard, resident, residentDesc, address, onAffidavit ) values ( '$_POST[packet_id]', '$_POST[defendant_id]', '$_POST[action_type]', '$_POST[action_str]', '$_POST[serverID]', '$_POST[sort_value]', '$_POST[recordDate]', '$_POST[wizard]', '$_POST[resident]', '$_POST[residentDesc]', '$_POST[address]', '$_POST[onAffidavit]' )");
	echo "<div style='border:double 3px #00ff00; font-size:25px;' align='center'>New Data Received - <a href='enterHistory.php'>Clear Form</a></div>";
}elseif($_POST[history_id]){
	//@mysql_query("insert into standard_history ( packet_id, defendant_id, action_type, action_str, serverID, sort_value, recordDate, wizard, resident, residentDesc, address, onAffidavit ) values ( '$_POST[packet_id]', '$_POST[defendant_id]', '$_POST[action_type]', '$_POST[action_str]', '$_POST[serverID]', '$_POST[sort_value]', '$_POST[recordDate]', '$_POST[wizard]', '$_POST[resident]', '$_POST[residentDesc]', '$_POST[address]', '$_POST[onAffidavit]' )");
	echo "<div style='border:double 3px #00ff00; font-size:25px;' align='center'>Update Data Received - <a href='enterHistory.php'>Clear Form</a></div>";
}
else{
	echo "<div style='border:double 3px #ff0000; font-size:25px;' align='center'>All Fields Required</div>";
}
?>
<style>
td { border-bottom:solid 1px;}

</style>
<table align="center" cellspacing="0">
<form method="post">
	<tr>
		<td style="width:20%">Logic #</td>
		<td style="width:40%">
			<select name="packet_id">
			<?
			$r=@mysql_query("select distinct packet_id from standard_packets order by packet_id DESC");
			while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
			?>
				<option value="<?=$d[packet_id];?>">S<?=$d[packet_id];?></option>
			<? } ?>
			</select><b>-</b><select name="defendant_id">
				<option>1</option>
				<option>2</option>
				<option>3</option>
				<option>4</option>
				<option>5</option>
				<option>6</option>
			</select>
		</td>
		<td style="width:40%">
			&nbsp;
		</td>
	</tr>
	<tr>
		<td>Affidavit Line Item</td>
		<td>
			<textarea name="action_str" cols="50" rows="4"></textarea> 
		</td>
		<td>
			<?=sampleActionStr();?>
		</td>
	</tr>
	<tr>
		<td>Action Type</td>
		<td>
			<select name="action_type">
			<?
			$r=@mysql_query("select distinct action_type from standard_history where action_type <> '' order by action_type");
			while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
			?>
				<option><?=$d[action_type];?></option>
			<? } ?>
			</select>
		</td>
		<td>
			&nbsp;
		</td>
	</tr>
	<tr>
		<td>Server ID</td>
		<td>
			<select name="serverID">
			<?
			$r=@mysql_query("select distinct server_id from standard_packets where server_id <> '' order by server_id DESC");
			while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
				$rSub=@mysql_query("select name, company, level from ps_users where id = '$d[server_id]'");
				$dSub=mysql_fetch_array($rSub,MYSQL_ASSOC);
			?>
				<option value="<?=$d[server_id];?>">#<?=$d[server_id];?>, <?=$dSub[name];?>, <?=$dSub[company];?>, <?=$dSub[level];?></option>
			<? } ?>
			<?
			$r=@mysql_query("select distinct server_ida from standard_packets where server_ida <> '' order by server_ida DESC");
			while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
				$rSub=@mysql_query("select name, company, level from ps_users where id = '$d[server_ida]'");
				$dSub=mysql_fetch_array($rSub,MYSQL_ASSOC);
			?>
				<option value="<?=$d[server_ida];?>">#<?=$d[server_ida];?>, <?=$dSub[name];?>, <?=$dSub[company];?>, <?=$dSub[level];?> (a)</option>
			<? } ?>
			<?
			$r=@mysql_query("select distinct server_idb from standard_packets where server_idb <> '' order by server_idb DESC");
			while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
				$rSub=@mysql_query("select name, company, level from ps_users where id = '$d[server_idb]'");
				$dSub=mysql_fetch_array($rSub,MYSQL_ASSOC);
			?>
				<option value="<?=$d[server_idb];?>">#<?=$d[server_idb];?>, <?=$dSub[name];?>, <?=$dSub[company];?>, <?=$dSub[level];?> (b)</option>
			<? } ?>
			<?
			$r=@mysql_query("select distinct server_idc from standard_packets where server_idc <> '' order by server_idc DESC");
			while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
				$rSub=@mysql_query("select name, company, level from ps_users where id = '$d[server_idc]'");
				$dSub=mysql_fetch_array($rSub,MYSQL_ASSOC);
			?>
				<option value="<?=$d[server_idc];?>">#<?=$d[server_idc];?>, <?=$dSub[name];?>, <?=$dSub[company];?>, <?=$dSub[level];?> (c)</option>
			<? } ?>
			<?
			$r=@mysql_query("select distinct server_idd from standard_packets where server_idd <> '' order by server_idd DESC");
			while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
				$rSub=@mysql_query("select name, company, level from ps_users where id = '$d[server_idd]'");
				$dSub=mysql_fetch_array($rSub,MYSQL_ASSOC);
			?>
				<option value="<?=$d[server_idd];?>">#<?=$d[server_idd];?>, <?=$dSub[name];?>, <?=$dSub[company];?>, <?=$dSub[level];?> (d)</option>
			<? } ?>
			<?
			$r=@mysql_query("select distinct server_ide from standard_packets where server_ide <> '' order by server_ide DESC");
			while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
				$rSub=@mysql_query("select name, company, level from ps_users where id = '$d[server_ide]'");
				$dSub=mysql_fetch_array($rSub,MYSQL_ASSOC);
			?>
				<option value="<?=$d[server_ide];?>">#<?=$d[server_ide];?>, <?=$dSub[name];?>, <?=$dSub[company];?>, <?=$dSub[level];?> (e)</option>
			<? } ?>
			<?
			$r=@mysql_query("select distinct id from ps_users where id <> '' and level = 'Operations' order by id DESC");
			while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
				$rSub=@mysql_query("select name, company, level from ps_users where id = '$d[id]'");
				$dSub=mysql_fetch_array($rSub,MYSQL_ASSOC);
			?>
				<option value="<?=$d[id];?>">#<?=$d[id];?> <?=$dSub[name];?>, <?=$dSub[company];?>, <?=$dSub[level];?></option>
			<? } ?>
			
			
			
			
			
			
			
			
			</select>
		</td>
		<td>
			&nbsp;
		</td>
	</tr>
	<tr>
		<td>Sort Order</td>
		<td>
			<input name="sort_value" value="0" size="2">
		</td>
		<td>
			&nbsp;
		</td>
	</tr>
	<tr>
		<td>Record Date</td>
		<td>
			<input name="recordDate" size="40"> 
		</td>
		<td>
			<?=date('Y-m-d H:i:s');?>
		</td>
	</tr>
	<tr>
		<td>Wizard</td>
		<td>
			<select name="wizard">
			<?
			$r=@mysql_query("select distinct wizard from standard_history where wizard <> '' order by wizard");
			while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
			?>
				<option><?=$d[wizard];?></option>
			<? } ?>
			</select>
		</td>
		<td>
			&nbsp;
		</td>
	</tr>
	<tr>
		<td>Subserved: Name</td>
		<td>
			<input name="resident" size="40"> 
		</td>
		<td>
			<?=sampleResident();?>
		</td>
	</tr>
	<tr>
		<td>Subserved: Event Description</td>
		<td>
			<textarea name="residentDesc" cols="50" rows="4"></textarea> 
		</td>
		<td>
			<?=sampleResidentDesc();?>
		</td>
	</tr>
	<tr>
		<td>Address: Description</td>
		<td>
			<input name="address" size="40"> 
		</td>
		<td>
			<?=sampleAddress();?>
		</td>
	</tr>
	<tr>
		<td>On Affidavit</td>
		<td>
			<input name="onAffidavit" value="checked" type="checkbox" checked>
		</td>
		<td>
			<input type="submit" value="Insert into database.">
		</td>
	</tr>

</form>
</table>