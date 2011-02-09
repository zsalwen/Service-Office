<pre><?
$starttime = time()+microtime();
function getFolder($otd){
	$path=explode("/",$otd);
	$count=(count($path)-2);
	$folder=$path[$count];
	return $folder;
}

function getFN($otd){
	$path=explode("/",$otd);
	$count=(count($path)-1);
	$fn=$path[$count];
	return $fn;
}

function hardLog($str,$type){
	if ($type == "user"){
		$log = "/logs/user.log";
	}
	if ($type == "client"){
		$log = "/logs/client.log";
	}
	if ($type == "server"){
		$log = "/logs/contractors.log";
	}
	if ($type == "debug"){
		$log = "/logs/debug.log";
	}
	// this is important code 
	if ($log){
		error_log('['.date('h:i:sA m/d/y')."] [".$_SERVER["REMOTE_ADDR"]."] [".trim($str)."]\n", 3, $log);
	}
	// this is important code 
}
?>
<style>
	@media print {
	  .noprint { display: none; }
	}
	table {padding:0px;}
</style> 
<?
//open noprint div
echo "<div class='noprint'>";

hardLog('Cleansing Temp Folder of PDFs & PNGs','user');
$cmd="rm -f *.pdf";
echo $cmd."<br>";
system($cmd);
$cmd="rm -f *.png";
echo $cmd."<br>";
system($cmd);

mysql_connect();
mysql_select_db('service');
$packet=$_GET[packet];
$current="/sandbox/staff/temp";
hardLog('Downloading Full PDF Package','user');
if ($_GET[svc] == 'OTD'){
	$r1=@mysql_query("select otd from ps_packets where packet_id = '$packet'");
}else{
	$r1=@mysql_query("select otd from evictionPackets where eviction_id = '$packet'");
}
$d1=mysql_fetch_array($r1,MYSQL_ASSOC);
//echo link to PDF
$otdStr=str_replace('portal//var/www/dataFiles/service/orders/','PS_PACKETS/',$d1[otd]);
$otdStr=str_replace('data/service/orders/','PS_PACKETS/',$otdStr);
$otdStr=str_replace('portal/','',$otdStr);
if (!strpos($otdStr,'mdwestserve.com')){
	$otdStr="http://mdwestserve.com/".$otdStr;
}
echo "<div class='noprint'><a href='$otdStr'>Load PDF for Packet $packet</a></div>";
$files1 = str_replace('http://mdwestserve.com/ps/affidavits//','',$d1[otd]);
$files1 = str_replace('http://mdwestserve.com/ps/affidavits/','',$files1);
$files1 = str_replace('http://mdwestserve.com/affidavits/','',$files1);
hardLog('Adding '.$files1 ,'user');
$fn1=getFN($d1[otd]);
$folder1="/data/service/orders/".getFolder($d1[otd]);
$files .=  " '".getFN($d1[otd])."' ";

//copy source file into temp folder
$cmd2= "cp -c '".$folder1."/".$fn1."' '".$current."'";
echo $cmd2."<br>";
system($cmd2);

//output jpeg files
$cmd3="gs -dNOPAUSE -q -r100 -sDEVICE=jpeg -sOutputFile=packet".$packet."-%d.jpeg -dBATCH ".$files."";
echo "<b>".$cmd3."</b><br>";
passthru($cmd3);

//close noprint div
echo "</div>";

//display
$i=0;


while ($i < 150){$i++;
	$src="packet$packet-$i.jpeg";
	if (file_exists($src)){
		echo "<table align='center' style='page-break-after:always'><tr><td><img src='$src' height='1200'></td></tr></table>";
	}
}

//echo page load time in noprint div
$endtime = time()+microtime();
$totaltime = round($endtime - $starttime,4);
echo "<div class='noprint'><h1>Load Time: $totaltime</h1></div><script>document.title='".$_GET[svc]." $packet, PDF Load Time: $totaltime'</script>";

//cleanse temp folder
hardLog('Cleansing Temp Folder of PDFs & PNGs','user');
$cmd="rm -f *.pdf";
echo $cmd."<br>";
system($cmd);

if ($_GET['autoPrint'] == 1){
	echo "<script>
	if (window.self) window.print();
	</script>";
}

echo "</pre>";
hardLog('Download Complete','user');?>