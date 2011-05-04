<?
mysql_connect();
mysql_select_db('core');
function getFolder($otd){
	$path=explode("/",$otd);
	$count=(count($path)-2);
	$i=-1;
	while ($i < $count){$i++;
		if ($path["$i"] != ''){
			$folder .= "/".$path["$i"];
		}
	}
	return $folder;
}
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
function my_exec($cmd, $input='')
         {$proc=proc_open($cmd, array(0=>array('pipe', 'r'), 1=>array('pipe', 'w'), 2=>array('pipe', 'w')), $pipes);
          fwrite($pipes[0], $input);fclose($pipes[0]);
          $stdout=stream_get_contents($pipes[1]);fclose($pipes[1]);
          $stderr=stream_get_contents($pipes[2]);fclose($pipes[2]);
          $rtn=proc_close($proc);
          return array('stdout'=>$stdout,
                       'stderr'=>$stderr,
                       'return'=>$rtn
                      );
         }
//var_export(my_exec('echo -e $(</dev/stdin) | wc -l', 'h\\nel\\nlo')); 
function explodePrint($str){
	$explode=explode('page-break-after:always; ',$str);
	$count=count($explode)-1;
	$i=-1;
	while ($i < $count){$i++;
		if ($i == $count){
			$implode .= $explode["$i"];
		}elseif($i > 0){
			$implode .= "page-break-after:always; ".$explode["$i"];
		}else{
			$implode .= $explode["$i"];
		}
	}
	return $implode;
}
function pdfAD($id){
	$r=@mysql_query("select LiveAffidavit from ps_packets where packet_id = '$id' LIMIT 0,1");
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	$myFile = trim($d[LiveAffidavit]);
	$folder=getFolder($myFile);
	/*$fh = fopen($myFile, 'w') or die("can't open file");
	$html=getPage($url,"Packet $id HTML",'5','');
	$la=explodePrint($html);
	fwrite($fh, $la);
	fclose($fh);*/
	$command = 'python DocumentConverter.py $myFile $folder/'.$id.'.pdf';
	$error=my_exec($command);
	if (is_array($error)){
		foreach($error as $value => $key){
			$error2 .= "<li>$value :: $key</li>";
		}
	}else{
		$error2=$error;
	}
	//$error = system($command,$result);
	/*if (trim($error) == '1'){
		@mysql_query("INSERT INTO attachment (path,status) values ('/gitbox/Service-Office/affidavitMaster/".$id.".pdf','PDF Error - ".$_SERVER['HTTP_HOST']."')");
	}*/
	echo "<div>".$command."</div>";
	echo "<div>".$error2."</div>";
	echo "<div>".$result."</div>";
	header('Location: '.$folder.'/'.$id.'.pdf');
	//echo "<script>window.open('$id.pdf','test')</script>";
}

if($_GET['pdf']){
	pdfAD($_GET[id]);
}

function printAD($id,$ip){
	$r=@mysql_query("select LiveAffidavit from ps_packets where packet_id = '$id' LIMIT 0,1");
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
$id=$_GET[id];
?>

<div style="background-color:#FFFFFF;">
<?
        if ($_POST[whiteboard]){
            $whiteboard = addslashes($_POST[whiteboard]);
			if (strpos($whiteboard,"'http://staff.mdwestserve.com/obstyle.css'") !== true){
				$whiteboard="<link href='http://staff.mdwestserve.com/obstyle.css' rel='stylesheet' type='text/css' />".$whiteboard;
			}
			$user = $_COOKIE[psdata][user_id];
            /*$q = "update ps_packets set LiveAffidavit='$whiteboard' WHERE packet_id = '$_GET[id]'";
            $r = @mysql_query ($q) or die(mysql_error());*/
			$r=@mysql_query("select LiveAffidavit from ps_packets where packet_id = '$id' LIMIT 0,1");
			$d=mysql_fetch_array($r,MYSQL_ASSOC);
			if (trim($d[LiveAffidavit]) != ''){
				system('rm -f '.trim($d[LiveAffidavit]), $retval);
			}
			$dir='/data/service/affidavits/';
			$year=date('Y');
			$month=date('m');
			$day=date('d');
			$path=$dir.$year.'/'.$month.'/'.$day;
			if(!file_exists($dir.$year)){
				//create year folder, with appropriate permissions
				mkdir ($dir.$year,0777);
			}
			if(!file_exists($dir.$year.'/'.$month)){
				//create month folder, with appropriate permissions
				mkdir ($dir.$year.'/'.$month,0777);
			}
			if(!file_exists($path)){
				//create day folder, with appropriate permissions
				mkdir ($path,0777);
			}
			$myFile = "$id.html";
			$fullPath=$path."/".$myFile;
			$fh = fopen($fullPath, 'w') or die("can't open file");
			fwrite($fh, $whiteboard);
			fclose($fh);
			@mysql_query("update ps_packets set LiveAffidavit = '$fullPath' where packet_id = '$id'") or die(mysql_error());
            $saved = 1;
        }
        $q = "SELECT LiveAffidavit, attorneys_id FROM ps_packets WHERE packet_id = '$_GET[id]' LIMIT 0,1";
        $r = @mysql_query ($q) or die(mysql_error());
        $d = mysql_fetch_array($r, MYSQL_ASSOC);
		$url=str_replace('/data/service/affidavits/','http://mdwestserve.com/aM/',trim($d[LiveAffidavit]));
		$html=str_replace("<link href='http://staff.mdwestserve.com/obstyle.css' rel='stylesheet' type='text/css' />",'',getPage($url,"Packet $_GET[id] HTML",'5',''));
        ?>
<script language="JavaScript" type="text/javascript" src="wysiwyg.js"></script>
<? if ($_GET[edit] && !$saved ){ ?>
<form method="post">
<center>
<textarea id="whiteboard" rows="30" cols="100" name="whiteboard"><?=stripslashes($html)?></textarea>
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
<td <?=$mouseover2;?> valign="center" align="center"><a href="?id=<?=$_GET[id];?>&pdf=1" target="_Blank">Open .pdf</a></td>
</tr>
</table>

</div>
<center><div style="border:solid 1px #ccc; width:900px;"><?=stripslashes($html)?></div></center>
<? } ?>
</div>

<? }else{
echo "missing id?";
}

if($_GET['print']){
//if(printAD($_GET[id],'72.4.227.230') == 'fail'){
printAD($_GET[id],'75.94.82.44');
//}
}
 ?>