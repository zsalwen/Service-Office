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

function pageCount($file){
	 if(file_exists($file)) {
		 //open the file for reading
		 if($handle = @fopen($file, "rb")) {
			 $count = 0;
			 $i=0;
			 while (!feof($handle)) {
				 if($i > 0){
					 $contents .= fread($handle,8152);
				 }else{
					 $contents = fread($handle, 1000);
				 }
				$i++;
			}
			fclose($handle);

			//get all the trees with 'pages' and 'count'. the biggest number
			//is the total number of pages, if we couldn't find the /N switch above.               
			//if(preg_match_all("/\/Type\s*\/Pages\s*.*\s*\/Count\s+([0-9]+)/", $contents, $capture, PREG_SET_ORDER)) {
			if(preg_match_all("/\/Count\s+([0-9]+)/", $contents, $capture, PREG_SET_ORDER)) {
				foreach($capture as $c) {
					if($c[1] > $count)
						$count = $c[1];
				}
				return $count;           
			}
		}
	}
}

hardLog('Cleansing Temp Folder of PDFs','user');
$cmd="rm -f *.pdf";
echo $cmd."<br>";
system($cmd);

mysql_connect();
mysql_select_db('core');
$packet=$_GET[packet];
echo "Packet: ".$packet."<br>";
$current="/gitbox/Service-Office/temp";
hardLog('Downloading Full PDF Package','user');

$r1=@mysql_query("select otd, client_file, prevOTD from ps_packets where packet_id = '$packet'");
$d1=mysql_fetch_array($r1,MYSQL_ASSOC);
$file1 = str_replace('http://mdwestserve.com/ps/affidavits//','',$d1[otd]);
$file1 = str_replace('http://mdwestserve.com/ps/affidavits/','',$file1);
$file1 = str_replace('http://mdwestserve.com/affidavits/','',$file1);
$pageCount=pageCount($file);
//checking if file exists and is longer than one page (failed merges resolve as a single-page blank document)
if (file_exists($file) && $pageCount > 1){
	//if true, merge as usual
	echo "<li>File 1: $file1</li>";
	echo "<li>File 1 pageCount: $pageCount</li>";
	hardLog('Adding '.$file1 ,'user');
	$fn1=getFN($d1[otd]);
	$folder=getFolder($d1[otd]);
	$path="/data/service/orders/".$folder;
//else check if merge already happened and failed 
}elseif($d1[prevOTD] != ''){
	//if true, attempt merge again using original OTD file
	$file1 = str_replace('http://mdwestserve.com/ps/affidavits//','',$d1[prevOTD]);
	$file1 = str_replace('http://mdwestserve.com/ps/affidavits/','',$file1);
	$file1 = str_replace('http://mdwestserve.com/affidavits/','',$file1);
	echo "<li>File 1: $file1</li>";
	hardLog('Adding '.$file1 ,'user');
	$fn1=getFN($d1[prevOTD]);
	$folder=getFolder($d1[prevOTD]);
	$path="/data/service/orders/".$folder;
}else{
	//else alert the user and continue as is.
	echo "<li>OTD is only one page long, this might get messy...</li>";
	echo "<li>File 1: $file1</li>";
	echo "<li>File 1 pageCount: $pageCount</li>";
	hardLog('Adding '.$file1 ,'user');
	$fn1=getFN($d1[otd]);
	$folder=getFolder($d1[otd]);
	$path="/data/service/orders/".$folder;
}
echo "<li>Folder 1: $path</li>";
$files .=  ' '.$fn1.' ';

//second file
$fn2="RequestforMediation.pdf";
$files .=  ' '.$fn2.' ';

//copy source file into temp folder
$cmd2= "cp -c '".$path."/".$fn1."' '".$current."'";
echo $cmd2."<br>";
system($cmd2);

//copy destination file into temp folder
$cmd3= "cp -c '".$path."/".$fn2."' '".$current."'";
echo $cmd3."<br>";
system($cmd3);

//merge files
$cmd4='gs -dNOPAUSE -sDEVICE=pdfwrite -sOUTPUTFILE=OTD'.$packet.'.pdf -dBATCH '.$files;
echo $cmd4."<br>";
passthru($cmd4);

//move file into destination folder
$cmd5="mv -f 'OTD".$packet.".pdf' '".$path."/OTD".$packet.".pdf'";
echo $cmd5."<br>";
system($cmd5);

//update db with newly concatenated PDF
$url="http://mdwestserve.com/PS_PACKETS/".$folder."/OTD".$packet.".pdf";
echo "<li>URL: $url</li>";
$q="UPDATE ps_packets SET otd='$url', prevOTD='$d1[otd]' WHERE packet_id='$packet'";
$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());

hardLog('Cleansing Temp Folder of PDFs','user');
$cmd="rm -f *.pdf";
echo $cmd."<br>";
system($cmd);
if (file_exists($path."/OTD".$packet.".pdf")){
	hardLog('Download Complete','user');
}else{
	echo "<script>alert('Merge Error! Consult Zach or Patrick!')</script>";
	echo "<h1>Merge Error! Consult Zach or Patrick!</h1>";
	error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Merging Request For Mediation Into OTD$packet FAILED \n",3,"/logs/user.log");
	die();
}
error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Merging Request For Mediation Into OTD$packet \n",3,"/logs/user.log");
if ($_GET[redirect]){
	echo "<script>alert('PDF Package Ready--Redirecting');window.location.href='$_GET[redirect]';</script></pre>";
}else{
	echo "<script>alert('PDF Package Ready for Download');window.location.href='$url';</script></pre>";
} ?>