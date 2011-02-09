<?
function alpha2ID($alpha){
	if ($alpha == 'a'){ return "1";}
	if ($alpha == 'b'){ return "1"; }
	if ($alpha == 'c'){ return "1"; }
	if ($alpha == 'd'){ return "2"; }
	if ($alpha == 'e'){ return "2"; }
	if ($alpha == 'f'){ return "3"; }
	if ($alpha == 'g'){ return "3"; }
	if ($alpha == 'h'){ return "4"; }
	if ($alpha == 'i'){ return "4"; }
	if ($alpha == 'j'){ return "5"; }
	if ($alpha == 'k'){ return "5"; }
	if ($alpha == 'l'){ return "6"; }
	if ($alpha == 'm'){ return "6"; }
}
function add2Letter($address){
	if ($address == '1'){return '';}
	if ($address == '2'){return 'a';}
	if ($address == '3'){return 'b';}
	if ($address == '4'){return 'c';}
	if ($address == '5'){return 'd';}
	if ($address == '6'){return 'e';}
}
mysql_connect();
mysql_select_db('service');
$path = "/data/service/photos/";
$q="SELECT * FROM ps_packets";
$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
echo "<table><tr><td>#</td><td>Eviction</td><td>Photo Field</td><td>Local Path</td><td>Browser Address</td></tr>";
$i2=0;
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$i=0;
	while ($i < 6){$i++;
		foreach(range('a','m') as $letter){
			$var="photo".$i.$letter;
			if ($d["$var"]){$i2++;
				$addressID=alpha2ID($letter);
				$browserAddress = str_replace('ps/','',$d["$var"]);
				$link = explode('/',$d["$var"]);
				$linkCount = count($link)-1;
				$linkCount2 = $linkCount-1;
				$localPath = $path.$link["$linkCount2"].$link["$linkCount"];
				$addLetter=add2letter($addressID);
				$user=$d["server_id.$addLetter"];
				$q2="SELECT * FROM ps_photos WHERE packetID='$d[packet_id]' AND defendantID='$i' AND addressID='$addressID'";
				$r2=@mysql_query($q2) or die ("Query: $q2<br>".mysql_error());
				$d2=mysql_fetch_array($r2,MYSQL_ASSOC);
				if ($d2[packetID] == ''){
					$query = "INSERT into ps_photos (packetID,defendantID,addressID,serverID,localPath,browserAddress) VALUES ('$d[packet_id]','$i','$addressID','$user','$localPath','$browserAddress')";
					//@mysql_query($query);
					echo "<tr style='color:black; background-color:white;'><td>$i2</td><td>$d[packet_id]</td><td>$var</td><td>$localPath</td><td>$browserAddress</td></tr>";
				}else{
					echo "<tr style='color:red; background-color:black;'><td>$i2</td><td>$d[packet_id]</td><td>$var</td><td>$localPath</td><td>$browserAddress -- PRE-EXISTS</td></tr>";
				}
			}
		}
	}
}
echo "</table>";
?>