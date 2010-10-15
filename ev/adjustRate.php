<?
include 'common.php';
mysql_connect();
mysql_select_db('core');
function getRate($package){
	$x=0;
	$q="SELECT eviction_id, contractor_rate FROM evictionPackets WHERE package_id='$package'";
	$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		if($rate == ''){
			$rate=$d[contractor_rate];
			if ($d[contractor_rate] != ''){
				$x=1;
			}
		}elseif($rate == $d[contractor_rate]){
			$x++;
		}elseif($rate != $d[contractor_rate]){
		
			$diff .= "<br>CONFLICTED: Packet ".$d[eviction_id]." has a rate of ".$d[contractor_rate];
		}
	}
		$returnStr = $rate."x <b>".$x." files</b>";
		if ($diff){
			$returnStr .= $diff;
		}
		return $returnStr;
}
function getRatea($package){
	$x=0;
	$q="SELECT eviction_id, contractor_ratea FROM evictionPackets WHERE package_id='$package'";
	$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		if(!$rate){
			$rate=$d[contractor_ratea];
			if ($d[contractor_ratea] != ''){
				$x=1;
			}
		}elseif($rate == $d[contractor_ratea]){
			$x++;
		}elseif($rate != $d[contractor_ratea]){
			$diff .= "<br>CONFLICTED: Packet ".$d[eviction_id]." has a rate of ".$d[contractor_ratea];
		}
	}
		$returnStr = $rate."x <b>".$x." files</b>";
		if ($diff){
			$returnStr .= $diff;
		}
		return $returnStr;
}
function getRateb($package){
	$x=0;
	$q="SELECT eviction_id, contractor_rateb FROM evictionPackets WHERE package_id='$package'";
	$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		if(!$rate){
			$rate=$d[contractor_rateb];
			if ($d[contractor_rateb] != ''){
				$x=1;
			}
		}elseif($rate == $d[contractor_rateb]){
			$x++;
		}elseif($rate != $d[contractor_rateb]){
			$diff .= "<br>CONFLICTED: Packet ".$d[eviction_id]." has a rate of ".$d[contractor_rateb];
		}
	}
		$returnStr = $rate."x <b>".$x." files</b>";
		if ($diff){
			$returnStr .= $diff;
		}
		return $returnStr;
}
function getRatec($package){
	$x=0;
	$q="SELECT eviction_id, contractor_ratec FROM evictionPackets WHERE package_id='$package'";
	$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		if(!$rate){
			$rate=$d[contractor_ratec];
			if ($d[contractor_ratec] != ''){
				$x=1;
			}
		}elseif($rate == $d[contractor_ratec]){
			$x++;
		}elseif($rate != $d[contractor_ratec]){
			$diff .= "<br>CONFLICTED: Packet ".$d[eviction_id]." has a rate of ".$d[contractor_ratec];
		}
	}
		$returnStr = $rate."x <b>".$x." files</b>";
		if ($diff){
			$returnStr .= $diff;
		}
		return $returnStr;
}
function getRated($package){
	$x=0;
	$q="SELECT eviction_id, contractor_rated FROM evictionPackets WHERE package_id='$package'";
	$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		if(!$rate){
			$rate=$d[contractor_rated];
			if ($d[contractor_rated] != ''){
				$x=1;
			}
		}elseif($rate == $d[contractor_rated]){
			$x++;
		}elseif($rate != $d[contractor_rated]){
			$diff .= "<br>CONFLICTED: Packet ".$d[eviction_id]." has a rate of ".$d[contractor_rated];
		}
	}
		$returnStr = $rate."x <b>".$x." files</b>";
		if ($diff){
			$returnStr .= $diff;
		}
		return $returnStr;
}
function getRatee($package){
	$x=0;
	$q="SELECT eviction_id, contractor_ratee FROM evictionPackets WHERE package_id='$package'";
	$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		if(!$rate){
			$rate=$d[contractor_ratee];
			if ($d[contractor_ratee] != ''){
				$x=1;
			}
		}elseif($rate == $d[contractor_ratee]){
			$x++;
		}elseif($rate != $d[contractor_ratee]){
			$diff .= "<br>CONFLICTED: Packet ".$d[eviction_id]." has a rate of ".$d[contractor_ratee];
		}
	}
		$returnStr = $rate."x <b>".$x." files</b>";
		if ($diff){
			$returnStr .= $diff;
		}
		return $returnStr;
}

