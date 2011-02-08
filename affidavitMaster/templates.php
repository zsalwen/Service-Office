<?
mysql_connect();
mysql_select_db('service');

function pdfAD($id){
$r=@mysql_query("select LiveAffidavit from ps_packets where packet_id = '$id'");
$d=mysql_fetch_array($r,MYSQL_ASSOC);
$myFile = "$id.html";
$fh = fopen($myFile, 'w') or die("can't open file");
$la=explodePrint(trim($d[LiveAffidavit]));
fwrite($fh, $la);
fclose($fh);
$command = 'python DocumentConverter.py /devbox/Service-Office/affidavitMaster/'.$id.'.html /devbox/Service-Office/affidavitMaster/'.$id.'.pdf';
$error = system($command,$result);
//echo "<div>".$command."</div>";
//echo "<div>".$error."</div>";
//echo "<div>".$result."</div>";
header('Location: '.$id.'.pdf');
}

if($_GET['pdf']){
pdfAD($_GET[id]);
}



if ($_GET[id]){
?>

<div style="background-color:#FFFFFF;">
<?
        if ($_POST[whiteboard]){
            $whiteboard = addslashes($_POST[whiteboard]);
$user = $_COOKIE[psdata][user_id];
            $q = "update template set html='$whiteboard' WHERE id = '$_GET[id]'";
            $r = @mysql_query ($q) or die(mysql_error());
            $saved = 1;
        }
        $q = "SELECT html FROM template WHERE id = '$_GET[id]'";
        $r = @mysql_query ($q) or die(mysql_error());
        $d = mysql_fetch_array($r, MYSQL_ASSOC);
        ?>
<script language="JavaScript" type="text/javascript" src="wysiwyg.js"></script>
<? if ($_GET[edit] && !$saved ){


?>
<form method="post">
<center>
<textarea id="whiteboard" rows="30" cols="100" name="whiteboard"><?=stripslashes($d[html])?></textarea>
<script language="JavaScript">
generate_wysiwyg('whiteboard');
</script> <br>
<input style="font-size:24px; color:#006666;" name="submit" type="submit" value="Save Ad"></center>
</form>
<? }else{?>
<? $mouseover = "onmouseover=\"style.backgroundColor='#FFFF00';\" onmouseout=\"style.backgroundColor='#ffffff'\"";?>
<? $mouseover2 = "onmouseover=\"style.backgroundColor='#FFCC00';\" onmouseout=\"style.backgroundColor='#ffffff'\"";?>
<? $mouseover3 = "onmouseover=\"style.backgroundColor='#FF6600';\" onmouseout=\"style.backgroundColor='#ffffff'\"";?>

<style>
a { text-decoration:none; color:#000; }
</style>
<div align="center">
<center>Document Template Center</center>
<? $r8=mysql_query("select * from template");
while($d8=mysql_fetch_array($r8,MYSQL_ASSOC)){?>
<li><a href="?id=<?=$d8[id]?>"><?=$d8[id]?>: <?=$d8[description]?></a></li>
<? } ?>
</div>
<? } ?>
</div>

<? }else{
echo "missing auction id?";
}




 ?>