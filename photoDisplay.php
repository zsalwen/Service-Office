<?
include 'common.php';
mysql_connect();
mysql_select_db('core');
?>
<style>
legend{background-color:#FFFFCC;}
div{text-align:center;}
fieldset, legend, div, table {padding:0px;}
</style>
<?
$packet=$_GET[packet];
if(strpos($packet,'EV') !== false){
	$packet2=str_replace('EV','',$packet);
	$q="SELECT name1, name2, name3, name4, name5, name6 FROM evictionPackets WHERE eviction_id='$packet2'";
}else{
	$q="SELECT name1, name2, name3, name4, name5, name6 FROM ps_packets WHERE packet_id='$packet'";
}
$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
$d=mysql_fetch_array($r,MYSQL_ASSOC);
echo "<table align='center' valign='top'><tr>";
$i=0;
while ($i < 6){$i++;
	if ($d["name$i"]){
		$html=trim(getPage("http://data.mdwestserve.com/findPhotos.php?packet=$packet&def=$i", 'MDWS Find Photos', '5', ''));
		echo "<td valign='top'><fieldset><legend>".strtoupper($d["name$i"])."</legend>";
		echo $html;
		echo "</fieldset></td>";
		if ($i%2 == 0){
			echo "</tr><tr>";
		}
	}
}
echo "</tr></table>";
?>