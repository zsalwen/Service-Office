<?
function dbIN($str){
$str = trim($str);
$str = addslashes($str);
$str = strtolower($str);
$str = ucwords($str);
return $str;
}
function dbOUT($str){
$str = stripslashes($str);
return $str;
}
mysql_connect();
mysql_select_db('service');
if ($_POST[note] && $_POST[field] && $_GET[packet]){
$r=@mysql_query("select $_POST[field] from ps_packets where packet_id = '$_GET[packet]'");
$d=mysql_fetch_array($r,MYSQL_ASSOC);
$oldNote = $d[$_POST[field]];
$newNote = "<li>From ".$_COOKIE[psdata][name]." on ".date('m/d/y g:ia').": \"".$_POST[note]."\"</li>".$oldNote;
@mysql_query("UPDATE ps_packets SET $_POST[field]='".dbIN($newNote)."' WHERE packet_id='$_GET[packet]'") or die(mysql_error());
			$about = strtoupper($_POST[field]);
			$to = "Service Update <service@mdwestserve.com>";
			$subject = "OTD $about Update: Packet ".$_GET[packet];
			$headers  = "MIME-Version: 1.0 \n";
			$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
			$headers .= "From: ".$_COOKIE[psdata][name]." <".$_COOKIE[psdata][email].">  \n";
			$body = "<hr><a href='http://staff.mdwestserve.com/otd/order.php?packet=$_GET[packet]'>View Order Page</a>";
			mail($to,$subject,stripslashes($newNote.$body),$headers);
}
if ($_POST[note] && $_POST[field] && $_GET[eviction]){
$r=@mysql_query("select $_POST[field] from evictionPackets where eviction_id = '$_GET[eviction]'");
$d=mysql_fetch_array($r,MYSQL_ASSOC);
$oldNote = $d[$_POST[field]];
$newNote = "<li>".date('m/d/y g:ia').": ".dbIN($_POST[note])." by ".$_COOKIE[psdata][name]."</li>".$oldNote;
@mysql_query("UPDATE evictionPackets SET $_POST[field]='".$newNote."' WHERE eviction_id='$_GET[eviction]'") or die(mysql_error());
			$about = strtoupper($_POST[field]);
			$to = "Service Update <service@mdwestserve.com>";
			$subject = "EV $about Update: Eviction ".$_GET[eviction];
			$headers  = "MIME-Version: 1.0 \n";
			$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
			$headers .= "From: ".$_COOKIE[psdata][name]." <".$_COOKIE[psdata][email].">  \n";
			$body = "<hr><a href='http://staff.mdwestserve.com/ev/order.php?packet=$_GET[eviction]'>View Order Page</a>";
			mail($to,$subject,stripslashes($newNote.$body),$headers); 
}
if ($_POST[note] && $_POST[field] && $_GET[standard]){
$r=@mysql_query("select $_POST[field] from standard_packets where packet_id = '$_GET[standard]'");
$d=mysql_fetch_array($r,MYSQL_ASSOC);
$oldNote = $d[$_POST[field]];
$newNote = "<li>".date('m/d/y g:ia').": ".dbIN($_POST[note])." by ".$_COOKIE[psdata][name]."</li>".$oldNote;
@mysql_query("UPDATE standard_packets SET $_POST[field]='".$newNote."' WHERE packet_id='$_GET[standard]'") or die(mysql_error());
			$about = strtoupper($_POST[field]);
			$to = "Service Update <service@mdwestserve.com>";
			$subject = "S $about Update: Standard ".$_GET[standard];
			$headers  = "MIME-Version: 1.0 \n";
			$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
			$headers .= "From: ".$_COOKIE[psdata][name]." <".$_COOKIE[psdata][email].">  \n";
			$body = "<hr><a href='http://staff.mdwestserve.com/standard/order.php?packet=$_GET[standard]'>View Order Page</a>";
			mail($to,$subject,stripslashes($newNote.$body),$headers); 
}
?>
<style>
body { margin:0px; padding:0px; }
table { height:100%; width:100%;  margin:0px; padding:0px;}
.note{ background-color:#cccccc;  margin:0px; padding:0px; font-size:12px; } 
.title { background-color:#99ff33;  margin:0px; padding:0px; font-size:10px;  }
form { margin:0px; padding:0px; }
</style>
<? 
if ($_GET[packet]){ $type = "Presale Service"; $q="select * from ps_packets where packet_id='$_GET[packet]'"; } 
if ($_GET[eviction]){ $type = "Eviction Service"; $q="select * from evictionPackets where eviction_id = '$_GET[eviction]'"; } 
if ($_GET[standard]){ $type = "Standard Service"; $q="select * from standard_packets where packet_id = '$_GET[standard]'"; } 
if ($type){
$r=@mysql_query($q);
$d=mysql_fetch_array($r,MYSQL_ASSOC);
?>
<table><tr><td valign="top"><div style="height:100px; width:100%;">
	<? if($d[processor_notes]){?>
	<div class="title">Note to Operations</div>
	<div class="note"><?=dbOUT($d[processor_notes]);?></div>
	<? }?>
	<? if ($d[server_notes]){ ?>
	<div class="title">Server Notes</div>
	<div class="note"><?=dbOUT($d[server_notes]);?></div>
	<? }?>
	<? if ($d[vacantDescription]){ ?>
	<div class="title">Note about Vacancy</div>
	<div class="note"><?=dbOUT($d[vacantDescription]);?></div>
	<? }?>
	<? if ($d[extended_notes]){ ?>
	<div class="title">Note to Client</div>
	<div class="note"><?=dbOUT($d[extended_notes]);?></div>
	<? } ?>
	<? if ($d[prepAlert]){ ?>
	<div class="title">Prep Alert</div>
	<div class="note"><?=dbOUT($d[prepAlert]);?></div>
	<? } ?>
	</div>
</td><td align="center"style="height:100px; width:200px; background-color:#FF3300;">
	<form method="POST">
		<div><input name="note"></div>
		<div>
			<select name="field">
				<option value="processor_notes">Internal <?=$type?></option>
				<option value="server_notes">Server Alert</option>
				<option value="prepAlert">Prep Alert</option>
				<option value="extended_notes">Client Alert</option>
				<option value="vacantDescription">Note about Vacancy</option>
			</select>
		</div>
		<div><input type="submit" value="Record Note"></div>
	</form>
</div>
</td></tr></table>
<? 
}else{
echo "missing packet or eviction id";
}
mysql_close(); ?>