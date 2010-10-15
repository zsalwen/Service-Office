<?
function getFolder($otd){
	$path=explode("/",$otd);
	$count=(count($path)-2);
	$folder2=$path[$count];
	return $folder2;
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
mysql_select_db('core');
$packet=$_GET[id];

$skip=$_GET[skip];
$stop=$skip-1;
$start=$skip+1;

$current="/sandbox/staff/temp";
if ($_GET[type] == 'EV'){
	$r1=@mysql_query("select otd from evictionPackets where eviction_id = '$packet'");
}else{
	$r1=@mysql_query("select otd from ps_packets where packet_id = '$packet'");
}
$type=strtolower($_GET[type]);
$d1=mysql_fetch_array($r1,MYSQL_ASSOC);
$file1 = str_replace('http://mdwestserve.com/ps/affidavits//','',$d1[otd]);
$file1 = str_replace('http://mdwestserve.com/ps/affidavits/','',$file1);
$file1 = str_replace('http://mdwestserve.com/affidavits/','',$file1);
echo "<li>$file1</li>";
hardLog('Adding '.$file1 ,'user');
$fn1=getFN($d1[otd]);
$folder=getFolder($d1[otd]);
echo "<li>Folder: $folder</li>";
$folder2="/data/service/orders/".$folder;
echo "<li>Folder 2: $folder2</li>";
$files .=  " '".getFN($d1[otd])."' ";

//copy source file into temp folder
$cmd2= "cp -c '".$folder2."/".$fn1."' '".$current."'";
echo $cmd2."<br>";
system($cmd2);
if ($skip != '1'){
	//output first file (everything before page to be skipped);
	$cmd3='gs -dNOPAUSE -sDEVICE=pdfwrite -dFirstPage=1 -dLastPage='.$stop.' -sOutputFile=packet1.pdf -dBATCH '.$files;
	echo $cmd3."<br>";
	passthru($cmd3);
	$files2 .=  ' packet1.pdf ';

	//output second file (everything after page to be skipped);
	$cmd4='gs -dNOPAUSE -sDEVICE=pdfwrite -dFirstPage='.$start.' -sOutputFile=packet2.pdf -dBATCH '.$files;
	echo $cmd4."<br>";
	passthru($cmd4);
	$files2 .=  ' packet2.pdf ';

	//merge files
	$cmd5='gs -dNOPAUSE -sDEVICE=pdfwrite -sOUTPUTFILE=packet'.$packet.'.pdf -dBATCH '.$files2;
	echo $cmd5."<br>";
	passthru($cmd5);
}else{
	//output everything after page to be skipped;
	$cmd4='gs -dNOPAUSE -sDEVICE=pdfwrite -dFirstPage=2 -sOutputFile=packet'.$packet.'.pdf -dBATCH '.$files;
	echo $cmd4."<br>";
	passthru($cmd4);
}
$path="http://staff.mdwestserve.com/otd/temp/packet".$packet.".pdf";
//move file into destination folder
$cmd6="mv -f 'packet".$packet.".pdf' '".$folder2."/packet".$packet.".pdf'";
echo $cmd6."<br>";
system($cmd6);

//update db with newly concatenated PDF
$path="http://mdwestserve.com/PS_PACKETS/$folder/packet$packet.pdf";
echo "<li>$path</li>";
$path2="http://staff.mdwestserve.com/$type/order.php";
if ($_GET[packet]){
$path2 .= "?packet=$packet";
}
if ($type == 'ev'){
	$q="UPDATE evictionPackets SET otd='$path' WHERE eviction_id='$packet'";
}else{
	$q="UPDATE ps_packets SET otd='$path' WHERE packet_id='$packet'";
}
$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());

hardLog('Cleansing Temp Folder of PDFs','user');
$cmd="rm -f *.pdf";
echo $cmd."<br>";
system($cmd);
?>
<script>alert('PDF Package Ready for Download');window.open('<?=$path?>','Packet <?=$packet?>');window.location.href='<?=$path2?>';</script></pre>
<? hardLog('Download Complete','user');?>