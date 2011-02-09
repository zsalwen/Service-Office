<?
session_start();
mysql_connect();
mysql_select_db('service');
function zipData($zip){
	$r=@mysql_query("select * from zip_code where zip_code = '$zip'");
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	@mysql_query("update defendants set county = '$d[county]' where defendantzip = '$d[zip_code]'");
	error_log(date('r').": Linking $d[county] to $d[zip_code] \n", 3, '/logs/webservice.log');
	return "<td>$d[id]</td><td>$d[zip_code]</td><td>$d[city]</td><td>$d[county]</td><td>$d[state_name]</td><td>$d[state_prefix]</td><td>$d[area_code]</td><td>$d[time_zone]</td><td>$d[lat]</td><td>$d[lon]</td>";
}
function zipCounter($zip){
	$r=@mysql_query("select defendantzip from defendants where defendantzip = '$zip' and packet = ''");
	$count=mysql_num_rows($r);
	return "<td>$count</td>";
}
$r=@mysql_query("select distinct defendantzip from defendants where packet = '' order by defendantzip");
?>
<table border="1" cellpadding="0" cellspacing="0">
	<tr>
		<td>$d[defendantzip]</td>
		<td>$d[id]</td>
		<td>$d[zip_code]</td>
		<td>$d[city]</td>
		<td>$d[county]</td>
		<td>$d[state_name]</td>
		<td>$d[state_prefix]</td>
		<td>$d[area_code]</td>
		<td>$d[time_zone]</td>
		<td>$d[lat]</td>
		<td>$d[lon]</td>
		<td>$count</td>
	</tr>
<? while($d=mysql_fetch_array($r,MYSQL_ASSOC)){ ?>
	<tr>
		<td><?=$d[defendantzip];?></td>
		<?=zipData($d[defendantzip]);?>
		<?=zipCounter($d[defendantzip]);?>
	</tr>
<? } ?>
</table>