if ($_POST[submit1]){
	$q1 = "UPDATE evictionPackets SET 	contractor_rate='$_POST[quote1]', 
									contractor_ratea='$_POST[quote1a]', 
									contractor_rateb='$_POST[quote1b]', 
									contractor_ratec='$_POST[quote1c]', 
									contractor_rated='$_POST[quote1d]', 
									contractor_ratee='$_POST[quote1e]'
										WHERE package_id='$_POST[id]'";		
	$r1 = @mysql_query ($q1) or die(mysql_error());
	hardLog('updated package rate information for '.$_GET[id],'user');
}

if ($_POST[submit2]){
function packageFile($file_id, $contractor_rate, $contractor_ratea, $contractor_rateb, $contractor_ratec, $contractor_rated, $contractor_ratee){
	$q = "UPDATE evictionPackets SET contractor_rate='$contractor_rate',
								contractor_ratea='$contractor_ratea',
								contractor_rateb='$contractor_rateb',
								contractor_ratec='$contractor_ratec',
								contractor_rated='$contractor_rated',
								contractor_ratee='$contractor_ratee'
									WHERE eviction_id = '$file_id'";
	$r=@mysql_query($q) or die("Query: $q<br><br><br>".mysql_error());
}

function makePackage($array1,$array2,$array3,$array4,$array5,$array6,$array7){
	foreach ($array1 as $id) {
		packageFile($id,$array2[$id],$array3[$id],$array4[$id],$array5[$id],$array6[$id],$array7[$id]);
	}
}	
		makePackage($_POST[update],$_POST[rate1],$_POST[rate1a],$_POST[rate1b],$_POST[rate1c],$_POST[rate1d],$_POST[rate1e]);
		if ($_GET[id]){
			$id=$_GET[id];
		}else{
			$id=$_POST[id];
		}
}

