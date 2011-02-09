<?
mysql_connect();
mysql_select_db('service');
include 'common.php';
function dbIN($str){
$str = trim($str);
$str = addslashes($str);
$str = strtolower($str);
$str = ucwords($str);
return $str;
}
function defCheckList($packet){
	$r=@mysql_query("SELECT name1, name2, name3, name4, name5, name6 FROM ps_packets WHERE packet_id='$packet'") or die(mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	$r1=@mysql_query("SELECT serveA1, serveA2, serveA3, serveA4, serveA5, serveA6, allowSubService1, allowSubService2, allowSubService3, allowSubService4, allowSubService5, allowSubService6 FROM ps_instructions WHERE packetID='$packet'") or die(mysql_error());
	$d1=mysql_fetch_array($r1,MYSQL_ASSOC);
	$i=0;
	$count=0;
	while ($i < 6){$i++;
		if ($d["name$i"] != ''){$count++;}
	}
	$i=0;
	$columns=0;
	while ($i < 6){$i++;
		if ($d["name$i"] != ''){
			$serve="";
			$subServe="";
			if ($d1["serveA$i"] == 'checked'){ $serve="checked";}
			if ($d1["allowSubService$i"] == 'checked'){ $subServe="checked";}
			$list .= "<td style='background-color:".row_color($i,"#CCCCCC","#999999")."'><input type='checkbox' name='serveA$i' $serve value='checked'> Serve ".substr($d["name$i"],0,40)."<br><input type='checkbox' name='allowSubService$i' $subServe value='checked'> Allow Sub Service?</td>";
			if ($count != 4){
				if ($i == 3){
					$list .= "</tr><tr>";
				}
				if ($i <= 3 ){
					$columns++;
				}
			}else{
				if ($i == 2){
					$list .= "</tr><tr>";
				}
				if ($i <= 2){
					$columns=2;
				}
			}
			$onclick2 .= "document.matrix.serveA$i.checked='checked'; document.matrix.allowSubService$i.checked='checked'; ";
		}
	}
	return "<table style='padding:0px; border-collapse:collapse;' border='1'><tr>$list</tr><tr><td colspan='$columns' align='right'><input type='button' name='select' value='Select All' onclick=\"$onclick2\"></td></tr></table>";
}
function makeProcessorNote($packet,$note,$name,$email){
	$r=@mysql_query("select processor_notes from ps_packets where packet_id = '$packet'");
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	$oldNote = $d[processor_notes];
	$newNote = "<li>From ".$name." on ".date('m/d/y g:ia').": ".addslashes($note)."</li>".$oldNote;
	@mysql_query("UPDATE ps_packets SET processor_notes='".dbIN($newNote)."' WHERE packet_id='$packet'") or die(mysql_error());
		$about = strtoupper("processor_notes");
		$to = "Service Update <service@mdwestserve.com>";
		$subject = "OTD $about Update: Packet ".$packet;
		$headers  = "MIME-Version: 1.0 \n";
		$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
		$headers .= "From: ".$name." <".$email.">  \n";
		$body = "<hr><a href='http://staff.mdwestserve.com/otd/order.php?packet=$packet'>View Order Page</a>";
	mail($to,$subject,stripslashes($newNote.$body),$headers);
}
$packet=$_GET[packet];
if ($_POST[revert]){
	$q2="DELETE FROM ps_instructions WHERE packetID='$_POST[packet]'";
	@mysql_query($q2) or die ("Query: $q2<br>".mysql_error());
}
$q1="SELECT name1, name2, name3, name4, name5, name6, address1, address1a, address1b, address1c, address1d, address1e, city1, city1a, city1b, city1c, city1d, city1e, state1, state1a, state1b, state1c, state1d, state1e, zip1, zip1a, zip1b, zip1c, zip1d, zip1e, pobox, pocity, postate, pozip, pobox2, pocity2, postate2, pozip2, server_notes, attorneys_id FROM ps_packets WHERE packet_id='$packet'";
$r1=@mysql_query($q1) or die ("Query: $q1<br>".mysql_error());
$d1=mysql_fetch_array($r1,MYSQL_ASSOC);
if ($_POST[submit]){
	$q="SELECT * FROM ps_instructions WHERE packetID='$packet'";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	$i=0;
	if ($d[packetID]){
		$qs = "UPDATE ps_instructions SET packetID='$packet'";
		while ($i < 6){$i++;
			if ($d1["name$i"]){
				if ($i == 1){
					$qs2 .= ", attempts='".$_POST["attempts"]."'";
				}
				$qs2 .= ", customA$i='".$_POST["add$i"]."'";
				$qs2 .= ", serveA$i='".$_POST["serveA$i"]."'";
				$qs2 .= ", allowSubService$i='".$_POST["allowSubService$i"]."'";
				foreach(range('a','e') as $letter){
					if ($d1["address1$letter"]){
						$var = $i.$letter;
						if ($i == 1){
							$qs2 .= ", attempts$letter='".$_POST["attempts$letter"]."'";
						}
						$qs2 .= ", customA$var='".$_POST["add$var"]."'";
					}
				}
			}
		}
		$qs2 .= ", allowPosting='".$_POST[allowPosting]."', postSeparateDay='".$_POST[postSeparateDay]."', photograph='".$_POST[photograph]."', contact='".$_POST[contact]."', useNotes='".$_POST[useNotes]."', envInstruct='".$_POST[envInstruct]."', entryID='".$_COOKIE[psdata][user_id]."', entryDT=NOW()";
		$qs .= $qs2;
		$qs .= " WHERE packetID='$packet'";
		echo "<center><h2>CUSTOM INSTRUCTIONS UPDATED FOR PACKET $packet</h2></center>";
		error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Updating Custom Instructions For OTD$packet \n",3,"/logs/user.log");
		timeline($_GET[packet],$_COOKIE[psdata][name]." Updated Custom Instructions");
		makeProcessorNote($packet,"<B>UPDATED CUSTOM INSTRUCTIONS</B>",$_COOKIE[psdata][name],$_COOKIE[psdata][email]);
	}else{
		$qs="INSERT INTO ps_instructions (packetID";
		while ($i < 6){$i++;
			if ($d1["name$i"]){
				if ($i == 1){
					$fields .= ", attempts";
					$values .= ", '".$_POST["attempts"]."'";
				}
				$fields .= ", customA$i";
				$values .= ", '".$_POST["add$i"]."'";
				$fields .= ", serveA$i";
				$values .= ", '".$_POST["serveA$i"]."'";
				$fields .= ", allowSubService$i";
				$values .= ", '".$_POST["allowSubService$i"]."'";
				foreach(range('a','e') as $letter){
					if ($d1["address1$letter"]){
						$var = $i.$letter;
						if ($i == 1){
							$fields .= ", attempts$letter";
							$values .= ", '".$_POST["attempts$letter"]."'";
						}
						$fields .= ", customA$var";
						$values .= ", '".$_POST["add$var"]."'";
					}
				}
			}
		}
		$qs .= $fields.", allowPosting, postSeparateDay, photograph, contact, useNotes, envInstruct, entryID, entryDT) values ('$packet'".$values.", '".$_POST[allowPosting]."', '".$_POST[postSeparateDay]."', '".$_POST[photograph]."', '".$_POST[contact]."', '".$_POST[useNotes]."', '".$_POST[envInstruct]."', '".$_COOKIE[psdata][user_id]."', NOW())";
		echo "<center><h2>CUSTOM INSTRUCTIONS CREATED FOR PACKET $packet</h2></center>";
		error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Creating Custom Instructions For OTD$packet \n",3,"/logs/user.log");
		timeline($packet,$_COOKIE[psdata][name]." Created Custom Instructions");
		makeProcessorNote($packet,"<B>CREATED CUSTOM INSTRUCTIONS</B>",$_COOKIE[psdata][name],$_COOKIE[psdata][email]);
	}
	@mysql_query($qs) or die("Query: $qs<br>".mysql_error());
	if ($_POST[matrixEntries] == 'checked'){
		echo "<script>window.open('http://service.mdwestserve.com/matrixEntries.php?packet=$packet&autoClose=1&mailDate=$mailDate','Mail Entries')</script>";
	}
}
$q="SELECT * FROM ps_instructions WHERE packetID='$packet'";
$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
$d=mysql_fetch_array($r,MYSQL_ASSOC);
$i=0;
$columns=0;
$header="<table align='center' style='border-collapse:collapse;' border='1'><tr><td style='font-size:16px; font-weight:bold;'>OTD$packet</td>";
while ($i < 6){$i++;
	if ($d1["name$i"]){
		$row .= "<tr><td>".strtoupper($d1["name$i"])."</td>";
		$columns++;
		if ($d1[address1]){
			$onclick .= "document.matrix.add$i.checked='checked';
			";
			if ($i == 1){
				$attempts=$d["attempts"];
				if ($attempts == ''){
					$attempts=2;
				}
				$header .= "<td><small>".strtoupper($d1[address1])."<br>".strtoupper($d1[city1]).", ".strtoupper($d1[state1])." ".strtoupper($d1[zip1])."</small><br><center style='background-color:#CCCCCC;'><input size='1' value='$attempts' name='attempts'> Attempts</center></td>";
				$columns++;
			}
			$row .= "<td><input name='add$i' id='add$i' type='checkbox' value='1'";
			if ($d["customA$i"] == 1){
				$row .= " checked ";
			}
			$row .= "></td>";
		}
		foreach(range('a','e') as $letter){
			if ($d1["address1$letter"]){
				$var=$i.$letter;
				$onclick .= "document.matrix.add$var.checked='checked';
				";
				if ($i == 1){
					$attempts=$d["attempts$letter"];
					if ($attempts == ''){
						$attempts=2;
					}
					$header .= "<td><small>".strtoupper($d1["address1$letter"])."<br>".strtoupper($d1["city1$letter"]).", ".strtoupper($d1["state1$letter"])." ".strtoupper($d1["zip1$letter"])."</small><br><center style='background-color:#CCCCCC;'><input size='1' value='$attempts' name='attempts$letter'> Attempts</center></td>";
					$columns++;
				}
				$row .= "<td><input name='add$i$letter' id='add$i$letter' type='checkbox' value='1'";
				if ($d["customA$var"] == 1){
					$row .= " checked ";
				}
				$row .= "></td>";
			}
		}
		$row .= "</tr>";
	}
}
$header .= "</tr>";
$row .= "</tr></td><tr><td colspan='$columns' align='right'><input type='button' name='select' value='Select All' onclick='selectAll();'></td></tr></table>";
?>
<script>
function selectAll(){
	<?=$onclick?>
}
function selectAll2(){
	<?=$onclick2?>
}
</script>
<style>
form {padding:0px;}
</style>
<form name='matrix' method='post'>
<table align="center" width="870px" style="padding:0px; border-collapse:collapse; border: 1px solid; background-color:#FFFFFF;">
	<tr>
		<td align='center'><?=$header?><?=$row?></td>
	</tr>
	<tr>
		<td align='center'><small><?=defCheckList($packet);?></small></td>
	</tr>
	<tr>
		<td align='center'><table width='700px' style='padding:0px;'><tr>
			<td>Allow Posting? <input type="checkbox" name="allowPosting" <? if ($d[allowPosting] == 'checked'){ echo "checked";} ?> value="checked"></td>
			<td>Photographs: <input name="photograph" value="<?if ($d[photograph]){ echo $d[photograph];}else{ echo "All Attempts & Posting"; }?>" size="18"></td>
			<td>Envelope Stuffing Instructions<select name='envInstruct'><option><?=$d[envInstruct]?></option><option>GREEN</option><option>WHITE</option><option></option></select></td>
			<td>Post on Separate Day? <input type="checkbox" name="postSeparateDay" <? if ($d[postSeparateDay] == 'checked'){ echo "checked";} ?> value="checked"></td>
		</tr></table></td>
	</tr>
	<tr>
		<td align='center'><table width='700px' style='padding:0px;'><tr>
			<td>Contact Info: <textarea rows="2" cols="35" name="contact"><?if ($d["contact"]){ echo $d["contact"];}else{ echo "Office: 410-828-4568";}?></textarea></td>
			<td>Include Server Notes <input type="checkbox" name="useNotes" <? if ($d[useNotes] == 'checked'){ echo "checked";} ?> value="checked"> <div style="padding:0px; border:1px solid;"><? if ($d1[server_notes]){ echo "<small>$d1[server_notes]</small>";}else{ echo "<i>NONE</i>";}?></div></td>
		</tr></table></td>
	</tr>
	<tr>
		<td align="center" style='background-color:#999999; padding:0px;' width='100%'><table align='center'><tr><td align='center' valign='top'><input type="submit" name="submit" value="GO!"></form></td><td align='center' valign='top'>&nbsp;|&nbsp;</td><td align='center' valign='top'><form method='post' name='reset'><input type='hidden' name='packet' value='<?=$packet?>'><input type="submit" name="revert" value="REVERT"></form></td></tr></table></td>
	</tr>
</table>
<?
if ($_POST[submit]){
	echo "<table align='center' style='background-color:#FFFFFF;'><tr><td align='center'>";
	echo "<iframe src='http://service.mdwestserve.com/customInstructions.php?packet=$packet' height='350' width='870' name='preview' id='preview'></iframe>";
	echo "</td></tr></table>";
}
if ($_POST[revert]){
	if ($d1[attorneys_id] == '1'){
		$client='.burson';
	}elseif($d[attorneys_id] == '56'){
		$client='.brennan';
	}elseif($d[attorneys_id] == '70'){
		$client='.bgw';
	}else{
		$client='';
	}
	echo "<table align='center' style='background-color:#FFFFFF;'><tr><td align='center'>";
	echo "<iframe src='http://service.mdwestserve.com/ps_instructions$client.php?packet=$packet' height='350' width='700' name='preview' id='preview'></iframe>";
	echo "</td></tr></table>";
}
?>