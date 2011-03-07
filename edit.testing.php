<?
// rescan commands
$rTest=@mysql_query("select * from rescanRequests where packetID = '$d[packet_id]'");
$dTest=mysql_fetch_array($rTest,MYSQL_ASSOC);
if ($_GET[rescan]){
	if(!$dTest[byID]){
		hardLog('requested rescan '.$d[packet_id],'user');
		//mail('patrick@mdwestserve.com',$_COOKIE[psdata][name].' requested rescan '.$d[packet_id],$_COOKIE[psdata][name].' requested rescan of packet '.$d[packet_id]);
		@mysql_query("INSERT INTO rescanRequests (packetID,byID) values ('$d[packet_id]','".$_COOKIE[psdata][user_id]."') ");
		echo "<script>automation();</script>";
	}else{
		hardLog('approved rescan '.$d[packet_id],'user');
		//mail('patrick@mdwestserve.com',$_COOKIE[psdata][name].' approved rescan '.$d[packet_id],$_COOKIE[psdata][name].' approved rescan of packet '.$d[packet_id]);
		@mysql_query("UPDATE rescanRequests set rescanID = '".$_COOKIE[psdata][user_id]."', rescanDate = NOW() where packetID = '$d[packet_id]'");
		echo "<script>automation();</script>";
	}
}
$rTest=@mysql_query("select * from rescanRequests where packetID = '$d[packet_id]'");
$dTest=mysql_fetch_array($rTest,MYSQL_ASSOC);
$rescanStatus = rescanStatus($dTest[byID],$dTest[rescanID],$d[packet_id]);
// end rescan commands



// export commands
$rTest=@mysql_query("select * from exportRequests where packetID = '$d[packet_id]'");
$dTest=mysql_fetch_array($rTest,MYSQL_ASSOC);
if ($_GET[export]){
	if(!$dTest[byID]){
		mail('patrick@mdwestserve.com',$_COOKIE[psdata][name].' requested export '.$d[packet_id],$_COOKIE[psdata][name].' requested export of packet '.$d[packet_id]);
		@mysql_query("INSERT INTO exportRequests (packetID,byID) values ('$d[packet_id]','".$_COOKIE[psdata][user_id]."') ");
		echo "<script>automation();</script>";
	}elseif($dTest[byID] != $_COOKIE[psdata][user_id]){
		mail('patrick@mdwestserve.com',$_COOKIE[psdata][name].' approved '.$d[packet_id],$_COOKIE[psdata][name].' approved export of packet '.$d[packet_id]);
		@mysql_query("UPDATE exportRequests set confirmID = '".$_COOKIE[psdata][user_id]."' where packetID = '$d[packet_id]'");
		echo "<script>automation();</script>";
	}else{	
		echo "<script>alert('You cannot approve exports you requested silly goose!');</script>";
	}
}
$rTest=@mysql_query("select * from exportRequests where packetID = '$d[packet_id]'");
$dTest=mysql_fetch_array($rTest,MYSQL_ASSOC);
$exportStatus = exportStatus($dTest[byID],$dTest[confirmID],$d[packet_id]);
// end export commands

$rWatch=@mysql_query("select * from fileWatch where clientFile = '$d[client_file]'");
while($dWatch=mysql_fetch_array($rWatch,MYSQL_ASSOC)){
	echo "<script>alert('".addslashes($dWatch[message])."');</script>";
}
?>