<?
if ($_GET[packet] && !$_POST[packet]){
	$i=1;
}elseif (!$_POST['i']){
	$i='a';
}else{
	$i=$_POST['i'];
}
if ($_GET[packet]){
	$packet=$_GET[packet];
}else{
	$packet=$_POST[packet];
}
$def=$_POST[def];
mysql_connect();
mysql_select_db('service');
include 'common.php';
function subWord($str,$num){
	$length=strlen($str);
	$explode=explode(' ',$str);
	$strCount=count($explode);
	$i=0;
	$finalStr='';
	while($i < $strCount){
		if (!ctype_alpha(substr($explode[$i], -1))){
			$explode[$i] = substr($explode[$i],0,-1);
		}
		if (strlen($finalStr) <= $num){
			$testStr = $finalStr.$explode[$i].' ';
			if (strlen($testStr) < $num){
				$finalStr .= $explode[$i].' ';
			}
		}
		$i++;
	}
	return trim($finalStr);
}
function defList($packet,$def){
	$r=@mysql_query("SELECT name1, name2, name3, name4, name5, name6 FROM ps_packets WHERE packet_id='$packet'") or die(mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	$i=0;
	if ($d["name$def"] != '' && $def != ''){
		$list .= "<option value='$def' style='background-color:red;'>Editing ".subWord($d["name$def"],30)."</option>";
	}
	while ($i < 6){$i++;
		if ($d["name$i"] != '' && $d["name$def"] != $d["name$i"]){
			$list .= "<option value='$i'>".subWord($d["name$i"],30)."</option>";
		}
	}
	return $list;
}
function defCheckList($packet){
	$r=@mysql_query("SELECT name1, name2, name3, name4, name5, name6 FROM ps_packets WHERE packet_id='$packet'") or die(mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	$r1=@mysql_query("SELECT serveA1, serveA2, serveA3, serveA4, serveA5, serveA6 FROM ps_instructions WHERE packetID='$packet'") or die(mysql_error());
	$d1=mysql_fetch_array($r1,MYSQL_ASSOC);
	$i=0;
	while ($i < 6){$i++;
		if ($d["name$i"] != ''){
			if ($i%2 != 0){
				$list .= "<tr>";
			}
			$serve="";
			if ($d1["serveA$i"] == 'checked'){ $serve="checked";}
			$list .= "<td><input type='checkbox' name='serveA$i' $serve value='checked'> Serve ".$d["name$i"]."</td>";
			if ($i%2 == 0){
				$list .= "</tr>";
			}
		}
	}
	return $list;
}
function getDef($def,$packet){
	$r=@mysql_query("SELECT name$def FROM ps_packets WHERE packet_id='$packet'") or die(mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	return $d["name$def"];
}
$q="SELECT * FROM ps_instructions WHERE packetID='$packet'";
$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
$ddr=mysql_fetch_array($r,MYSQL_ASSOC);
if ($i != 'a' && !$_POST[bypass]){
	echo "<table border='1' style='border: 1px solid; border-collapse:collapse; padding:0px !important;' width='100%' height='100%'><tr><td width='25%' bgcolor='#6699FF'>";
	include "instructWiz.menu.php";
	echo "</td><td width='75%'>";
	$width="width='100%' height='100%'";
	$height="300";
}
echo "<table align='center' border='1' style='border: 1px solid; background-color: #1078E1; border-color:black; padding:0px !important;' $width><tr><td align='center'>";
if ($_POST[packet] || $_GET[packet]){
echo "<b>PACKET $packet</b><br>";
}
include "instructWiz.$i.php";
echo "</td></tr></table>";
if ($i != 'a' && !$_POST[bypass]){
	echo "</td></tr><tr><td colspan='2' align='center' style='border: 1px solid; background-color: #1078E1; border-color:black;'>";
	echo "<iframe src='http://staff.mdwestserve.com/otd/instructGen.php?packet=$packet&def=$def&type=$type' height='$height' width='850' name='preview' id='preview'></iframe>";
	echo "</td></tr></table>";
}
?>