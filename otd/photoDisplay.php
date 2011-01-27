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
}?>
<style>
legend{background-color:#FFFFCC;}
div{text-align:center;}
fieldset, legend, div, table {padding:0px;}
</style>
<?
$packet=$_GET[packet];
$q="SELECT name1, name2, name3, name4, name5, name6 FROM ps_packets WHERE packet_id='$packet'";
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