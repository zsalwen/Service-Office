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

/*
if($_GET['doc']){
docAD($_GET[id]);
}
*/
if($_GET['pdf']){
pdfAD($_GET[id]);
}

function printAD($id,$ip){
$r=@mysql_query("select LiveAffidavit from ps_packets where packet_id = '$id'");
$d=mysql_fetch_array($r,MYSQL_ASSOC);
$myFile = "$id.html";
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, $d[LiveAffidavit]);
fclose($fh);
passthru('/usr/local/bin/html2ps '.$id.'.html > '.$id.'.pcl');
$file = $id.'.pcl';
$remote_file = $id.'.pcl';




if ($conn_id = ftp_connect($ip)) {
//echo "Current directory is now: " . ftp_pwd($conn_id) . "\n";
} else {
//echo "Couldn't change directory\n";
mail('insidenothing@gmail.com','Affidavit Master: PRINTER FAILED TO CONNECT','Couldn\'t connect');
error_log(date('r')." Affidavit Master: $ip WARNING: Couldn't connect. \n", 3, '/logs/printer.log');
return 'fail';
}





$login_result = ftp_login($conn_id, 'alpha', 'beta');





ftp_pasv($conn_id, true);
if (ftp_chdir($conn_id, "PORT1")) {
//echo "Current directory is now: " . ftp_pwd($conn_id) . "\n";
} else {
//echo "Couldn't change directory\n";
mail('insidenothing@gmail.com','Affidavit Master: PRINTER CHDIR','Couldn\'t change directory');
error_log(date('r')." Affidavit Master: $ip WARNING: Couldn't change ftp directory - $id. \n", 3, '/logs/printer.log');
}
if (ftp_put($conn_id, $remote_file, $file, FTP_BINARY)) {
//echo "successfully uploaded $file\n";
$last_line = system('rm -f '.$id.'.pcl', $retval);
$last_line = system('rm -f '.$id.'.rtf', $retval);
$last_line = system('rm -f '.$id.'.html', $retval);
error_log(date('r')." $ip NOTICE: Burson edited ad $id printed successfully. \n", 3, '/logs/printer.log');
} else {
//echo "There was a problem while uploading $file\n";
mail('insidenothing@gmail.com','Affidavit Master: AI Break: FTP PUT','There was a problem while uploading '.$file);
error_log(date('r')." Affidavit Master: $ip ERROR: There was a problem while uploading - $id. \n", 3, '/logs/printer.log');
return 'fail';
}
ftp_close($conn_id);
}

if ($_GET[id]){
?>

<div style="background-color:#FFFFFF;">
<?
        if ($_POST[whiteboard]){
            $whiteboard = addslashes($_POST[whiteboard]);
$user = $_COOKIE[psdata][user_id];
            $q = "update ps_packets set LiveAffidavit='$whiteboard' WHERE packet_id = '$_GET[id]'";
            $r = @mysql_query ($q) or die(mysql_error());
            $saved = 1;
        }
        $q = "SELECT LiveAffidavit, attorneys_id FROM ps_packets WHERE packet_id = '$_GET[id]'";
        $r = @mysql_query ($q) or die(mysql_error());
        $d = mysql_fetch_array($r, MYSQL_ASSOC);
        ?>
<script language="JavaScript" type="text/javascript" src="wysiwyg.js"></script>
<? if ($_GET[edit] && !$saved ){
//addNote($_GET[id],"DPTC: edit ad");
?>
<form method="post">
<center>
<textarea id="whiteboard" rows="30" cols="100" name="whiteboard"><?=stripslashes($d[LiveAffidavit])?></textarea>
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
<li><?=$d8[id]?>: <?=$d8[description]?></li>
<? } ?>
</div>
<? } ?>
</div>

<? }else{
echo "missing auction id?";
}




 ?>