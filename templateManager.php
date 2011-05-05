<?
include 'common.php';
?>
<script>
function prompter(){
	var reply = prompt("Please enter a name for the new template, using only alphanumeric characters, and replacing spaces with decimals", "")
	if (reply == null){
		alert("That is not a valid reason")
		window.location="http://staff.mdwestserve.com/templateManager.php";
	}
	else{
		window.location="http://staff.mdwestserve.com/templateManager.php?create=1&name="+reply;
	}
}
</script>
<script language="JavaScript" type="text/javascript" src="affidavitMaster/wysiwyg.js"></script>
<script language="javascript1.2">
   // attach the editor to all textareas of your page.
   WYSIWYG.attach('whiteboard');
</script>
<?
if($_GET[create]){
	if (!$_GET[name]){
		echo "<script>prompter();</script>";
	}elseif(!$_POST[affidavit]){
		$name=str_replace(' ','.',$_GET[name]);
		//display blank whiteboard with template name above, save to new file on submit
		?>
		<form method="post">
		<input type="hidden" name="affidavit" value="<?=$name?>">
		<center><h1><?=$name?></h1>
		<textarea id="whiteboard" rows="30" cols="100" name="whiteboard"></textarea><br>
		<input style="font-size:24px; color:#006666;" name="submit" type="submit" value="Save Ad"></center>
		</form>
		<?
	}
}
if($_GET[affidavit] || $_POST[affidavit]){
	if ($_POST[whiteboard]){
		//save whiteboard to file
		$whiteboard = $_POST[whiteboard];
		$myFile = "$_POST[affidavit].affidavit";
		$fullPath="/data/service/templates/".$myFile;
		$fh = fopen($fullPath, 'w') or die("can't open file");
		fwrite($fh, $whiteboard);
		fclose($fh);
		$saved=1;
	}
	if ($_GET[edit] && !$saved){
		$url="/data/service/templates/".$_GET[affidavit];
		$html=getPage($url,$_GET[affidavit],'5','');
		?>
		<form method="post">
		<input type="hidden" name="affidavit" value="<?=$_GET[affidavit]?>">
		<center><h1><?=$_GET[affidavit]?></h1>
		<textarea id="whiteboard" rows="30" cols="100" name="whiteboard"><?=stripslashes($html);?></textarea><br>
		<input style="font-size:24px; color:#006666;" name="submit" type="submit" value="Save Ad"></center>
		</form>
		<?
	}else{ ?>
		<div align="right">
		<form><input type="hidden" name="affidavit" value="<?=$_GET[name]?>"><input name="edit" value="Edit Template" style="font-size:24px; color:#006666;" type="submit"></form></div>
		<div><?=stripslashes($whiteboard)?></div>
<?	}
}else{ ?>
	<form><table><tr><td>SELECT TEMPLATE TO EDIT <select name="affidavit" onchange="form.submit()"><?
	$directory = '/data/service/templates';
    $results = array();
    $handler = opendir($directory);
    while ($file = readdir($handler)) {
        if ($file != '.' && $file != '..' && $file != 'CVS'){
            echo "<option>$file</option>";
		}
    }
    closedir($handler); 
	echo "<option>NEW TEMPLATE</option>";
	?>
	</select> <input type='submit' value='GO!' name='submit'></td></tr></table></form>
<?
}
?>