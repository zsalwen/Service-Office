<?
session_start();
include 'security.php';
include 'functions-calendar.php';
mysql_connect();
mysql_select_db('service');
if ($_GET['date']){
$today=$_GET['date'];
}else{
$today = date('Y-m-d');
}
$r=@mysql_query("select DISTINCT circuit_court from ps_packets where estFileDate = '$today' order by circuit_court ");

function isActive($status){
	if ($status == "IN PROGRESS" || $status == "ASSIGNED"){ return "<b style='color:#FF0000;'>Service Still in progress.</b>"; }
	if ($status == "READY TO MAIL"){ return "<b style='color:#cccc00;'>Mail Still in progress.</b>"; }
	if ($status == "SERVICE COMPLETED"){ return "<b style='color:#00ff00;'>Ready.</b>"; }

}

function withCourier($packet){
	$q="SELECT * from docuTrack WHERE packet='$packet' and document='OUT WITH COURIER'";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	if ($d){
		$document="<small>".$d[binder]." OUT WITH COURIER by ".$d[location]."</small>";
		return $document;
	}
}

function EVwithCourier($packet){
	$q="SELECT * from docuTrack WHERE packet='EV$packet' and document='OUT WITH COURIER'";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	if ($d){
		$document="<small>".$d[binder]." OUT WITH COURIER by ".$d[location]."</small>";
		return $document;
	}
}




?>
<style>
fieldset {
margin:0px;
padding:0px;
		}
legend	{
margin:0px;
padding:0px;
		}
ol	{
margin:0px;
padding:0px;
		}
li	{
margin:0px;
padding:0px;
padding-left:10px;
		}

</style>
<table width="100%"><tr><td valign="top">
<h2>OTD Filing / Returns for <?=$today;?></h2>
<?
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
?>
<fieldset>
	<legend><?=$d[circuit_court]?></legend>
<ol>
<?	
$x=@mysql_query("select packet_id, date_received, case_no, fileDate, service_status, process_status from ps_packets where estFileDate = '$today' AND circuit_court = '$d[circuit_court]' and status <> 'CANCELLED'");
while ($dx=mysql_fetch_array($x,MYSQL_ASSOC)){
?><li><a href="/otd/order.php?packet=<?=$dx[packet_id]?>" target="_Blank"><?=$dx[packet_id]?></a> <?=$dx[date_received]?> <?=$dx[case_no]?> <?=isActive($dx[service_status])?> <?=isActive($dx[process_status])?> <? if ($dx[fileDate] != "0000-00-00"){ echo "Filed on ".$dx[fileDate]; }else{ echo withCourier($dx[packet_id]);}?></li><?
}
?>	
</ol>
</fieldset>
<?
}
$r=@mysql_query("select DISTINCT circuit_court from evictionPackets where estFileDate = '$today' order by circuit_court  ");
?><h2>EV Filing / Returns for <?=$today;?></h2><?
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
?>
<fieldset>
	<legend><?=$d[circuit_court]?></legend>
<ol>
	<?	
$x=@mysql_query("select eviction_id, date_received, case_no, fileDate, service_status, process_status from evictionPackets where estFileDate = '$today' AND circuit_court = '$d[circuit_court]' and status <> 'CANCELLED'");
//$count=mysql_num_rows($x);
while ($dx=mysql_fetch_array($x,MYSQL_ASSOC)){
?><li><a href="/ev/order.php?packet=<?=$dx[eviction_id]?>" target="_Blank"><?=$dx[eviction_id]?></a> <?=$dx[date_received]?> <?=$dx[case_no]?> <?=isActive($dx[service_status])?> <?=isActive($dx[process_status])?> <? if ($dx[fileDate] != "0000-00-00"){ echo "Filed on ".$dx[fileDate]; }else{ echo EVwithCourier($dx[eviction_id]);}?></li><?}
?>	
</ol>
</fieldset>
<?
}
?>
</td><td valign="top" width="50px"><? include 'cal1.php'; ?><? include 'cal2.php'; ?></td></tr></table>
<?
include 'footer.php';
?>