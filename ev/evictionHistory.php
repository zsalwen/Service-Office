<?
include 'common.php';
include 'lock.php';
$id=$_GET['id'];
$q="SELECT * from evictionPackets where eviction_id = '$id'";
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
	        <FIELDSET style="background-color:#CCCCCC; padding:0px">
			<legend accesskey="C" align="center" style="font-size:12px; font-weight:bold;">History Items for <a href="order.php?packet=<?=$d[eviction_id]?>">Eviction <?=$d[eviction_id]?>:</a> <?=$d[address1]?> <?=$d[address1a]?> (Servers: <?=id2name($d[server_id]);?><? if ($d[server_ida]){echo ", ".id2name($d[server_ida]);}?>)</legend>
<form target="_blank" action="http://service.mdwestserve.com/ev_wizard.php">
<div align="left">
<small>Jump: </small><input name="jump" size="2" /> <small>Server: </small><input name="server" size="2" /><BR>
<small>mailDate: </small><input name="mailDate" size="10" value="<?=date('Y-m-d')?>" /> <input type="submit" value="Wizard" />
</div>
</form>
<form target="_blank" action="http://service.mdwestserve.com/evictionAff.php">
<div align="left">
<small>ID: </small><input name="id" size="2" value="<?=$id?>" /> <small>Def: </small><input name="def" size="2" value="1" /> <input type="submit" value="Single Affidavit" />
</div>
</form>
<form target="_blank" action="http://service.mdwestserve.com/evictionAff.php">
<div align="left">
<input type="hidden" name="mail" value="1">
<small>ID: </small><input name="id" size="2" value="<?=$id?>" /> <input type="submit" value="Mailing Affidavits" />
</div>
</form>
<form target="_blank" action="http://service.mdwestserve.com/evictionAff.php">
<div align="left">
<small>ID: </small><input name="id" size="2" value="<?=$id?>" /> <input type="submit" value="Case Affidavits" />
</div>
</form>
<?
$q1="SELECT * from evictionHistory where eviction_id = '$id' order by history_id ASC";
$r1=@mysql_query($q1) or die("Query: $q1<br>".mysql_error());
while($d1=mysql_fetch_array($r1, MYSQL_ASSOC)){
?>
<FIELDSET style="padding:0px">
<LEGEND ACCESSKEY=C <? if ($d1[action_type] == 'UNLINKED'){?> style="background-color:#FFCCFF" <? } ?>>History Item <?=$d1[history_id]?>, Defendant <?=$d1[defendant_id]?>, by <?=id2name($d1[serverID]);?>: <?=$d1[action_type]?></LEGEND>
<table width="100%" align="left">
	<tr>
		<td valign="top" align="left" width="40%"><small><?=$d1[action_str]?><? if ($d1[residentDesc]){ echo"<br /><b>D:</b> ".$d1[residentDesc];}?></small></td>
    </tr>
</table>
</FIELDSET>
<? } ?>
</FIELDSET>
</td></tr></table>