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
if ($_POST[note] && $_GET[id]){
$r=@mysql_query("select * from market where marketID = '$_GET[id]'");
$d=mysql_fetch_array($r,MYSQL_ASSOC);
$oldNote = $d[notes];
$newNote = "<li>From ".$_COOKIE[psdata][name]." on ".date('m/d/y g:ia').": \"".$_POST[note]."\"</li>".$oldNote;
@mysql_query("UPDATE market SET notes='".dbIN($newNote)."', coldCall=NOW() WHERE marketID='$_GET[id]'") or die(mysql_error());
			$about = strtoupper($_POST[note]);
			$to = "Marketing Update <service@mdwestserve.com>";
			$subject = "Marketing Update For $d[name]: $about ".$_GET[id];
			$headers  = "MIME-Version: 1.0 \n";
			$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
			$headers .= "From: ".$_COOKIE[psdata][name]." <".$_COOKIE[psdata][email].">  \n";
			$body = "<hr><a href='http://staff.mdwestserve.com/market/details.php?id=$_GET[id]'>View Details Page</a>";
			mail($to,$subject,stripslashes($newNote.$body),$headers);
			error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Entering Marketing Note For ".strtoupper($d[name])." (ID $_GET[id])",3,"/logs/user.log");
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
if ($_GET[id]){ $q="select * from market where marketID='$_GET[id]'"; } 
$r=@mysql_query($q);
$d=mysql_fetch_array($r,MYSQL_ASSOC);
?>
<table><tr><td valign="top"><div style="height:100px; width:100%;">
	<? if($d[notes]){?>
	<div class="note"><?=dbOUT($d[notes]);?></div>
	<? }?>
	</div>
</td><td align="center"style="height:100px; width:200px; background-color:#FF3300;">
	<form method="POST">
		<div><input name="note"></div>
		<div><input type="submit" value="Record Note"></div>
	</form>
</div>
</td></tr></table>
<? 
mysql_close(); ?>