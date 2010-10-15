<?
include 'common.php';
include 'lock.php';

function stripTime($history){
	$q="SELECT wizard, action_str from evictionHistory WHERE history_id='$history'";
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

if ($_POST[submit]){
	$i=0;
	while ($i <= $_POST[count]){$i++;
		if ($_POST["update$i"] == 1){
			$q="UPDATE evictionHistory SET action_str='".addslashes($_POST["action_str$i"])."', serverID='".$_POST["serverID$i"]."', address='".$_POST["address$i"]."', resident='".$_POST["resident$i"]."', residentDesc='".addslashes($_POST["residentDesc$i"])."' WHERE history_id='".$_POST["history_id$i"]."'";
			$r=@mysql_query($q);
			echo "<center style='background-color:#FFFFFF; font-size:20px;'>Entry ".$_POST["history_id$i"]." Modified</center>";
		}
	}
}

if ($_GET[delete]){
$q2="SELECT * from evictionHistory where history_id='$_GET[delete]'";
$r2=@mysql_query($q2) or die("Query: $q2<br>".mysql_error());
$d2=mysql_fetch_array($r2,MYSQL_ASSOC);
?>
<table align="center">
	<tr>
		<td align="center">Are you <i>SURE</i> you want to delete this entry?</td>
	</tr>
        <td>
		<?=$d2[wizard]?><br>
		<?=stripslashes($d2[action_str]);?><br>
		Address: <?=$d2[address]?><br>
		Server: <?=id2name($d2[serverID]);?><br>
		<? if($d2[resident]){ ?>
		Resident Name: <?=$d2[resident]?><br>
		<? } ?>
		<? if($d2[residentDesc]){ ?>
		Resident Description:<br>
		<?=stripslashes($d2[residentDesc])?>
		<? } ?>
        </td>
	</tr>
	<tr>
		<td align="center"><a href="historyModify.php?packet=<?=$_GET[packet]?>&confirm=<?=$_GET[delete]?>">YES</a> | <a href="historyModify.php?packet=<?=$_GET[packet]?>">NO</a></td>
	</tr>
</table>
<? }

if ($_GET[confirm]){
	$q2="DELETE from evictionHistory where history_id = '$_GET[confirm]'";
	$r2=@mysql_query($q2) or die("Query: $q2<br>".mysql_error());
	?>
<table align="center">
	<tr>
		<td align="center">Entry Deleted.</td>
	</tr>
	<tr>
		<td align="center"><a href="historyModify.php?packet=<?=$_GET[packet]?>">Return to Entries</a></td>
	</tr>
</table>
	<? }

$packet=$_GET['packet'];
$q="SELECT eviction_id, address1, address1a, server_id, server_ida from evictionPackets where eviction_id = '$packet'";
$r=@mysql_query($q) or die(mysql_error());
$d=mysql_fetch_array($r, MYSQL_ASSOC);
if ($_GET[def]){
	$def=$_GET[def];
	$q1="SELECT * from evictionHistory where eviction_id = '$packet' AND defendant_id='$def' order by history_id ASC";
}else{
	$q1="SELECT * from evictionHistory where eviction_id = '$packet' order by history_id ASC";
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
i {display: inline;}
.plus {background-color:#0099FF; border:ridge 2px #33CCFF; text-align:center; width:20px !important; font-weight:bold; font-size:16px; position:relative; left:93%;top:-12;}
</style>
<table align="center" width="550px">
	<tr align="center">
    	<td align="center">
	        <FIELDSET style="background-color:#CCFFCC; padding:0px">
			<legend accesskey="C" style="font-size:12px; font-weight:bold;">History Items for <a href="order.php?packet=<?=$d[eviction_id]?>">Packet <?=$d[eviction_id]?>:</a></legend>
			<? if ($_GET[form]){ ?>
			<form target="_blank" action="wizard.php">
			<div align="left">
			<small>Jump: </small><input name="jump" size="2" /> <small>Server: </small><input name="server" size="2" /><BR>
			<small>mailDate: </small><input name="mailDate" size="10" value="<?=date('Y-m-d')?>" /> <input type="submit" value="Wizard" />
			</div>
			</form><form target="_self" action="miniwizard.php">
			<div align="left">
			<small>Jump: </small><input name="jump" size="2" /> <small>Server: </small><input name="server" size="2" /> <input type="submit" value="MiniWizard" />
			</div>
			</form>
			<form target="_blank" action="obAffidavit.php">
			<div align="left">
			<small>Packet: </small><input name="packet" size="2" value="<?=$packet?>" /> <small>Def: </small><input name="def" size="2" value="1" /> <input type="submit" value="Single Affidavit" />
			</div>
			</form>
			<form target="_blank" action="obAffidavit.php">
			<div align="left">
			<input type="hidden" name="mail" value="1">
			<small>Packet: </small><input name="packet" size="2" value="<?=$packet?>" /> <input type="submit" value="Mailing Affidavits" />
			</div>
			</form>
			<form target="_blank" action="obAffidavit.php">
			<div align="left">
			<small>Packet: </small><input name="packet" size="2" value="<?=$packet?>" /> <input type="submit" value="Case Affidavits" />
			</div>
			</form>
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
<table width="100%" align="center">
<div id="table<?=$i?>">
	<tr>
        <td>
        <input type="hidden" name="history_id<?=$i?>" value="<?=$d1[history_id]?>">
		<input type="hidden" name="update<?=$i?>" value="0">
		<textarea name="action_str<?=$i?>" rows="5" cols="63" onKeyDown="this.form.update<?=$i?>.value=1;"><?=stripslashes($d1[action_str]);?></textarea><br>
		<? if($d1[wizard] === 'BORROWER' || $d1[wizard] === 'NOT BORROWER'){ ?>
		Address: <input name="address<?=$i?>" size="70" onfocus="this.form.update<?=$i?>.value=1;" value="<?=$d1[address]?>"><br>
		<? } ?>
		Server ID: <input name="serverID<?=$i?>" size="3" onfocus="this.form.update<?=$i?>.value=1;" value="<?=$d1[serverID]?>"><br>
		<? if($d1[resident]){ ?>
		Resident Name: <input name="resident<?=$i?>" size="30" onfocus="this.form.update<?=$i?>.value=1;" value="<?=$d1[resident]?>"><br>
		<? } ?>
		<? if($d1[wizard] === 'BORROWER' || $d1[wizard] === 'NOT BORROWER'){ ?>
		Resident Description:<br>
		<input name="residentDesc<?=$i?>" onfocus="this.form.update<?=$i?>.value=1;" size="100" maxlength="255" value="<?=trim(stripslashes($d1[residentDesc]))?>"><br>
		<? } ?>
        <li><small><a href="historyModify.php?packet=<?=$d1[eviction_id]?>&delete=<?=$d1[history_id]?>">DELETE ENTRY</a></small></li>
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
