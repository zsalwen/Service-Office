<?
include 'common.php';
function defCount($packet_id){
	$c=0;
	$r=@mysql_query("SELECT name1, name2, name3, name4, name5, name6 from ps_packets WHERE packet_id='$packet_id' LIMIT 0,1");
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	$i=0;
	while ($i < 6){$i++;
		if ($d["name$i"]){$c++;}
	}
	return $c;
}
function artLink($art,$color){
	$tracking = getPage("http://mdwestserve.com/ps/usps.php?track=$art", "USPS Tracking Article $art", '5', '');
	return "<div class='$color'>Live USPS Database Tracking of $art<br>$tracking</div>";
}
function article($packet,$add){
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
function getAdd($add,$city,$state,$zip){
	return "$add<br>$city, $state $zip";
}
?>
<style>
.g {border:solid 1px #00FF00; height:180px;width:300px; font-size:11px; background-color:#00FF00 !important;}
.y {border:solid 1px #"y"; height:180px;width:300px; font-size:11px; background-color:#"y" !important;}
</style>
<?
$packet=$_GET[packet];
$q="SELECT * FROM ps_packets WHERE packet_id='$packet' LIMIT 0,1";
$r=@mysql_query($q) or die("Query $q<br>".mysql_error());
$d=mysql_fetch_array($r,MYSQL_ASSOC);
$list="<table align='center' border='1' style='border-collapse:collapse; border: 1px solid black;'><tr><td align='center' colspan='[COLS]' style='font-size:18px;font-weight:bold;'>OTD$packet</td></tr><tr>";
$defCount=defCount($packet);
$i=0;
$count=0;
$cols=1;
while ($i < 6){$i++;
	if ($d["name$i"]){
		if (($i >= ($defCount/2)) && ($cols == 1)){
			$list .= "</tr><tr>";
			$cols=2;
		}
		$list .= "<td valign='top'><fieldset><legend>".strtoupper($d["name$i"])."</legend>";
		//now get articles for all addresses and PO Boxes
		$list .= getAdd($d["address$i"],$d["city$i"],$d["state$i"],$d["zip$i"]);
		$art=article($packet,$i);
		if ($art != 0){$count++;
			$list .= artLink($art,"g");
		}elseif($d["article$i"] != ''){$count++;
			$list .= artLink($d["article$i"],"y");
		}else{
			$list .= "<i>NO MAIL RECORDED</i><br>";
		}
		foreach(range('a','e') as $letter){
			$var=$i.$letter;
			if ($d["address$var"]){
				$list .= getAdd($d["address$var"],$d["city$var"],$d["state$var"],$d["zip$var"]);
				$art=article($packet,$var);
				if ($art != 0){$count++;
					$list .= artLink($art,"g");
				}elseif($d["article$var"] != ''){$count++;
					$list .= artLink($d["article$var"],"y");
				}else{
					$list .= "<i>NO MAIL RECORDED</i><br>";
				}
			}
		}
		if ($d[pobox]){
			$var=$i."PO";
			$list .= getAdd($d["address$var"],$d["city$var"],$d["state$var"],$d["zip$var"]);
			$art=article($packet,$var);
			if ($art != 0){$count++;
				$list .= artLink($art,"g");
			}elseif($d["article$var"] != ''){$count++;
				$list .= artLink($d["article$var"],"y");
			}else{
				$list .= "<i>NO MAIL RECORDED</i><br>";
			}
		}
		if ($d[pobox2]){
			$var=$i."PO2";
			$list .= getAdd($d["address$var"],$d["city$var"],$d["state$var"],$d["zip$var"]);
			$art=article($packet,$var);
			if ($art != 0){$count++;
				$list .= artLink($art,"g");
			}elseif($d["article$var"] != ''){$count++;
				$list .= artLink($d["article$var"],"y");
			}else{
				$list .= "<i>NO MAIL RECORDED</i><br>";
			}
		}
		$list .= "</fieldset></td>";
	}
}
$list .= "</tr></table>";
if ($count == 0){
	echo "<center style='font-size:24px;'>NO MAILINGS TO DISPLAY FOR OTD$packet</center>";
}else{
	echo str_replace("[COLS]",$cols,$list);
}
?>