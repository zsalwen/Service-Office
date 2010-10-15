<?
include 'common.php';
function artLink($art,$color){
	$tracking = getPage("http://mdwestserve.com/ps/usps.php?track=$art", "USPS Tracking Article $art", '5', '');
	return "<div class='$color'>Live USPS Database Tracking of $art<br>$tracking</div>";
}
function article($packet,$add){
	$var=$packet."-".strtoupper($add)."X";
	$q="select article from usps where packet = '$var' LIMIT 0,1";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	if ($d["article"] != ''){
		return $d["article"];
	}else{
		return 0;
	}
}
?>
<style>
.g {border:solid 1px #00FF00; height:180px;width:300px; font-size:11px; background-color:#00FF00 !important;}
.y {border:solid 1px #"y"; height:180px;width:300px; font-size:11px; background-color:#"y" !important;}
</style>
<?
$packet=$_GET[packet];
$q="SELECT * FROM evictionPackets WHERE eviction_id='$packet' LIMIT 0,1";
$r=@mysql_query($q) or die("Query $q<br>".mysql_error());
$d=mysql_fetch_array($r,MYSQL_ASSOC);
$list="<table align='center' border='1' style='border-collapse:collapse; border: 1px solid black;'><tr><td align='center' style='font-size:18px;font-weight:bold;'>EV$packet MAILING TO OCCUPANT</td></tr><tr><td>";
$count=0;
if ($d[name1]){
	$list .= $d[address1].', '.$d[city1].', '.$d[state1].' '.$d[zip1].'<br>';
	$art=article('EV'.$packet,1);
	if ($art != 0){$count++;
		$list .= artLink($art,"g");
	}elseif ($d[article1] != ''){$count++;
		//now get article for mailing
		$list .= artLink($d[article1],"y");
	}
}
$list .= "</td></tr></table>";
if ($count == '0'){
	echo "<center style='font-size:24px;'>NO MAILINGS TO DISPLAY FOR EV$packet</center>";
}else{
	echo $list;
}
?>