<?
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
mysql_connect();
mysql_select_db('core');
$packet=$_GET[packet];
echo "Packet: ".$packet."<br>";
//$current="/sandbox/staff/temp";
//hardLog('Downloading Full PDF Package','user');

$r1=@mysql_query("select otd, client_file from ps_packets where packet_id = '$packet'");
$d1=mysql_fetch_array($r1,MYSQL_ASSOC);
$file1 = str_replace('http://mdwestserve.com/ps/affidavits//','',$d1[otd]);
$file1 = str_replace('http://mdwestserve.com/ps/affidavits/','',$file1);
$file1 = str_replace('http://mdwestserve.com/affidavits/','',$file1);
echo "<li>File 1: $file1</li>";
$fn1=getFN($d1[otd]);
$folder1="/data/service/orders/".getFolder($d1[otd]);
echo "Page Count: ".pageCount($folder1.'/'.$fn1);
?>