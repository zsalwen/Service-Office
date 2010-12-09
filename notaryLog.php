<?
@mysql_connect ();
mysql_select_db ('core');
function id2name($id){
	$q="SELECT name FROM ps_users WHERE id = '$id'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	return $d[name];
}
function getCase($packet,$type){
	if ($type=="EV"){
		$idType="eviction_id";
		$table="evictionPackets";
	}elseif($type=="S"){
		$idType="packet_id";
		$table="standard_packets";
	}else{
		$idType="packet_id";
		$table="ps_packets"
	}
	$q="SELECT case_no FROM $table WHERE $idType = '$id'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	return $d[case_no];
}
function justDate($dt){
	$date=explode(' ',$dt);
	return $date[0];
}
$id=$_COOKIE[psdata][user_id];
echo "<table align='center' border='1' style='border-collapse:collapse;'><tr><td colspan='4' align='center'><h2>NOTARY LOG FOR ".strtoupper($_COOKIE[psdata][name])."</h2></td></tr><tr><td align='center'>Packet ID</td><td align='center'>Date Notarized</td><td align='center'>Signer</td><td>Case #</td></tr>";
$q="SELECT * FROM docutrack WHERE document='NOTARIZED AFFIDAVIT' AND server='$id' ORDER BY packet ASC";
$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$packet='';
	if (strpos(strtoupper($d[packet]),'S') !== false){
		$packet=str_replace('S','',strtoupper($d[packet]));
		$type="S";
	}elseif(strpos(strtoupper($d[packet]),'EV') !== false){
		$packet=str_replace('EV','',strtoupper($d[packet]));
		$type="EV";
	}else{
		$packet=$d[packet]
		$type="OTD";
	}
	echo "<tr><td>".strtoupper($d[packet])."</td><td>".justDate($d[binder])."</td><td>".id2name($d[server])."</td><td>".getCase($packet,$type)."</td></tr>";
}
echo "</table>";
?>