if($_GET[id] != ''){
	$q1 = "SELECT server_id, server_ida, server_idb, server_idc, server_idd, server_ide FROM evictionPackets WHERE package_id = '$_GET[id]'";
	$r1 = @mysql_query ($q1) or die("Query: $q1<br>".mysql_error());
	$d1 = mysql_fetch_array($r1, MYSQL_ASSOC); 
	$quote1=getRate($_GET[id]);
	$quote1a=getRatea($_GET[id]);
	$quote1b=getRateb($_GET[id]);
	$quote1c=getRatec($_GET[id]);
	$quote1d=getRated($_GET[id]);
	$quote1e=getRatee($_GET[id]);	
	?>
	<form name="form1" method="post">
	<div align="center"><fieldset><legend>PACKAGE OVERVIEW:</legend>
	<table align="center" border="2" bgcolor="#6666FF">
		<tr class="head">
			<td>Package #</td>
			<td>Server</td>
			<td>Server 'A'</td>
			<td>Server 'B'</td>
			<td>Server 'C'</td>
			<td>Server 'D'</td>
			<td>Server 'E'</td>
		</tr>
		<tr>
			<td><?=$_GET[id]?></td>
			<td style="font-size:12px;"><?=id2name($d1[server_id])?></td>
			<td style="font-size:12px;"><?=id2name($d1[server_ida])?></td>
			<td style="font-size:12px;"><?=id2name($d1[server_idb])?></td>
			<td style="font-size:12px;"><?=id2name($d1[server_idc])?></td>
			<td style="font-size:12px;"><?=id2name($d1[server_idd])?></td>
			<td style="font-size:12px;"><?=id2name($d1[server_ide])?></td>
		</tr>
		
<?
	$quote1=explode('x',$quote1);
	$quote1a=explode('x',$quote1a);
	$quote1b=explode('x',$quote1b);
	$quote1c=explode('x',$quote1c);
	$quote1d=explode('x',$quote1d);
	$quote1e=explode('x',$quote1e);
	$rest1=trim($quote1[1]);
	$rest1a=trim($quote1a[1]);
	$rest1b=trim($quote1b[1]);
	$rest1c=trim($quote1c[1]);
	$rest1d=trim($quote1d[1]);
	$rest1e=trim($quote1e[1]);
	$quote1=trim($quote1[0]);
	$quote1a=trim($quote1a[0]);
	$quote1b=trim($quote1b[0]);
	$quote1c=trim($quote1c[0]);
	$quote1d=trim($quote1d[0]);
	$quote1e=trim($quote1e[0]);
		?>
		<tr>
			<td>Update Quote</td>
			<td style="font-size:12px;">$<input name="quote1" size="3" value="<?=$quote1?>"> x <?=$rest1?></td>
			<td style="font-size:12px;">$<input name="quote1a" size="3" value="<?=$quote1a?>"> x <?=$rest1a?></td>
			<td style="font-size:12px;">$<input name="quote1b" size="3" value="<?=$quote1b?>"> x <?=$rest1b?></td>
			<td style="font-size:12px;">$<input name="quote1c" size="3" value="<?=$quote1c?>"> x <?=$rest1c?></td>
			<td style="font-size:12px;">$<input name="quote1d" size="3" value="<?=$quote1d?>"> x <?=$rest1d?></td>
			<td style="font-size:12px;">$<input name="quote1e" size="3" value="<?=$quote1e?>"> x <?=$rest1e?></td>
			<input type="hidden" name="id" value="<? if($_GET[id]){ echo $_GET[id];}elseif($_POST[id]){ echo $_POST[id];} ?>">
		</tr>
		<tr>
			<td></td><td colspan="5" align="right"><input type="submit" name="submit1" value="Submit"></td><td></td>
		</tr>
	</table>
	</fieldset></div>
	</form>
<?	$query = "SELECT eviction_id, server_id, server_ida, server_idb, server_idc, server_idd, server_ide, contractor_rate, contractor_ratea, contractor_rateb, contractor_ratec, contractor_rated, contractor_ratee FROM evictionPackets WHERE package_id = '$_GET[id]'";
	$result = @mysql_query ($query) or die("Query: $query<br>".mysql_error());
	echo "<div align='center'><fieldset><legend>PACKET-BY-PACKET VIEW:</legend>";
	echo "<form name='form2' method='post'><table align='center' border='1' bgcolor='#FFCCCC'>";
	echo "<tr class='head'>
			<td>Packet #</td>
			<td>Server</td>
			<td>Server 'A'</td>
			<td>Server 'B'</td>
			<td>Server 'C'</td>
			<td>Server 'D'</td>
			<td>Server 'E'</td>
		</tr>";
	while ($data = mysql_fetch_array($result, MYSQL_ASSOC)){?>
		<tr>
			<td><b><?=$data[eviction_id]?></b></td>
			<td style="font-size:12px;"><?=id2name($data[server_id])?></td>
			<td style="font-size:12px;"><?=id2name($data[server_ida])?></td>
			<td style="font-size:12px;"><?=id2name($data[server_idb])?></td>
			<td style="font-size:12px;"><?=id2name($data[server_idc])?></td>
			<td style="font-size:12px;"><?=id2name($data[server_idd])?></td>
			<td style="font-size:12px;"><?=id2name($data[server_ide])?></td>
		</tr>
		<tr>
			<td>Quote <input type="checkbox" value="<?=$data[eviction_id]?>" name="update[<?=$data[eviction_id]?>]"></td>
			<td style="font-size:12px;">$<input name="rate1[<?=$data[eviction_id]?>]" value="<?=$data[contractor_rate]?>" size="3"></td>
			<td style="font-size:12px;">$<input name="rate1a[<?=$data[eviction_id]?>]" value="<?=$data[contractor_ratea]?>" size="3"></td>
			<td style="font-size:12px;">$<input name="rate1b[<?=$data[eviction_id]?>]" value="<?=$data[contractor_rateb]?>" size="3"></td>
			<td style="font-size:12px;">$<input name="rate1c[<?=$data[eviction_id]?>]" value="<?=$data[contractor_ratec]?>" size="3"></td>
			<td style="font-size:12px;">$<input name="rate1d[<?=$data[eviction_id]?>]" value="<?=$data[contractor_rated]?>" size="3"></td>
			<td style="font-size:12px;">$<input name="rate1e[<?=$data[eviction_id]?>]" value="<?=$data[contractor_ratee]?>" size="3"></td>
		</tr>
	<? } ?>
		<tr>
			<td></td><td colspan="5" align="right"><input type="submit" name="submit2" value="Submit"></td><td></td>
		</tr>
	<?
	echo "</table></form></fieldset></div>";



?>

<body bgcolor="#99CCFF">
<style>
fieldset { background-color:#FFFFFF;  border:solid 1px #000000; width:800px;}
.altset { background-color:#FFFFFF;  border:solid 1px #000000;}
.altset2 { background-color:#FFFFFF;  border:solid 1px #000000;}
legend, input, select { padding:0px; background-color:#FFFFCC; border:solid 1px #000000;}
td { font-variant:small-caps; background-color:#FFFFFF; text-align:center; }
.head { font-weight: bold; text-align: center;}
</style>
<? }else{ ?>
<form>
<table align="center">
	<tr>
		<td align="center"><input name="id" value="Enter Package #" onClick="value=''" size="15"></td>
	</tr>
	<tr>
		<td align="center"><input type="submit" value="GO!"></td>
	</tr>
	<tr>
		<td align="center"><hr>OR<hr></td>
	</tr>
	<tr>
		<td align="center"><a href="view_packages.php" style="text-decoration:none; color:#336699;">Lookup Packages</a></td>
	</tr>
</table>
</form>

<? } ?>