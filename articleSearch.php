<?
mysql_connect();
mysql_select_db('core');
function rmSpace($str){
	return str_replace(' ','',$str);
}
function testArt($packet,$add){
	$var=$packet."-".strtoupper($add)."X";
	$q="select article from usps where packet = '$var' LIMIT 0,1";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	if ($d["article"] != ''){
		return $d["article"];
	}else{
		return 0;
	}
}
function testArt2($art){
	$art=rmSpace($art);
	$q="select packet from usps where article='$art' LIMIT 0,1";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	if ($d["packet"] != ''){
		return $d["packet"];
	}else{
		return 0;
	}
}
function enterArticle($art,$packet){
	$q="INSERT INTO usps (article, packet, status, processor, history) values ('$art', '$packet', 'SENT', '".$_COOKIE[psdata][name]."', '$history')";
	@mysql_query($q) or die ("Query: $q<br>".mysql_error());
}
function updateArticle($art,$packet){
	$art=rmSpace($art);
	$packet2=$packet."X";
	$q="UPDATE usps SET packet='$packet2' WHERE article='$art'";
	@mysql_query($q) or die ("Query: $q<br>".mysql_error());
}
function solveArt($art,$packet,$add){
	$art=rmSpace($art);
	$matrix=$packet.'-'.strtoupper($add).'X';
	if (testArt($packet,$add) != 0){
		$test2=testArt2($art);
		if ($test2 != 0){
			echo "<div style='background-color:red;font-weight:bold;'>Article $art has packet # $test2 in USPS, should be $packet-$add.</div>";
		}else{
			echo "<div style='background-color:green;font-weight:bold;'>OTD$packet missing packet # for article $add: $art in USPS</div>";
			$query="UPDATE usps SET packet='$matrix' WHERE article='$art'";
			@mysql_query($query) or die ("Query: $query<br>".mysql_error());
		}
	}else{
		echo "OTD$packet missing article $add: $art in USPS<br>";
		enterArticle($art,$matrix);
	}
}
function reverseArticle($art){
	$q="SELECT article1, article1a, article1b, article1c, article1d, article1e, article2, article2a, article2b, article2c, article2d, article2e, article3, article3a, article3b, article3c, article3d, article3e, article4, article4a, article4b, article4c, article4d, article4e, article5, article5a, article5b, article5c, article5d, article5e, article6, article6a, article6b, article6c, article6d, article6e, article1PO, article1PO2, article2PO, article2PO2, article3PO, article3PO2, article4PO, article4PO2, article5PO, article5PO2, article6PO, article6PO2, packet_id FROM ps_packets WHERE article1='$art' OR article1a='$art' OR article1b='$art' OR article1c='$art' OR article1d='$art' OR article1e='$art' OR article2='$art' OR article2a='$art' OR article2b='$art' OR article2c='$art' OR article2d='$art' OR article2e='$art' OR article3='$art' OR article3a='$art' OR article3b='$art' OR article3c='$art' OR article3d='$art' OR article3e='$art' OR article4='$art' OR article4a='$art' OR article4b='$art' OR article4c='$art' OR article4d='$art' OR article4e='$art' OR article5='$art' OR article5a='$art' OR article5b='$art' OR article5c='$art' OR article5d='$art' OR article5e='$art' OR article6='$art' OR article6a='$art' OR article6b='$art' OR article6c='$art' OR article6d='$art' OR article6e='$art' OR article1PO='$art' OR article1PO2='$art' OR article2PO='$art' OR article2PO2='$art' OR article3PO='$art' OR article3PO2='$art' OR article4PO='$art' OR article4PO2='$art' OR article5PO='$art' OR article5PO2='$art' OR article6PO='$art' OR article6PO2='$art' LIMIT 0,1";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	while ($d=mysql_fetch_array($r, MYSQL_ASSOC)){
		while ($i < 6){$i++;
			if ($d["article$i"] == $art){
				$q="UPDATE usps SET packet='".$d[packet_id]."-".$i."X' WHERE article='$art' AND packet=''";
				@mysql_query($q) or die ("Query: $q<br>".mysql_error());
				echo $q."<br>";
			}
			foreach(range('a','e') as $letter){
				$var=$i.$letter;
				if ($d["article$var"] == $art){
					$q="UPDATE usps SET packet='".$d[packet_id]."-".strtoupper($var)."X' WHERE article='$art' AND packet=''";
					@mysql_query($q) or die ("Query: $q<br>".mysql_error());
					echo $q."<br>";
				}
			}
			if ($d["article$iPO"] == $art){
				$q="UPDATE usps SET packet='".$d[packet_id]."-".$i."POX' WHERE article='$art' AND packet=''";
				@mysql_query($q) or die ("Query: $q<br>".mysql_error());
				echo $q."<br>";
			}
			if ($d["article$iPO2"] == $art){
				$q="UPDATE usps SET packet='".$d[packet_id]."-".$i."PO2X' WHERE article='$art' AND packet=''";
				@mysql_query($q) or die ("Query: $q<br>".mysql_error());
				echo $q."<br>";
			}
		}
	}
	$q="SELECT eviction_id, article1, article2, article3, article4, article5, article6 FROM evictionPackets WHERE article1='$art' OR article2='$art' OR article3='$art' OR article4='$art' OR article5='$art' OR article6='$art' LIMIT 0,1";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	while ($d=mysql_fetch_array($r, MYSQL_ASSOC)){
		while ($i < 6){$i++;
			if ($d["article$i"] == $art){
				$q="UPDATE usps SET packet='EV".$d[eviction_id]."-".$i."X' WHERE article='$art' AND packet=''";
				@mysql_query($q) or die ("Query: $q<br>".mysql_error());
				echo $q."<br>";
			}
		}
	}
}
echo "SEARCHING OTDs<br>";
$q="SELECT packet_id, article1, article1a, article1b, article1c, article1d, article1e, article2, article2a, article2b, article2c, article2d, article2e, article3, article3a, article3b, article3c, article3d, article3e, article4, article4a, article4b, article4c, article4d, article4e, article5, article5a, article5b, article5c, article5d, article5e, article6, article6a, article6b, article6c, article6d, article6e, article1PO, article1PO2, article2PO, article2PO2, article3PO, article3PO2, article4PO, article4PO2, article5PO, article5PO2, article6PO, article6PO2 FROM ps_packets ORDER BY packet_id ASC";
$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
while ($d=mysql_fetch_array($r, MYSQL_ASSOC)){
	$packet=$d[packet_id];
	$i=0;
	while ($i < 6){$i++;
		if ($d["article$i"] != ''){
			solveArt($d["article$i"],$packet,$i);
		}
		foreach(range('a','e') as $letter){
			$var=$i.$letter;
			if ($d["article$var"] != ''){
				solveArt($d["article$var"],$packet,$var);
			}
		}
		$var=$i."PO";
		if ($d["article$var"] != ''){
			solveArt($d["article$var"],$packet,$var);
		}
		$var=$i."PO2";
		if ($d["article$var"] != ''){
			solveArt($d["article$var"],$packet,$var);
		}
	}
}
echo "<hr>SEARCHING EVs<br>";
$q="SELECT article1, article2, article3, article4, article5, article6, eviction_id FROM evictionPackets ORDER BY eviction_id ASC";
$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
while ($d=mysql_fetch_array($r, MYSQL_ASSOC)){
	$packet=$d[eviction_id];
	$i=0;
	while ($i < 6){$i++;
		if ($d["article$i"] != ''){
			if (testArt("EV".$packet,$i) == 0){
				echo "EV$packet missing article $i in USPS<br>";
			}
		}
	}
}
echo "<hr>SEARCHING USPS<br>";
$q="SELECT * FROM usps WHERE article <> '' AND packet=''";
$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
while ($d=mysql_fetch_array($r, MYSQL_ASSOC)){
	reverseArticle($d[article]);
}
?>