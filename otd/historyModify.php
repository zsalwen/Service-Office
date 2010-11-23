<?
include 'common.php';
include 'lock.php';

function stripTime($history){
	$q="SELECT wizard, action_str from ps_history WHERE history_id='$history'";
	$r=@mysql_query($q) or die(mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	if ($d[wizard] == 'MAILING DETAILS'){
		$dateTime=explode(".</LI>",$d[action_str]);
		$dateTime=explode("BY FIRST CLASS MAIL ON ",$dateTime[0]);
		$dateTime=$dateTime[1];
	}elseif($d[wizard] == 'BORROWER' || $d[wizard] == 'NOT BORROWER'){
		$dateTime=explode("DATE OF SERVICE: ",$d[action_str]);
		$dateTime=$dateTime[1];
	}else{
		$dateTime=explode("</LI>",$d[action_str]);
		$dateTime=explode("<BR>",$dateTime[1]);
		$dateTime=$dateTime[0];
	}
	return $dateTime;
}
function monthConvert($month){
	if ($month == '01'){ return 'January'; }
	if ($month == '02'){ return 'February'; }
	if ($month == '03'){ return 'March'; }
	if ($month == '04'){ return 'April'; }
	if ($month == '05'){ return 'May'; }
	if ($month == '06'){ return 'June'; }
	if ($month == '07'){ return 'July'; }
	if ($month == '08'){ return 'August'; }
	if ($month == '09'){ return 'September'; }
	if ($month == '10'){ return 'October'; }
	if ($month == '11'){ return 'November'; }
	if ($month == '12'){ return 'December'; }
}
function month2num($month){
	if (strtoupper($month) == 'JANUARY' || $month == 1){
		return '1';
	}elseif (strtoupper($month) == 'FEBRUARY' || $month == 2){
		return '2';
	}elseif (strtoupper($month) == 'MARCH' || $month == 3){
		return '3';
	}elseif (strtoupper($month) == 'APRIL' || $month == 4){
		return '4';
	}elseif (strtoupper($month) == 'MAY' || $month == 5){
		return '5'; 
	}elseif (strtoupper($month) == 'JUNE' || $month == 6){
		return '6';
	}elseif (strtoupper($month) == 'JULY' || $month == 7){
		return '7';
	}elseif (strtoupper($month) == 'AUGUST' || $month == 8){
		return '8';
	}elseif (strtoupper($month) == 'SEPTEMBER' || $month == 9){
		return '9';
	}elseif (strtoupper($month) == 'OCTOBER' || $month == 10){
		return '10';
	}elseif (strtoupper($month) == 'NOVEMBER' || $month == 11){
		return '11';
	}elseif (strtoupper($month) == 'DECEMBER' || $month == 12){
		return '12'; 
	}else{
		return $month;
	}
}
function dateImplode($date){
	$str=explode(' AT ',$date);
	$time=$str[1];
	$time=explode(' ',$time);
	if ($time[1] == 'PM'){
		$time=makePM($time[0].":00");
	}else{
		$time=$time[0].":00";
	}
	$date2=explode(' ',$str[0]);
	$month=month2num(trim($date2[0]));
	$day=str_replace(',','',$date2[1]);
	$year=$date2[2];
	return $year.'-'.addZero($month).'-'.addZero($day)." $time";
}
function postDateImplode($date){
	$str=explode(' ',$date);
	if ($str[4] == 'PM'){
		$time=makePM($str[3].":00");
	}else{
		$time=$str[3].":00";
	}
	$month=month2num(trim($str[0]));
	$day=str_replace(',','',$str[1]);
	$year=$str[2];
	return $year.'-'.addZero($month).'-'.addZero($day)." $time";
}

function dateExplode($date){
	$date=explode('-',$date);
	$date=monthConvert($date[1])." ".$date[2].", ".$date[0];
	return $date;
}
function getActionDate($histID,$str){
	$q="SELECT wizard from ps_history WHERE history_id='$histID'";
	$r=@mysql_query($q) or die(mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	if ($d[wizard] == 'MAILING DETAILS'){
		$dateTime=explode(".</LI>",$str);
		$dateTime=explode("BY FIRST CLASS MAIL ON ",$dateTime[0]);
		$dateTime=$dateTime[1];
	}elseif($d[wizard] == 'BORROWER' || $d[wizard] == 'NOT BORROWER'){
		$dateTime=explode("DATE OF SERVICE: ",$str);
		$dateTime=$dateTime[1];
	}else{
		$dateTime=explode("</LI>",$str);
		$dateTime=explode("<BR>",$dateTime[1]);
		$dateTime=$dateTime[0];
	}
	$dt=trim($dateTime);
	if (strpos($dt,"AT") !== false){
		//dt with "AT" in middle, explode by " AT "
		return dateImplode($dt);
	}elseif(strpos($dt,":") !== false){
		//dt with " " in middle, explode by " "
		return postDateImplode($dt);
	}else{
		//just date
		return dateExplode($dt);
	}
}
if ($_POST[packet]){
	$packet=$_POST[packet];
}else{
	$packet=$_GET[packet];
}
if ($_GET[def]){
	$getDef="&def=".$_GET[def];
	$getDef2="<input type='hidden' name='def' value='$_GET[def]'>";
}elseif($_POST[def]){
	$getDef="&def=".$_POSTT[def];
	$getDef2 .= "<input type='hidden' name='def' value='$_POST[def]'>";
}
if ($_GET[form]){
	$getDef .= "&form=".$_GET[form];
	$getDef2 .= "<input type='hidden' name='form' value='$_GET[form]'>";
}elseif($_POST[form]){
	$getDef .= "&form=".$_POST[form];
	$getDef2 .= "<input type='hidden' name='form' value='$_POST[form]'>";
}

if ($_POST[submit]){
	$i=0;
	$i2=0;
	while ($i <= $_POST[count]){$i++;
		if ($_POST["update$i"] == 1 && $_POST["delete$i"] != 'checked'){
			$dt=getActionDate($_POST["history_id$i"],$_POST["action_str$i"]);
			echo "<script>alert('History ID: ".$_POST["history_id$i"]." | NEW actionDate: $dt | OLD actionDate: ".$_POST["actionDate$i"]."')</script>";
			$q="UPDATE ps_history SET action_str='".addslashes($_POST["action_str$i"])."', serverID='".$_POST["serverID$i"]."', address='".$_POST["address$i"]."', resident='".$_POST["resident$i"]."', residentDesc='".addslashes($_POST["residentDesc$i"])."', onAffidavit='".$_POST["onAffidavit$i"]."' WHERE history_id='".$_POST["history_id$i"]."'";
			$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
			echo "<center style='background-color:#FFFFFF; font-size:20px;'>Entry ".$_POST["history_id$i"]." Modified</center>";
		}elseif($_POST["delete$i"] == 'checked'){$i2++;
			$q2="SELECT * from ps_history where history_id='".$_POST["history_id$i"]."'";
			$r2=@mysql_query($q2) or die("Query: $q2<br>".mysql_error());
			$d2=mysql_fetch_array($r2,MYSQL_ASSOC);
			$list .= "<tr><td style='border: 1px solid black;'>$d2[wizard]<br>".stripslashes($d2[action_str])."<br>Address: $d2[address]<br>Server: ".id2name($d2[serverID]);
			if($d2[resident]){ $list .= "<br>Resident Name: $d2[resident]"; }
			if($d2[residentDesc]){ $list .= "<br>Resident Description:<br>".stripslashes($d2[residentDesc]); }
			$list .= "</td></tr>"; 
			$list2 .= "<input type='hidden' name='history_id$i2' value='".$_POST["history_id$i"]."'>";
		}
	}
	if ($i2 > 0){
		if ($i2 > 1){
			$msg="Are you <i>SURE</i> you want to delete these entries?";
		}else{
			$msg="Are you <i>SURE</i> you want to delete this entry?";
		}
		echo "<table align='center' style='background-color:FFFFFF;'>
			<tr><td align='center'>$msg</td></tr>$list
			<tr><td align='center'><form method='post' name='form2' style='display:inline;'><input type='hidden' name='packet' value='$packet'>$list2<input type='hidden' name='entryCount' value='$i2'>$getDef2<input style='background-color:green;' type='submit' name='confirm' value='YES'></form> | <form action='http://staff.mdwestserve.com/otd/historyModify.php' name='form3' style='display:inline;'><input type='hidden' name='packet' value='$packet'>$getDef2<input style='background-color:red;' type='submit' name='restart' value='NO'></form></td>
				</tr>
			</table>";
	}
}

if ($_GET[effort]){
	$q2="SELECT * from ps_history where history_id='$_GET[effort]'";
	$r2=@mysql_query($q2) or die("Query: $q2<br>".mysql_error());
	$d2=mysql_fetch_array($r2,MYSQL_ASSOC);
	if ($d2[wizard] == 'FIRST EFFORT'){
		$update='SECOND EFFORT';
		$actionStr=str_replace('FIRST EFFORT',$update,$d2[action_str]);
	}elseif($d2[wizard] == 'SECOND EFFORT'){
		$update='FIRST EFFORT';
		$actionStr=str_replace('SECOND EFFORT',$update,$d2[action_str]);
	}
	$r3=@mysql_query("UPDATE ps_history SET wizard='$update', action_str='$actionStr' WHERE history_id='$_GET[effort]'") or die(mysql_error());
	echo "<center style='background-color:#FFFFFF; font-size:20px;'>Entry ".$_GET[effort]." Modified</center>";
}

if ($_POST[confirm]){
	$i3=0;
	while ($i3 < $_POST[entryCount]){$i3++;
		$q2="DELETE from ps_history where history_id = '".$_POST["history_id$i3"]."'";
		$r2=@mysql_query($q2) or die("Query: $q2<br>".mysql_error());
	}
	if ($_POST[entryCount] > 1){
		$msg="Entries Deleted.";
	}else{
		$msg="Entry Deleted.";
	}
	echo "<table align='center'><tr><td align='center'>$msg</td></tr></table>";
}
//only display table if not attempting to delete items
if ($i2 < 1){
	$q="SELECT packet_id, address1, address1a, server_id, server_ida from ps_packets where packet_id = '$packet'";
	$r=@mysql_query($q) or die(mysql_error());
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	if ($_GET[def]){
		$def=$_GET[def];
		$q1="SELECT * from ps_history where packet_id = '$packet' AND defendant_id='$def' order by history_id ASC";
	}else{
		$q1="SELECT * from ps_history where packet_id = '$packet' order by history_id ASC";
	}
	$r1=@mysql_query($q1) or die("Query: $q1<br>".mysql_error());
	$d2=mysql_num_rows($r1);
	$i=0;
	while ($i < $d2){$i++;
		$hideshow .= "hideshow(document.getElementById('table$i'));";
		$hide .= "hide(document.getElementById('table$i'));";
		$show .= "show(document.getElementById('table$i'));";;
		$changeText .= "ChangeText('plus$i');";
		$makeMinus .= "makeMinus('plus$i');";
		$makePlus .= "makePlus('plus$i');";
	}
	?>
	<script src="common.js" type="text/javascript"></script>
	<script>
	function updateBtn(field){
		var head1 = document.getElementById(field);
		if (head1.value == "Show All"){
			head1.value="Hide All";
			<?=$show?>
			<?=$makeMinus?>
		}else if(head1.value == "Hide All"){
			head1.value="Show All";
			<?=$hide?>
			<?=$makePlus?>
		}
	}
	function ChangeText(field){
		if (document.getElementById(field).innerHTML == '+'){
			document.getElementById(field).innerHTML = '-';
		}else{
			document.getElementById(field).innerHTML = '+';
		}
	}
	function makeMinus(field){
		document.getElementById(field).innerHTML = '-';
	}
	function makePlus(field){
		document.getElementById(field).innerHTML = '+';
	}
	function autoPrintUpdate(){
		document.getElementById('autoPrintSingle').value = '1';
		document.getElementById('autoPrintMailing').value = '1';
		document.getElementById('autoPrintServer').value = '1';
		document.getElementById('autoPrintCase').value = '1';
	}
	function callme(rno){
		var tmp = document.getElementById(rno);
		if (tmp.className == 'div3'){
			tmp.className='div2'
		}else{
			tmp.className='div3'
		}
	}
	</script>
	<style>
	table { padding:0px;}
	body { margin:0px; padding:0px; background-color:#999999}
	input, select { background-color:#CCFFFF; font-variant:small-caps; font-size:12px }
	textarea { background-color:#CCFFFF; font-variant:small-caps; }
	td { font-variant:small-caps;}
	legend {border:solid 1px #FF0000; background-color:#FFFFFF; padding:0px; font-size:13px;}
	.legend{border:solid 1px #FF0000; background-color:#FFFFFF; font-size:13px; width:100% !important; height:30px !important;}
	.span{position:relative; top:7;}
	.div2{background-color:orange;}
	.div3{background-image:none;}
	form {display: inline;}
	i {display: inline;}
	.plus {background-color:#0099FF; border:ridge 2px #33CCFF; text-align:center; width:20px !important; font-weight:bold; font-size:16px; position:relative; left:93%;top:-12;}
	</style>
	<table align="center" width="550px">
		<tr align="center">
			<td align="center">
				<FIELDSET style="background-color:#CCFFCC; padding:0px">
				<legend accesskey="C" style="font-size:12px; font-weight:bold;">History Items for <a href="order.php?packet=<?=$d[packet_id]?>">Packet <?=$d[packet_id]?>:</a></legend>
				<? if ($_GET[form]){ ?>
				<table>
					<tr>
						<form name="auto"><td align="center"><small>autoPrint</small><br><input type="checkbox" name="autoPrint" value="checked" onClick="autoPrintUpdate()"></td></form>
						<td><table>
						<form target="_blank" name="wizard" action="http://service.mdwestserve.com/wizard.php">
							<tr>
								<td>
									<small>Jump: </small>
								</td><td>
									<input name="jump" size="4" />
								</td><td>
									<small>Server: </small>
								</td><td>
									<input name="server" size="2" />
								</td><td align='right'>
									<small>mailDate: </small><input name="mailDate" size="10" value="<?=date('Y-m-d')?>" /> <input type="submit" value="Wizard" />
								</td>
							</tr>
							</form>
							<form target="_blank" action="http://service.mdwestserve.com/obAffidavit.php">
							<input type="hidden" id="autoPrintSingle" name="autoPrint" value="">
							<tr>
								<td>
									<small>Packet: </small>
								</td><td>
									<input name="packet" size="4" value="<?=$packet?>" />
								</td><td>
									<small>Def: </small>
								</td><td>
									<input name="def" size="2" value="1" />
								</td><td align='right'>
									<input type="submit" value="Single Affidavit" />
								</td>
							</tr>
							</form>
							<form target="_blank" action="http://service.mdwestserve.com/obAffidavit.php">
							<input type="hidden" name="mail" value="1">
							<input type="hidden" id="autoPrintMailing" name="autoPrint" value="">
							<tr>
								<td>
									<small>Packet: </small>
								</td><td>
									<input name="packet" size="4" value="<?=$packet?>" />
								</td><td></td><td></td><td align='right'>
									<input type="submit" value="Mailing Affidavits" />
								</td>
							</tr>
							</form>
							<form target="_blank" action="http://service.mdwestserve.com/obAffidavit.php">
							<input type="hidden" name="ps" value="1">
							<input type="hidden" id="autoPrintServer" name="autoPrint" value="">
							<tr>
								<td>
									<small>Packet: </small>
								</td><td>
									<input name="packet" size="4" value="<?=$packet?>" />
								</td><td></td><td></td><td align='right'>
									<input type="submit" value="Server Affidavits" />
								</td>
							</tr>
							</form>
							<form target="_blank" action="http://service.mdwestserve.com/obAffidavit.php">
							<input type="hidden" id="autoPrintCase" name="autoPrint" value="">
							<tr>
								<td>
									<small>Packet: </small>
								</td><td>
									<input name="packet" size="4" value="<?=$packet?>" />
								</td><td></td><td></td><td align='right'>
									<input type="submit" value="Case Affidavits" />
								</td>
							</form>
							</tr>
						</table></td></tr></table>
				<? } ?>
				<form method="post" name="form1">
				<tr><td align="center">
				<FIELDSET  style="background-color:#CCFFCC; padding:0px">
				<input type="submit" name="submit" value="Submit"> | <input type="button" onClick="updateBtn('btn1');updateBtn('btn2');" id="btn1" value="Hide All">
				</FIELDSET>
				</td></tr><tr><td>
	<?
	$i=0;
	while($d1=mysql_fetch_array($r1, MYSQL_ASSOC)){$i++;
	?>
	<FIELDSET style="padding:0px">
	<LEGEND ACCESSKEY=C<? if ($d1[action_type] == 'UNLINKED'){?> style="background-color:#FFCCFF"<? } ?>>
	<div onClick="hideshow(document.getElementById('table<?=$i?>'));ChangeText('plus<?=$i?>');" class="legend">
	<div class="span"><?=$d1[history_id]?>, Def. <?=$d1[defendant_id]?>, by <?=id2name($d1[serverID]);?>: <?=$d1[wizard]?>-<small id="dateTime<?=$i?>"><?=trim(stripTime($d1[history_id]));?></small></div><div class='plus' id='plus<?=$i?>'>-</div></div></LEGEND>
	<div id="table<?=$i?>">
	<table width="100%" align="center">
		<tr>
			<td>
			<input type="hidden" name="history_id<?=$i?>" value="<?=$d1[history_id]?>">
			<input type="hidden" name="actionDate<?=$i?>" value="<?=$d1[actionDate]?>">
			<input type="hidden" name="update<?=$i?>" value="0">
			<textarea name="action_str<?=$i?>" rows="5" cols="63" onKeyDown="this.form.update<?=$i?>.value=1;"><?=stripslashes($d1[action_str]);?></textarea><br>
			<? if($d1[wizard] === 'BORROWER' || $d1[wizard] === 'NOT BORROWER'){ ?>
			Address: <input name="address<?=$i?>" size="70" onfocus="this.form.update<?=$i?>.value=1;" value="<?=$d1[address]?>"><br>
			<? } ?>
			Server ID: <input name="serverID<?=$i?>" size="3" onfocus="this.form.update<?=$i?>.value=1;" value="<?=$d1[serverID]?>"> | <span <? if ($d1[onAffidavit]=='checked'){echo "style='background-color:green;'";}else{echo "style='background-color:red;'";} ?>>onAffidavit: <input <? if ($d1[onAffidavit]=='checked'){echo "checked";} ?> value="checked" type="checkbox" name="onAffidavit<?=$i?>" onfocus="this.form.update<?=$i?>.value=1;"></span><br>
			<? if($d1[resident]){ ?>
			Resident Name: <input name="resident<?=$i?>" size="30" onfocus="this.form.update<?=$i?>.value=1;" value="<?=$d1[resident]?>"><br>
			<? } ?>
			<? if($d1[wizard] === 'BORROWER' || $d1[wizard] === 'NOT BORROWER'){ ?>
			Resident Description:<br>
			<input name="residentDesc<?=$i?>" onfocus="this.form.update<?=$i?>.value=1;" size="100" maxlength="255" value="<?=trim(stripslashes($d1[residentDesc]))?>"><br>
			<? } ?>
			<li><small><div style='display:inline;' id='delDiv<?=$i?>' class='div3'>DELETE ENTRY <input type='checkbox' name='delete<?=$i?>' value='checked' onclick=callme('delDiv<?=$i?>')> <span style='font-size:10px; color:000000;'>(will delete on submit)</span></div>
			<? if($d1[wizard] === 'FIRST EFFORT' || $d1[wizard] === 'SECOND EFFORT'){ ?> | <? } ?>
			<? if($d1[wizard] === 'FIRST EFFORT'){ ?><a href="historyModify.php?packet=<?=$d1[packet_id]?>&effort=<?=$d1[history_id]?><?=$getDef?>">CONVERT 1ST -> 2ND</a>
			<? }elseif($d1[wizard] === 'SECOND EFFORT'){ ?><a href="historyModify.php?packet=<?=$d1[packet_id]?>&effort=<?=$d1[history_id]?><?=$getDef?>">CONVERT 2ND -> 1ST</a><? }?></small></li>
			</td>
		</tr>
	</table>
	</div>
	</FIELDSET>
	<? } ?>
	<input type="hidden" name="count" value="<?=$i?>">
	</td></tr><tr><td align="center">
	<FIELDSET  style="background-color:#CCFFCC; padding:0px">
	<input type="submit" name="submit" value="Submit"> | <input type="button" onClick="updateBtn('btn2');updateBtn('btn1');" id="btn2" value="Hide All"></form>
	</FIELDSET>
	</FIELDSET>
	</td></tr></table>
<? } ?>