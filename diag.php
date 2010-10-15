<?
// secure page now that we are exposing code
if ($_COOKIE[psdata][name]){
function internalTrace($trace,$line){


return $newTrace;
}
function search($file,$search){
	$pos = strpos($file, $search);
	if ($pos === false) {
		return 0;
	} else {
		return 1;
	}
}
?>
<script src="sorttable.js"></script>
<style>
body{ margin:0px; padding:0px; }
td { font-size:12px; }
li { white-space:pre; }
/* Sortable tables */
table.sortable thead {
    background-color:#eee;
    color:#666666;
    font-weight: bold;
    cursor: default;
}

</style>
<table cellpadding='0' cellspacing='0' border='1' width='100%'><form>
	<tr>
		<td valign='top' colspan='2' bgcolor='#FFFFCC'>
		<? if($_GET[logfile]){ ?>
		Currently Viewing Log <?=$_GET[logfile];?>, <?=filesize('/logs/'.$_GET[logfile]);?> b
		<? } ?>
		<? if($_GET[trace]){ ?>
		Currently Viewing Source Code <?=$_GET[trace];?>, <?=filesize('/sandbox/'.$_GET[trace]);?> b
		<? } ?>
		</td>
		<td rowspan='4' valign='top'>
		<?
		if($_GET[logfile]){
			$file = fopen('/logs/'.$_GET[logfile], "r") or exit("Unable to Load!");
			while(!feof($file))
			  {
			  $line = fgets($file);
				echo "<li>".$line."</li>";
			}
			fclose($file);
		}
		if($_GET[trace]){
			$file = fopen('/sandbox/'.$_GET[trace], "r") or exit("Unable to Load!");
			while(!feof($file))
			  {
			  $line = fgets($file);
				$lineTest = search(htmlentities($line),'include');
				if($lineTest != 0){
					echo "<li><a href='?trace=".internalTrace($_GET[trace],$line)."'>Internal Trace</a>: ".htmlentities($line)."</li>";
				}else{
					 //echo "<li>B: $lineTest: ".htmlentities($line)."</li>";
				}
				}
			
			fclose($file);
			highlight_file('/sandbox/'.$_GET[trace]);
		}
		?>
		</td>
	</tr>
	<tr>
		<td valign='top' bgcolor='#FFCCFF'>
		<table class="sortable">
		<thead>
		  <tr><th>Load</th><th>Code</th><th>Size</th></tr>
		</thead>
		<?
		$directory = '/logs/user';
		$dh = opendir($directory);
		$files = array();
		$spiders = array();
		$isps = array();
			while (($file = readdir($dh)) !== false) {
				if ($file != '.' && $file != '..'){
					if (search($file,'googlebot')){
						array_push($spiders, $file);
					}elseif(search($file,'spinn3r')){
						array_push($spiders, $file);
					}elseif(search($file,'yandex')){
						array_push($spiders, $file);
					}elseif(search($file,'msn')){
						array_push($spiders, $file);
					}elseif(search($file,'amazonaws')){
						array_push($spiders, $file);
					}elseif(search($file,'baidu')){
						array_push($spiders, $file);
					}elseif(search($file,'yahoo')){
						array_push($spiders, $file);
					}elseif(search($file,'verizon')){
						array_push($isps, $file);
					}elseif(search($file,'clearwire')){
						array_push($isps, $file);
					}elseif(search($file,'blackberry')){
						array_push($isps, $file);
					}elseif(search($file,'comcast')){
						array_push($isps, $file);
					}elseif(search($file,'mycingular')){
						array_push($isps, $file);
					}else{
						array_push($files, $file); // List in Main Array
					}
				}
			}	
		sort($files);
		foreach ($files as $file) {
				echo "<tr><td><a href='?logfile=user/$file'>Log</a></td><td>".str_replace('.log','',$file)."</td><td>".filesize('/logs/user/'.$file)."</td></tr>";
		}
		closedir($dh);
		?>
		</table>
		</td>
		<td valign='top' bgcolor='#CCFFFF'>
		<table class="sortable">
		<thead>
		  <tr><th>Load</th><th>Code</th><th>Size</th><th>Trace</th></tr>
		</thead>
		<?
		$directory = '/logs/code';
		$dh = opendir($directory);
		$files = array();
		$hosting = array();
			while (($file = readdir($dh)) !== false) {
				if ($file != '.' && $file != '..'){
					if(search($file,'hosting')){
						array_push($hosting, $file);
					}elseif(search($file,'patrick')){
						array_push($hosting, $file);
					}elseif(search($file,'thirdParty')){
						array_push($hosting, $file);
					}else{
						array_push($files, $file); // List in Main Array
					}
				}
			}	
		sort($files);
		foreach ($files as $file) {
				$page44=str_replace('-','/',str_replace('.log','',$file));
				$size44=filesize('/logs/code/'.$file);
				//checkInterfaceDatabase($page44,$file,$size44);
				echo "<tr><td><a href='?logfile=code/$file'>Log</a></td><td>".str_replace('-','/',str_replace('.log','',$file))."</td><td>".filesize('/logs/code/'.$file)."</td><td><a href='?trace=".str_replace('-','/',str_replace('.log','',$file))."'>Trace</a></td></tr>";
		}
		closedir($dh);
		?>
		</table>
		</td>
	</tr>
	<tr>
		<td valign='top' bgcolor='#cccccc'>
		<?
		sort($spiders);
		foreach ($spiders as $file) {
				echo "<input onClick=\"this.form.submit();\" type='radio' name='logfile' value='user/$file'>".str_replace('.log','',str_replace('-','/',$file))."</br>";
		}
		?>
		</td>
		<td valign='top' bgcolor='#cccccc'>
		<?
		sort($hosting);
		foreach ($hosting as $file) {
				echo "<input onClick=\"this.form.submit();\" type='radio' name='logfile' value='code/$file'>".str_replace('.log','',str_replace('-','/',$file))."</br>";
		}
		?>
		</td>
	</tr>
	<tr>
		<td valign='top'>
		<?
		sort($isps);
		foreach ($isps as $file) {
				echo "<input onClick=\"this.form.submit();\" type='radio' name='logfile' value='user/$file'>".str_replace('.log','',str_replace('-','/',$file))."</br>";
		}
		?>
		</td>
		<td valign='top'>
			Empty Cell
		</td>
	</tr>
	</form>
</table>	
<? }else{?>
Go Away
<? }?>