<?
include 'common.php';
include 'lock.php';

if ($_POST[history_id]){
	$q="UPDATE evictionHistory SET action_str='".addslashes($_POST[action_str])."', serverID='$_POST[serverID]', address='$_POST[address]', resident='$_POST[resident]', residentDesc='".addslashes($_POST[residentDesc])."' WHERE history_id='$_POST[history_id]'";
	$r=@mysql_query($q);
	echo "<center style='background-color:#FFFFFF;'><h3>Entry Modified</h3></center>";
}

if ($_GET[delete]){
$q2="SELECT * from evictionHistory where history_id='$_GET[delete]'";
$r2=@mysql_query($q2) or die("Query: $q2<br>".mysql_error());
$d2=mysql_fetch_array($r2,MYSQL_ASSOC);
?>
<table>
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
		<td align="center"><a href="evictionHistoryModify.php?id=<?=$_GET[id]?>&confirm=<?=$_GET[delete]?>">YES</a> | <a href="evictionHistoryModify.php?id=<?=$_GET[id]?>">NO</a></td>
	</tr>
</table>
<? }

if ($_GET[confirm]){
	$q2="DELETE from evictionHistory where history_id = '$_GET[confirm]'";
	$r2=@mysql_query($q2) or die("Query: $q2<br>".mysql_error());
	?>
<table>
	<tr>
		<td align="center">Entry Deleted.</td>
	</tr>
	<tr>
		<td align="center"><a href="evictionHistoryModify.php?id=<?=$_GET[id]?>">Return to Entries</a></td>
	</tr>
</table>
	<? }

$id=$_GET['id'];
$q="SELECT eviction_id, address1, server_id from evictionPackets where eviction_id = '$id'";
$r=@mysql_query($q) or die(mysql_error());
$d=mysql_fetch_array($r, MYSQL_ASSOC);
?>
<style>
table { padding:0px;}
body { margin:0px; padding:0px; background-color:#999999}
input, select { background-color:#CCFFFF; font-variant:small-caps; font-size:12px }
textarea { background-color:#CCFFFF; font-variant:small-caps; }
td { font-variant:small-caps;}
legend {border:solid 1px #FF0000; background-color:#FFFFFF; padding:0px; font-size:13px}
</style>
<table width="100%" align="left">
	<tr align="center">
    	<td align="center">
	        <FIELDSET style="background-color:#CCFFCC; padding:0px">
			<legend accesskey="C" align="center" style="font-size:12px; font-weight:bold;">History Items for <a href="order.php?packet=<?=$d[eviction_id]?>">Eviction <?=$d[eviction_id]?>:</a> <?=$d[address1]?> (Server: <?=id2name($d[server_id]);?>)</legend>
<?
$q1="SELECT * from evictionHistory where eviction_id = '$id' order by history_id ASC";
$r1=@mysql_query($q1) or die("Query: $q1<br>".mysql_error());
while($d1=mysql_fetch_array($r1, MYSQL_ASSOC)){
?>
<FIELDSET style="padding:0px">
<LEGEND ACCESSKEY=C <? if ($d1[action_type] == 'UNLINKED'){?> style="background-color:#FFCCFF" <? } ?>>History Item <?=$d1[history_id]?>, Defendant <?=$d1[defendant_id]?>, by <?=id2name($d1[serverID]);?>: <?=$d1[action_type]?></LEGEND>
<table width="100%" align="left">
	<tr>
        <td>
        <form name="<?=$d1[history_id]?>" method="post">
        <input type="hidden" name="history_id" value="<?=$d1[history_id]?>">
		<small><b><?=$d1[wizard]?></b></small><br>
		<textarea name="action_str" rows="5" cols="50"><?=stripslashes($d1[action_str]);?></textarea><br>
		Address: <input name="address" size="40" value="<?=$d1[address]?>"><br>
		Server ID: <input name="serverID" size="3" value="<?=$d1[serverID]?>"><br>
		<? if($d1[resident]){ ?>
		Resident Name: <input name="resident" size="30" value="<?=$d1[resident]?>"><br>
		<? } ?>
		<? if($d1[residentDesc]){ ?>
		Resident Description:<br>
		<input name="residentDesc" size="60" maxlength="255" value="<?=trim(stripslashes($d1[residentDesc]))?>"><br>
		<? } ?>
        <li><small><a href="evictionHistoryModify.php?id=<?=$d1[eviction_id]?>&delete=<?=$d1[history_id]?>">DELETE ENTRY</a></small></li>
        <input type="submit" value="Submit">
        </form></td>

    </tr>
</table>
</FIELDSET>
<? } ?>
</FIELDSET>
</td></tr></table>
<? mysql_close();?>