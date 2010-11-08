<?
include 'common.php';
mysql_connect();
mysql_select_db('core');
function alpha2desc($alpha){
	if ($alpha == 'a'){ return "FIRST DOT ATTEMPT"; }
	if ($alpha == 'b'){ return "SECOND DOT ATTEMPT"; }
	if ($alpha == 'c'){ return "POSTED DOT PROPERTY"; }
}
function photoAddress($packet,$defendant,$alpha){
	$r=@mysql_query("SELECT * from evictionPackets where eviction_id = '$packet'");
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	if ($alpha == "a" || $alpha == "b"|| $alpha == "c"){
		if ($d["address$defendant"]){
			return $d["address$defendant"].", ".$d["state$defendant"];
		}
	}
}?>
<style>
legend{background-color:#FFFFCC;}
div{text-align:center;}
fieldset, legend, div, table {padding:0px;}
</style>
<?
$packet=$_GET[packet];
$q="SELECT photo1a, photo1b, photo1c, photo2a, photo2b, photo2c, photo3a, photo3b, photo3c, photo4a, photo4b, photo4c, photo5a, photo5b, photo5c, photo6a, photo6b, photo6c, name1, name2, name3, name4, name5, name6 FROM evictionPackets WHERE eviction_id='$packet'";
$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
$d=mysql_fetch_array($r,MYSQL_ASSOC);
echo "<table align='center' valign='top'><tr>";
$i=0;
while ($i < 6){$i++;
	if ($d["name$i"]){
		echo "<td valign='top'><fieldset><legend>".strtoupper($d["name$i"])."</legend>";
		foreach(range('a','c') as $letter){ 
			$photoLink="photo".$i.$letter;
			$photo=str_replace('http://mdwestserve.com/ps/','http://mdwestserve.com/',$d["$photoLink"]);
			if ($d["$photoLink"] != ''){
				echo "<div><a href='".$photo."' target='_blank'><img src='".$photo."' height='250' width='400'><br>".alpha2desc($letter)."</a></div>";
			}
		
		}
	echo "</fieldset></td>";
	if ($i%2 == 0){
		echo "</tr><tr>";
	}
	}
}
echo "</tr></table>";
?>