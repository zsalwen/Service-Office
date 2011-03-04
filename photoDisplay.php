<?
include 'common.php';
mysql_connect();
mysql_select_db('core');
function alpha2desc($alpha){
	if ($alpha == 'a'){ return "FIRST DOT ATTEMPT"; }
	if ($alpha == 'b'){ return "SECOND DOT ATTEMPT"; }
	if ($alpha == 'c'){ return "POSTED DOT PROPERTY"; }
	if ($alpha == 'd'){ return "FIRST LKA ATTEMPT"; }
	if ($alpha == 'e'){ return "SECOND LKA ATTEMPT"; }
	if ($alpha == 'f'){ return "FIRST ALT ATTEMPT"; }
	if ($alpha == 'g'){ return "SECOND ALT ATTEMPT"; }
	if ($alpha == 'h'){ return "FIRST ALT ATTEMPT"; }
	if ($alpha == 'i'){ return "SECOND ALT ATTEMPT"; }
	if ($alpha == 'j'){ return "FIRST ALT ATTEMPT"; }
	if ($alpha == 'k'){ return "SECOND ALT ATTEMPT"; }
	if ($alpha == 'l'){ return "FIRST ALT ATTEMPT"; }
	if ($alpha == 'm'){ return "SECOND ALT ATTEMPT"; }
}
function photoAddress($packet,$defendant,$alpha){
	$r=@mysql_query("SELECT * from ps_packets where packet_id = '$packet'");
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	if ($alpha == "a" || $alpha == "b"|| $alpha == "c"){
		if ($d["address$defendant"]){
			return $d["address$defendant"].", ".$d["state$defendant"];
		}
	}
	if ($alpha == "d" || $alpha == "e"){
		if ($d["address$defendant"."a"]){
			return $d["address$defendant"."a"].", ".$d["state$defendant"."a"];
		}
	}
	if ($alpha == "f" || $alpha == "g"){
		if ($d["address$defendant"."b"]){
			return $d["address$defendant"."b"].", ".$d["state$defendant"."b"];
		}
	}
	if ($alpha == "h" || $alpha == "i"){
		if ($d["address$defendant"."c"]){
			return $d["address$defendant"."c"].", ".$d["state$defendant"."c"];
		}
	}
	if ($alpha == "j" || $alpha == "k"){
		if ($d["address$defendant"."d"]){
			return $d["address$defendant"."d"].", ".$d["state$defendant"."d"];
		}
	}
	if ($alpha == "l" || $alpha == "m"){
		if ($d["address$defendant"."e"]){
			return $d["address$defendant"."e"].", ".$d["state$defendant"."e"];
		}
	}
}
function byteConvert(&$bytes){
	$b = (int)$bytes;
	$s = array('B', 'kB', 'MB', 'GB', 'TB');
	if($b < 0){
		return "0 ".$s[0];
	}
	$con = 1024;
	$e = (int)(log($b,$con));
	return '<b>'.number_format($b/pow($con,$e),0,',','.').' '.$s[$e].'</b>'; 
}
function photoCount($packet,$def){
	$count=trim(getPage("http://data.mdwestserve.com/countPhotos.php?packet=$packet&def=$def", 'MDWS Count Photos', '5', ''));
	if ($count==''){
		$count=0;
	}
	return $count;
}?>
<style>
legend{background-color:#FFFFCC;}
div{text-align:center;}
fieldset, legend, div, table {padding:0px;}
</style>
<?
$packet=$_GET[packet];
$def=$_GET[defendant];
if (!$_GET[server] && !$_GET[viewAll]){
	$q="SELECT photoID FROM ps_photos WHERE packetID='$packet'";
	if ($def != ''){
		$q .= " AND defendantID='$def'";
	}
	$r=@mysql_query($q);
	//echo "$q<br>";
	$serverCount=mysql_num_rows($r);
	$allCount=photoCount($packet,$def);
	if ($serverCount != $allCount){
		echo "<table align='center' valign='top'><tr><td><a href='photoDisplay.php?packet=$packet&defendant=$def&server=1'>View Photos (As Server Would See) [$serverCount]</a></td><td><a href='photoDisplay.php?packet=$packet&defendant=$def&viewAll=1'>View All Photos [$allCount]</a></td></tr></table>";
	}else{
		//list all photos within ps_photos table for this packet & defendant
		if(strpos($packet,'EV') !== false){
			$packet2=str_replace('EV','',$packet);
			$q="SELECT name1, name2, name3, name4, name5, name6 FROM evictionPackets WHERE eviction_id='$packet2'";
		}else{
			$q="SELECT name1, name2, name3, name4, name5, name6 FROM ps_packets WHERE packet_id='$packet'";
		}
		$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
		$d=mysql_fetch_array($r,MYSQL_ASSOC);
		//echo "$q<br>";
		echo "<table align='center' valign='top'><tr><td valign='top'><fieldset><legend>".strtoupper($d["name$def"])."</legend>";
		$q2="SELECT * FROM ps_photos WHERE packetID='$packet'";
		if ($def != ''){
			$q2 .= " AND defendantID='$def'";
		}
		$r2=@mysql_query($q2) or die ("Query: $q2<br>".mysql_error());
		//echo "$q2<br>";
		while ($d2=mysql_fetch_array($r2,MYSQL_ASSOC)){
			$path=str_replace('/data/service/photos/','',$d2[localPath]);
			$size = byteConvert(filesize($d2[localPath]));
			$letter = explode("/",$path);
			$letter = explode(".",$letter[1]);
			$path="http://mdwestserve.com/photographs/".$path;
			$i2=0;
			while ($i2 < count($letter)){
				if ((trim($letter["$i2"]) != '') && (strlen(trim($letter["$i2"])) == 1)){
					if (ctype_alpha($letter["$i2"])){
						$desc=alpha2desc($letter["$i2"]);
					}
				}elseif(($i2 == count($letter)-2) && is_numeric($letter["$i2"])){
					$time=date('n/j/y @ H:i:s',$letter["$i2"]);
				}
			$i2++;
			}
			if ($dP[description] != ''){
				$desc=strtoupper($dP[description]);
			}
			echo "<div><a href='$path' target='_blank'><img src='$path' height='250' width='400'><br>$desc - <small>Uploaded: $time [<b>$size</b>]</small></a></div>";
		}
		echo "</fieldset></td></tr></table>";
	}
}elseif($_GET[viewAll]){
	//use Service-Web-Service/findPhotos.php to search packet's directory for all photos
	if(strpos($packet,'EV') !== false){
		$packet2=str_replace('EV','',$packet);
		$q="SELECT name1, name2, name3, name4, name5, name6 FROM evictionPackets WHERE eviction_id='$packet2'";
	}else{
		$q="SELECT name1, name2, name3, name4, name5, name6 FROM ps_packets WHERE packet_id='$packet'";
	}
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	//echo "$q<br>";
	$html=trim(getPage("http://data.mdwestserve.com/findPhotos.php?packet=$packet&def=$def", 'MDWS Find Photos', '5', ''));
	echo "<table align='center' valign='top'><tr>";
	if ($d["name$def"]){
		echo "<td valign='top'><fieldset><legend>".strtoupper($d["name$def"])."</legend>";
		echo $html;
		echo "</fieldset></td>";
	}
	echo "</tr></table>";
}elseif($_GET[server]){
	//list all photos within ps_photos table for this packet & defendant
	if(strpos($packet,'EV') !== false){
		$packet2=str_replace('EV','',$packet);
		$q="SELECT name1, name2, name3, name4, name5, name6 FROM evictionPackets WHERE eviction_id='$packet2'";
	}else{
		$q="SELECT name1, name2, name3, name4, name5, name6 FROM ps_packets WHERE packet_id='$packet'";
	}
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	//echo "$q<br>";
	echo "<table align='center' valign='top'><tr><td valign='top'><fieldset><legend>".strtoupper($d["name$def"])."</legend>";
	$q2="SELECT * FROM ps_photos WHERE packetID='$packet' AND defendantID='$def'";
	$r2=@mysql_query($q2) or die ("Query: $q2<br>".mysql_error());
	//echo "$q2<br>";
	while ($d2=mysql_fetch_array($r2,MYSQL_ASSOC)){
		$path=str_replace('/data/service/photos/','',$d2[localPath]);
		$size = byteConvert(filesize($d2[localPath]));
		$letter = explode("/",$path);
		$letter = explode(".",$letter[1]);
		$path="http://mdwestserve.com/photographs/".$path;
		$i2=0;
		while ($i2 < count($letter)){
			if ((trim($letter["$i2"]) != '') && (strlen(trim($letter["$i2"])) == 1)){
				if (ctype_alpha($letter["$i2"])){
					$desc=alpha2desc($letter["$i2"]);
				}
			}elseif(($i2 == count($letter)-2) && is_numeric($letter["$i2"])){
				$time=date('n/j/y @ H:i:s',$letter["$i2"]);
			}
		$i2++;
		}
		if ($dP[description] != ''){
			$desc=strtoupper($dP[description]);
		}
		echo "<div><a href='$path' target='_blank'><img src='$path' height='250' width='400'><br>$desc - <small>Uploaded: $time [<b>$size</b>]</small></a></div>";
	}
	echo "</fieldset></td></tr></table>";
}
?>