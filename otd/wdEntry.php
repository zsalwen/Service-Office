<?
mysql_connect();
mysql_select_db('service');
include 'common.php';
$packet=$_GET[packet];
$q="SELECT name1, name2, name3, name4, name5, name6, circuit_court FROM ps_packets WHERE packet_id='$packet'";
$r=@mysql_query($q) or die("Query $q<br>".mysql_error());
$d=mysql_fetch_array($r,MYSQL_ASSOC);

if ($_POST[submit]){
	while ($i < 6){$i++;
		if ($d["name$i"]){
			$q1="SELECT * from watchDog WHERE packetID='$packet' AND defID='$i'";
			$r1=@mysql_query($q1) or die("Query $q1<br>".mysql_error());
			if ($d1=mysql_fetch_array($r1,MYSQL_ASSOC)){
				$q="UPDATE watchDog SET firstName='".$_POST["fn$i"]."', lastName='".$_POST["ln$i"]."' WHERE watchID='$d[watchID]'";
			}else{
				$q="INSERT INTO watchDog (packetID, defID, firstName, lastName) VALUES ('$packet','$i','".$_POST["fn$i"]."','".$_POST["ln$i"]."')";
			}
		$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
		}
	}
}
?>
<form method="post">
<input type="hidden" name="packet" value="<?=$packet?>">
<input type="hidden" name="county" value="<?=$d[circuit_court]?>">
<table>
<? $i=0;
while ($i < 6){$i++;
	if ($d["name$i"]){?>
	<tr>
		<td>First Name</td>
		<td><input name="fn<?=$i?>"></td>
	</tr>
	<tr>
		<td>Last Name</td>
		<td><input name="ln<?=$i?>"></td>
	</tr>
<? } ?>
	<tr>
		<td colspan="2" align="right"><input type="submit" name="submit" value="Submit"></td>
	</tr>
</table>
</form>