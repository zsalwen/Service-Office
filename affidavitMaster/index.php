<?
mysql_connect();
mysql_select_db('core');
/*
function addNote($id,$note){
$q1 = "SELECT notes FROM schedule_items WHERE schedule_id = '$id'";
$r1 = @mysql_query ($q1) or die(mysql_error());
$d1 = mysql_fetch_array($r1, MYSQL_ASSOC);
$notes = "<li>".date('g:iA n/j/y').' '.$_COOKIE[psdata][name].': '.$note."</li>".$d1[notes];
if ($_COOKIE[psdata][name]){
$user = $_COOKIE[psdata][name];
}else{
$user = 'Server A.I.';
}
error_log("[".date('g:iA n/j/y').'] ['.$user.'] ['.$id.'] ['.$note."]\n", 3, '/logs/notes.log');
$notes = addslashes($notes);
$q1 = "UPDATE schedule_items set notes='$notes' WHERE schedule_id = '$id'";
$r1 = @mysql_query ($q1) or die(mysql_error());
}
function docAD($id){
$r=@mysql_query("select LiveAdHTML from schedule_items where schedule_id = '$id'");
$d=mysql_fetch_array($r,MYSQL_ASSOC);
$myFile = "$id.html";
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, $d[LiveAdHTML]);
fclose($fh);
$error = system('python DocumentConverter.py /gitbox/Service-Office/'.$id.'.html /gitbox/Service-Office/'.$id.'.doc',$result);
header('Location: '.$id.'.doc');
}
*/
function pdfAD($id){
$r=@mysql_query("select LiveAffidavit from ps_packets where packet_id = '$id'");
$d=mysql_fetch_array($r,MYSQL_ASSOC);
$myFile = "$id.html";
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, trim($d[LiveAffidavit]));
fclose($fh);
$command = 'python DocumentConverter.py /gitbox/Service-Office/affidavitMaster/'.$id.'.html /gitbox/Service-Office/affidavitMaster/'.$id.'.pdf';
$error = system($command,$result);
//echo "<div>".$command."</div>";
//echo "<div>".$error."</div>";
//echo "<div>".$result."</div>";
header('Location: '.$id.'.pdf');
}
function pdfAD2($id){
$r=@mysql_query("select LiveAffidavit from ps_packets where packet_id = '$id'");
$d=mysql_fetch_array($r,MYSQL_ASSOC);
$myFile = "$id.html";
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, trim($d[LiveAffidavit]));
fclose($fh);
$command1='/usr/local/bin/html2ps '.$id.'.html > '.$id.'.pcl';
$error1=passthru($command1,$result1);
$command2='/usr/local/bin/html2ps '.$id.'.pcl > '.$id.'.html';
$error2=passthru($command2,$result2);
$command3 = 'python DocumentConverter.py /gitbox/Service-Office/affidavitMaster/'.$id.'.html /gitbox/Service-Office/affidavitMaster/'.$id.'.pdf';
$error3 = system($command3,$result3);
echo "<div>COMMAND1: [".$command1."]</div>";
echo "<div>ERROR1: [".$error1."]</div>";
echo "<div>RESULT1: [".$result1."]</div>";
echo "<div>COMMAND2: [".$command2."]</div>";
echo "<div>ERROR2: [".$error2."]</div>";
echo "<div>RESULT2: [".$result2."]</div>";
echo "<div>COMMAND3: [".$command3."]</div>";
echo "<div>ERROR3: [".$error3."]</div>";
echo "<div>RESULT3: [".$result3."]</div>";
echo "<script>window.open('$id.pdf', '$_GET[id] PDF')</script>";
//header('Location: '.$id.'.pdf');
}
/*
if($_GET['doc']){
docAD($_GET[id]);
}
*/
if($_GET['pdf']){
pdfAD($_GET[id]);
}
if($_GET['pdf2']){
pdfAD2($_GET[id]);
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
<center>Document Processing and Transmission Center</center>
<table height="30px" width="100%" border="1" cellpadding="0" cellspacing="0">
<tr>
<td <?=$mouseover;?> valign="center" align="center"><a href="?id=<?=$_GET[id];?>&edit=Edit Ad">Edit</a></td>
<td <?=$mouseover2;?> valign="center" align="center"><a href="?id=<?=$_GET[id];?>&print=1">Autoprint</a></td>
<td <?=$mouseover2;?> valign="center" align="center"><a href="loader.php?id=<?=$_GET[id];?>">Reload From History Items</a></td>
<!--
<td <?=$mouseover2;?> valign="center" align="center"><a href="?id=<?=$_GET[id];?>&doc=1" target="_Blank">Open .doc</a></td>
-->
<td <?=$mouseover2;?> valign="center" align="center"><a href="?id=<?=$_GET[id];?>&pdf=1" target="_Blank">Open .pdf</a></td>
<td <?=$mouseover2;?> valign="center" align="center"><a href="?id=<?=$_GET[id];?>&pdf2=1">Open PCL->.pdf</a></td>
<!--
<td <?=$mouseover3;?> valign="center" align="center"><a href="bursonSendToPublisher.php?auction=<?=$_GET[id];?>" target="_Blank">Send To Paper</a></td>
<td <?=$mouseover3;?> valign="center" align="center"><a href="SendToClient.php?auction=<?=$_GET[id];?>" target="_Blank">Send To Client</a></td>
<td <?=$mouseover3;?> valign="center" align="center"><a href="bursonSendCorrectToPublisher.php?auction=<?=$_GET[id];?>" target="_Blank">Send Correction To Paper</a></td>
<td <?=$mouseover3;?> valign="center" align="center"><a href="SendCorrectToClient.php?auction=<?=$_GET[id];?>" target="_Blank">Send Correction To Client</a></td>
-->
</tr>
</table>
<?
/*
$q = "SELECT dept_email FROM papers WHERE paper_name = '$d[paper]'";
$r = @mysql_query ($q) or die(mysql_error());
$paper = mysql_fetch_array($r, MYSQL_ASSOC);
$q = "SELECT sendAdTo FROM attorneys WHERE attorneys_id = '$d[attorneys_id]'";
$r = @mysql_query ($q) or die(mysql_error());
$attorney = mysql_fetch_array($r, MYSQL_ASSOC);
*/


?>
<!--
<table height="30px" width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td valign="center" align="center">Staff From: <?=$_COOKIE[psdata][email];?></td>
<td valign="center" align="center">Publisher To: <?=$paper[dept_email];?></td>
<td valign="center" align="center">Staff Cc: hwa.archive@gmail.com</td>
<td valign="center" align="center">Client To: <?=$attorney[sendAdTo];?></td>
</tr>
</table>
-->
</div>
<center><div style="border:solid 1px #ccc; width:900px;"><?=stripslashes($d[LiveAffidavit])?></div></center>
<? } ?>
</div>

<? }else{
echo "missing auction id?";
}

if($_GET['print']){
//if(printAD($_GET[id],'72.4.227.230') == 'fail'){
printAD($_GET[id],'75.94.82.44');
//}
}

// spell checker
if($d[LiveAffidavit]){
$pspell_link = pspell_new("en");
echo "<div>Spell Checker<ol>";
$word = explode(" ", strip_tags(stripslashes($d[LiveAffidavit])));
foreach($word as $k => $v) {
   if (pspell_check($pspell_link, $v)) {
      //echo "spelled right";
   } else {
$strip = htmlspecialchars($v);
      echo "<li>Sorry, (<b>$strip</b>), wrong spelling";
   };
};
echo "</ol></div>";
}
/*
$q1 = "UPDATE schedule_items set adProofed='".$_COOKIE[psdata][name]." on ".date('r')."' WHERE schedule_id = '$_GET[id]'";
$r1 = @mysql_query ($q1) or die(mysql_error());
*/
 ?>