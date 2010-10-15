<?
if ($_POST[def] != 'ALL'){
	if ($ddr == ''){
		$q="INSERT INTO ps_instructions (packetID,serveA".$def.",customA".$def.") values ('$packet','".$_POST["serveA$def"]."','".$_POST["customA$def"]."')";
	}else{
		$q="UPDATE ps_instructions SET serveA$def='".$_POST["serveA$def"]."', customA$def='".$_POST["customA$def"]."' WHERE packetID='$packet'";
	}
}else{
	$q="SELECT name1, name2, name3, name4, name5, name6 FROM ps_packets WHERE packet_id='$packet'";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	$ddr2=mysql_fetch_array($r,MYSQL_ASSOC);
	$i=0;
	if ($ddr == ''){
		$q="INSERT INTO ps_instructions (packetID, ";
		$q1=") values ('$packet', ";
		while ($i < 6){$i++;
			if ($ddr2["name$i"]){
				$q .= "serveA$i, ";
				$q1 .= "'".$_POST["serveA$i"]."', ";
				$q .= "customA$i, ";
				$q1 .= "'".str_replace('[NAME]',$ddr2["name$i"],$_POST["customA0"])."', ";
			}
		}
		$q=substr($q,0,-2);
		$q1=substr($q1,0,-2);
		$q .= $q1.")";
		}else{
		$q="UPDATE ps_instructions SET ";
		while ($i < 6){$i++;
			if ($ddr2["name$i"]){
				$q .= "serveA$i='".$_POST["serveA$i"]."', ";
				$q .= "customA$i='".str_replace('[NAME]',$ddr2["name$i"],$_POST["customA0"])."', ";
			}
		}
		$q=substr($q,0,-2);
		$q .= " WHERE packetID='$packet'";
	}
}
$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
//echo $q."<br>";
echo "<center>PACKET SUCCESSFULLY UPDATED</center>";
?>
<form method="post">
<input type="hidden" name="i" value="1">
<table align="center" border="1" style="border-collapse:collapse;">
	<tr>
		<td align="center">Enter Another Packet</td>
	</tr>
	<tr>
		<td><input name="packet"></td>
	</tr>
	<tr>
		<td  align="center"><input type="submit" name="submit" value="GO!"></td>
	</tr>
</table>
</form>