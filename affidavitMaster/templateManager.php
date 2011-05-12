<?
mysql_connect();
mysql_select_db('core');
function getPage($url, $referer, $timeout, $header){
	if(!isset($timeout))
        $timeout=30;
    $curl = curl_init();
    if(strstr($referer,"://")){
        curl_setopt ($curl, CURLOPT_REFERER, $referer);
    }
    curl_setopt ($curl, CURLOPT_URL, $url);
    curl_setopt ($curl, CURLOPT_TIMEOUT, $timeout);
    curl_setopt ($curl, CURLOPT_USERAGENT, sprintf("Mozilla/%d.0",rand(4,5)));
    curl_setopt ($curl, CURLOPT_HEADER, (int)$header);
    curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, 0);
    $html = curl_exec ($curl);
    curl_close ($curl);
    return $html;
}
?>
<script>
function prompter(){
	var reply = prompt("Please enter a name for the new template, using only alphanumeric characters, and replacing spaces with decimals", "")
	if (reply == null){
		alert("That is not a valid reason")
		window.location="http://staff.mdwestserve.com/affidavitMaster/templateManager.php";
	}
	else{
		window.location="http://staff.mdwestserve.com/affidavitMaster/templateManager.php?create=1&name="+reply;
	}
}
</script>
<?
if($_GET[create]){
	if (!$_GET[name]){
		echo "<script>prompter();</script>";
	}elseif(!$_POST[affidavit]){
		$name=str_replace(' ','.',$_GET[name]);
		//display blank whiteboard with template name above, save to new file on submit
		?>
		<script language="JavaScript" type="text/javascript" src="wysiwyg.js"></script>
		<script language="javascript1.2">
		   // attach the editor to all textareas of your page.
		   WYSIWYG.attach('whiteboard');
		</script>
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
		if ($_POST[affidavit]){
			$myFile = "$_POST[affidavit]";
		}else{
			$myFile = "$_GET[affidavit]";
		}
		$fullPath="/data/service/templates/".$myFile;
		$fh = fopen($fullPath, 'w') or die("can't open file: [$fullPath]");
		fwrite($fh, $whiteboard);
		fclose($fh);
		$saved=1;
	}
	if ($_GET[edit] && !$saved){
		$url="http://mdwestserve.com/aT/".$_GET[affidavit];
		$html=getPage($url,$_GET[affidavit],'5','');
		?>
		<script language="JavaScript" type="text/javascript" src="wysiwyg.js"></script>
		<script language="javascript1.2">
		   // attach the editor to all textareas of your page.
		   WYSIWYG.attach('whiteboard');
		</script>
		<form method="post">
		<input type="hidden" name="affidavit" value="<?=$_GET[affidavit]?>">
		<center><h1><?=$_GET[affidavit]?></h1>
		<textarea id="whiteboard" rows="30" cols="100" name="whiteboard"><?=stripslashes($html);?></textarea><br>
		<input style="font-size:24px; color:#006666;" name="submit" type="submit" value="Save Ad"></center>
		</form>
		<?
	}else{ ?>
		<div align="right">
		<form method='get'><center><input type="hidden" name="affidavit" value="<?=$_GET[affidavit]?>"><input name="edit" value="Edit Template" style="font-size:24px; color:#006666;" type="submit"></form></div>
		<div style='width:800px;'><?=stripslashes($whiteboard)?></div>
		<hr>
		<p align=right><a href="templateManager.php">RETURN TO TEMPLATE LIST</a></p></center>
<?	}
}elseif(!$_GET[create]){ ?>
	<center><form><table><tr><td>SELECT TEMPLATE TO EDIT <select name="affidavit"><?
	$directory = '/data/service/templates';
    $results = array();
    $handler = opendir($directory);
    while ($file = readdir($handler)) {
        if ($file != '.' && $file != '..' && $file != 'CVS'){
            echo "<option>$file</option>";
		}
    }
    closedir($handler); 
	?>
	</select> <input type='submit' value='GO!' name='edit'> OR <a href="templateManager.php?create=1">Create New Template</a></td></tr></table></form></center>
<?
}
?>