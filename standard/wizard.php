<?
mysql_connect();
mysql_select_db('core');
function fillIn($str){
	if (!$str){
		$str = "_____________________";
	}
	return ucPrep($str);
}
if($_GET['id']){
$r=@mysql_query("select * from standard_affidavits where id = '$_GET[id]'");
$affidavit = mysql_fetch_array($r,MYSQL_ASSOC);
$r=@mysql_query("select * from standard_packets where packet_id = '$affidavit[packet]'");
$packet = mysql_fetch_array($r,MYSQL_ASSOC);
$r=@mysql_query("select * from ps_users where id = '$affidavit[serverX]'");
$server = mysql_fetch_array($r,MYSQL_ASSOC);
$filename = "/sandbox/staff/templates/".$packet[affidavitType];
$handle = fopen($filename, "rb");
$template .= fread($handle, filesize($filename));
// modify data
function ucPrep($str){
return ucwords(strtolower($str));
}

if($packet[name1]){
$names = $packet[name1];
}
if ($packet[name2]){
$names .= '<br>'.$packet[name2];
}
if ($packet[name3]){
$names .= '<br>'.$packet[name3];
}
if ($packet[name4]){
$names .= '<br>'.$packet[name4];
}
if ($packet[name5]){
$names .= '<br>'.$packet[name5];
}
if ($packet[name6]){
$names .= '<br>'.$packet[name6];
}

if($packet[address1]){
$defendant = ucPrep($names).'<br>'.ucPrep($packet[address1]).'<br>'.ucPrep($packet[city1]).', '.strtoupper($packet[state1]).' '.ucPrep($packet[zip1]);
}else{
$defendant = ucPrep($names);
}





if($affidavit[personal]){
	$served = $affidavit[personal];
}elseif($affidavit[resident]){
	$served = $affidavit[resident];
}elseif($affidavit[agent]){
	$served = $affidavit[agent];
}elseif($affidavit[officer]){
	$served = $affidavit[officer];
}
// packet information
$template = str_replace('[papers]',ucPrep($packet[addlDocs]),$template);
$template = str_replace('[packet]',ucPrep($packet[packet_id]),$template);
$template = str_replace('[altPlaintiff]',ucPrep($packet[altPlaintiff]),$template);
$template = str_replace('[defendant]',$defendant,$template);
$template = str_replace('[name1]',ucPrep($served),$template);
$template = str_replace('[address]',ucPrep($packet[address1]),$template);
$template = str_replace('[city]',ucPrep($packet[city1]),$template);
$template = str_replace('[state]',strtoupper($packet[state1]),$template);
$template = str_replace('[zip]',ucPrep($packet[zip1]),$template);
$template = str_replace('[courtState]',ucPrep($packet[courtState]),$template);
$template = str_replace('[courtCounty]',ucPrep($packet[circuit_court]),$template);
$template = str_replace('[courtType]',ucPrep($packet[courtType]),$template);
$template = str_replace('[clientFile]',ucPrep($packet[client_file]),$template);
$template = str_replace('[caseNumber]',ucPrep($packet[case_no]),$template);
// affidavit information
$template = str_replace('[who]',ucPrep($affidavit[whoX]),$template);
$template = str_replace('[when]',ucPrep($affidavit[whenX]),$template);
$template = str_replace('[when2]',strtoupper($affidavit[whenX]),$template);
$template = str_replace('[where]',ucPrep($affidavit[whereX]),$template);
$template = str_replace('[where2]',strtoupper($affidavit[whereX]),$template);
$template = str_replace('[how]',ucPrep($affidavit[howX]),$template);
$template = str_replace('[attempt1]',ucPrep($affidavit[attempt1]),$template);
$template = str_replace('[attempt2]',ucPrep($affidavit[attempt2]),$template);
$template = str_replace('[attempt3]',ucPrep($affidavit[attempt3]),$template);
$template = str_replace('[ifMail]',ucPrep($affidavit[ifMail]),$template);
$template = str_replace('[cb1]',strtoupper($affidavit[cb1]),$template);
$template = str_replace('[who1]',fillIn($affidavit[personal]),$template);
$template = str_replace('[cb2]',strtoupper($affidavit[cb2]),$template);
$template = str_replace('[who2]',fillIn($affidavit[resident]),$template);
$template = str_replace('[cb3]',strtoupper($affidavit[cb3]),$template);
$template = str_replace('[cb4]',strtoupper($affidavit[cb4]),$template);
$template = str_replace('[who4]',fillIn($affidavit[officer]),$template);
$template = str_replace('[who4a]',fillIn($affidavit[agent]),$template);
$template = str_replace('[cb5]',strtoupper($affidavit[cb5]),$template);
$template = str_replace('[who5]',fillIn($affidavit[whoX]),$template);
// server information
$template = str_replace('[serverName]',strtoupper($server[name]),$template);
$template = str_replace('[serverAge]',strtoupper($server[age]),$template);
$template = str_replace('[serverCompany]',strtoupper($server[company]),$template);
$template = str_replace('[serverAddress]',strtoupper($server[address]),$template);
$template = str_replace('[serverCity]',strtoupper($server[city]),$template);
$template = str_replace('[serverState]',strtoupper($server[state]),$template);
$template = str_replace('[serverZip]',strtoupper($server[zip]),$template);
$template = str_replace('[serverPhone]',strtoupper($server[phone]),$template);


$cord='S'.$affidavit[packet]."-1-".$affidavit[serverX]."-".$_GET[id]."%";


// we need to open a barcode and save it to the server



	$url = 'http://staff.mdwestserve.com/standard/barcode.php?barcode='.$cord.'&width=300&height=40';
    $timeout = 30;
    $curl = curl_init();
    curl_setopt ($curl, CURLOPT_URL, $url);
    curl_setopt ($curl, CURLOPT_TIMEOUT, $timeout);
    curl_setopt ($curl, CURLOPT_USERAGENT, sprintf("Mozilla/%d.0",rand(4,5)));
    curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, 0);
    $png = curl_exec ($curl);
    curl_close ($curl);
    
	
	
	
	
	
	$path = '/data/service/photos/'.str_replace('%','',$cord);
	$filename = $path.'/barcode.png';
	$urlname = 'http://mdwestserve.com/photographs/'.str_replace('%','',$cord).'/barcode.png';

// Let's make sure the file exists and is writable first.
			if (!file_exists($path)){
				mkdir ($path,0777);
			}
			if (file_exists($filename)){
				unlink ($filename);
			}
			touch ($filename);
if (is_writable($filename)) {

    // In our example we're opening $filename in append mode.
    // The file pointer is at the bottom of the file hence
    // that's where $somecontent will go when we fwrite() it.
    if (!$handle = fopen($filename, 'a')) {
         echo "Cannot open file ($filename)";
         exit;
    }

    // Write $somecontent to our opened file.
    if (fwrite($handle, $png) === FALSE) {
        echo "Cannot write to file ($filename)";
        exit;
    }


    fclose($handle);

} else {
    echo "The file $filename is not writable";
}

	// done with the barcode
	
	
	
	
	
	

echo "<center><div>$template</div><IMG border='1' SRC='$urlname' height='40' width='300'></center>";
}else{
	echo "Logged Out";
}
?>