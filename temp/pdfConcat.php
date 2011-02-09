<pre><?
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

hardLog('Cleansing Temp Folder of PDFs','user');
$cmd="rm -f *.pdf";
echo $cmd."<br>";
system($cmd);

mysql_connect();
mysql_select_db('service');
$packet1=$_GET[src];
$packet2=$_GET[dest];
$current="/sandbox/staff/temp";
hardLog('Downloading Full PDF Package','user');

$r1=@mysql_query("select otd from ps_packets where packet_id = '$packet1'");
$d1=mysql_fetch_array($r1,MYSQL_ASSOC);
$file1 = str_replace('http://mdwestserve.com/ps/affidavits//','',$d1[otd]);
$file1 = str_replace('http://mdwestserve.com/ps/affidavits/','',$file1);
$file1 = str_replace('http://mdwestserve.com/affidavits/','',$file1);
echo "<li>$file1</li>";
hardLog('Adding '.$file1 ,'user');
$fn1=getFN($d1[otd]);
$folder1="/data/service/orders/".getFolder($d1[otd]);
echo "<li>Folder 1: $folder1</li>";
$files .=  ' '.getFN($d1[otd]).' ';

$r2=@mysql_query("select otd from ps_packets where packet_id = '$packet2'");
$d2=mysql_fetch_array($r2,MYSQL_ASSOC);
$file2 = str_replace('http://mdwestserve.com/ps/affidavits//','',$d2[otd]);
$file2 = str_replace('http://mdwestserve.com/ps/affidavits/','',$file2);
$file2 = str_replace('http://mdwestserve.com/affidavits/','',$file2);
echo "<li>$file2</li>";
hardLog('Adding '.$file2 ,'user');
$fn2=getFN($d2[otd]);
$folder2="/data/service/orders/".getFolder($d2[otd]);
echo "<li>Folder 2: $folder2</li>";
$files .=  ' '.getFN($d2[otd]).' ';

//copy source file into temp folder
$cmd2= "cp -c '".$folder1."/".$fn1."' '".$current."'";
echo $cmd2."<br>";
system($cmd2);

//copy destination file into temp folder
$cmd3= "cp -c '".$folder2."/".$fn2."' '".$current."'";
echo $cmd3."<br>";
system($cmd3);

//merge files
$cmd4='gs -dNOPAUSE -sDEVICE=pdfwrite -sOUTPUTFILE=packet'.$packet2.'.pdf -dBATCH '.$files;
echo $cmd4."<br>";
passthru($cmd4);

//move file into destination folder
$cmd5="mv -f 'packet".$packet2.".pdf' '".$folder2."/packet".$packet2.".pdf'";
echo $cmd5."<br>";
system($cmd5);

//update db with newly concatenated PDF
$path="http://mdwestserve.com/PS_PACKETS/".getFolder($d2[otd])."/packet".$packet2.".pdf";
$q="UPDATE ps_packets SET otd='$path' WHERE packet_id='$packet2'";
$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());

hardLog('Cleansing Temp Folder of PDFs','user');
$cmd="rm -f *.pdf";
echo $cmd."<br>";
system($cmd);
?>
<script>alert('PDF Package Ready for Download');window.location.href='<?=$path?>';</script></pre>
<? hardLog('Download Complete','user');?>