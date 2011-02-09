<?
mysql_connect();
mysql_select_db('service');
include 'common.php';
?>
<style>
.entryPoint{background-color:#FFFFCC; border:double 2px; height:100%;}
.plus {background-color:#0099FF; border:ridge 2px #33CCFF; text-align:center; width:20px !important; font-weight:bold; font-size:16px; display:inline-block; vertical-align:50%; width:100%;}
input { background-color:#CCFFFF; font-variant:small-caps; font-size:12px }
textarea { background-color:#CCFFFF; font-variant:small-caps; }
.back {background-color:#0099FF;}
</style>
<?
function defCount($packet){
	$q="SELECT name1, name2, name3, name4, name5, name6 FROM ps_packets WHERE packet_id='$packet'";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	$i=0;
	$count=0;
	while ($i < 6){$i++;
		if ($d["name$i"]){$count++;}
	}
	return $count;
}
function addCount($packet){
	$q="SELECT address1, address1a, address1b, address1c, address1d, address1e FROM ps_packets WHERE packet_id='$packet'";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	$count=0;
	if ($d[address1]){$count++;}
	foreach(range('a','e') as $letter){
		if ($d["address1$letter"]){$count++;}
	}
	return $count;
}
function entryPoint($packet,$def,$add){
	$address=$def.$add;
	$q="SELECT name".$def.", address".$address.", city".$address.", state".$address.", zip".$address.", server_id".$add." FROM ps_packets WHERE packet_id='$packet'";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	$q1="SELECT customA$address FROM ps_instructions WHERE packetID='$packet'";
	$r1=@mysql_query($q1) or die ("Query: $q1<br>".mysql_error());
	$d1=mysql_fetch_array($r1,MYSQL_ASSOC);
	$server=id2name($d["server_id$add"]);
	$html="<table class='entryPoint' height='100%' width='100%'>
	<tr>
	<td valign='middle'>
	<div class='plus' id='plus$address' onClick=\"hideshow2(document.getElementById('customA$address'));ChangeText('plus$address');\">
	+
	</div>
	<textarea id='customA$address' name='customA$address' rows='3' cols='35' style='display:none;'>";
	//add content to textarea if present in database, otherwise use best estimate for service format A
	
	if ($d1["customA$address"]){
		$html .= $d1["customA$address"];
	}else{
		if ($add == ''){
			$html .= "After all other attempts have proven unsuccessful, if $server is unable to serve ".$d["name$def"].": $server is to post ".$d["address$address"].", ".$d["city$address"].", ".$d["state$address"]." ".$d["zip$address"];
		}else{
			$html .= "$server is to perform 2 attempts on ".$d["name$def"]." at ".$d["address$address"].", ".$d["city$address"].", ".$d["state$address"]." ".$d["zip$address"];
		}
	}
	$html .= "</textarea>
				</td>
				</tr>
				</table>";
	return $html;
}
function entryPoint2($packet,$add){
	$defCount=defCount($packet);
	$address="1".$add;
	$q="SELECT name1, address".$address.", city".$address.", state".$address.", zip".$address.", server_id".$add." FROM ps_packets WHERE packet_id='$packet'";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	$server=id2name($d["server_id$add"]);
	$i=0;
	$txtList='';
	while ($i < $defCount){$i++;
		$add2=$i.$add;
		$txtList .= "document.form1.customA$add2.value=this.value;";
	}
	$html="<table class='entryPoint' height='100%' width='100%'>
	<tr>
	<td valign='middle'>
	<div class='plus' id='plusROW$address' onClick=\"hideshow2(document.getElementById('customROW$add'));ChangeText('plusROW$address');\" >
	+
	</div>
	<textarea id='customROW$add' name='customROW$add' onKeyDown=\"$txtList\" rows='3' cols='35' style='display:none;'>";
	if ($add == ''){
		$html .= "After all other attempts have proven unsuccessful, if $server is unable to serve [NAME]: $server is to post ".$d["address$address"].", ".$d["city$address"].", ".$d["state$address"]." ".$d["zip$address"];
	}else{
		$html .= "$server is to perform 2 attempts on ".$d["name$def"]." at ".$d["address$address"].", ".$d["city$address"].", ".$d["state$address"]." ".$d["zip$address"];
	}
	$html .= "</textarea>
				</td>
				</tr>
				</table>";
	return $html;
}
function defList($packet,$add){
	$q="SELECT name1, name2, name3, name4, name5, name6 FROM ps_packets WHERE packet_id='$packet'";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	$i=0;
	while ($i < 6){$i++;
		$address=$i.$add;
		if ($d["name$i"]){
			$defList .= "<td>".entryPoint($packet,$i,$add)."</td>";
		}
	}
	$defList .= "<td>".entryPoint2($packet,$add)."</td>";
	return $defList;
}
function defList2($packet){
	$q="SELECT name1, name2, name3, name4, name5, name6 FROM ps_packets WHERE packet_id='$packet'";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	$i=0;
	$addCount=addCount($packet);
	$defCount=defCount($packet);
	while ($i < 6){$i++;
		if ($d["name$i"]){
			$clickList='';
			if ($addCount >= 1){
				$clickList .= "hideshow2(document.getElementById('customA$i'));";
				$lastList .= "hideshow2(document.getElementById('customROW'));";
			}
			$chr=chr($addCount+96);
			if ($chr=='a'){
				$clickList .= "hideshow2(document.getElementById('customA".$i."a'));";
				$lastList .= "hideshow2(document.getElementById('customROWa'));";
			}else{
				foreach(range('a',$chr) as $letter){
					$clickList .= "hideshow2(document.getElementById('customA".$i.$letter."'));";
					$lastList .= "hideshow2(document.getElementById('customROW".$letter."'));";
				}
			}
			$defList2[0] .= "<td align='center' onClick=\"$clickList\">".$d["name$i"]."</td>";
			if ($i == $defCount){
				$defList2[0] .= "<td align='center' onClick=\"$lastList\">CLICK TO EDIT ENTIRE ROW</td>";
			}
			$defList2[1] = $i+1;
		}
	}
	return $defList2;
}
if ($_GET[packet]){
	$packet=$_GET[packet];
}else{
	$packet=$_POST[packet];
}
if ($_POST[submit]){
	$q="SELECT * FROM ps_instructions WHERE packetID='$packet'";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	if ($d[packetID]){
		$q1="UPDATE ps_instructions SET ";
		$i=0;
		while ($i < 6){$i++;
			if ($_POST["customA$i"]){
				if ($list != ''){
					$list .= ", customA$i='".$_POST["customA$i"]."'";
				}else{
					$list .= "customA$i='".$_POST["customA$i"]."'";
				}
			}
			foreach(range('a','e') as $letter){
				$address=$i.$letter;
				if ($_POST["customA$address"]){
					if ($list != ''){
						$list .= ", customA$address='".$_POST["customA$address"]."'";
					}else{
						$list .= "customA$address='".$_POST["customA$address"]."'";
					}
				}
			}
		}
		$q1 .= $list." WHERE packetID='$packet'";
	}else{
		$i=0;
		while ($i < 6){$i++;
			if ($_POST["customA$i"]){
				if ($db == ""){
					$db .= "customA$i";
				}else{
					$db .= ", customA$i";
				}
				if ($formVal == ""){
					$formVal .="'".$_POST["customA$i"]."'";
				}else{
					$formVal .=", '".$_POST["customA$i"]."'";
				}
			}
			foreach(range('a','e') as $letter){
				$address=$i.$letter;
				if ($_POST["customA$address"] != ''){
					if ($db == ""){
						$db .= "customA$address";
					}else{
						$db .= ", customA$address";
					}
					if ($formVal == ""){
						$formVal .="'".$_POST["customA$address"]."'";
					}else{
						$formVal .=", '".$_POST["customA$address"]."'";
					}
				}
			}
		}
		$q1 .= "INSERT INTO ps_instructions (".$db.", packetID) VALUES (".$formVal.", '$packet')";
	}
	$r1=@mysql_query($q1) or die ("Query: $q1<br>".mysql_error());
	echo "<h1 style='text-align:center;'>SUBMISSION SUCCESSFUL</h1>";
}
$defCount=defCount($packet);
$q="SELECT * FROM ps_packets WHERE packet_id='$packet'";
$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
$d=mysql_fetch_array($r,MYSQL_ASSOC);
$i=0;
//get instruction values for dropdown
$typeA = addslashes(trim(getPage("http://staff.mdwestserve.com/otd/instructGen.php?packet=$packet&def=$def&type=A", 'MDWS Instructions Type A', '5', '')));
$typeB = addslashes(trim(getPage("http://staff.mdwestserve.com/otd/instructGen.php?packet=$packet&def=$def&type=B", 'MDWS Instructions Type B', '5', '')));
$typeC = addslashes(trim(getPage("http://staff.mdwestserve.com/otd/instructGen.php?packet=$packet&def=$def&type=C", 'MDWS Instructions Type C', '5', '')));
while ($i < 6){$i++;
	if ($d["name$i"]){
		if ($d["address$i"]){
			$hide .= "hide(document.getElementById('customA$i'));";
			$show2 .= "show2(document.getElementById('customA$i'));";
			$makePlus .= "makePlus('plus$i');";
			$makeMinus .= "makeMinus('plus$i');";
			$clear .= "document.form1.customA$i.value='';";
		}
		foreach(range('a','e') as $letter){
			$address=$i.$letter;
			if ($d["address$address"] != ''){
				$hide .= "hide(document.getElementById('customA$address'));";
				$show2 .= "show2(document.getElementById('customA$address'));";
				$makePlus .= "makePlus('plus$address');";
				$makeMinus .= "makeMinus('plus$address');";
				$clear .= "document.form1.customA$address.value='';";
			}
		}
	}
}
if ($d["address$i"]){
	$hide .= "hide(document.getElementById('customROW'));";
	$show2 .= "show2(document.getElementById('customROW'));";
	$makePlus .= "makePlus('plusROW1');";
	$makeMinus .= "makeMinus('plusROW1');";
	$clear .= "document.form1.customROW.value='';";
}
foreach(range('a','e') as $letter){
	$address="1".$letter;
	if ($d["address$address"] != ''){
		$hide .= "hide(document.getElementById('customROW$letter'));";
		$show2 .= "show2(document.getElementById('customROW$letter'));";
		$makePlus .= "makePlus('plusROW$address');";
		$makeMinus .= "makeMinus('plusROW$address');";
		$clear .= "document.form1.customROW$letter.value='';";
	}
}
?>
<script src="common.js" type="text/javascript"></script>
<script>
function ChangeText(field){
	if (document.getElementById(field).innerHTML == '+'){
		document.getElementById(field).innerHTML = '-';
	}else{
		document.getElementById(field).innerHTML = '+';
	}
}
function hideshow2(which){
	if (!document.getElementById)
		return
	if (which.style.display=="inline")
		which.style.display="none"
	else
		which.style.display="inline"
}
function show2(which){
	if (!document.getElementById)
		return
		which.style.display="inline"
}
function makeMinus(field){
	document.getElementById(field).innerHTML = '-';
}
function makePlus(field){
	document.getElementById(field).innerHTML = '+';
}
function updateBtn(field){
	var head1 = document.getElementById(field);
	if (head1.value == "Show All"){
		head1.value="Hide All";
		<?=$show2?>
		<?=$makeMinus?>
	}else if(head1.value == "Hide All"){
		head1.value="Show All";
		<?=$hide?>
		<?=$makePlus?>
	}
}
function updateServiceInstructions(selObj,defNum) {
   var txtArea = selObj.form.elements['custom'+defNum];
   txtArea.value = '';

   for (var loop=0; loop<selObj.options.length; loop++) {
      if (selObj.options[loop].selected) {
         txtArea.value += selObj.options[loop].value;
      }
   }
}
</script>
<?
echo "<table border='1' style='border-collapse:collapse; border:solid 1px; font-weight:bold;' class='back'>";
$defList2=defList2($packet);
$totalColumns=$defList2[1]+1;
$defList2=$defList2[0];
echo "<form method='post' action='instructWiz.php' target='preview'>
<input type='hidden' name='packet' value='$packet'>
<input type='hidden' name='bypass' value='1'>
<input type='hidden' name='i' value='4'>
<input type='hidden' name='def' value='ALL'>
<td>
<input type='submit' name='submit' value='Edit File Settings'>
</td>
</form>
<form method='post' name='form1'>
<input type='hidden' name='packet' value='$packet'>".$defList2;
foreach(range('e','a') as $letter){
	if ($d["address1$letter"] != ''){
		$i2=0;
		$clickList='';
		while ($i2 < $defCount){$i2++;
			$address2=$i2.$letter;
			$clickList .= "hideshow2(document.getElementById('customA$address2'));";
		}
		$clickList .= "hideshow2(document.getElementById('customROW$letter'));";
		echo "<tr>
		<td onClick=\"$clickList\">".$d["address1$letter"]."<br>".$d["city1$letter"].", ".$d["state1$letter"]." ".$d["zip1$letter"]."</td>
		".defList($packet,$letter)."</tr>";
	}
}
if ($d[address1] != ''){
	$i2=0;
	$clickList='';
	while ($i2 < $defCount){$i2++;
		$clickList .= "hideshow2(document.getElementById('customA$i2'));";
	}
	$clickList .= "hideshow2(document.getElementById('customROW'));";
	echo "<tr><td onClick=\"$clickList\">";
	echo "$d[address1]<br>$d[city1], $d[state1] $d[zip1]</td>
	".defList($packet,'')."</tr>";
}
echo "<tr>
<td colspan='$totalColumns' align='center'>
<input type='button' value='Clear Fields' onClick=\"$clear\"> | <input type='reset' value='Reset Fields to Original Text'> | <input type='submit' name='submit' value='Submit'> | <input type='button' onClick=\"updateBtn('btn1')\" id='btn1' value='Show All'>
</td>
</tr>
</form>
<tr>
<td colspan='$totalColumns' align='center'>
<iframe name='preview' src='http://service.mdwestserve.com/customInstructions.php?packet=$packet' height='300px' width='100%'></iframe></td></tr></table>";
?>