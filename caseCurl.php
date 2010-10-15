<?
mysql_connect();
mysql_select_db('core');
include 'common.php';
//$id=$_GET[id];
$id=8;
$q="SELECT * from watchDog WHERE watchID='$id'";
$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
$d=mysql_fetch_array($r,MYSQL_ASSOC);
$fn=$d[firstName];
$ln=$d[lastName];
$court=$d[county];
$response=$d[response];
$html=getPage("http://data.mdwestserve.com/caseSearch.php?firstName=$fn&lastName=$ln&court=$court", 'MDWS caseSearch curl', '20', '');
$html=addslashes(trim($html));
echo $html;
if ($html != $response){
	$q1="UPDATE watchDog SET response='$html' WHERE watchID='$id'";
	$r1=@mysql_query($q1) or die("Query: $q1<br>".mysql_error());
	echo "<div style='border:1px solid; font-size:18px;'>UPDATED!</div>";
}else{
	echo "<div style='border:1px solid; font-size:18px;'>SAME AS STORED INFORMATION.  SYSTEM NOT UPDATED.</div>";
}
?>